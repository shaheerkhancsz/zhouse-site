<?php

namespace WPFormsWebhooks;

use WPForms\Tasks\Meta;
use WPForms\Helpers\Crypto;
use WPForms_Entries_Single;
use WPFormsWebhooks\Admin\Entry;
use WPForms\Pro\AntiSpam\SpamEntry;
use WPFormsWebhooks\Helpers\Formatting;

/**
 * Class Process.
 *
 * @since 1.0.0
 */
class Process {

	/**
	 * Action name for async task.
	 *
	 * @since 1.0.0
	 */
	public const ACTION = 'wpforms_webhooks_process_delivery_webhook';

	/**
	 * Action for resending failed webhooks.
	 *
	 * @since 1.6.0
	 */
	public const RESEND_ACTION = 'wpforms_webhooks_resend_failed';

	/**
	 * Array of form fields.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Submitted form content.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $entry = [];

	/**
	 * Form data and settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $form_data = [];

	/**
	 * ID of a saved entry.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $entry_id;

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {

		add_action( 'wpforms_process_complete', [ $this, 'process' ], 10, 4 );
		add_action( self::ACTION,               [ $this, 'delivery' ] );

		// Register resubmit handler.
		add_action( 'wpforms_entry_details_init', [ $this, 'resend_failed_webhooks' ], -100 );
	}

	/**
	 * Receive all wpforms_process_complete params and do the actual processing.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    Array of form fields.
	 * @param array $entry     Submitted form content.
	 * @param array $form_data Form data and settings.
	 * @param int   $entry_id  ID of a saved entry.
	 */
	public function process( $fields, $entry, $form_data, $entry_id ) {

		// Only run if we have webhooks.
		if (
			empty( $form_data['settings']['webhooks'] ) ||
			! isset( $form_data['settings']['webhooks_enable'] ) ||
			! wp_validate_boolean( $form_data['settings']['webhooks_enable'] )
		) {
			return;
		}

		$this->fields    = $fields;
		$this->entry     = $entry;
		$this->form_data = $form_data;
		$this->entry_id  = $entry_id;

		// Another listener on this same `wpforms_process_complete` dispatch may have
		// flagged the entry at an earlier priority.
		if ( $this->is_entry_marked_as_spam( (int) $this->entry_id ) ) {
			return;
		}

		foreach ( $form_data['settings']['webhooks'] as $webhook ) {

			// Check for conditional logic.
			if ( ! $this->is_conditionals_passed( $webhook ) ) {
				continue;
			}

			wpforms()->obj( 'tasks' )
					 ->create( self::ACTION )->async()
					 ->params( $webhook, $this->fields, $this->form_data, $this->entry_id )
					 ->register();
		}
	}

	/**
	 * Process the addon async task - delivery the webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param int $meta_id Task meta ID.
	 */
	public function delivery( $meta_id ) {

		$meta = $this->get_task_meta( $meta_id );

		// We expect a certain type and number of params.
		if ( ! is_array( $meta ) || count( $meta ) !== 4 ) {
			return;
		}

		// We expect a certain meta data structure for this task.
		list( $webhook, $this->fields, $this->form_data, $this->entry_id ) = $meta;

		// Re-check at send time: the entry may have been flagged as spam after
		// this task was scheduled.
		if ( $this->is_entry_marked_as_spam( (int) $this->entry_id ) ) {
			return;
		}

		$this->send_webhook( $webhook );
	}

