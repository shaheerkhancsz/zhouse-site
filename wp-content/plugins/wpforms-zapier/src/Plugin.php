<?php

namespace WPFormsZapier;

use WPForms_Provider;
use WPForms_Updater;

/**
 * Zapier integration.
 *
 * @since 1.0.0
 */
class Plugin extends WPForms_Provider {

	/**
	 * Script handle.
	 *
	 * @since 1.9.0
	 */
	private const HANDLE = 'wpforms-admin-builder-zapier';

	/**
	 * Meta key for storing Zapier zap IDs.
	 *
	 * @since 1.9.0
	 */
	private const META_KEY = 'wpforms_zapier';

	/**
	 * Callback key for Zapier callback requests.
	 *
	 * @since 1.9.0
	 */
	private const CALLBACK_KEY = 'wpforms_zapier';

	/**
	 * Meta key for storing a Zapier plugin version in a form.
	 *
	 * @since 1.9.0
	 */
	private const VERSION_META_KEY = 'wpforms_zapier_version';

	/**
	 * Meta key for storing the Include Abandoned Entries default determined at connection time.
	 *
	 * @since 1.9.0.1
	 */
	private const ABANDONED_ENTRIES_META_KEY = 'wpforms_zapier_include_abandoned_entries';

	/**
	 * Zapier docs link.
	 *
	 * @since 1.4.0
	 */
	private const ZAPIER_DOCS_LINK = 'https://wpforms.com/docs/how-to-install-and-use-zapier-addon-with-wpforms/';

	/**
	 * Zapier SDK link.
	 *
	 * @since 1.5.0
	 */
	private const ZAPIER_SDK_LINK = 'https://cdn.zapier.com/packages/partner-sdk/v0/zapier-elements';

	/**
	 * Returns the instance.
	 *
	 * @since 1.6.0
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {

		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {

		$this->version  = WPFORMS_ZAPIER_VERSION;
		$this->name     = 'Zapier';
		$this->slug     = 'zapier';
		$this->priority = 60;
		$this->icon     = WPFORMS_ZAPIER_URL . 'assets/images/addon-icon-zapier.png';
		$this->type     = 'Zap';

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.5.0
	 */
	private function hooks(): void {

		add_action( 'init', [ $this, 'zapier_callback' ] );
		add_filter( 'wpforms_providers_zapier_configured', [ $this, 'builder_sidebar_configured' ] );
		add_action( 'wpforms_builder_enqueues_before', [ $this, 'builder_enqueues' ] );

		add_filter( 'wpforms_form_handler_add_notices', [ $this, 'add_disconnected_warning' ], 10, 3 );
		add_filter( 'wpforms_providers_status_is_configured', [ $this, 'is_configured' ], 10, 2 );
		add_filter( 'wpforms_providers_status_is_connected', [ $this, 'is_configured' ], 10, 2 );

		// Process abandoned entries.
		add_action( 'wpforms_process_complete_form_abandonment', [ $this, 'process_abandonment_entry' ], 10, 4 );
	}

	/**
	 * Check if the form is configured with Zapier.
	 * Configured and connected statuses are the same for the Zapier.
	 * It's configured only when connected.
	 *
	 * @since 1.7.0
	 *
	 * @param bool|mixed $is_configured If the form configured.
	 * @param string     $provider      Provider slug.
	 *
	 * @return bool
	 */
	public function is_configured( $is_configured, string $provider ): bool {

		if ( $provider !== 'zapier' ) {
			return (bool) $is_configured;
		}

		return ! empty( get_post_meta( $this->form_data['id'], self::META_KEY, true ) );
	}

	/**
	 * Add a warning to the form / form template that Zapier was disconnected after duplication.
	 *
	 * @since 1.6.0
	 *
	 * @param array|mixed $notices       Array with notices.
	 * @param array       $new_form_data Form data.
	 * @param int         $form_id       Original form ID.
	 *
	 * @return array
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection HtmlUnknownTarget
	 */
	public function add_disconnected_warning( $notices, array $new_form_data, int $form_id ): array {

		$notices = (array) $notices;

		// Check if the original form had any Zaps connected.
		$is_zapier_connected = get_post_meta( $form_id, self::META_KEY, true );

		if ( ! $is_zapier_connected ) {
			return $notices;
		}

		$notices['zapier'] = [
			'title'   => __( 'Zaps Have Been Disabled', 'wpforms-zapier' ),
			'code'    => 'disconnected_on_duplication',
			'message' => sprintf( /* translators: %s - URL to the list of Zaps. */
				__( 'Head over to the Zapier settings in the Marketing tab or visit your <a href="%s" target="_blank" rel="noopener noreferrer">Zapier account</a> to restore them.', 'wpforms-zapier' ),
				esc_url( 'https://zapier.com/app/zaps' )
			),
		];

		return $notices;
	}

