<?php

namespace WPFormsEntryAutomation\Admin;

use WPForms_Builder;
use WPForms\Pro\Admin\Entries\Helpers;
use WPForms_Builder_Panel_Settings;
use WPFormsEntryAutomation\Export\Provider\FTP;
use WPFormsEntryAutomation\Export\Provider\GoogleDrive;
use WPFormsEntryAutomation\Helpers\DropboxClient;
use WPFormsEntryAutomation\Helpers\FormBuilderAjax;
use WPFormsEntryAutomation\Plugin;
use WPFormsGoogleDrive\Api\Client;

/**
 * Form builder integration.
 *
 * @since 1.0.0
 */
class FormBuilder {

	/**
	 * AJAX handler.
	 *
	 * @since 1.0.0
	 *
	 * @var FormBuilderAjax
	 */
	private $ajax;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->ajax = new FormBuilderAjax( Plugin::SLUG );
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		$namespace_slug = Plugin::SLUG;

		$this->ajax->hooks();

		// Add settings panel hooks.
		add_filter( 'wpforms_builder_settings_sections', [ $this, 'register_settings_section' ], 10, 2 );
		add_action( 'wpforms_form_settings_panel_content', [ $this, 'settings_section_content' ] );
		add_action( 'wpforms_builder_print_footer_scripts', [ $this, 'builder_custom_templates' ], 11 );
		add_filter( "wpforms_builder_ajax_{$namespace_slug}_tasks_get", [ $this, 'ajax_get_tasks' ], 10, 2 );
		add_filter( 'wpforms_save_form_args', [ $this, 'save_form' ], 11, 3 );
		add_filter( 'wpforms_builder_save_form_response_data', [ $this, 'refresh_connections' ], 10, 3 );
		add_filter( 'wpforms_builder_strings', [ $this, 'register_strings' ], 10, 2 );

