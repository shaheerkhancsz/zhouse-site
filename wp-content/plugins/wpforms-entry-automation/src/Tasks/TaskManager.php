<?php

namespace WPFormsEntryAutomation\Tasks;

use WPFormsEntryAutomation\Plugin;

/**
 * Manages task creation, editing, execution.
 *
 * @since 1.0.0
 */
class TaskManager {

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		add_action( 'wpforms_save_form', [ $this, 'process' ], 10, 2 );
		add_action( 'wp_restore_post_revision', [ $this, 'restore_tasks' ], 10, 2 );
	}

	/**
	 * Process form save.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id Form ID.
	 * @param array $form    Form data.
	 */
	public function process( $form_id, $form ): void {

		$form_data = json_decode( stripslashes( $form['post_content'] ), true );

		$this->handle_automation_tasks( $form_id, $form_data );
	}

	/**
	 * Restore tasks for form revisions.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id     Form ID.
	 * @param int $revision_id Revision ID.
	 */
	public function restore_tasks( $form_id, $revision_id ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$post_type = get_post_type( $form_id );

		if ( $post_type !== 'wpforms' ) {
			return;
		}

		$form_data = wpforms()->obj( 'form' )->get( $form_id, [ 'content_only' => true ] );

		$this->handle_automation_tasks( $form_id, $form_data );
	}

	/**
	 * Handle automation tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id   Form ID.
	 * @param array $form_data Form data.
	 */
	private function handle_automation_tasks( $form_id, $form_data ): void {

		$entry_automation_tasks = $form_data['settings'][ Plugin::SLUG ] ?? [];

		$tasks_manager = wpforms_entry_automation()->get( 'tasks' );

		$form_tasks = $tasks_manager->get_by_form_id( $form_id );

		// If the form has no new or saved tasks, we don't need to do anything.
		if ( empty( $entry_automation_tasks ) && empty( $form_tasks ) ) {
			return;
		}

		$this->process_tasks( $form_id, $form_tasks, $entry_automation_tasks );
	}

	/**
	 * Process tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id         Form ID.
	 * @param array $form_tasks      Form tasks.
	 * @param array $form_data_tasks Form data tasks.
	 */
	private function process_tasks( int $form_id, array $form_tasks, array $form_data_tasks ): void {

		// Clean up form data tasks before processing.
		// Following the standard WPForms pattern, we remove __lock__ and filter out non-array values.
		// Note: Unlike providers that iterate and skip, we use array operations (array_diff, array_intersect),
		// so we must clean the data first to prevent __lock__ or invalid values from being processed.
		unset( $form_data_tasks['__lock__'] );

		$form_data_tasks = array_filter(
			$form_data_tasks,
			static function ( $task ) {

				return is_array( $task );
			}
		);

		$tasks_to_delete = array_diff( array_keys( $form_tasks ), array_keys( $form_data_tasks ) );
		$tasks_to_create = array_diff( array_keys( $form_data_tasks ), array_keys( $form_tasks ) );
		$tasks_to_update = array_intersect( array_keys( $form_tasks ), array_keys( $form_data_tasks ) );
		$tasks_manager   = wpforms_entry_automation()->get( 'tasks' );

		foreach ( $tasks_to_delete as $connection_id ) {
			$task_id = $form_tasks[ $connection_id ]['id'];

			if ( empty( $task_id ) ) {
				continue;
			}

			$action_id = $tasks_manager->get_action_id( (int) $task_id );

			if ( ! empty( $action_id ) ) {
				wpforms_entry_automation()->get( 'action_tasks' )->unschedule( $action_id );
			}

			$tasks_manager->delete( $form_tasks[ $connection_id ]['id'] );
		}

		foreach ( $tasks_to_create as $task_id ) {
			$this->create_task( $form_id, $form_data_tasks[ $task_id ] );
		}

		foreach ( $tasks_to_update as $connection_id ) {
			$this->update_task( $form_tasks[ $connection_id ]['id'], $form_id, $form_data_tasks[ $connection_id ], $form_tasks[ $connection_id ] );
		}
	}

	/**
	 * Update an existing task.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $task_id    Task ID.
	 * @param int   $form_id    Form ID.
	 * @param array $data       Task data.
	 * @param array $saved_data Saved task data.
	 */
	private function update_task( int $task_id, int $form_id, array $data, $saved_data ): void {

		$task_data = $this->prepare_task_data( $form_id, $data, $saved_data );

		// If the schedule has changed, we need to remove the old action and add a new one.
		// We don't need to do this if the task is a child task, as the parent task will handle the schedule.
		if ( $task_data['schedule'] !== $saved_data['schedule'] ) {
			wpforms_entry_automation()->get( 'action_tasks' )->unschedule( $saved_data['action_id'] );

			if ( empty( $task_data['parent_id'] ) ) {
				wpforms_entry_automation()->get( 'action_tasks' )->add( $task_id, $data['schedule'] );
			} else {
				$task_data['action_id'] = 0;
			}
		}

		wpforms_entry_automation()->get( 'tasks' )->update( $task_id, $task_data );
	}

	/**
	 * Create a new task.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id Form ID.
	 * @param array $data    Task data.
	 */
	private function create_task( int $form_id, array $data ): void {

		$task_data = $this->prepare_task_data( $form_id, $data );

		$task_id = wpforms_entry_automation()->get( 'tasks' )->create( $task_data );

		// If the task is not a child task, we need to create a new action task.
		if ( empty( $task_data['parent_id'] ) ) {
			wpforms_entry_automation()->get( 'action_tasks' )->add( $task_id, $data['schedule'] );
		}
	}

	/**
	 * Prepares task data based on the provided form ID, data, and optional saved data.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $form_id    The ID of the form associated with the task.
	 * @param array $data       The data containing details for preparing the task, such as connection info and schedule.
	 * @param array $saved_data Optional previously saved data for retrieving additional information such as destination. Defaults to an empty array.
	 *
	 * @return array The prepared task data array, including task details and metadata.
	 */
	private function prepare_task_data( int $form_id, array $data, array $saved_data = [] ): array {

		$current_time = gmdate( 'Y-m-d H:i:s' );

		$prepare_data = [
			'form_id'         => $form_id,
			'connection_id'   => $data['id'],
			'connection_name' => $data['name'],
			'type'            => $data['action'],
			'export_to'       => $data['export_to'],
			'destination'     => $this->prepare_destination( $data, $saved_data ), // phpcs:ignore Universal.Operators.DisallowShortTernary.Found
			'status'          => $data['status'] ? 'active' : 'inactive',
			'parent_id'       => ! empty( $data['schedule']['queue'] ) ? $data['parent_id'] : null,
			'schedule'        => wp_json_encode( $data['schedule'] ?? [] ),
			'updated_at'      => $current_time,
		];

		if ( empty( $saved_data ) ) {
			$prepare_data['last_run']   = null;
			$prepare_data['created_at'] = $current_time;
		}

		return $prepare_data;
	}

	/**
	 * Prepares the destination based on the provided data and saved data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data       Task data.
	 * @param array $saved_data The previously saved task data for fallback destination details.
	 *
	 * @return string The prepared destination (e.g., email address or FTP host).
	 */
	private function prepare_destination( array $data, array $saved_data ): string {

		$export_to = $data['export_to'] ?? '';

		if ( $export_to === 'email' ) {
			$address = $data['email']['address'] ?? '';

			return wpforms_process_smart_tags( $address, [] );
		}

		if ( $export_to === 'ftp' ) {
			return $data['ftp']['host'] ?? '';
		}

		return $saved_data['destination'] ?? '';
	}
}
