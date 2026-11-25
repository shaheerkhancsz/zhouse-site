<?php

namespace WPFormsSlack\Provider\Settings;

use WPFormsSlack\Api\Auth;
use WPFormsSlack\Plugin;
use WPForms\Providers\Provider\Settings\FormBuilder as FormBuilderAbstract;

/**
 * Class FormBuilder handles functionality in the Form Builder.
 *
 * @since 1.0.0
 */
class FormBuilder extends FormBuilderAbstract {

	/**
	 * Register all hooks (actions and filters).
	 *
	 * @since 1.0.0
	 */
	protected function init_hooks() {

		parent::init_hooks();

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 *
	 * @uses FormBuilder::ajax_accounts_get()
	 * @uses FormBuilder::ajax_connections_get()
	 */
	private function hooks() {

		static $ajax_events = [
			'ajax_accounts_get',
			'ajax_connections_get',
		];

		array_walk(
			$ajax_events,
			static function ( $ajax_event, $key, $instance ) {

				add_filter(
					"wpforms_providers_settings_builder_{$ajax_event}_{$instance->core->slug}",
					[ $instance, $ajax_event ]
				);
			},
			$this
		);

		add_filter( 'wpforms_save_form_args', [ $this, 'save_form' ], 11, 3 );
		add_filter( 'wpforms_builder_strings', [ $this, 'strings' ], 10, 2 );
		add_filter( 'wpforms_builder_save_form_response_data', [ $this, 'refresh_connections' ], 10, 3 );
	}

	/**
	 * Pre-process provider data before saving it in form_data when editing a form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form Form array, usable with wp_update_post.
	 * @param array $data Data retrieved from $_POST and processed.
	 * @param array $args Custom data aren't intended to be saved.
	 *
	 * @return array
	 */
	public function save_form( $form, array $data, array $args ): array {

		$form = (array) $form;

		$form_data = json_decode( stripslashes( $form['post_content'] ), true );

		if ( ! empty( $form_data['providers'][ Plugin::SLUG ] ) ) {
			$modified_form_data = $this->modify_form_data( $form_data );

			if ( ! empty( $modified_form_data ) ) {
				$form['post_content'] = wpforms_encode( $modified_form_data );

				return (array) $form;
			}
		}

		/*
		 * This part works when modification is locked or current filter was called on NOT the Providers panel.
		 * Then we need to restore provider connections from the previous form content.
		 */

		// Get a "previous" form content (current content is still not saved).
		$prev_form = ! empty( $data['id'] ) ? wpforms()->obj( 'form' )->get( $data['id'], [ 'content_only' => true ] ) : [];

		if ( ! empty( $prev_form['providers'][ Plugin::SLUG ] ) ) {
			$provider = $prev_form['providers'][ Plugin::SLUG ];

			if ( ! isset( $form_data['providers'] ) ) {
				$form_data = array_merge( $form_data, [ 'providers' => [] ] );
			}

			$form_data['providers'] = array_merge( (array) $form_data['providers'], [ Plugin::SLUG => $provider ] );
			$form['post_content']   = wpforms_encode( $form_data );
		}

		return (array) $form;
	}

	/**
	 * Prepare modifications for the form content if it's not locked.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form content.
	 *
	 * @return array
	 */
	private function modify_form_data( array $form_data ): array {

		$lock = '__lock__';

		/**
		 * The Connection is locked.
		 * Why? A user clicked the "Save" button when one of the AJAX requests
		 * for retrieving data from the API was in progress or failed.
		 */
		if (
			isset( $form_data['providers'][ Plugin::SLUG ][ $lock ] ) &&
			absint( $form_data['providers'][ Plugin::SLUG ][ $lock ] ) === 1
		) {
			return [];
		}

		// Modify content as we need, done by reference.
		foreach ( $form_data['providers'][ Plugin::SLUG ] as $connection_id => &$connection_data ) {
			if ( $connection_id === $lock ) {
				unset( $form_data['providers'][ Plugin::SLUG ][ $lock ] );

				continue;
			}

			$connection = isset( $connection_data['account_id'] ) ? wpforms_slack()->get( 'client' )->new_connection( $connection_data['account_id'] ) : null;

			if ( $connection === null ) {
				continue;
			}

			$connection_data = wpforms_slack()->get( 'sanitizer' )->connection( $connection_data );
		}

		return $form_data;
	}

	/**
	 * Refresh the builder to update data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $response_data The data to be sent in the response.
	 * @param int   $form_id       Form ID.
	 * @param array $data          Form data.
	 *
	 * @return array
	 */
	public function refresh_connections( $response_data, int $form_id, array $data ): array {

		if ( empty( $data['providers'][ Plugin::SLUG ] ) ) {
			return $response_data;
		}

		$this->form_data = wpforms()->obj( 'form' )->get( $form_id, [ 'content_only' => true ] );

		$response_data[ Plugin::SLUG ] = $this->ajax_connections_get();

		return (array) $response_data;
	}

	/**
	 * Retrieve saved provider connections data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_connections_data(): array {

		if ( ! isset( $this->form_data['providers'][ Plugin::SLUG ] ) ) {
			return [];
		}

		return (array) $this->form_data['providers'][ Plugin::SLUG ];
	}

	/**
	 * Get the list of all saved connections.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ajax_connections_get(): array {

		$connections = [
			'connections'    => ! empty( $this->get_connections_data() ) ? array_reverse( $this->get_connections_data(), true ) : [],
			'conditionals'   => [],
			'actions'        => $this->get_actions(),
			'actions_fields' => [
				'send_direct_message'  => $this->get_send_direct_message_fields(),
				'send_channel_message' => $this->get_send_channel_message_fields(),
				'set_reminder'         => $this->get_set_reminder_fields(),
			],
		];

		foreach ( $connections['connections'] as $key => $connection ) {
			if ( empty( $connection['id'] ) ) {
				unset( $connections['connections'][ $key ] );
				continue;
			}

			// This will either return an empty placeholder or complete set of rules, as a DOM.
			$connections['conditionals'][ $connection['id'] ] = wpforms_conditional_logic()
				->builder_block(
					[
						'form'       => $this->form_data,
						'type'       => 'panel',
						'parent'     => 'providers',
						'panel'      => Plugin::SLUG,
						'subsection' => $connection['id'],
						// TODO: Since WPForms 1.9.5, this property will be redundant and can be removed once addon requirements are updated.
						'reference'  => __( 'Marketing provider connection', 'wpforms-slack' ),
					],
					false
				);
		}

		$accounts = $this->ajax_accounts_get();

		return array_merge( $connections, $accounts );
	}

	/**
	 * Get accounts data.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array with available accounts and custom fields, forms and tags related to that accounts.
	 *               May return an empty subarray if an account has no custom fields, forms and tags respectively.
	 */
	public function ajax_accounts_get(): array {

		$accounts = wpforms_slack()->get( 'account' )->get_all();
		$users    = [];
		$channels = [];

		foreach ( $accounts as $account_id => $account_name ) {
			$connection = ! empty( $account_id ) ? wpforms_slack()->get( 'client' )->new_connection( $account_id ) : null;

			if ( $connection === null ) {
				continue;
			}

			$users[ $account_id ]    = $this->get_users( $account_id, $connection );
			$channels[ $account_id ] = $this->get_channels( $account_id, $connection );
		}

		return [
			'accounts' => $accounts,
			'users'    => $users,
			'channels' => $channels,
		];
	}

	/**
	 * Get a list of users for an account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 * @param object $connection Connection object.
	 *
	 * @return array
	 */
	private function get_users( $account_id, $connection ) {

		$cache_key = 'wpforms_slack_users_' . $account_id;
		$users     = get_transient( $cache_key );

		if ( $users && is_array( $users ) ) {
			return $users;
		}

		$users = $connection->get_users();

		set_transient( $cache_key, $users, 15 * MINUTE_IN_SECONDS );

		return $users;
	}

	/**
	 * Get a list of channels for an account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 * @param object $connection Connection object.
	 *
	 * @return array
	 */
	private function get_channels( $account_id, $connection ) {

		$cache_key = 'wpforms_slack_channels_' . $account_id;
		$channels  = get_transient( $cache_key );

		if ( $channels && is_array( $channels ) ) {
			return $channels;
		}

		$channels = $connection->get_channels();

		set_transient( $cache_key, $channels, 15 * MINUTE_IN_SECONDS );

		return $channels;
	}

	/**
	 * Get a list of actions.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_actions(): array {

		return [
			'send_channel_message' => __( 'Send a Channel Message', 'wpforms-slack' ),
			'send_direct_message'  => __( 'Send a Direct Message', 'wpforms-slack' ),
			'set_reminder'         => __( 'Set a Reminder', 'wpforms-slack' ),
		];
	}

	/**
	 * Get fields for the send direct message action.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_send_direct_message_fields(): array {

		return [
			'users'   => [
				'label'       => __( 'Select User', 'wpforms-slack' ),
				'type'        => 'select',
				'multiple'    => true,
				'required'    => true,
				'placeholder' => __( '--- Select User ---', 'wpforms-slack' ),
			],
			'message' => [
				'label'       => __( 'Message', 'wpforms-slack' ),
				'type'        => 'textarea',
				'required'    => true,
				'description' => __( 'This message will be sent to the selected users.', 'wpforms-slack' ),
			],
		];
	}

	/**
	 * Get fields for the send channel message action.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_send_channel_message_fields(): array {

		return [
			'channels' => [
				'label'       => __( 'Select Channel', 'wpforms-slack' ),
				'type'        => 'select',
				'multiple'    => true,
				'required'    => true,
				'placeholder' => __( '--- Select a channel ---', 'wpforms-slack' ),
			],
			'message'  => [
				'label'       => __( 'Message', 'wpforms-slack' ),
				'type'        => 'textarea',
				'required'    => true,
				'description' => __( 'This message will be sent to the selected channels.', 'wpforms-slack' ),
			],
		];
	}

	/**
	 * Get fields for the set reminder action.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_set_reminder_fields(): array {

		return [
			'time'    => [
				'label'       => __( 'Select Time', 'wpforms-slack' ),
				'type'        => 'text',
				'required'    => true,
				'description' => wp_kses(
					'Enter a future reminder time using natural language. For example, try phrases like "in 10 minutes," "tomorrow at 9 AM," or "next Monday at 3 PM." <strong>Note</strong>: any typos will cause issues with scheduling the reminder.',
					[
						'strong' => [],
					]
				),
			],
			'message' => [
				'label'       => __( 'Message', 'wpforms-slack' ),
				'type'        => 'textarea',
				'required'    => true,
				'description' => __( 'This message will be sent to the authorized user when the reminder is triggered.', 'wpforms-slack' ),
			],
		];
	}

	/**
	 * Enqueue JavaScript and CSS files.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {

		parent::enqueue_assets();

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-slack-builder',
			WPFORMS_SLACK_URL . "assets/js/admin-builder{$min}.js",
			[ 'wpforms-admin-builder-providers', 'choicesjs', 'wpforms-chrono' ],
			WPFORMS_SLACK_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-slack-builder',
			'wpforms_slack_builder',
			[
				'remove_item' => __( 'Remove item', 'wpforms-slack' ),
			]
		);

		wp_enqueue_script(
			'wpforms-chrono',
			WPFORMS_SLACK_URL . 'assets/js/vendor/chrono.min.js',
			[],
			'2.7.7',
			true
		);

		wp_enqueue_style(
			'wpforms-slack-builder',
			WPFORMS_SLACK_URL . "assets/css/admin-builder{$min}.css",
			[],
			WPFORMS_SLACK_VERSION
		);
	}

	/**
	 * Add builder strings.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $strings Form Builder strings.
	 * @param object $form    Form data and settings.
	 *
	 * @return array
	 */
	public function strings( $strings, $form ): array {

		$strings = (array) $strings;

		$strings['slack'] = [
			'time_validation_error' => esc_html( 'Invalid time format. Please enter a valid future time or timestamp in English only. Ensure that you use clear time indicators, such as "tomorrow," or "next week." Avoid ambiguous expressions.' ),
		];

		return (array) $strings;
	}

	/**
	 * Use this method to register own templates for form builder.
	 * Make sure that you have `tmpl-` in template name in `<script id="tmpl-*">`.
	 *
	 * @since 1.0.0
	 */
	public function builder_custom_templates() {

		$templates = [
			'connection',
			'error',
			'select-field',
			'text-field',
			'textarea-field',
		];

		foreach ( $templates as $template ) {
			$template_name = ucwords( str_replace( '-', ' ', $template ) );
			$script_id     = 'tmpl-wpforms-' . esc_attr( Plugin::SLUG ) . '-builder-content-connection';

			if ( $template !== 'connection' ) {
				$script_id .= '-' . $template;
			}
			?>
			<!-- Single Slack connection block: <?php echo esc_attr( $template_name ); ?>. -->
			<script type="text/html" id="<?php echo esc_attr( $script_id ); ?>">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wpforms_render( WPFORMS_SLACK_PATH . 'templates/builder/' . $template );
				?>
			</script>
			<?php
		}
	}

	/**
	 * Check if account configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_configured(): bool {

		return ! empty( wpforms_slack()->get( 'account' )->get_all() );
	}

	/**
	 * Display provider screen.
	 *
	 * @since 1.0.0
	 */
	public function display_content() {

		if ( $this->is_configured() ) {

			parent::display_content();

			return;
		}
		?>

		<div class="wpforms-panel-content-section wpforms-builder-provider wpforms-panel-content-section-<?php echo esc_attr( Plugin::SLUG ); ?>"
			id="<?php echo esc_attr( Plugin::SLUG ); ?>-provider"
			data-provider="<?php echo esc_attr( Plugin::SLUG ); ?>">

			<?php $this->display_content_header(); ?>

			<div class="wpforms-builder-provider-connections-default">
				<img src="<?php echo esc_url( $this->core->icon ); ?>" alt="<?php esc_attr_e( 'Connect WPForms to Slack.', 'wpforms-slack' ); ?>">
				<div class="wpforms-builder-provider-settings-default-content">
					<p><?php esc_html_e( 'Get the most out of WPForms — use it with an active Slack account.', 'wpforms-slack' ); ?></p>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Section content header.
	 *
	 * @since 1.0.0
	 */
	protected function display_content_header() {

		if ( $this->is_configured() ) {
			parent::display_content_header();

			$this->check_connection();
			$this->display_notice();

			return;
		}
		?>
		<div class="wpforms-builder-provider-title wpforms-panel-content-section-title">
			<?php echo esc_html( $this->core->name ); ?>

			<button type="button" onclick="window.location.href='<?php echo esc_url( Auth::get_authorization_url() ); ?>'" class="wpforms-slack-auth">
				<?php esc_html_e( 'Add New Account', 'wpforms-slack' ); ?>
			</button>
		</div>
		<?php
		$this->check_connection();
		$this->display_notice();
	}

	/**
	 * Check if the connection is valid.
	 *
	 * @since 1.0.0
	 */
	private function check_connection() {

		foreach ( wpforms_slack()->get( 'account' )->get_invalid_accounts() as $account_id => $account ) {

			echo '<p class="wpforms-alert wpforms-alert-warning wpforms-slack-expired-accounts-warning">';

			if ( $account['account_error'] === 'missing_scope' ) {
				echo wpforms_slack()->get( 'account' )->get_missing_scope_account_notice_message( $account_id, $account ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo wpforms_slack()->get( 'account' )->get_expired_account_notice_message( $account_id, $account ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			echo '</p>';
		}
	}

	/**
	 * Show notice occurred while refreshing token.
	 *
	 * @since 1.0.0
	 */
	private function display_notice() {

		// Retrieve the transient value.
		$error_message = get_transient( 'wpforms_slack_error_message' );

		// Check if the transient contains a value.
		if ( $error_message ) {

			printf(
				'<p class="wpforms-alert wpforms-alert-warning wpforms-slack-expired-accounts-warning">%s</p>',
				esc_html( $error_message )
			);

			// Delete the transient after displaying it.
			delete_transient( 'wpforms_slack_error_message' );
		}
	}
}