		// We have some dependencies on providers-related scripts.
		if ( ! empty( $_GET['form_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_action( 'wpforms_builder_enqueues', [ $this, 'enqueue_assets' ] );
		}
	}

	/**
	 * Process and return tasks and connections based on form data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response  The response from the AJAX request.
	 * @param array $form_data The data from the form submission.
	 *
	 * @return array An array containing the processed tasks and their connections.
	 *
	 * @noinspection PhpUnusedParameterInspection, PhpMissingParamTypeInspection
	 */
	public function ajax_get_tasks( $response, $form_data ): array {

		$form_id = $form_data['id'] ?? 0;
		$tasks   = (array) ( $form_data['settings'][ Plugin::SLUG ] ?? [] );

		// Remove __lock__ if it exists (shouldn't be in DB, but safety check).
		unset( $tasks['__lock__'] );

		// Filter out non-array values (only real task connections should be arrays).
		$tasks = array_filter( $tasks, 'is_array' );

		$tasks = array_map(
			static function ( $task ) use ( $form_id ) {
				$task['state'] = wpforms_builder_settings_block_get_state(
					$form_id,
					$task['id'],
					Plugin::SLUG
				);

				return $task;
			},
			$tasks
		);

		$tasks = array_map( [ $this, 'mask_ftp_password' ], $tasks );

		$dropbox_folders = [];

		if ( wpforms_is_addon_initialized( 'dropbox' ) ) {
			$dropbox_folders = wpforms_entry_automation()->get( 'dropbox_client' )->get_folders( $tasks, $form_data );
		}

		return [
			'connections'     => $tasks,
			'dropbox_folders' => $dropbox_folders,
		];
	}

	/**
	 * Replace a stored FTP password with a placeholder before it reaches the client.
	 *
	 * The real password is never sent to the builder. `save_form()` resolves the
	 * placeholder back to the stored password when the connection is saved unchanged.
	 *
	 * @since 1.3.0
	 *
	 * @param array $task Task (connection) data.
	 *
	 * @return array
	 */
	private function mask_ftp_password( array $task ): array {

		if ( ! empty( $task['ftp']['password'] ) ) {
			$task['ftp']['password'] = FTP::PASSWORD_PLACEHOLDER;
		}

		return $task;
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
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection JsonEncodingApiUsageInspection
	 */
	public function save_form( $form, array $data, array $args ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$form = (array) $form;

		// Skip modifications if this method was called outside the Form Builder.
		if ( empty( $args['context'] ) || $args['context'] !== 'save_form' ) {
			return $form;
		}

		$form_data = (array) json_decode( stripslashes( $form['post_content'] ?? '' ), true );

		// Get a "previous" form content (current content is still not saved).
		$prev_form = ! empty( $data['id'] ) ? (array) wpforms()->obj( 'form' )->get( $data['id'], [ 'content_only' => true ] ) : [];

		if ( ! empty( $form_data['settings'][ Plugin::SLUG ] ) ) {
			$this->restore_ftp_passwords( $form_data, $prev_form );

			$form_data          = wpforms_entry_automation()->get( 'delivery_manager' )->restrict_connections( $form_data, $prev_form );
			$modified_form_data = $this->modify_form_data( $form_data );

			if ( ! empty( $modified_form_data ) ) {
				$form['post_content'] = wpforms_encode( $modified_form_data );

				return $form;
			}

			// A locked connection is kept as submitted below, so the restricted copy is what has to be stored.
			$form['post_content'] = wpforms_encode( $form_data );
		}

		/*
		 * This part works when modification is locked or the current filter was called on NOT the Providers panel.
		 * Then we need to restore provider connections from the previous form content.
		 */

		if ( ! empty( $prev_form['settings'][ Plugin::SLUG ] ) ) {
			$provider = $prev_form['settings'][ Plugin::SLUG ];

			// Remove __lock__ when restoring previous data.
			// This handles cases where a form is saved from another panel or when a connection is locked.
			unset( $provider['__lock__'] );

			// If after removing __lock__ there are no real tasks, don't restore anything.
			// This prevents creating empty connection boxes.
			if ( empty( $provider ) ) {
				return $form;
			}

			if ( ! isset( $form_data['settings'] ) ) {
				$form_data = array_merge( $form_data, [ 'settings' => [] ] );
			}

			$form_data['settings'] = array_merge( (array) $form_data['settings'], [ Plugin::SLUG => $provider ] );
			$form['post_content']  = wpforms_encode( $form_data );
		}

		return $form;
	}

	/**
	 * Resolve the FTP password placeholder back to the stored password.
	 *
	 * The builder never receives the real password (see mask_ftp_password()), so an
	 * unmodified connection resubmits the placeholder on every save. Swap it back for
	 * the previously stored password so it isn't overwritten with the placeholder.
	 *
	 * @since 1.3.0
	 *
	 * @param array $form_data Form data being saved, modified by reference.
	 * @param array $prev_form Previously stored form data.
	 */
	private function restore_ftp_passwords( array &$form_data, array $prev_form ): void {

		$prev_connections = (array) ( $prev_form['settings'][ Plugin::SLUG ] ?? [] );

		foreach ( $form_data['settings'][ Plugin::SLUG ] as $connection_id => &$connection ) {
			if ( ! is_array( $connection ) || ( $connection['ftp']['password'] ?? '' ) !== FTP::PASSWORD_PLACEHOLDER ) {
				continue;
			}

			$connection['ftp']['password'] = $prev_connections[ $connection_id ]['ftp']['password'] ?? '';
		}

		unset( $connection );
	}

	/**
	 * Add summary text to the provided response data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $response_data The current response data array.
	 * @param int   $form_id       The form ID being processed.
	 * @param array $data          Additional data including entry automation details.
	 *
	 * @return array Modified response data with added summary text.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function refresh_connections( $response_data, $form_id, array $data ): array {

		$response_data    = (array) $response_data;
		$entry_automation = $data['settings'][ Plugin::SLUG ] ?? [];
		$form_obj         = wpforms()->obj( 'form' );

		if ( empty( $entry_automation ) || ! $form_obj ) {
			return $response_data;
		}

		$form_data                     = $form_obj->get( $form_id, [ 'content_only' => true ] );
		$response_data[ Plugin::SLUG ] = $this->ajax_get_tasks( null, $form_data );

		return $response_data;
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
			isset( $form_data['settings'][ Plugin::SLUG ][ $lock ] ) &&
			absint( $form_data['settings'][ Plugin::SLUG ][ $lock ] ) === 1
		) {
			return [];
		}

		// Modify content as we need, done by reference.
		foreach ( $form_data['settings'][ Plugin::SLUG ] as $connection_id => &$connection_data ) {
			if ( $connection_id === $lock ) {
				unset( $form_data['settings'][ Plugin::SLUG ][ $lock ] );

				continue;
			}

			// Skip non-array values to prevent type errors from other plugins adding data to this namespace.
			if ( ! is_array( $connection_data ) ) {
				continue;
			}

			$connection_data            = wpforms_entry_automation()->get( 'sanitizer' )->sanitize( $connection_data, $form_data );
			$connection_data['summary'] = $this->get_connection_summary( $connection_data );
		}

		unset( $connection_data );

		if ( ! empty( $form_data['settings'][ Plugin::SLUG ] ) ) {
			$form_data['settings'][ Plugin::SLUG ] = $this->prepare_items( $form_data['settings'][ Plugin::SLUG ] );
		}

		return $form_data;
	}

	/**
	 * Prepare items based on their schedule queue.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items List of items to process.
	 *
	 * @return array Modified list of items with `parent_id` set where applicable.
	 */
	private function prepare_items( array $items ): array {

		$previous_id = null;

		foreach ( $items as $key => $item ) {
			if ( ! empty( $item['schedule']['queue'] ) ) {
				$items[ $key ]['parent_id'] = $previous_id;
			}

			$previous_id = $item['id'];
		}

		return $items;
	}

	/**
	 * Get connection summary.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return string
	 */
	public function get_connection_summary( array $connection_data ): string {

		if ( $connection_data['action'] === 'export' ) {
			return $this->get_export_summary( $connection_data );
		}

		if ( $connection_data['action'] === 'delete' ) {
			return $this->get_delete_summary( $connection_data );
		}

		return '';
	}

	/**
	 * Get Export summary.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return string Summary.
	 */
	private function get_export_summary( array $connection_data ): string {

		$schedule  = $connection_data['schedule'] ?? [];
		$format    = $connection_data['format'] ?? [];
		$scheduler = wpforms_entry_automation()->get( 'scheduler' );
		$statuses  = $scheduler::get_statuses( $connection_data['filters'] ?? [] );

		return sprintf(
			/* translators: %1$s - entries statuses, %2$s - format, %3$s - export to, %4$s - schedule. */
			__( 'This task is scheduled to export %1$s entries in %2$s format and %3$s %4$s.', 'wpforms-entry-automation' ),
			$scheduler::get_string_list( $statuses ),
			strtoupper( $format ),
			$this->get_export_to_summary( $connection_data ),
			$scheduler::get_summary_formatted_time( $schedule )
		);
	}

	/**
	 * Get Export To summary.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return string Summary.
	 */
	private function get_export_to_summary( array $connection_data ): string {

		$export_to = $connection_data['export_to'] ?? '';

		if ( $export_to === 'email' ) {
			$address = $connection_data['email']['address'] ?? '';

			/* translators: %1$s - email addresses. */
			return sprintf( __( 'email to %1$s', 'wpforms-entry-automation' ), wpforms_process_smart_tags( $address, [] ) );
		}

		$delivery = wpforms_entry_automation()->get( 'delivery_manager' )->get_delivery( $export_to );

		/* translators: %1$s - export to. */
		return sprintf( __( 'upload to %1$s', 'wpforms-entry-automation' ), $delivery->get_title() ?? $export_to );
	}

	/**
	 * Get Delete summary.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return string Summary.
	 */
	private function get_delete_summary( array $connection_data ): string {

		$schedule  = $connection_data['schedule'] ?? [];
		$scheduler = wpforms_entry_automation()->get( 'scheduler' );

		$statuses = $scheduler::get_statuses( $connection_data['filters'] ?? [] );

		return sprintf(
			/* translators: %1$s - entries statuses, %2$s - schedule. */
			__( 'This task is scheduled to delete %1$s entries %2$s.', 'wpforms-entry-automation' ),
			$scheduler::get_string_list( $statuses ),
			$scheduler::get_summary_formatted_time( $schedule )
		);
	}

	/**
	 * Register settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections  Settings sections.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 *
	 * @noinspection PhpMissingParamTypeInspection, PhpUnusedParameterInspection
	 */
	public function register_settings_section( $sections, array $form_data ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$sections                 = (array) $sections;
		$sections[ Plugin::SLUG ] = esc_html__( 'Entry Automation', 'wpforms-entry-automation' );

		return $sections;
	}

	/**
	 * Settings section content.
	 *
	 * @since 1.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings panel instance.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function settings_section_content( $instance ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		?>

		<div class="wpforms-panel-content-section wpforms-panel-content-section-<?php echo esc_attr( Plugin::SLUG ); ?>" id="<?php echo esc_attr( Plugin::SLUG ); ?>">
			<div class="wpforms-panel-content-section-title wpforms-builder-entity-title">
				<?php esc_html_e( 'Entry Automation', 'wpforms-entry-automation' ); ?>

				<span class="wpforms-builder-provider-title-spinner wpforms-hidden">
					<i class="wpforms-loading-spinner wpforms-loading-md wpforms-loading-inline"></i>
				</span>

				<button class="wpforms-builder-entity-connection-add"
						data-form_id="<?php echo esc_attr( $instance->form->ID ); ?>">
					<?php esc_html_e( 'Add New Task', 'wpforms-entry-automation' ); ?>
				</button>
			</div>

			<input type="hidden" class="wpforms-builder-entity-connections-save-lock" value="1" name="settings[<?php echo esc_attr( Plugin::SLUG ); ?>][__lock__]">

			<?php $this->display_content(); ?>
		</div>
		<?php
	}

	/**
	 * Display content.
	 *
	 * @since 1.0.0
	 */
	private function display_content(): void {

		$this->empty_state();
		?>
		<div class="wpforms-panel-content-section-<?php echo esc_attr( Plugin::SLUG ); ?>-content">
			<?php $this->display_tools_notice(); ?>
			<div class="wpforms-builder-entity-connections">
			</div>
		</div>
		<?php
	}

	/**
	 * Display an empty state.
	 *
	 * @since 1.0.0
	 */
	private function empty_state(): void {

		$url = wpforms_utm_link(
			'https://wpforms.com/docs/entry-automation-addon/',
			'Builder Settings',
			'Entry Automation Documentation'
		);
		?>
			<div class="wpforms-builder-provider-connections-default wpforms-entity-empty-state">
				<img src="<?php echo esc_url( $this->get_addon_icon_url() ); ?>" alt="<?php esc_attr_e( 'Create WPForms Entry Automation Tasks.', 'wpforms-entry-automation' ); ?>">
				<div class="wpforms-builder-provider-settings-default-content">
					<p><?php esc_html_e( 'To get started with Entry Automation, you’ll need to create a new task.', 'wpforms-entry-automation' ); ?></p>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Learn more about Entry Automation', 'wpforms-entry-automation' ); ?>
					</a>
				</div>
			</div>
		<?php
	}

	/**
	 * Display tools notice.
	 *
	 * @since 1.0.0
	 */
	private function display_tools_notice(): void {

		$dismissed_notices = get_user_meta( get_current_user_id(), 'wpforms_admin_notices', true );

		if ( ! empty( $dismissed_notices['wpforms-entry-automation-dashboard-notice'] ) ) {
			return;
		}

		printf(
			'<div class="wpforms-alert wpforms-alert-info wpforms-alert-dismissible wpforms-hidden" id="wpforms-entry-automation-dashboard-notice">
					<div class="wpforms-alert-message">
						<h3>%1$s</h3>
						<p>%2$s</p>
					</div>
					<div class="wpforms-alert-buttons">
						<button type="button" class="wpforms-dismiss-button" title="%3$s" />
					</div>
				</div>',
			esc_html__( 'Entry Automation Dashboard', 'wpforms-entry-automation' ),
			sprintf(
				wp_kses( /* translators: %s - link to the Entry Automation dashboard. */
					__( 'We\'ve added a new Tools page where you can <a href="%s" target="_blank">monitor and manage your Entry Automation tasks</a>.', 'wpforms-entry-automation' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
							'rel'    => [],
						],
					]
				),
				esc_url( admin_url( 'admin.php?page=wpforms-tools&view=entry-automation' ) )
			),
			esc_attr__( 'Dismiss this notice', 'wpforms-entry-automation' )
		);
	}

	/**
	 * Get the URL of the add-on icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string URL of the add-on icon.
	 */
	private function get_addon_icon_url(): string {

		return WPFORMS_ENTRY_AUTOMATION_URL . 'assets/images/addon-icon.png';
	}

	/**
	 * Enqueue JavaScript and CSS files.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets(): void {

		$min = wpforms_get_min_suffix();

		// Enqueue WPForms Core builder providers dependencies.
		// These are required for Entry Automation to integrate with the providers' system.
		wp_enqueue_script(
			'wpforms-admin-builder-providers',
			WPFORMS_PLUGIN_URL . "assets/js/admin/builder/providers{$min}.js",
			[ 'wpforms-utils', 'wpforms-builder', 'wpforms-admin-builder-templates' ],
			WPFORMS_VERSION,
			true
		);

		wp_enqueue_script(
			'wpforms-jquery-timepicker',
			WPFORMS_PLUGIN_URL . 'assets/lib/jquery.timepicker/jquery.timepicker.min.js',
			[ 'jquery' ],
			'1.11.5',
			true
		);

		wp_enqueue_style(
			'wpforms-jquery-timepicker',
			WPFORMS_PLUGIN_URL . 'assets/lib/jquery.timepicker/jquery.timepicker.min.css',
			[],
			'1.11.5'
		);

		wp_enqueue_style(
			'wpforms-flatpickr',
			WPFORMS_PLUGIN_URL . 'assets/lib/flatpickr/flatpickr.min.css',
			[],
			'4.6.9'
		);

		wp_enqueue_script(
			'wpforms-entry-automation-connections',
			WPFORMS_ENTRY_AUTOMATION_URL . "assets/js/connections$min.js",
			[ 'jquery' ],
			WPFORMS_ENTRY_AUTOMATION_VERSION,
			true
		);

		wp_enqueue_script(
			'wpforms-entry-automation-builder-ajax',
			WPFORMS_ENTRY_AUTOMATION_URL . "assets/js/builder.ajax$min.js",
			[ 'jquery' ],
			WPFORMS_ENTRY_AUTOMATION_VERSION,
			true
		);

		wp_enqueue_script(
			'wpforms-flatpickr',
			WPFORMS_PLUGIN_URL . 'assets/lib/flatpickr/flatpickr.min.js',
			[ 'jquery' ],
			'4.6.9',
			true
		);

		wp_enqueue_style(
			'wpforms-entry-automation-builder',
			WPFORMS_ENTRY_AUTOMATION_URL . "assets/css/builder$min.css",
			[],
			WPFORMS_ENTRY_AUTOMATION_VERSION
		);

		wp_enqueue_script(
			'wpforms-entry-automation-builder',
			WPFORMS_ENTRY_AUTOMATION_URL . "assets/js/builder/builder.es5$min.js",
			[
				'choicesjs',
				'wpforms-flatpickr',
				'wpforms-admin-builder-providers',
				'wpforms-builder-smart-tags',
				'wpforms-builder-choices-list',
				'wpforms-builder-chocolate-choices',
				'wpforms-entry-automation-connections',
				'wpforms-entry-automation-builder-ajax',
			],
			WPFORMS_ENTRY_AUTOMATION_VERSION,
			true
		);

		$localized_data = [
			'adminNonce'           => wp_create_nonce( 'wpforms-admin' ),
			'locale'               => sanitize_key( wpforms_get_language_code() ),
			'daysOfTheMonth'       => esc_html__( 'Days of the Month', 'wpforms-entry-automation' ),
			'daysOfTheWeek'        => esc_html__( 'Days of the Week', 'wpforms-entry-automation' ),
			'weekDays'             => [
				[
					'label' => esc_html__( 'Mon', 'wpforms-entry-automation' ),
					'value' => 'mon',
				],
				[
					'label' => esc_html__( 'Tue', 'wpforms-entry-automation' ),
					'value' => 'tue',
				],
				[
					'label' => esc_html__( 'Wed', 'wpforms-entry-automation' ),
					'value' => 'wed',
				],
				[
					'label' => esc_html__( 'Thu', 'wpforms-entry-automation' ),
					'value' => 'thu',
				],
				[
					'label' => esc_html__( 'Fri', 'wpforms-entry-automation' ),
					'value' => 'fri',
				],
				[
					'label' => esc_html__( 'Sat', 'wpforms-entry-automation' ),
					'value' => 'sat',
				],
				[
					'label' => esc_html__( 'Sun', 'wpforms-entry-automation' ),
					'value' => 'sun',
				],
			],
			'additionalFields'     => $this->get_additional_fields(),
			'advancedFilterFields' => Helpers::get_search_fields_advanced_options(),
			'statuses'             => $this->get_statuses(),
			'frequencyOptions'     => [
				'week'  => esc_html__( 'Days of the Week', 'wpforms-entry-automation' ),
				'month' => esc_html__( 'Days of the Month', 'wpforms-entry-automation' ),
				'first' => esc_html__( 'First Day of the Month', 'wpforms-entry-automation' ),
				'last'  => esc_html__( 'Last Day of the Month', 'wpforms-entry-automation' ),
			],
			'timeFormatOptions'    => wpforms_entry_automation()->get( 'scheduler' )::get_time_format_options(),
			'frequencyDaysLabels'  => [
				'week'  => [
					'label'   => esc_html__( 'Days of the Week', 'wpforms-entry-automation' ),
					'tooltip' => esc_html__( 'Select days of the week to export entries.', 'wpforms-entry-automation' ),
				],
				'month' => [
					'label'   => esc_html__( 'Days of the Month', 'wpforms-entry-automation' ),
					'tooltip' => esc_html__( 'Select days of the month to export entries.', 'wpforms-entry-automation' ),
				],
			],
			'fileFormats'          => wpforms_entry_automation()->get( 'formatter_manager' )->get_formats_labels(),
			'onDuplicateOptions'   => [
				'inc'  => esc_html__( 'Increment File Name', 'wpforms-entry-automation' ),
				'over' => esc_html__( 'Overwrite File', 'wpforms-entry-automation' ),
				'add'  => esc_html__( 'Add Entries to File', 'wpforms-entry-automation' ),
			],
			'exportDestinations'   => wpforms_entry_automation()->get( 'delivery_manager' )->get_builder_data(),
			'entriesNumberOptions' => [
				'all' => esc_html__( 'All Entries', 'wpforms-entry-automation' ),
				'new' => esc_html__( 'New Entries Since Last Export', 'wpforms-entry-automation' ),
			],
			'ftp'                  => [
				'success'  => [
					'title'   => esc_html__( 'Connection Established', 'wpforms-entry-automation' ),
					'content' => esc_html__( 'We successfully connected to your ftp server.', 'wpforms-entry-automation' ),
				],
				'failed'   => [
					'title'   => esc_html__( 'Connection Failed', 'wpforms-entry-automation' ),
					'content' => esc_html__( 'There was an error while connecting to your ftp server. Please try again.', 'wpforms-entry-automation' ),
				],
				'required' => [
					'title'   => esc_html__( 'Required Connection', 'wpforms-entry-automation' ),
					'content' => esc_html__( 'You should test your FTP connection before saving the task.', 'wpforms-entry-automation' ),
				],
			],
			'taskModal'            => [
				'task_label'         => esc_html__( 'Task', 'wpforms-entry-automation' ),
				'prompt_connection'  => esc_html__( 'Enter a task nickname', 'wpforms-entry-automation' ),
				'error_name'         => esc_html__( 'You must provide a task nickname.', 'wpforms-entry-automation' ),
				'confirm_connection' => esc_html__( 'Are you sure you want to delete this task?', 'wpforms-entry-automation' ),
				'queue_first'        => esc_html__( 'This task is the first in the queue. Make sure the next task is scheduled. Are you sure you want to delete it?', 'wpforms-entry-automation' ),
				'action_required'    => esc_html__( 'In order to complete your form\'s {provider} integration, please select "Export Entries" or "Delete Entries" task type.', 'wpforms-entry-automation' ),
				'days_required'      => esc_html__( 'In order to complete your form\'s {provider} integration, please select at least one day to schedule the task.', 'wpforms-entry-automation' ),
			],
			'columnsChanged'       => esc_html__( 'Entry Information columns have been changed. Please note that the columns in the existing file will not change.', 'wpforms-entry-automation' ),
		];

		if ( wpforms_is_addon_initialized( 'google-drive' ) ) {
			$localized_data['googleDriveAuth'] = Client::get_auth_url(
				add_query_arg(
					[
						'page'    => 'wpforms-builder',
						'view'    => 'settings',
						'form_id' => WPForms_Builder::instance()->form->ID ?? 0,
						'section' => Plugin::SLUG,
					],
					admin_url( 'admin.php' )
				)
			);

			$localized_data['googleDriveRequiredConnection'] = esc_html__( 'You should add a connection and select existing Google Drive account.', 'wpforms-entry-automation' );
			$localized_data['canSelectExistingFolder']       = GoogleDrive::current_user_can_select_existing_folder();
		}

		if ( wpforms_is_addon_initialized( 'dropbox' ) ) {
			$dropbox_auth                  = DropboxClient::get_authorization_url(
				[
					'view'    => 'settings',
					'section' => Plugin::SLUG,
				]
			);
			$localized_data['dropboxAuth'] = add_query_arg(
				[
					'reapprove' => true,
				],
				$dropbox_auth
			);

			$localized_data['dropboxRequiredConnection'] = esc_html__( 'You should add a connection and select existing Dropbox account.', 'wpforms-entry-automation' );

			$localized_data['dropboxPermissionModal'] = [
				'title'   => esc_html__( 'Permissions Update', 'wpforms-entry-automation' ),
				'content' => esc_html__( 'To update files in your Dropbox, we need to request additional permissions. Please reconnect your Dropbox account to continue.', 'wpforms-entry-automation' ),
			];
		}

		// Localize a script with translatable string.
		wp_localize_script(
			'wpforms-entry-automation-builder',
			'wpformsEntryAutomationBuilderVars',
			$localized_data
		);
	}

	/**
	 * Get available statuses.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_statuses(): array {

		$statuses = [
			'published' => esc_html__( 'Published', 'wpforms-entry-automation' ),
			'trash'     => esc_html__( 'Trash', 'wpforms-entry-automation' ),
			'spam'      => esc_html__( 'Spam', 'wpforms-entry-automation' ),
			'partial'   => esc_html__( 'Partial', 'wpforms-entry-automation' ),
			'abandoned' => esc_html__( 'Abandoned', 'wpforms-entry-automation' ),
		];

		/**
		 * Filter to modify the list of statuses available in the form builder.
		 *
		 * @since 1.0.0
		 *
		 * @param array $statuses List of statuses.
		 */
		return (array) apply_filters(
			'wpforms_entry_automation_admin_form_builder_get_statuses',
			$statuses
		);
	}