	/**
	 * Load the plugin updater.
	 *
	 * @since 1.0.0
	 * @deprecated 1.7.0
	 *
	 * @todo Remove with core 1.9.2
	 *
	 * @param string $key License key.
	 *
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function updater( $key ) {

		_deprecated_function( __METHOD__, '1.7.0 of the WPForms Zapier plugin' );

		new WPForms_Updater(
			[
				'plugin_name' => 'WPForms Zapier',
				'plugin_slug' => 'wpforms-zapier',
				'plugin_path' => plugin_basename( WPFORMS_ZAPIER_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_ZAPIER_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_ZAPIER_VERSION,
				'key'         => $key,
			]
		);
	}

	/**
	 * Forms configured with Zapier do not have the connection information stored in the form_data,
	 * so the default indicator that shows if the form is configured will not work.
	 * Instead, we filter the indicator and check the correct data location.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function builder_sidebar_configured(): string {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = isset( $_GET['form_id'] ) ? absint( wp_unslash( $_GET['form_id'] ) ) : 0;

		if ( ! empty( $form_id ) ) {
			$zaps = get_post_meta( $form_id, self::META_KEY, true );

			if ( ! empty( $zaps ) ) {
				return 'configured';
			}
		}

		return '';
	}

	/**
	 * Enqueue scripts in the Form Builder.
	 *
	 * @since 1.5.0
	 */
	public function builder_enqueues(): void {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			self::HANDLE,
			WPFORMS_ZAPIER_URL . "assets/css/admin-builder-zapier{$min}.css",
			[ 'wpforms-builder' ],
			WPFORMS_ZAPIER_VERSION
		);

