<?php

namespace WPFormsEntryAutomation\Tasks;

/**
 * Delete entries task implementation.
 *
 * @since 1.0.0
 */
class DeleteTask {

	/**
	 * Execute the delete task.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Task arguments.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function execute( array $args ): bool {

		// Get entries to delete.
		$entries = $this->get_entries( $args );

		if ( empty( $entries ) ) {
			// No entries to delete.
			return true;
		}

		// Delete entries.
		$delete_result = $this->delete_entries( $entries );

		// Log deletion.
		if ( $delete_result ) {
			$this->log_deletion( $entries, $args );
		} else {
			// Log failure.
			wpforms_log(
				'Entry Automation - Entry Deletion Failed',
				[
					'entry_ids' => wp_list_pluck( $entries, 'entry_id' ),
				],
				[
					'type'    => 'error',
					'form_id' => absint( $args['form_id'] ),
				]
			);
		}

		return $delete_result;
	}

	/**
	 * Get entries to delete.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments to filter entries.
	 *
	 * @return array Entries to delete.
	 */
	private function get_entries( array $args ): array {

		// Prepare arguments.
		$db_args = [
			'number'      => -1, // Get all entries.
			'offset'      => 0,
			'form_id'     => $args['form_id'],
			'entry_id'    => $args['entry_id'],
			'is_filtered' => ! empty( $args['entry_id'] ),
			'date'        => $args['dates'] ?? [],
			'select'      => 'entry_ids',
			'status'      => $args['status'],
		];

		if ( $args['search']['term'] !== '' ) {
			$db_args['value']         = $args['search']['term'];
			$db_args['value_compare'] = $args['search']['comparison'];
			$db_args['field_id']      = $args['search']['field'];
		}

		// Get entries.
		return wpforms()->obj( 'entry' )->get_entries( $db_args );
	}

	/**
	 * Delete entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entries Entries to delete.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function delete_entries( array $entries ): bool {

		$success = true;

		// Delete each entry.
		foreach ( $entries as $entry ) {
			$entry_id = $entry->entry_id;
			$result   = wpforms()->obj( 'entry' )->delete( $entry_id, [ 'cap' => '' ] );

			if ( ! $result ) {
				$success = false;
			}
		}

		return $success;
	}

	/**
	 * Log deletion.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entries Deleted entries.
	 * @param array $args    Task arguments.
	 */
	private function log_deletion( array $entries, array $args ): void {

		// Log deletion.
		wpforms_log(
			'Entry Automation - Entries Deleted',
			[
				'entry_count' => count( $entries ),
				'entry_ids'   => wp_list_pluck( $entries, 'entry_id' ),
			],
			[
				'type'    => 'log',
				'form_id' => absint( $args['form_id'] ),
			]
		);
	}
}
