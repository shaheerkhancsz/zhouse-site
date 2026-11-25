<?php

namespace WPFormsEntryAutomation\Export\Formatter;

use WPForms\Helpers\File;
use WPForms\Pro\Admin\Entries\Export\Ajax as ExportAjax;
use WPForms\Pro\Admin\Entries\Export\File as ExportFile;

/**
 * CSV export format.
 *
 * @since 1.0.0
 */
class CSV extends Formatter {

	/**
	 * Get the formatter's human-readable title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Formatter title.
	 */
	public function get_title(): string {

		return esc_html__( 'CSV', 'wpforms-entry-automation' );
	}

	/**
	 * Get the file extension for the formatter.
	 *
	 * @since 1.0.0
	 *
	 * @return string File extension.
	 */
	public function get_file_extension(): string {

		return 'csv';
	}

	// phpcs:disable WPForms.PHP.BackSlash.RemoveBackslash
	/**
	 * Format entries based on a formatter type.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $entries         The entries to be formatted and included in the XLSX file.
	 * @param array  $form_data       Form Data.
	 * @param array  $task_data       Task-specific data used during the formatting process.
	 * @param array  $connection      Connection-specific data or settings.
	 * @param string $exist_file_path Path to an existing file for appending, if any.
	 *
	 * @throws \Exception Exception.
	 *
	 * @return string Temporary file name containing the generated CSV data.
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

		$request_data['extension']  = 'csv';
		$request_data['request_id'] = pathinfo( $task_data['file_name'] ?? '', PATHINFO_FILENAME );

		$export->ajax->request_data = $request_data;

		if ( ! empty( $exist_file_path ) && File::exists( $exist_file_path ) ) {
			return $export->file->append_csv( $exist_file_path, $request_data );
		}

		return $export->file->write_csv( $request_data, true );
	}
	// phpcs:enable WPForms.PHP.BackSlash.RemoveBackslash
}