	/**
	 * Register strings for the form builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $strings List of strings.
	 * @param array $form    Form data.
	 *
	 * @return array
	 *
	 * @noinspection PhpMissingParamTypeInspection, PhpUnusedParameterInspection
	 */
	public function register_strings( $strings, $form ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$strings = (array) $strings;

		$strings[ Plugin::SLUG . '_def_name' ] = esc_html__( 'Entry Automation Task', 'wpforms-entry-automation' );

		return $strings;
	}

	/**
	 * Use this method to register own templates for form builder.
	 * Make sure that you have `tmpl-` in the template name in `<script id="tmpl-*">`.
	 *
	 * @since 1.0.0
	 */
	public function builder_custom_templates(): void {

		$templates = array_merge(
			[
				'task',
				'export',
				'delete',
				'filter',
				'schedule',
				'schedule-days',
				'on-duplicate-option',
				'form-fields-list',
				'export-to/email',
				'export-to/ftp',
			],
			wpforms_entry_automation()->get( 'delivery_manager' )->get_custom_templates()
		);

		foreach ( $templates as $template ) {
			$this->render_template( $template );
		}

		// Error template.
		$this->builder_error_template();
	}

	/**
	 * Retrieve additional fields for entries.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of additional fields with their respective labels.
	 */
	private function get_additional_fields(): array {

		$additional_fields = [
			'entry_id'   => esc_html__( 'Entry ID', 'wpforms-entry-automation' ),
			'date'       => esc_html__( 'Entry Date', 'wpforms-entry-automation' ),
			'notes'      => esc_html__( 'Entry Notes', 'wpforms-entry-automation' ),
			'status'     => esc_html__( 'Type', 'wpforms-entry-automation' ),
			'viewed'     => esc_html__( 'Viewed', 'wpforms-entry-automation' ),
			'starred'    => esc_html__( 'Starred', 'wpforms-entry-automation' ),
			'user_agent' => esc_html__( 'User Agent', 'wpforms-entry-automation' ),
			'ip_address' => esc_html__( 'User IP', 'wpforms-entry-automation' ),
			'user_uuid'  => esc_html__( 'Unique Generated User ID', 'wpforms-entry-automation' ),
			'pstatus'    => esc_html__( 'Payment Status', 'wpforms-entry-automation' ),
			'pginfo'     => esc_html__( 'Payment Gateway Information', 'wpforms-entry-automation' ),
			'del_fields' => esc_html__( 'Include data of previously deleted fields', 'wpforms-entry-automation' ),
		];

		// The provider options below mirror the ones core registers on the classic
		// Tools -> Export Entries screen, but they are added explicitly here instead
		// of running the base list through the core
		// `wpforms_pro_admin_entries_export_additional_info_fields` filter on purpose:
		// that filter also carries other addons (e.g. User Journey, Quiz) whose Entry
		// Automation support has not been scoped or verified. Each option is gated on
		// its own addon so only vetted fields ever appear here.
		if ( wpforms_is_addon_initialized( 'geolocation' ) ) {
			$additional_fields['geodata'] = esc_html__( 'Geolocation Details', 'wpforms-entry-automation' );
		}

		if ( wpforms_is_addon_initialized( 'dropbox' ) ) {
			$additional_fields['dropbox_links'] = esc_html__( 'Dropbox Links', 'wpforms-entry-automation' );
		}

		if ( wpforms_is_addon_initialized( 'google-drive' ) ) {
			$additional_fields['google_drive_link'] = esc_html__( 'Google Drive Links', 'wpforms-entry-automation' );
		}

		return $additional_fields;
	}

