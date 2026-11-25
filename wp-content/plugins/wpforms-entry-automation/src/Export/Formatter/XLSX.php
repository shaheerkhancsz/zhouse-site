<?php

namespace WPFormsEntryAutomation\Export\Formatter;

use WPForms\Helpers\File;
use WPForms\Pro\Admin\Entries\Export\Ajax as ExportAjax;
use WPForms\Pro\Admin\Entries\Export\File as ExportFile;

/**
 * Excel export format.
 *
 * @since 1.0.0
 */
class XLSX extends Formatter {

	// phpcs:disable WPForms.PHP.BackSlash.RemoveBackslash
	/**
	 * Formats entries and generates an XLSX file with provided data.
	 * This method processes the provided entries, formats them based on the given fields,
	 * task data, and connection, and then generates an XLSX file with proper headers
	 * and data rows. The final file is stored temporarily and its path is returned.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $entries         The entries to be formatted and included in the XLSX file.
	 * @param array  $form_data       Form Data.
	 * @param array  $task_data       Task-specific data used during the formatting process.
	 * @param array  $connection      Connection-specific data or settings.
	 * @param string $exist_file_path Existing file path for appending data.
	 *
	 * @throws \Exception Exception.
	 *
	 * @return string Temporary file name containing the generated XLSX data.
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

		$request_data['extension']  = 'xlsx';
		$request_data['request_id'] = pathinfo( $task_data['file_name'] ?? '', PATHINFO_FILENAME );

		$export->ajax->request_data = $request_data;

		if ( ! empty( $exist_file_path ) && File::exists( $exist_file_path ) ) {
			return $export->file->append_xlsx( $exist_file_path, $request_data );
		}

		return $export->file->write_xlsx( $request_data, true );
	}
	// phpcs:enable WPForms.PHP.BackSlash.RemoveBackslash

	/**
	 * Get the formatter's human-readable title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Formatter title.
	 */
	public function get_title(): string {

		return esc_html__( 'XLSX', 'wpforms-entry-automation' );
	}

	/**
	 * Get the file extension for the formatter.
	 *
	 * @since 1.0.0
	 *
	 * @return string File extension.
	 */
	public function get_file_extension(): string {

		return 'xlsx';
	}
}