		wp_enqueue_script(
			self::HANDLE,
			WPFORMS_ZAPIER_URL . "assets/js/admin-builder-zapier{$min}.js",
			[ 'jquery', 'wpforms-builder', 'wpforms-utils' ],
			WPFORMS_ZAPIER_VERSION,
			true
		);
	}

	/**
	 * Define formatted fields delimiter.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function get_format_fields_delimiter(): string {

		/**
		 * Allow developers to filter a delimiter format.
		 *
		 * @since 1.4.0
		 *
		 * @param string $delimiter Delimiter.
		 */
		return (string) apply_filters( 'wpforms_zapier_get_format_fields_delimiter', ', ' ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Build the entry field information to send to Zapier.
	 *
	 * @since 1.0.0
	 *
	 * @param array|int  $form_data Form data or Form ID.
	 * @param array      $entry     Entry details.
	 * @param string|int $entry_id  Entry ID.
	 *
	 * @return array
	 */
	public function format_fields( $form_data, $entry = '', $entry_id = '' ): array { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded

		$data   = [];
		$fields = wpforms_get_form_fields( $form_data );

		if ( empty( $fields ) ) {
			return $data;
		}

		$entry_id = absint( $entry_id );

		if ( $entry_id ) {
			$data['id']     = $entry_id;
			$entry_obj      = wpforms()->obj( 'entry' );
			$entry_instance = $entry_obj ? $entry_obj->get( $entry_id ) : null;

			if ( $entry_instance ) {
				$status       = $entry_instance->status ?? '';
				$data['type'] = $status ? $status : 'published';
			}
		}

		foreach ( $fields as $field_id => $field ) {
			/* translators: %s - field id. */
			$label  = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : sprintf( esc_html__( 'Field #%s', 'wpforms-zapier' ), $field_id );
			$extras = [];

			$has_value = isset( $entry[ $field_id ]['value'] ) && ! wpforms_is_empty_string( $entry[ $field_id ]['value'] );

			if ( $has_value ) {
				$value = wpforms_sanitize_textarea_field( $entry[ $field_id ]['value'] );
			} else {
				$value = '';
			}

			if ( in_array( $field['type'], [ 'checkbox', 'select' ], true ) ) {
				$value = '';

				if ( $has_value && is_array( $entry ) && is_array( $entry[ $field_id ] ) ) {
					$value = implode( $this->get_format_fields_delimiter(), explode( "\n", trim( $entry[ $field_id ]['value'] ) ) );
				}
			}

			if ( $field['type'] === 'richtext' ) {
				$value = isset( $entry[ $field_id ]['value'] ) ? wpforms_sanitize_richtext_field( $entry[ $field_id ]['value'] ) : '';
			}

			// Allow multiple file uploading.
			if ( $field['type'] === 'file-upload' ) {

				$value = ( ! empty( $entry[ $field_id ]['value'] ) ) ? explode( "\n", stripslashes( $entry[ $field_id ]['value'] ) ) : [];
			}

			if ( empty( $entry ) ) {
				$data[] = [
					'key'   => 'field' . $field_id,
					'label' => $label,
					'type'  => 'unicode',
				];
			} else {
				$data[ 'field' . $field_id ] = $value;
			}

			// Add additional subfields.
			if ( $field['type'] === 'name' ) {
				$extras = [
					'first'  => esc_html__( 'First', 'wpforms-zapier' ),
					'middle' => esc_html__( 'Middle', 'wpforms-zapier' ),
					'last'   => esc_html__( 'Last', 'wpforms-zapier' ),
				];
			} elseif ( $field['type'] === 'checkbox' ) {
				foreach ( $field['choices'] as $choice_id => $choice ) {
					$choice['value'] = sanitize_text_field( $choice['value'] );
					$choice['label'] = sanitize_text_field( $choice['label'] );

					if ( empty( $choice['label'] ) ) {
						if (
							( count( $field['choices'] ) === 1 && $value === 'Checked' ) ||
							( count( $field['choices'] ) > 1 && 'Choice ' . $choice_id === $value )
						) {
							$choice_checked = 'checked';
						} else {
							$choice_checked = '';
						}
					} else {
						$choice_checked = ( strpos( $value, $choice['label'] ) !== false ) ? 'checked' : '';
					}
					if ( empty( $entry ) ) {
						$data[] = [
							'key'   => 'field' . $field_id . '_choice' . $choice_id,
							'label' => $choice['label'],
							'type'  => 'unicode',
						];
					} else {
						$choice['value'] = $has_value ? $choice['value'] : $choice['label'];
						$choice['value'] = ( ! empty( $choice_checked ) ) ? $choice['value'] : '';

						$data[ 'field' . $field_id . '_choice' . $choice_id ] = ( ! empty( $field['show_values'] ) && 1 === (int) $field['show_values'] ) ? $choice['value'] : $choice_checked;
					}
				}
			} elseif ( $field['type'] === 'address' ) {
				$extras = [
					'address1' => esc_html__( 'Line 1', 'wpforms-zapier' ),
					'address2' => esc_html__( 'Line 2', 'wpforms-zapier' ),
					'city'     => esc_html__( 'City', 'wpforms-zapier' ),
					'state'    => esc_html__( 'State', 'wpforms-zapier' ),
					'region'   => esc_html__( 'Region', 'wpforms-zapier' ),
					'postal'   => esc_html__( 'Postal', 'wpforms-zapier' ),
					'country'  => esc_html__( 'Country', 'wpforms-zapier' ),
				];
			} elseif ( $field['type'] === 'date-time' ) {
				$extras = [
					'date' => esc_html__( 'Date', 'wpforms-zapier' ),
					'time' => esc_html__( 'Time', 'wpforms-zapier' ),
					'unix' => esc_html__( 'Unix Timestamp', 'wpforms-zapier' ),
				];
			} elseif ( in_array( $field['type'], [ 'payment-total', 'payment-single', 'payment-multiple', 'payment-select' ], true ) ) {
				// Decode for currency symbols.
				if ( ! empty( $entry ) ) {
					$data[ 'field' . $field_id ] = html_entity_decode( $value );
				}

				// Send raw amount.
				$extras = [
					'amount_raw' => esc_html__( 'Plain Amount', 'wpforms-zapier' ),
				];
			}

			// Add extra fields.
			if ( ! empty( $extras ) ) {
				foreach ( $extras as $extra_key => $extra ) {
					$extra_value = ! empty( $entry[ $field_id ][ $extra_key ] ) ? sanitize_text_field( $entry[ $field_id ][ $extra_key ] ) : '';
					$extra_label = sprintf( '%s (%s)', $label, $extra );

					if ( empty( $entry ) ) {
						$data[] = [
							'key'   => 'field' . $field_id . '_' . $extra_key,
							'label' => $extra_label,
							'type'  => 'unicode',
						];
					} else {
						$data[ 'field' . $field_id . '_' . $extra_key ] = $extra_value;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Process and submit entry to the provider.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields    Final/sanitized submitted field data.
	 * @param array $entry     Copy of the original $_POST.
	 * @param array $form_data Form data and settings.
	 * @param int   $entry_id  Entry ID.
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id = 0 ) {

		// Only run if this form has connections for this provider and the entry has fields.
		$zaps = get_post_meta( $form_data['id'], self::META_KEY, true );

		if ( empty( $zaps ) || empty( $fields ) ) {
			return;
		}

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation, WPForms.PHP.ValidateHooks.InvalidHookName
		$data = apply_filters( 'wpforms_zapier_process_entry_data', $this->format_fields( $form_data, $fields, $entry_id ), $entry_id, $form_data );

		/*
		 * Fire for each Zap.
		 */
		foreach ( $zaps as $zap ) {
			// Only process this Zap if it is enabled.
			if ( empty( $zap['hook'] ) ) {
				continue;
			}

			$post_data = [
				'ssl'     => true,
				'body'    => wp_json_encode( $data ),
				'headers' => [
					'X-WPForms-Zapier-Version' => WPFORMS_ZAPIER_VERSION,
				],
			];
			$response  = wp_remote_post( $zap['hook'], $post_data );

			// Check for errors.
			if ( is_wp_error( $response ) ) {
				wpforms_log(
					esc_html__( 'Zapier Zap error', 'wpforms-zapier' ),
					$post_data,
					[
						'type'    => [ 'provider', 'error' ],
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					]
				);
			}
		}
	}

	/**
	 * Process and submit an entry to the provider if the form is configured to pass abandoned entries.
	 *
	 * @since 1.9.0
	 *
	 * @param array $fields    Final/sanitized submitted field data.
	 * @param array $entry     Copy of the original $_POST.
	 * @param array $form_data Form data and settings.
	 * @param int   $entry_id  Entry ID.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function process_abandonment_entry( $fields, $entry, $form_data, $entry_id ): void {

		// If the 'include_abandoned_entries' is enabled for this form, then process the entry.
		if ( $this->is_abandoned_entries_enabled( $form_data ) ) {
			$this->process_entry( $fields, $entry, $form_data, $entry_id );
		}
	}

	/**
	 * Return WPForms Zapier API key.
	 *
	 * If one hasn't been generated yet, then we create one and save it.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_apikey(): string {

		$key = (string) get_option( 'wpforms_zapier_apikey' );

		if ( empty( $key ) ) {
			$chars = array_merge( range( 0, 9 ), range( 'a', 'z' ) );
			$key   = '';

			for ( $i = 0; $i < 20; $i++ ) {
				$key .= $chars[ wp_rand( 0, count( $chars ) - 1 ) ];
			}

			update_option( 'wpforms_zapier_apikey', $key );
		}

		return $key;
	}

	/************************************************************************
	 * API methods - these methods interact directly with the provider API. *
	 ************************************************************************/

	/**
	 * Callback to provide Zapier with specific information for forms and fields.
	 *
	 * @since 1.0.0
	 *
	 * @noinspection NonSecureUniqidUsageInspection
	 */
	public function zapier_callback(): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded

		$data = [];

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$wpforms_zapier = isset( $_GET[ self::CALLBACK_KEY ] )
			? sanitize_text_field( wp_unslash( $_GET[ self::CALLBACK_KEY ] ) )
			: '';

		$wpforms_action = isset( $_GET['wpforms_action'] )
			? sanitize_text_field( wp_unslash( $_GET['wpforms_action'] ) )
			: '';

		$wpforms_form = isset( $_GET['wpforms_form'] )
			? absint( wp_unslash( $_GET['wpforms_form'] ) )
			: 0;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// WPForms Zapier API key is required.
		if ( empty( $wpforms_zapier ) ) {
			return;
		}

		// Callback action is required.
		if ( ! in_array( $wpforms_action, [ 'forms', 'entries', 'entry', 'subscribe', 'unsubscribe' ], true ) ) {
			$this->print_response_error( esc_html__( 'You have triggered an incorrect action that is not supported by WPForms Zapier.', 'wpforms-zapier' ) );

			return;
		}

		// Validate provided API Key.
		$apikey = get_option( 'wpforms_zapier_apikey' );

		if ( empty( $apikey ) || trim( $wpforms_zapier ) !== $apikey ) {
			$this->print_response_error( esc_html__( 'Please provide a correct WPForms Zapier API key.', 'wpforms-zapier' ) );

			return;
		}

		// Provide available forms.
		if ( $wpforms_action === 'forms' ) {
			$args         = [];
			$form_handler = wpforms()->obj( 'form' );

			if ( ! $form_handler ) {
				$this->print_response_error( esc_html__( 'Internal error.', 'wpforms-zapier' ), 400 );

				return;
			}

			// @WPFormsBackCompatStart User Generated Templates since WPForms v1.8.8
			if ( defined( get_class( $form_handler ) . '::POST_TYPES' ) ) {
				$args['post_type'] = $form_handler::POST_TYPES;
			}
			// @WPFormsBackCompatEnd

			$forms = $form_handler->get( '', $args );

			if ( empty( $forms ) ) {
				$this->print_response_error( esc_html__( 'Please create at least one form on your site.', 'wpforms-zapier' ), 400 );

				return;
			}

			foreach ( $forms as $form ) {
				$data['forms'][] = [
					'id'   => $form->ID,
					'name' => wpforms_decode_string( $form->post_title ),
				];
			}

			$this->print_response( $data );

			return;
		}

		// Provide available fields from a recent form entry.
		if ( $wpforms_action === 'entries' && ! empty( $wpforms_form ) ) {
			$entry_handler = wpforms()->obj( 'entry' );

			if ( ! $entry_handler ) {
				$this->print_response_error( esc_html__( 'Internal error.', 'wpforms-zapier' ), 400 );

				return;
			}

			$entries = $entry_handler->get_entries(
				[
					'form_id' => $wpforms_form,
				]
			);

			if ( empty( $entries ) ) {
				$this->print_response_error( esc_html__( 'Please make sure that you have at least one entry for your form.', 'wpforms-zapier' ), 400 );

				return;
			}

			foreach ( $entries as $entry ) {
				$fields     = json_decode( $entry->fields, true );
				$entry_data = $this->format_fields( $wpforms_form, $fields, $entry->entry_id );

				/**
				 * Filter the entry data for the callback.
				 *
				 * @since 1.8.0
				 *
				 * @param array $entry_data Entry data.
				 * @param int   $entry_id   Entry ID.
				 * @param int   $form_id    Form ID.
				 */
				$data[] = (array) apply_filters( 'wpforms_zapier_process_entry_data_for_callback', $entry_data, $entry->entry_id, $wpforms_form );
			}

			$this->print_response( $data );

			return;
		}

		// Provide available fields.
		if ( $wpforms_action === 'entry' && ! empty( $wpforms_form ) ) {
			$data = $this->format_fields( $wpforms_form );

			if ( ! $data ) {
				$this->print_response_error( esc_html__( 'Please make sure that your form is not empty and has fields.', 'wpforms-zapier' ), 400 );

				return;
			}

			/**
			 * Filter the entry-available fields.
			 *
			 * @since 1.8.0
			 *
			 * @param array $data         Entry available fields.
			 * @param int   $wpforms_form Form ID.
			 */
			$data = (array) apply_filters( 'wpforms_zapier_entry_available_fields', $data, $wpforms_form );

			$this->print_response( $data );
		}

		// Subscribe/Add Zap.
		if ( $wpforms_action === 'subscribe' ) {
			$form_id = $wpforms_form;

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$hook = ! empty( $_GET['hook_url'] ) ? esc_url_raw( wp_unslash( $_GET['hook_url'] ) ) : '';
			$name = ! empty( $_GET['zap_name'] ) ? sanitize_text_field( wp_unslash( $_GET['zap_name'] ) ) : '';
			$link = ! empty( $_GET['zap_link'] ) ? esc_url_raw( wp_unslash( $_GET['zap_link'] ) ) : '';
			$live = ! empty( $_GET['zap_live'] ) && strtolower( sanitize_text_field( wp_unslash( $_GET['zap_live'] ) ) ) === 'true';
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			$this->set_zapier_form_version( $form_id );

			$zaps = get_post_meta( $form_id, self::META_KEY, true );

			if ( empty( $zaps ) ) {
				$zaps = [];
			}

			$id          = uniqid();
			$zaps[ $id ] = [
				'name' => $name,
				'hook' => $hook,
				'link' => $link,
				'live' => $live,
				'date' => time(),
			];

			update_post_meta( $form_id, self::META_KEY, $zaps );

			$this->print_response(
				[
					'status'  => 'subscribed',
					'message' => esc_html__( 'The form was successfully subscribed to this Zap.', 'wpforms-zapier' ),
				]
			);

			return;
		}

		// Unsubscribe/Delete Zap.
		if ( $wpforms_action === 'unsubscribe' ) {
			$form_id = $wpforms_form;

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$url  = ! empty( $_GET['hook_url'] ) ? esc_url_raw( wp_unslash( $_GET['hook_url'] ) ) : '';
			$zaps = get_post_meta( $form_id, self::META_KEY, true );

			if ( ! empty( $zaps ) ) {
				foreach ( $zaps as $zap_id => $zap ) {
					if ( $url === $zap['hook'] ) {
						unset( $zaps[ $zap_id ] );
					}
				}

				if ( empty( $zaps ) ) {
					delete_post_meta( $form_id, self::META_KEY );
				} else {
					update_post_meta( $form_id, self::META_KEY, $zaps );
				}
			}

			$this->print_response(
				[
					'status'  => 'unsubscribed',
					'message' => esc_html__( 'The form was successfully unsubscribed from this Zap.', 'wpforms-zapier' ),
				]
			);

			return;
		}

		$this->print_response_error( esc_html__( 'Bad Request', 'wpforms-zapier' ), 400 );
	}

	/**
	 * Print error message.
	 *
	 * @since 1.7.0
	 *
	 * @param string $error      Error text.
	 * @param int    $error_code Error code.
	 *
	 * @return void
	 */
	private function print_response_error( string $error, int $error_code = 401 ): void {

		static $code_headers = [
			400 => 'HTTP/1.1 400 Bad Request',
			401 => 'HTTP/1.1 401 Unauthorized',
		];

		$header = $code_headers[ $error_code ] ?? $code_headers[400];

		wpforms_log(
			esc_html__( 'Zapier Zap error', 'wpforms-zapier' ),
			[
				'error'      => $error,
				'error_code' => $error_code,
			],
			[
				'type' => [ 'provider', 'error' ],
			]
		);

		nocache_headers();
		header( $header );

		echo wp_json_encode(
			[ 'error' => $error ]
		);

		exit;
	}

	/**
	 * Print response.
	 *
	 * @since 1.7.0
	 *
	 * @param array $data Response data.
	 *
	 * @return void
	 */
	private function print_response( array $data ): void {

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );

		echo wp_json_encode( $data );

		exit;
	}

	/**
	 * Post entry to Zapier webhook.
	 *
	 * @since 1.0.0
	 */
	public function zapier_post(): void {
	}

	/********************************************************
	 * Builder methods - these methods _build_ the Builder. *
	 ********************************************************/

	/**
	 * Custom Zapier builder content.
	 *
	 * @since 1.0.0
	 */
	public function builder_output() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = ! empty( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
		$zaps    = get_post_meta( $form_id, self::META_KEY, true );

		?>
		<div class="wpforms-panel-content-section wpforms-panel-content-section-<?php echo esc_attr( $this->slug ); ?>" id="<?php echo esc_attr( $this->slug ); ?>-provider">
			<div class="wpforms-panel-content-section-title">

				<?php echo esc_html( $this->name ); ?>

			</div>

			<div class="wpforms-provider-connections-wrap wpforms-clear">
				<div class="wpforms-provider-connections">
					<?php

					printf( '<p class="wpforms-%s-description">%s</p>', esc_attr( $this->slug ), esc_html__( 'Zapier automatically moves info between WPForms and other apps you use every day, so you can save time, reduce tedious tasks, and focus on your most important work. Set up automated workflows (called Zaps) that send form entries to your CRM, email app, marketing automation tool, and much more — no manual work or coding required.', 'wpforms-zapier' ) );

					$this->builder_output_doc_link();

					$this->settings_data();

					if ( ! empty( $zaps ) ) {
						foreach ( $zaps as $zap ) {
							echo '<div class="wpforms-provider-connection">';

							$name = ! empty( $zap['name'] ) ? sanitize_text_field( $zap['name'] ) : esc_html__( 'No name', 'wpforms-zapier' );
							$live = $zap['live'] ? esc_html__( 'Yes', 'wpforms-zapier' ) : esc_html__( 'No', 'wpforms-zapier' );

							echo '<div class="wpforms-provider-connection-header"><span>' . esc_html( $name ) . '</span></div>
								<div class="wpforms-provider-connection-content">
									<p>
										<strong>' . esc_html__( 'Date Connected', 'wpforms-zapier' ) . '</strong>
										<span>' . esc_html( wpforms_date_format( $zap['date'], '', true ) ) . '</span>
									</p>
									<p>
										<strong>' . esc_html__( 'Live', 'wpforms-zapier' ) . '</strong>
										<span>' . esc_html( $live ) . '</span>
									</p>

								</div>
							</div>
							';
						}
					}

					/**
					 * Filter the featured zaps list.
					 *
					 * @since 1.3.0
					 *
					 * @param array $featured_zaps Featured Zaps list.
					 */
					$zaps = (array) apply_filters( // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
						'wpforms_zapier_featured_zaps',
						[
							412343, // MailerLite.
							412371, // HubSpot.
							412378, // ConvertKit.
							412383, // Zoho CRM.
							14480,  // Infusionsoft.
							14477,  // Trello.
							413887, // Twilio SMS.
							14475,  // Google Calendar.
							14481,  // Slack.
						]
					);

					if ( ! empty( $zaps ) ) {
						$zaps = implode( ',', array_map( 'intval', $zaps ) );

						// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript, WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
						printf(
							'<h3 style="margin-top: 0;">%1$s</h3>
							<script id="wpforms-zapier-builder-embed-js" type="module" data-src="%2$s"></script>
							<link id="wpforms-zapier-builder-embed-css" rel="stylesheet" href="" data-href="%3$s" />
							<zapier-zap-templates ids="%4$s" limit="5" use-this-zap="show" theme="light"></zapier-zap-templates>',
							esc_html__( 'Popular ways to use WPForms + Zapier', 'wpforms-zapier' ),
							esc_attr( self::ZAPIER_SDK_LINK . '/zapier-elements.esm.js' ),
							esc_attr( self::ZAPIER_SDK_LINK . '/zapier-elements.css' ),
							esc_attr( $zaps )
						);
						// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript, WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
					}

					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Output settings data.
	 *
	 * @since 1.9.0
	 */
	private function settings_data(): void {

		printf( '<div class="wpforms-%s-settings-data-wrapper">', esc_attr( $this->slug ) );

		$this->builder_output_apikey();
		$this->builder_output_apiurl();
		$this->builder_output_abandoned_entries_settings();

		echo '</div>';
	}

	/**
	 * Output documentation paragraph.
	 *
	 * @since 1.3.0
	 */
	public function builder_output_doc_link(): void {

		$href = wpforms_utm_link( self::ZAPIER_DOCS_LINK, 'Marketing Integrations', 'Zapier Documentation' );

		printf( '<p><a href="%s" target="_blank" rel="noopener noreferrer" class="wpforms-%s-doc-link">%s</a></p>', esc_url( $href ), esc_attr( $this->slug ), esc_html__( 'Get started connecting WPForms with Zapier', 'wpforms-zapier' ) );
	}

	/**
	 * Add pass abandoned entries setting to the Zapier provider settings.
	 *
	 * @since 1.9.0
	 */
	private function builder_output_abandoned_entries_settings(): void {

		// Check if the Form Abandonment addon is active.
		if ( ! wpforms_is_addon_initialized( 'form-abandonment' ) ) {
			return;
		}

		$include_abandoned_entries = $this->is_abandoned_entries_enabled( $this->form_data );

		// An unchecked toggle is not submitted, so "explicitly off" would be indistinguishable
		// from "never set" on save. For connected forms, the hidden input persists an explicit "0"
		// into the form data, so the connection-time default is needed only until the first save.
		if ( ! empty( get_post_meta( absint( $this->form_data['id'] ?? 0 ), self::META_KEY, true ) ) ) {
			echo '<input type="hidden" name="providers[zapier][include_abandoned_entries]" value="0">';
		}

		wpforms_panel_field(
			'toggle',
			'zapier',
			'include_abandoned_entries',
			$this->form_data,
			esc_html__( 'Include Abandoned Entries', 'wpforms-zapier' ),
			[
				'parent' => 'providers',
				'class'  => isset( $this->form_data['settings']['form_abandonment'] ) ? '' : 'wpforms-hidden',
				'value'  => $include_abandoned_entries ? 1 : 0,
			]
		);
	}

	/**
	 * Determine whether abandoned entries should be passed to Zapier for a form.
	 *
	 * Respects the value saved in the form data when it exists. Otherwise, falls back
	 * to the default stored in post meta when the form was connected to Zapier.
	 *
	 * @since 1.9.0.1
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool
	 */
	private function is_abandoned_entries_enabled( array $form_data ): bool {

		if ( isset( $form_data['providers']['zapier']['include_abandoned_entries'] ) ) {
			return ! empty( $form_data['providers']['zapier']['include_abandoned_entries'] );
		}

		$form_id = absint( $form_data['id'] ?? 0 );

		if ( ! $form_id ) {
			return false;
		}

		return ! empty( get_post_meta( $form_id, self::ABANDONED_ENTRIES_META_KEY, true ) );
	}

	/**
	 * Set Zapier form version.
	 *
	 * @since 1.9.0
	 * @since 1.9.0.1 Stores the Include Abandoned Entries default in post meta instead of re-saving the form.
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return void
	 */
	private function set_zapier_form_version( int $form_id ): void {

		$version = get_post_meta( $form_id, self::VERSION_META_KEY, true );

		if ( $version ) {
			return;
		}

		// If a version is not set, do a one-time settings update.
		// The logic is described in #15932.
		$is_configured = ! empty( get_post_meta( $form_id, self::META_KEY, true ) );

		// For old (already configured) forms, we set the version to 1.0.0.
		// For new (not configured) forms, we set the current addon version.
		$version = $is_configured ? '1.0.0' : WPFORMS_ZAPIER_VERSION;

		update_post_meta( $form_id, self::VERSION_META_KEY, $version );

		// The default lives in post meta instead of the form data: re-saving the form
		// via WPForms_Form::update() from this unauthenticated callback strips all HTML
		// from the form content.
		update_post_meta( $form_id, self::ABANDONED_ENTRIES_META_KEY, version_compare( $version, '1.9.0', '>=' ) ? 1 : 0 );
	}

	/**
	 * Output API key paragraph.
	 *
	 * @since 1.3.0
	 */
	public function builder_output_apikey(): void {

		printf(
			'<p><span>' . esc_html__( 'Your WPForms Zapier API key', 'wpforms-zapier' ) . '</span>: %s</p>',
			esc_html( $this->get_apikey() )
		);
	}

	/**
	 * Output API URL paragraph.
	 *
	 * @since 1.5.0
	 */
	public function builder_output_apiurl(): void {

		printf(
			'<p><span>' . esc_html__( 'Your website URL for Zapier configuration', 'wpforms-zapier' ) . '</span>: %s</p>',
			esc_url( home_url() )
		);
	}

	/*************************************************************************
	 * Integrations tab methods - these methods relate to the settings page. *
	 *************************************************************************/

	/**
	 * Add a custom Zapier panel to the Settings Integrations tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array $active   Active integrations.
	 * @param array $settings Configuration settings for all providers.
	 *
	 * @noinspection HtmlUnknownTarget*/
	public function integrations_tab_options( $active, $settings ) {

		$forms = get_posts(
			[
				// phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				'posts_per_page' => 999,
				'post_type'      => 'wpforms',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'     => [
					[
						'key'     => self::META_KEY,
						'compare' => 'EXISTS',
					],
				],
			]
		);

		$slug      = esc_attr( $this->slug );
		$name      = esc_html( $this->name );
		$connected = ! empty( $forms );
		$class     = $connected ? 'connected' : '';
		$arrow     = 'right';

		// This lets us highlight a specific service by a special link.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$wpforms_integration = isset( $_GET['wpforms-integration'] ) ?
			sanitize_text_field( wp_unslash( $_GET['wpforms-integration'] ) ) :
			'';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! empty( $wpforms_integration ) ) {
			if ( $this->slug === $wpforms_integration ) {
				$class .= ' focus-in';
				$arrow  = 'down';
			} else {
				$class .= ' focus-out';
			}
		}

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<div id="wpforms-integration-<?php echo $slug; ?>" class="wpforms-settings-provider wpforms-clear <?php echo $slug; ?> <?php echo $class; ?>">
			<div class="wpforms-settings-provider-header wpforms-clear" data-provider="<?php echo $slug; ?>">
				<div class="wpforms-settings-provider-logo">
					<i title="<?php esc_attr_e( 'Show Accounts', 'wpforms-zapier' ); ?>" class="fa fa-chevron-<?php echo esc_attr( $arrow ); ?>" aria-hidden="true"></i>
					<img src="<?php echo esc_url( $this->icon ); ?>" alt="">
				</div>

				<div class="wpforms-settings-provider-info">
					<h3><?php echo $name; ?></h3>
					<p>
						<?php
						printf( /* translators: %s - provider name. */
							esc_html__( 'Integrate %s with WPForms', 'wpforms-zapier' ),
							$name
						);
						?>
					</p>
					<span class="connected-indicator green">
						<i class="fa fa-check-circle-o" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Connected', 'wpforms-zapier' ); ?></span>
					</span>
				</div>
			</div>

			<div class="wpforms-settings-provider-accounts" id="provider-<?php echo $slug; ?>">
				<p>
					<?php
					printf( /* translators: %s - API key. */
						esc_html__( 'Your WPForms Zapier API key: %s', 'wpforms-zapier' ),
						'<code>' . esc_attr( $this->get_apikey() ) . '</code>'
					);
					?>
				</p>

				<p>
					<?php
					printf( /* translators: %s - site home URL. */
						esc_html__( 'Your website URL for Zapier configuration: %s', 'wpforms-zapier' ),
						'<code>' . esc_url( home_url() ) . '</code>'
					);
					?>
				</p>

				<p>
					<?php
					printf(
						wp_kses( /* translators: %1$s - Documentation URL. */
							__(
								'If you need help connecting WPForms to Zapier, <a href="%1$s" rel="noopener noreferrer" target="_blank">read our documentation</a>.',
								'wpforms-zapier'
							),
							[
								'a' => [
									'href'   => [],
									'rel'    => [],
									'target' => [],
								],
							]
						),
						esc_url( wpforms_utm_link( self::ZAPIER_DOCS_LINK, 'Settings - Integration', 'Zapier Documentation' ) )
					);
					?>
				</p>

				<?php

				if ( empty( $forms ) ) {
					echo '<p>' . esc_html__( 'No forms are currently connected.', 'wpforms-zapier' ) . '</p>';

				} else {
					echo '<p>' . esc_html__( 'The forms below are currently connected to Zapier.', 'wpforms-zapier' ) . '</p>';
					echo '<div class="wpforms-settings-provider-accounts-list">';
					echo '<ul>';
					foreach ( $forms as $form ) {
						echo '<li>';
						echo '<span class="label">' . esc_html( $form->post_title ) . '</span>';
						echo '<span class="date">';
						echo esc_html(
							sprintf( /* translators: %1$s - Connection date. */
								__( 'Connected on: %1$s', 'wpforms-zapier' ),
								wpforms_date_format( $form->post_date, '', true )
							)
						);
						echo '</span>';
						echo '</li>';
					}
					echo '</ul>';
					echo '</div>';
				}

				?>
			</div>
		</div>
		<?php
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
