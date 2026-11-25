<?php

namespace WPFormsEntryAutomation\Export\Formatter;

use Exception;
use WPForms\Helpers\File;
use WPForms\Pro\Admin\Entries\Export\Ajax as ExportAjax;
use WPForms\Pro\Admin\Entries\Export\Export;
use WPForms\Pro\Admin\Entries\Export\File as ExportFile;

/**
 * JSON export format.
 *
 * @since 1.0.0
 */
class JSON extends Formatter {

	/**
	 * Format entries based on a formatter type.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $entries         Entries to format.
	 * @param array  $form_data       Form data.
	 * @param array  $task_data       Task data.
	 * @param array  $connection      Task connection data.
	 * @param string $exist_file_path Existing file path for appending data.
	 *
	 * @throws Exception Throws an exception if the file cannot be written.
	 *
	 * @return string|false Formatted entries on success, false on failure.
	 */
	protected function format_entries( array $entries, array $form_data, array $task_data, array $connection, string $exist_file_path = '' ): string {

		$export = wpforms()->obj( 'entries_export' );

		if ( ! $export ) {
			return '';
		}

		$export->ajax = new ExportAjax( $export );
		$export->file = new ExportFile( $export );

		$request_data = $export->ajax->get_request_data( $task_data['args'] ?? [] );

		if ( $request_data['count'] === 0 ) {
			return '';
		}

		$request_data['extension']  = $this->get_file_extension();
		$request_data['request_id'] = pathinfo( $task_data['file_name'] ?? '', PATHINFO_FILENAME );

		$export->ajax->request_data = $request_data;

		return $this->write_json( $request_data, $export );
	}

	/**
	 * Write the provided data in JSON format to a temporary file.
	 *
	 * This function processes entries based on the request data and the export
	 * configuration, converts them into JSON format, and writes the content to
	 * a temporary file. The file name of the written JSON file is returned.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $request_data Data required to process the export, including columns, offsets, and steps.
	 * @param Export $export       Export object containing file and configuration details.
	 *
	 * @throws Exception Throws an exception if the file cannot be written.
	 *
	 * @return string The file name of the written JSON file.
	 */
	private function write_json( array $request_data, Export $export ): string {

		$file_name     = $export->file->get_tmpfname( $request_data );
		$json          = [
			'labels' => [],
			'values' => [],
		];
		$entry_handler = wpforms()->obj( 'entry' );

		// Set up labels mapping (field_id: label).
		foreach ( $request_data['columns_row'] as $column_id => $column_label ) {
			$json['labels'][ $column_id ] = $column_label;
		}

		for ( $i = 1; $i <= $request_data['total_steps']; $i++ ) {
			$entries = $entry_handler->get_entries( $request_data['db_args'] );

			foreach ( $export->ajax->get_entry_data( $entries ) as $entry ) {
				$rows = [];

				foreach ( $request_data['columns_row'] as $column_id => $column_label ) {
					$rows[ $column_id ] = $entry[ $column_id ] ?? '';
				}

				// Insert the single entry.
				$json['values'][] = $rows;
			}

			$request_data['db_args']['offset'] = $i * $export->configuration['entries_per_step'];
		}

		// Save the JSON output.
		File::put_contents( $file_name, wp_json_encode( $json, JSON_UNESCAPED_UNICODE ) );

		return $file_name;
	}

	/**
	 * Get the formatter's human-readable title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Formatter title.
	 */
	public function get_title(): string {

		return esc_html__( 'JSON', 'wpforms-entry-automation' );
	}

	/**
	 * Get the file extension for the formatter.
	 *
	 * @since 1.0.0
	 *
	 * @return string File extension.
	 */
	public function get_file_extension(): string {

		return 'json';
	}
}
