<?php

namespace WPFormsGoogleSheets\Tasks;

use WPForms\Tasks\Task;
use WPForms\Tasks\Meta;
use WPFormsGoogleSheets\Provider\FieldMapper;

/**
 * Class ProcessActionTask.
 *
 * @since 1.0.0
 */
class ProcessTask extends Task {

	/**
	 * Async task action.
	 *
	 * @since 1.0.0
	 */
	public const ACTION = 'wpforms_google_sheets_process_action';

	/**
	 * Field mapper.
	 *
	 * @since 1.0.0
	 *
	 * @var FieldMapper
	 */
	private $field_mapper;

	/**
	 * Task meta.
	 *
	 * @since 1.0.0
	 *
	 * @var Meta
	 */
	private $meta;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Meta        $meta         Task meta.
	 * @param FieldMapper $field_mapper Field mapper.
	 */
	public function __construct( Meta $meta, FieldMapper $field_mapper ) {

		$this->meta         = $meta;
		$this->field_mapper = $field_mapper;

		parent::__construct( self::ACTION );
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Process the addon async tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param int $meta_id Task meta ID.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function process( $meta_id ): void {

		$meta_data = $this->meta->get( (int) $meta_id );

		// We should actually receive something.
		if ( empty( $meta_data ) || empty( $meta_data->data ) || ! is_array( $meta_data->data ) || count( $meta_data->data ) !== 4 ) {
			return;
		}

		// We expect a certain metadata structure for this task.
		[ $connection_data, $fields, $form_data, $entry_id ] = $meta_data->data;

		$connection_name = isset( $connection_data['name'] ) && ! wpforms_is_empty_string( $connection_data['name'] ) ? $connection_data['name'] : '';
		$form_id         = isset( $form_data['id'] ) ? absint( $form_data['id'] ) : 0;

		if ( empty( $connection_data['id'] ) || empty( $connection_data['spreadsheet_id'] ) || ! isset( $connection_data['sheet_id'] ) ) {
			$this->error_log(
				sprintf(
					'Invalid connection %s',
					$connection_name
				),
				$connection_data,
				$entry_id,
				$form_id
			);

			return;
		}

		$this->add_row( (array) $connection_data, (array) $fields, (array) $form_data, (int) $entry_id );
	}

	/**
	 * Process the addon run action.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Array of form fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        ID of a saved entry.
	 */
	public function add_row( array $connection_data, array $fields, array $form_data, int $entry_id ): void {

		$form_id        = isset( $form_data['id'] ) ? absint( $form_data['id'] ) : 0;
		$spreadsheet_id = $connection_data['spreadsheet_id'];
		$sheet_id       = $connection_data['sheet_id'];
		$row_values     = $this->field_mapper->prepare_row( $connection_data, $fields, $form_data, $entry_id );

		if ( count( array_filter( $row_values ) ) === 0 ) {
			$this->error_log( 'Submission skipped because there is nothing to send.', $connection_data, $entry_id, $form_id );

			return;
		}

		$success = true;
		$client  = wpforms_google_sheets()->get( 'client' );

		if ( ! $client || ! $client->append( $spreadsheet_id, $sheet_id, $row_values ) ) {
			$success = false;

			$this->error_log( 'Failed to append data to spreadsheet.', $connection_data, $entry_id, $form_id );
		}

		$retry = wpforms_google_sheets()->get( 'retry' );

		if ( ! $retry ) {
			return;
		}

		$success ? $retry->mark_success( $entry_id, $connection_data['id'] ) : $retry->mark_failed( $entry_id, $form_id, $connection_data['id'] );
	}

	/**
	 * Log an error.
	 *
	 * @since 2.4.0
	 *
	 * @param string $message         Error message.
	 * @param array  $connection_data Connection data.
	 * @param int    $entry_id        Entry ID.
	 * @param int    $form_id         Form ID.
	 */
	private function error_log( string $message, array $connection_data, int $entry_id, int $form_id ): void {

		wpforms_log(
			'Submission to Google Sheets failed' . "(#$entry_id).",
			[
				'message'    => $message,
				'connection' => $connection_data,
			],
			[
				'type'    => [ 'provider', 'error' ],
				'parent'  => $entry_id,
				'form_id' => $form_id,
			]
		);
	}
}
