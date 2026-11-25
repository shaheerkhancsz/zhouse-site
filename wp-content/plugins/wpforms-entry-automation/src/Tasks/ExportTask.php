<?php

namespace WPFormsEntryAutomation\Tasks;

use WPFormsEntryAutomation\Plugin;

/**
 * Export entries task implementation.
 *
 * @since 1.0.0
 */
class ExportTask {

	/**
	 * Execute the task.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection Connection data.
	 * @param array $form_data  Form data.
	 * @param array $args       Task arguments.
	 * @param array $task       Task data.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function execute( array $connection, array $form_data, array $args, array $task ): bool {

		$entries_file_path = $this->get_entries_file_path( $task, $connection, $args, $form_data );

		// Empty file path means no entries need to be delivered.
		if ( empty( $entries_file_path ) ) {
			return true;
		}

		// Deliver formatted entries.
		return $this->deliver_entries( $connection, $entries_file_path, $form_data );
	}

	/**
	 * Get the file path of the exported entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  Task data.
	 * @param array $connection Connection data.
	 * @param array $args       Task arguments.
	 * @param array $form_data  Form data.
	 *
	 * @return string File path of the exported entries.
	 */
	private function get_entries_file_path( array $task_data, array $connection, array $args, array $form_data ): string {

		$export = wpforms()->obj( 'entries_export' );

		if ( ! $export ) {
			return '';
		}

		$entries = wpforms()->obj( 'entry' )->get_entries( $args );

		if ( empty( $entries ) ) {
			return '';
		}

		$delivery_manager = wpforms_entry_automation()->get( 'delivery_manager' );

		// Get file name from the connection data.
		$file_name = $this->get_file_name( $task_data, $connection, $form_data );

		$task_data['args']      = $args;
		$task_data['file_name'] = $file_name;

		$existing_file        = [];
		$is_remote_file_exist = $delivery_manager->is_file_exist( $file_name, $task_data, $connection );

		if ( $is_remote_file_exist ) {
			$existing_file = $delivery_manager->manage_existing_file( $file_name, $task_data, $connection );

			if ( ! empty( $existing_file['file_name'] ) ) {
				$task_data['file_name'] = $existing_file['file_name'];
			}
		}

		$file_path = wpforms_entry_automation()
			->get( 'formatter_manager' )
			->format_entries(
				$task_data,
				$entries,
				$form_data,
				$existing_file['file_path'] ?? ''
			);

		// We may delete the existing remote file if the new one was successfully uploaded.
		if ( $file_path && $is_remote_file_exist ) {
			$delivery_manager->manage_existing_file_after( $task_data, $connection );
		}

		return $file_path;
	}

	/**
	 * Get the file name for the exported entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data       Task data.
	 * @param array $connection_data Connection data.
	 * @param array $form_data       Form data.
	 *
	 * @return string File name of the exported entries.
	 */
	private function get_file_name( array $task_data, array $connection_data, array $form_data ): string {

		$file_name  = $connection_data['file_name'] ?? '';
		$connection = $this->get_connection( $form_data, $task_data['connection_id'] );
		$format     = $connection['format'] ?? '';
		$formatter  = wpforms_entry_automation()->get( 'formatter_manager' )->get_formatter( $format );

		if ( ! $formatter ) {
			return '';
		}

		return $formatter->get_file_name( $file_name, $form_data );
	}

	/**
	 * Get connection data.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form_data     Form data.
	 * @param string $connection_id Connection ID.
	 *
	 * @return array Connection data.
	 */
	private function get_connection( array $form_data, string $connection_id ): array {

		return $form_data['settings'][ Plugin::SLUG ][ $connection_id ] ?? [];
	}

	/**
	 * Deliver formatted entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $connection        Connection data.
	 * @param string $entries_file_path Entries file path.
	 * @param array  $form_data         Form data.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function deliver_entries( array $connection, string $entries_file_path, array $form_data ): bool {

		// Deliver entries using delivery manager.
		$delivery_result = wpforms_entry_automation()->get( 'delivery_manager' )->deliver_entries( $connection, $entries_file_path, $form_data );

		// Clean up the temporary file.
		if ( $delivery_result ) {
			wp_delete_file( $entries_file_path );
		}

		return $delivery_result;
	}
}