	/**
	 * Send a webhook and store the result.
	 *
	 * @since 1.6.0
	 *
	 * @param array $data Webhook data.
	 */
	private function send_webhook( array $data ): void {

		$webhook      = new Webhook( $data );
		$webhook_data = $webhook->get_data();
		$start_time   = microtime( true );

		// Request method.
		$method = strtoupper( $webhook_data['method'] );

		// Request headers.
		$headers                 = $this->fill_http_header_params_value( $webhook_data['headers'] );
		$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';

		// Request body.
		$body = $this->fill_http_body_params_value( $webhook_data['body'] );

		// Format request data.
		if (
			$method !== 'GET' &&
			( $webhook_data['format'] === 'json' )
		) {

			// Add content type header.
			$headers['Content-Type'] = 'application/json; charset=utf-8';

			/**
			 * Filter options, that will be use for JSON encode.
			 *
			 * Maybe you need specific options like `JSON_UNESCAPED_UNICODE` etc.
			 * for resolve issue with Cyrillic alphabet.
			 *
			 * @see https://www.php.net/manual/en/function.json-encode.php
			 *
			 * @since 1.0.0
			 *
			 * @param mixed $json_encode_options Bitmask consisting of constants.
			 */
			$json_encode_options = apply_filters( 'wpforms_webhooks_process_delivery_json_encode_options', 0 );

			// Encode request body.
			$body = wp_json_encode( $body, $json_encode_options );
		}

		// Prepare request arguments.
		$options = [
			'method'      => $method,
			'timeout'     => MINUTE_IN_SECONDS,
			'redirection' => 0,
			'httpversion' => '1.0',
			'blocking'    => true,
			'user-agent'  => sprintf( 'wpforms-webhooks/%s', WPFORMS_WEBHOOKS_VERSION ),
			'headers'     => $headers,
			'body'        => $body,
			'cookies'     => [],
		];

		/**
		 * Filter options used in an HTTP request.
		 *
		 * @since 1.0.0
		 *
		 * @param array $options      An array of HTTP request arguments.
		 * @param array $webhook_data Webhook data.
		 * @param array $fields       Fields data.
		 * @param array $fields       Form data.
		 * @param int   $entry_id     Entry ID.
		 */
		$options = (array) apply_filters( 'wpforms_webhooks_process_delivery_request_options', $options, $webhook_data, $this->fields, $this->form_data, $this->entry_id );

		// Add custom headers.
		$options['headers']['X-WPForms-Webhook-Id']        = $webhook_data['id'];
		$options['headers']['X-WPForms-Webhook-Signature'] = $this->generate_signature( $body, $webhook->get_secret() );

		// Retrieve the raw response from a safe HTTP request.
		$response = wp_remote_request( $webhook_data['url'], $options );
		$duration = round( microtime( true ) - $start_time, 5 );

		// Check if webhook delivery was successful.
		$is_success = ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) >= 200 && wp_remote_retrieve_response_code( $response ) < 300;

		if ( $is_success ) {
			// Clear failed webhook meta if this was a successful resend.
			$this->update_webhooks_meta( [ $webhook_data['id'] ] );
		} else {
			// Log error.
			$this->log_errors( $response, $webhook_data, $duration );
			// Store failed webhook ID in entry_meta for easy retrieval.
			$this->store_failed_webhook( $webhook_data );
		}

