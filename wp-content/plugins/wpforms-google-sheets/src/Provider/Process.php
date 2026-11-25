<?php

namespace WPFormsGoogleSheets\Provider;

use WPFormsGoogleSheets\Plugin;
use WPFormsGoogleSheets\Tasks\ProcessTask;

/**
 * Class Process handles entries processing using the provider settings and configuration.
 *
 * @since 1.0.0
 */
class Process extends \WPForms\Providers\Provider\Process {

	/**
	 * Register hooks.
	 *
	 * @since 2.6.0
	 */
	public function hooks(): void {

		add_action( 'wpforms_process_complete_form_abandonment', [ $this, 'process_abandonment_entry' ], 10, 4 );
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

		if ( empty( $form_data['providers'][ Plugin::SLUG ] ) ) {
			return;
		}

		foreach ( $form_data['providers'][ Plugin::SLUG ] as $connection_data ) {
			$this->process_each_connection( $connection_data, $fields, $form_data, $entry_id );
		}
	}

	/**
	 * Receive all wpforms_process_complete_form_abandonment params and do the actual processing.
	 *
	 * @since 2.6.0
	 *
	 * @param array $fields    Array of form fields.
	 * @param array $entry     Submitted form content.
	 * @param array $form_data Form data and settings.
	 * @param int   $entry_id  ID of a saved entry.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function process_abandonment_entry( $fields, $entry, $form_data, $entry_id ): void {

		if ( empty( $form_data['providers'][ Plugin::SLUG ] ) ) {
			return;
		}

		foreach ( $form_data['providers'][ Plugin::SLUG ] as $connection_data ) {
			if ( isset( $connection_data['include_abandoned_entries'] ) ) {
				$this->process_each_connection( $connection_data, $fields, $form_data, $entry_id );
			}
		}
	}

	/**
	 * Iteration loop for connections - call action for each connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Array of form fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        ID of a saved entry.
	 */
	protected function process_each_connection( $connection_data, $fields, $form_data, $entry_id ) {

		// Check for conditional logic.
		if ( ! $this->is_conditionals_passed( $connection_data, $fields, $form_data, $entry_id ) ) {
			return;
		}

		wpforms()
			->obj( 'tasks' )
			->create( ProcessTask::ACTION )
			->async()
			->params( $connection_data, $fields, $form_data, $entry_id )
			->register();
	}

	/**
	 * Process Conditional Logic for the provided connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Array of form fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        ID of a saved entry.
	 *
	 * @return bool
	 */
	private function is_conditionals_passed( $connection_data, $fields, $form_data, $entry_id ) {

		$pass = $this->process_conditionals( $fields, $form_data, $connection_data );

		if ( ! $pass ) {
			wpforms_log(
				sprintf( 'The Google Sheets connection %s was not processed due to conditional logic.', $connection_data['name'] ?? '' ),
				$fields,
				[
					'type'    => [ 'provider', 'conditional_logic' ],
					'parent'  => $entry_id,
					'form_id' => $form_data['id'],
				]
			);
		}

		return $pass;
	}

	/**
	 * Resend failed connections for an entry.
	 *
	 * @since 2.6.0
	 *
	 * @param int $entry_id Entry ID.
	 *
	 * @return int Number of resent connections.
	 */
	public function resend_failed_connections( int $entry_id ): int { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$entry_single = wpforms()->obj( 'entry' )->get( $entry_id );

		if ( ! $entry_single ) {
			return 0;
		}

		$fields  = wpforms_is_json( $entry_single->fields ) ? json_decode( $entry_single->fields, true ) : [];
		$fields  = is_array( $fields ) ? $fields : [];
		$form_id = (int) $entry_single->form_id;
		$form    = wpforms()->obj( 'form' )->get( $form_id );

		if ( ! $form ) {
			return 0;
		}

		$form_data = wpforms_decode( $form->post_content );

		if ( empty( $form_data['providers'][ Plugin::SLUG ] ) ) {
			return 0;
		}

		$retry = wpforms_google_sheets()->get( 'retry' );

		if ( ! $retry ) {
			return 0;
		}

		$failed_connection_ids = $retry->get_failed_items( $entry_id );

		if ( empty( $failed_connection_ids ) ) {
			return 0;
		}

		$resend_count = 0;
		$process_task = wpforms_google_sheets()->get( 'process_task' );

		if ( ! $process_task ) {
			return 0;
		}

		foreach ( $form_data['providers'][ Plugin::SLUG ] as $connection_data ) {
			// Only resend connections that failed.
			if ( empty( $connection_data['id'] ) || ! in_array( $connection_data['id'], $failed_connection_ids, true ) ) {
				continue;
			}

			// Check conditional logic.
			if ( ! $this->is_conditionals_passed( $connection_data, $fields, $form_data, $entry_id ) ) {
				continue;
			}

			$process_task->add_row( (array) $connection_data, (array) $fields, (array) $form_data, (int) $entry_id );

			++$resend_count;
		}

		return $resend_count;
	}
}