	/**
	 * Render a specific template for the builder's task block.
	 *
	 * @param string $template The name of the template to render.
	 *
	 * @since 1.0.0
	 */
	private function render_template( string $template ): void {

		$template_name = ucwords( str_replace( '-', ' ', $template ) );
		$script_id     = 'tmpl-wpforms-' . esc_attr( Plugin::SLUG ) . '-builder-content-task';

		if ( $template !== 'task' ) {
			$script_id .= '-' . $template;
		}

		if ( strpos( $script_id, '/' ) !== false ) {
			$script_id = str_replace( '/', '-', $script_id );
		}

		?>
		<!-- Single Entry Automation task block: <?php echo esc_attr( $template_name ); ?>. -->
		<script type="text/html" id="<?php echo esc_attr( $script_id ); ?>">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpforms_render( WPFORMS_ENTRY_AUTOMATION_PATH . 'templates/builder/' . $template );
			?>
		</script>
		<?php
	}

	/**
	 * Update connection status.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $form_id       Form ID.
	 * @param string $connection_id Connection ID.
	 * @param array  $form_data     Form data.
	 * @param string $status        New status.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function update_connection_status( $form_id, $connection_id, $form_data, $status ): void {

		$form_data['settings'][ Plugin::SLUG ][ $connection_id ]['status'] = $status;

		// Remove filters to prevent losing data.
		// To save multiple choice fields correctly, we need to sanitize the data.
		// Sanitization used $_POST data.
		// If we run the ` update ` method without post data, we lose the multiple choice data.
		remove_all_filters( 'wpforms_save_form_args' );

		wpforms()->obj( 'form' )->update( $form_id, $form_data );
	}

	/**
	 * Builder error template.
	 * This generates an HTML template for displaying an error message
	 * when the task loading fails.
	 *
	 * @since 1.0.0
	 */
	private function builder_error_template(): void {

		?>
		<script type="text/html" id="tmpl-wpforms-<?php echo esc_attr( Plugin::SLUG ); ?>-builder-content-connection-default-error">
			<div
				class="wpforms-builder-provider-connections-error wpforms-hidden"
				id="wpforms-<?php echo esc_attr( Plugin::SLUG ); ?>-builder-provider-error"
			>
				<span class="wpforms-builder-provider-connections-error-message">
					<?php
					esc_html_e(
						'Something went wrong and we can’t load the tasks.',
						'wpforms-entry-automation'
					)
					?>
				</span>
			</div>
		</script>
		<?php
	}
}