		/**
		 * Fire when Webhook was delivered or not.
		 *
		 * @since 1.0.0
		 *
		 * @param array          $options  An array of HTTP request arguments.
		 * @param \WP_Error|array $response Response data.
		 * @param array          $args     Additional arguments.
		 */
		do_action(
			'wpforms_webhooks_process_delivery_after',
			$options,
			$response,
			[
				'webhook'   => $webhook_data,
				'fields'    => $this->fields,
				'form_data' => $this->form_data,
				'entry_id'  => $this->entry_id,
			]
		);
	}

	/**
	 * Fill in HTTP header params value a form submitted values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params HTTP header parameters.
	 *
	 * @return array
	 */
	protected function fill_http_header_params_value( $params ) {

		$filled_params = [];

		foreach ( $params as $param_name => $param_value ) {

			// Determine if it a custom value.
			if ( is_array( $param_value ) && strpos( $param_name, 'custom_' ) === 0 ) {
				$param_name = substr_replace( $param_name, '', 0, 7 );
				$value      = ! empty( $param_value['secure'] ) ? Crypto::decrypt( $param_value['value'] ) : $param_value['value'];
				$value      = wpforms_process_smart_tags( $value, $this->form_data, $this->fields, $this->entry_id, 'webhooks-http-header' );
			} else {
				$value = isset( $this->fields[ $param_value ] ) ? $this->fields[ $param_value ]['value'] : '';
			}

			$value = Formatting::sanitize_header_value( $value );

			if ( ! wpforms_is_empty_string( $value ) ) {
				$filled_params[ $param_name ] = $value;
			}
		}

		// phpcs:disable WPForms.Comments.ParamTagHooks.InvalidParamTagsQuantity
		/**
		 * Filter HTTP header parameters.
		 *
		 * @since 1.0.0
		 * @since 1.2.0 Argument $fields added.
		 *
		 * @param array   $filled_params Sanitized HTTP header parameters.
		 * @param array   $params        HTTP header parameters before sanitization.
		 * @param Process $process       Process object.
		 * @param array   $fields        Array of form fields.
		 */
		return (array) apply_filters( 'wpforms_webhooks_process_fill_http_header_params_value', $filled_params, $params, $this, $this->fields );
		// phpcs:enable WPForms.Comments.ParamTagHooks.InvalidParamTagsQuantity
	}

	/**
	 * Fill in HTTP body params value a form submitted values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params HTTP body parameters.
	 *
	 * @return array
	 */
	protected function fill_http_body_params_value( $params ) {

		$filled_params = [];

		foreach ( $params as $param_name => $param_value ) {

			// Determine if it a custom value.
			if ( is_array( $param_value ) && strpos( $param_name, 'custom_' ) === 0 ) {
				$param_name = substr_replace( $param_name, '', 0, 7 );
				$value      = ! empty( $param_value['secure'] ) ? Crypto::decrypt( $param_value['value'] ) : $param_value['value'];
				$value      = wpforms_process_smart_tags( $value, $this->form_data, $this->fields, $this->entry_id, 'webhooks-http-body' );
			} else {
				$value = isset( $this->fields[ $param_value ] ) ? $this->format( $this->fields[ $param_value ] ) : '';
			}

			$filled_params[ $param_name ] = $value;
		}

		// phpcs:disable WPForms.Comments.ParamTagHooks.InvalidParamTagsQuantity
		/**
		 * Filter HTTP body parameters.
		 *
		 * @since 1.0.0
		 * @since 1.2.0 Argument $fields added.
		 *
		 * @param array   $filled_params Sanitized HTTP body parameters.
		 * @param array   $params        HTTP body parameters before sanitization.
		 * @param Process $process       Process object.
		 * @param array   $fields        Array of form fields.
		 */
		return (array) apply_filters( 'wpforms_webhooks_process_fill_http_body_params_value', $filled_params, $params, $this, $this->fields );
		// phpcs:enable WPForms.Comments.ParamTagHooks.InvalidParamTagsQuantity
	}

	/**
	 * Apply a special formatting for some WPForms fields.
	 *
	 * @since 1.1.0
	 *
	 * @param array $field Field data.
	 *
	 * @return string|array
	 */
	protected function format( $field ) {

		// We want to prepare data for better using.
		$value = $this->maybe_has_multiple_delimiter( $field['value'], $field['type'] );

		switch ( $field['type'] ) {
			case 'date-time':
				$value = [
					'value' => $field['value'],
					'unix'  => $field['unix'],
				];
				break;

			case 'file-upload':
				$value = $this->get_field_file_value( $field );
				break;

			case 'payment-single':
			case 'payment-checkbox':
			case 'payment-multiple':
			case 'payment-select':
			case 'payment-total':
				$value = Formatting::decode_currency_symbols( $value );
				break;
		}

		return $value;
	}

	/**
	 * Prepare a submitted `file-uplaod` field value(s).
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data.
	 *
	 * @return string|array Retrieve a value for the Classic or an array of values for the Modern File Upload field.
	 */
	protected function get_field_file_value( $field ) {

		if ( ! empty( $field['value_raw'] ) && is_array( $field['value_raw'] ) ) {
			$value = array_column( $field['value_raw'], 'value' );
		} else {
			$value = $field['value'];
		}

		return $value;
	}

	/**
	 * Replace newline and carriage-return symbols on `||`.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_value Field value.
	 * @param string $field_type  Field type.
	 *
	 * @return string
	 */
	protected function maybe_has_multiple_delimiter( $field_value, $field_type ) {

		$multiple_field_types = [
			'address'          => true,
			'select'           => true,
			'checkbox'         => true,
			'likert_scale'     => true,
			'ranking'          => true,
			'payment-checkbox' => true,
			'payment-multiple' => true,
			'payment-select'   => true,
		];

		if ( isset( $multiple_field_types[ $field_type ] ) ) {
			$field_value = str_replace( [ "\r\n", "\n" ], '||', $field_value );
		}

		return $field_value;
	}

	/**
	 * Generate a signature.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $body   Request body.
	 * @param string       $secret Secret used for generating signature.
	 *
	 * @return string
	 */
	private function generate_signature( $body, $secret ) {

		// Prepare a signature.
		// We do it like a Stripe - https://stripe.com/docs/webhooks/signatures.
		$timestamp = time();
		$payload   = sprintf( '%d.%s', $timestamp, is_array( $body ) ? wp_json_encode( $body ) : $body );
		$signature = hash_hmac( 'sha256', $payload, $secret );

		return "t={$timestamp},v={$signature}";
	}

	/**
	 * Get task meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $meta_id Task meta ID.
	 *
	 * @return array|null Null when no data available.
	 */
	protected function get_task_meta( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive something.
		if ( empty( $meta ) || empty( $meta->data ) ) {
			return null;
		}

		return $meta->data;
	}

	/**
	 * Process Conditional Logic for the webhook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $webhook Webhook data.
	 *
	 * @return bool False if CL rules stopped the connection execution, true otherwise.
	 */
	protected function is_conditionals_passed( $webhook ) {

		if (
			empty( $webhook['conditional_logic'] ) ||
			empty( $webhook['conditionals'] )
		) {
			return true;
		}

		$pass = wpforms_conditional_logic()->process( $this->fields, $this->form_data, $webhook['conditionals'] );

		if (
			! empty( $webhook['conditional_type'] ) &&
			$webhook['conditional_type'] === 'stop'
		) {
			$pass = ! $pass;
		}

		// Check for conditional logic.
		if ( ! $pass ) {
			wpforms_log(
				sprintf( 'The Webhooks connection %s was not processed due to conditional logic.', $webhook['name'] ?? '' ),
				$this->fields,
				[
					'type'    => [ 'provider', 'conditional_logic' ],
					'parent'  => $this->entry_id,
					'form_id' => $this->form_data['id'],
				]
			);
		}

		return $pass;
	}

	/**
	 * Determine whether the entry is currently flagged as spam in the database.
	 *
	 * Core never dispatches `wpforms_process_complete` for its own spam verdicts, so
	 * the status can only have been written by another listener on the same dispatch,
	 * by an asynchronous verdict, or by an admin. Re-read the persisted status rather
	 * than trusting the in-memory submission state.
	 *
	 * A missing entry row means nothing to check — a form that stores no entries, or
	 * an entry deleted before the queued task ran — and delivery goes ahead.
	 *
	 * @since 1.8.0
	 *
	 * @param int $entry_id Entry ID.
	 *
	 * @return bool
	 */
	private function is_entry_marked_as_spam( int $entry_id ): bool {

		if ( ! $entry_id ) {
			return false;
		}

		$entry_obj = wpforms()->obj( 'entry' );

		// Load-bearing, do not remove: the entry object exists only in Pro, and so does
		// SpamEntry. Returning here is what keeps the constant below from being resolved
		// on Lite, where the Lite build excludes `src/Pro`.
		if ( ! $entry_obj ) {
			return false;
		}

		// Bypass the capability check: the visitor submitting the form is not
		// logged in and would otherwise fail the `view_entry_single` gate.
		$entry_row = $entry_obj->get( $entry_id, [ 'cap' => false ] );

		if ( ! $entry_row || $entry_row->status !== SpamEntry::ENTRY_STATUS ) {
			return false;
		}

		wpforms_log(
			'The Webhooks connections were not processed because the entry is marked as spam.',
			$this->fields,
			[
				'type'    => [ 'provider', 'spam' ],
				'parent'  => $entry_id,
				'form_id' => $this->form_data['id'] ?? '',
			]
		);

		return true;
	}

	/**
	 * Log an API-related error with all the data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response Response data, may be array or \WP_Error.
	 * @param array $webhook  Specific webhook data that errored.
	 * @param float $duration Request duration.
	 */
	protected function log_errors( $response, $webhook, $duration ) {

		wpforms_log(
			esc_html__( 'Webhook delivery failed.', 'wpforms-webhooks' ) . "(#{$this->entry_id})",
			[
				'response' => $response,
				'webhook'  => $webhook,
				'duration' => $duration,
			],
			[
				'type'    => [ 'addon', 'error' ],
				'parent'  => $this->entry_id,
				'form_id' => $this->form_data['id'],
			]
		);
	}

	/**
	 * Store failed webhook ID in entry_meta.
	 *
	 * @since 1.6.0
	 *
	 * @param array $webhook Webhook data that failed.
	 */
	private function store_failed_webhook( array $webhook ): void {

		if ( empty( $webhook['id'] ) || empty( $this->entry_id ) ) {
			return;
		}

		$entry_meta = wpforms()->obj( 'entry_meta' );

		if ( ! $entry_meta ) {
			return;
		}

		// Get existing failed webhooks meta.
		$existing_meta = $entry_meta->get_meta(
			[
				'entry_id' => $this->entry_id,
				'type'     => Entry::FAILED_WEBHOOKS_META,
				'number'   => 1,
			]
		);

		$failed_webhooks = [];

		if ( ! empty( $existing_meta ) && ! empty( $existing_meta[0]->data ) ) {
			$failed_webhooks = json_decode( $existing_meta[0]->data, true );
			$failed_webhooks = is_array( $failed_webhooks ) ? $failed_webhooks : [];
		}

		// Add this webhook ID to the failed list if not already present.
		if ( ! in_array( $webhook['id'], $failed_webhooks, true ) ) {
			$failed_webhooks[] = $webhook['id'];
		}

		// Update entry_meta.
		if ( ! empty( $existing_meta ) && ! empty( $existing_meta[0]->id ) ) {
			$entry_meta->update(
				$existing_meta[0]->id,
				[
					'data' => wp_json_encode( $failed_webhooks ),
				]
			);

			return;
		}

		// Create entry meta if not exist.
		$entry_meta->add(
			[
				'entry_id' => $this->entry_id,
				'form_id'  => $this->form_data['id'] ?? 0,
				'user_id'  => get_current_user_id(),
				'type'     => Entry::FAILED_WEBHOOKS_META,
				'data'     => wp_json_encode( $failed_webhooks ),
			],
			'entry_meta'
		);
	}

	/**
	 * Resend failed webhooks for an entry.
	 *
	 * @since 1.6.0
	 *
	 * @param WPForms_Entries_Single $entry_single Entry single instance.
	 */
	public function resend_failed_webhooks( WPForms_Entries_Single $entry_single ): void {

		// Check if this is a resend request.
		if ( empty( $_GET[ self::RESEND_ACTION ] ) ) {
			return;
		}

		// Verify nonce.
		if (
			! isset( $_GET['_wpnonce'] ) ||
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), self::RESEND_ACTION )
		) {
			wp_die( esc_html__( 'Security check failed.', 'wpforms-webhooks' ) );
		}

		// Set up instance variables.
		$this->fields    = json_decode( $entry_single->entry->fields, true );
		$this->form_data = wpforms_decode( $entry_single->form->post_content );
		$this->entry_id  = (int) $entry_single->entry->entry_id;

		// Check if webhooks are enabled.
		if (
			empty( $this->form_data['settings']['webhooks'] ) ||
			! isset( $this->form_data['settings']['webhooks_enable'] ) ||
			! wp_validate_boolean( $this->form_data['settings']['webhooks_enable'] )
		) {
			$this->redirect();
		}

		// Get failed webhook IDs from entry_meta.
		$failed_webhook_ids = $this->get_failed_webhook_ids_for_resend();

		if ( empty( $failed_webhook_ids ) ) {
			$this->redirect();
		}

		// No spam check here on purpose: a resend is an explicit admin action on a
		// specific entry, and core already hides the entry actions for spam entries.
		$resend_count = 0;

		foreach ( $this->form_data['settings']['webhooks'] as $webhook ) {
			// Only resend webhooks that failed.
			if ( ! in_array( $webhook['id'], $failed_webhook_ids, true ) ) {
				continue;
			}

			// Check conditional logic.
			if ( ! $this->is_conditionals_passed( $webhook ) ) {
				continue;
			}

			$this->send_webhook( $webhook );

			++$resend_count;
		}

		if ( $resend_count > 0 ) {
			// Save resend data for notice display.
			$this->save_resend_notice_data( $resend_count );
		}

		$this->redirect();
	}

	/**
	 * Get failed webhook IDs for resend.
	 *
	 * @since 1.6.0
	 *
	 * @return array Array of webhook IDs that failed.
	 */
	private function get_failed_webhook_ids_for_resend(): array {

		$entry_meta = wpforms()->obj( 'entry_meta' );

		if ( ! $entry_meta ) {
			return [];
		}

		// Get failed webhooks from entry_meta.
		$failed_meta = $entry_meta->get_meta(
			[
				'entry_id' => $this->entry_id,
				'type'     => Entry::FAILED_WEBHOOKS_META,
				'number'   => 1,
			]
		);

		if ( empty( $failed_meta ) || empty( $failed_meta[0]->data ) ) {
			return [];
		}

		$failed_webhooks = json_decode( $failed_meta[0]->data, true );

		if ( ! is_array( $failed_webhooks ) ) {
			return [];
		}

		return $failed_webhooks;
	}

	/**
	 * Save resend notice data for display.
	 *
	 * @since 1.6.0
	 *
	 * @param int $count Number of webhooks resent.
	 */
	private function save_resend_notice_data( int $count ): void {

		$entry_meta = wpforms()->obj( 'entry_meta' );

		if ( ! $entry_meta ) {
			return;
		}

		$entry_meta->add(
			[
				'entry_id' => $this->entry_id,
				'form_id'  => $this->form_data['id'],
				'user_id'  => get_current_user_id(),
				'type'     => Entry::RESEND_FAILED_WEBHOOKS_META,
				'data'     => wp_json_encode( [ 'count' => $count ] ),
			],
			'entry_meta'
		);
	}

	/**
	 * Redirect with cleaned URL.
	 *
	 * @since 1.6.0
	 */
	private function redirect(): void {

		$args = [
			'page'     => 'wpforms-entries',
			'view'     => 'details',
			'entry_id' => $this->entry_id,
		];

		$redirect_url = add_query_arg( $args, admin_url( 'admin.php' ) );

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Update failed webhooks entry meta by removing specified webhook IDs.
	 * If no failed webhooks remain after removal, the meta entry is deleted.
	 *
	 * @since 1.6.0
	 *
	 * @param array $webhook_ids Webhook IDs to remove from failed webhooks meta.
	 */
	private function update_webhooks_meta( array $webhook_ids ): void {

		if ( empty( $webhook_ids ) ) {
			return;
		}

		$entry_meta = wpforms()->obj( 'entry_meta' );

		if ( ! $entry_meta ) {
			return;
		}

		$meta = $entry_meta->get_meta(
			[
				'entry_id' => $this->entry_id,
				'type'     => Entry::FAILED_WEBHOOKS_META,
				'number'   => 1,
			]
		);

		if ( empty( $meta ) || empty( $meta[0]->data ) ) {
			return;
		}

		$failed_webhooks = json_decode( $meta[0]->data, true );

		if ( ! is_array( $failed_webhooks ) ) {
			return;
		}

		// Remove webhook IDs from the failed list.
		$failed_webhooks = array_diff( $failed_webhooks, $webhook_ids );
		$failed_webhooks = array_values( $failed_webhooks );

		if ( empty( $failed_webhooks ) ) {
			$entry_meta->delete( $meta[0]->id );

			return;
		}

		$entry_meta->update(
			$meta[0]->id,
			[
				'data' => wp_json_encode( $failed_webhooks ),
			]
		);
	}
}
