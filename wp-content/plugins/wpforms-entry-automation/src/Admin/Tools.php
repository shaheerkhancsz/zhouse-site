<?php

namespace WPFormsEntryAutomation\Admin;

/**
 * Entry automation tools page.
 *
 * @since 1.0.0
 */
class Tools {

	/**
	 * List table instance.
	 *
	 * @since 1.0.0
	 *
	 * @var ListTable
	 */
	private $list_table;

	/**
	 * Get the list table instance.
	 *
	 * @since 1.0.0
	 *
	 * @return ListTable
	 */
	private function get_list_table(): ListTable {

		if ( ! empty( $this->list_table ) ) {
			return $this->list_table;
		}

		// Make sure we have the required admin functions.
		require_once ABSPATH . 'wp-admin/includes/template.php';

		// Now it's safe to instantiate the list table.
		$this->list_table = new ListTable();

		$this->list_table->prepare_items();

		return $this->list_table;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		add_action( 'wpforms_admin_tools_views_entry_automation_display', [ $this, 'display' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'load-wpforms_page_wpforms-tools', [ $this, 'screen_options' ] );
		add_filter( 'set_screen_option_wpforms_tasks_per_page', [ $this, 'set_screen_option' ], 10, 3 );

		// Force display of the entry automation education screen.
		add_filter( 'wpforms_admin_education_admin_tools_entry_automation_is_addon_active', '__return_true' );
		add_filter( 'wpforms_admin_tools_views_entry_automation_check_capability', '__return_true' );

		// Handle form status updates.
		add_action( 'wpforms_form_handler_update_status', [ $this, 'update_status' ], 10, 2 );
		// Handle form deletion.
		add_action( 'wpforms_delete_form', [ $this, 'delete_tasks' ] );
	}

	/**
	 * Display the list table.
	 *
	 * @since 1.0.0
	 */
	public function display(): void {

		$this->get_list_table()->display_page();
	}

	/**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets(): void {

		if ( ! wpforms_is_admin_page( 'tools', 'entry-automation' ) ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		// Enqueue admin styles.
		wp_enqueue_style(
			'wpforms-entry-automation-dashboard',
			WPFORMS_ENTRY_AUTOMATION_URL . 'assets/css/dashboard' . $min . '.css',
			[],
			WPFORMS_ENTRY_AUTOMATION_VERSION
		);

		// Enqueue admin scripts.
		wp_enqueue_script(
			'wpforms-entry-automation-dashboard',
			WPFORMS_ENTRY_AUTOMATION_URL . 'assets/js/dashboard' . $min . '.js',
			[ 'jquery' ],
			WPFORMS_ENTRY_AUTOMATION_VERSION,
			true
		);

		// Localize script.
		wp_localize_script(
			'wpforms-entry-automation-dashboard',
			'wpformsEntryAutomation',
			[
				'admin_url'     => admin_url(),
				'i18n'          => $this->get_strings(),
				'modal_content' => $this->get_modal_content(),
			]
		);
	}

	/**
	 * Get the modal content.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_modal_content(): string {

		if ( $this->list_table->has_items() ) {
			return '';
		}

		$content = sprintf( '<p>%s</p>', esc_html__( 'Which form would you like to add an automation task to?', 'wpforms-entry-automation' ) );

		$content .= $this->get_forms_select();

		return $content;
	}

	/**
	 * Get the forms select.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_forms_select(): string {

		$forms = $this->get_forms();

		$select = '<select id="wpforms-entry-automation-select-form" name="form_id" class="choicesjs-select" data-search="1">';

		$select .= '<option value="">' . esc_html__( '-- Select a Form --', 'wpforms-entry-automation' ) . '</option>';

		foreach ( $forms as $form ) {
			$select .= '<option value="' . esc_attr( $form['id'] ) . '">' . esc_html( $form['name'] ) . '</option>';
		}

		$select .= '</select>';

		return $select;
	}

	/**
	 * Get localized strings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_strings(): array {

		return [
			'modal_title'       => __( 'Select a Form', 'wpforms-entry-automation' ),
			'modal_description' => __( 'Which form would you like to add an automation task to?', 'wpforms-entry-automation' ),
			'confirm'           => __( 'Continue', 'wpforms-entry-automation' ),
			'cancel'            => __( 'Cancel', 'wpforms-entry-automation' ),
		];
	}

	/**
	 * Get the forms.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_forms(): array {

		$form_handler = wpforms()->obj( 'form' );

		if ( ! $form_handler ) {
			return [];
		}

		$forms = $form_handler->get(
			'',
			[
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			]
		);

		if ( empty( $forms ) ) {
			return [];
		}

		return array_map(
			static function ( $form ) {

				return [
					'id'   => $form->ID,
					'name' => $form->post_title,
				];
			},
			$forms
		);
	}

	/**
	 * Add screen options.
	 *
	 * @since 1.0.0
	 */
	public function screen_options(): void {

		if ( ! wpforms_is_admin_page( 'tools', 'entry-automation' ) ) {
			return;
		}

		$this->get_list_table()->screen_option();
	}

	/**
	 * Save screen option value.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $status Status of the screen option value.
	 * @param string $option The option name.
	 * @param int    $value  The option value.
	 *
	 * @return mixed
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function set_screen_option( $status, $option, $value ) {

		if ( $option === 'wpforms_tasks_per_page' ) {
			return absint( $value );
		}

		return $status;
	}

	/**
	 * Update task status when form status is updated.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $form_id Form ID.
	 * @param string $status  Form status.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function update_status( $form_id, $status ): void {

		if ( ! in_array( $status, [ 'trash', 'publish' ], true ) ) {
			return;
		}

		$task_status = 'active';

		// Change task status to trashed if the form is moved to trash.
		if ( $status === 'trash' ) {
			$task_status = 'trashed';
		}

		$tasks = wpforms_entry_automation()->get( 'tasks' );

		$form_tasks = $tasks->get_by_form_id( $form_id );

		if ( empty( $form_tasks ) ) {
			return;
		}

		foreach ( $form_tasks as $task ) {
			$tasks->update_status( $task['id'], $task_status );
		}
	}

	/**
	 * Delete tasks for deleted forms.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ids Form IDs.
	 */
	public function delete_tasks( array $ids ): void {

		$tasks        = wpforms_entry_automation()->get( 'tasks' );
		$action_tasks = wpforms_entry_automation()->get( 'action_tasks' );

		foreach ( $ids as $id ) {
			$form_tasks = $tasks->get_by_form_id( $id );

			if ( empty( $form_tasks ) ) {
				continue;
			}

			foreach ( $form_tasks as $task ) {
				$tasks->delete( $task['id'] );
				$action_tasks->unschedule( $task['action_id'] );
			}
		}
	}
}
