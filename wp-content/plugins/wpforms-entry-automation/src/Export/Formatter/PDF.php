<?php

namespace WPFormsEntryAutomation\Export\Formatter;

use Exception;
use WPForms\Helpers\File;
use WPForms\Pro\Admin\Entries\Export\Ajax as ExportAjax;
use WPForms\Pro\Admin\Entries\Export\File as ExportFile;
use WPFormsEntryAutomation\Helpers\VendorPDF;
use WPFormsEntryAutomation\Plugin;

/**
 * PDF export format.
 *
 * @since 1.0.0
 */
class PDF extends Formatter {

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
	 * @throws Exception If an error occurs during PDF generation or file writing.
	 *
	 * @return string Formatted entries on success, empty string on failure.
	 */
	protected function format_entries( array $entries, array $form_data, array $task_data, array $connection, string $exist_file_path = '' ): string {

		return $this->write_pdf( $entries, $task_data, $form_data, $connection );
	}

	/**
	 * Generates a PDF document based on the provided request data and export configuration.
	 * Iterates through entries, creates formatted tables for each entry, and saves the output as a PDF file.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added $connection parameter for page break support.
	 * @since 1.3.0 Re-fetch each chunk via filtered db_args to honour the task's entry filter.
	 *
	 * @param array $entries    The entries to be included in the PDF.
	 * @param array $task_data  Task-specific data used during the PDF generation.
	 * @param array $form_data  Form data used for processing smart tags.
	 * @param array $connection Task connection data.
	 *
	 * @throws Exception If an error occurs during PDF generation or file writing.
	 *
	 * @return string The filename of the generated PDF document.
	 */
	private function write_pdf( array $entries, array $task_data, array $form_data, array $connection ): string {

		// Bail early when there is nothing to export. The chunks themselves are re-fetched per step below.
		if ( empty( $entries ) ) {
			return '';
		}

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

		$request_data['extension']  = 'pdf';
		$request_data['request_id'] = pathinfo( $task_data['file_name'] ?? '', PATHINFO_FILENAME );

		$export->ajax->request_data = $request_data;

		$pdf       = new VendorPDF();
		$file_name = $export->file->get_tmpfname( $request_data );

		// Apply font settings.
		$pdf->apply_font();

		$prev_y = $pdf->y;

		$pdf->ezText( wp_date( 'm/d/y, h:i A' ), 8, [ 'justification' => 'left' ] );
		$pdf->ezSetY( $prev_y + 2 );
		$pdf->ezText( $form_data['settings']['form_title'], 10, [ 'justification' => 'center' ] );
		$pdf->ezSetDy( - 20 );

		$one_entry_per_page = ! empty( $connection['one_entry_per_page'] );
		$entry_count        = $request_data['count'];

		// Running position across all chunks, so page breaks fire correctly on chunk boundaries.
		$entry_position = 0;

		for ( $i = 1; $i <= $request_data['total_steps']; $i++ ) {
			// Re-fetch the next chunk from the filtered query so only matching entries are exported.
			$entries_chunk = wpforms()->obj( 'entry' )->get_entries( $request_data['db_args'] );

			foreach ( $export->ajax->get_entry_data( $entries_chunk ) as $entry_index => $entry ) {
				$rows = $this->get_rows( $request_data, $entry, $form_data, $entries_chunk[ $entry_index ]->entry_id );

				// Create the table.
				$pdf->ezTable(
					$rows,
					[
						'name'  => esc_html__( 'Name', 'wpforms-entry-automation' ),
						'value' => esc_html__( 'Value', 'wpforms-entry-automation' ),
					],
					sprintf(
						/* translators: %s - Entry ID. */
						esc_html__( 'Entry #%s', 'wpforms-entry-automation' ),
						$entries_chunk[ $entry_index ]->entry_id
					),
					[
						'shadeCol'           => [ 0.9, 0.9, 0.9 ],
						'showHeadings'       => 0,
						'width'              => 500,
						'fontSize'           => 10,
						// Borders width.
						'innerLineThickness' => 0.1,
						'outerLineThickness' => 0.1,
						'cols'               => [
							'name'  => [ 'width' => 150 ],
							'value' => [ 'width' => 350 ],
						],
					]
				);

				++$entry_position;

				// Skip spacing after the last entry.
				if ( $entry_position >= $entry_count ) {
					continue;
				}

				if ( $one_entry_per_page ) {
					$pdf->ezNewPage();
				} else {
					// Make a gap in 20px.
					$pdf->ezSetDy( - 20 );
				}
			}

			// Advance the offset to the next chunk, mirroring the core CSV export path.
			$request_data['db_args']['offset'] = $i * $export->configuration['entries_per_step'];
		}

		// Save the PDF output.
		File::put_contents( $file_name, $pdf->ezOutput() );

		return $file_name;
	}

	/**
	 * Generates an array of rows using column labels and corresponding field values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $request_data The data containing column labels and other request information.
	 * @param array $entry        An array representing the entry from which to retrieve field values.
	 * @param array $form_data    The form data associated with the entry.
	 * @param int   $entry_id     The ID of the entry being processed.
	 *
	 * @return array The generated array of rows, each containing a name and value.
	 */
	private function get_rows( array $request_data, array $entry, array $form_data, int $entry_id ): array {

		$rows = [];

		foreach ( $request_data['columns_row'] as $column_id => $column_label ) {
			$rows[] = [
				'name'  => $column_label,
				'value' => $this->get_field_value( $entry, $column_id, $form_data, $entry_id ),
			];
		}

		return $rows;
	}

	/**
	 * Retrieve the value of a specific form field.
	 *
	 * This method handles both standard and smart tag field values, returning the corresponding value based on the field ID.
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry     The form entry data.
	 * @param mixed $field_id  The field ID or key.
	 * @param array $form_data The form data structure.
	 * @param int   $entry_id  The entry ID.
	 *
	 * @return mixed The value of the specified field.
	 */
	private function get_field_value( array $entry, $field_id, array $form_data, int $entry_id ) {

		if ( ! is_numeric( $field_id ) ) {
			return $entry[ $field_id ] ?? '';
		}

		return wpforms_process_smart_tags(
			sprintf( '{field_value_id="%d"}', $field_id ),
			$form_data,
			[
				$field_id => [
					'id'        => $field_id,
					'value_raw' => $entry[ $field_id ] ?? '',
				],
			],
			$entry_id,
			Plugin::SLUG
		);
	}

	/**
	 * Get the formatter's human-readable title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Formatter title.
	 */
	public function get_title(): string {

		return esc_html__( 'PDF', 'wpforms-entry-automation' );
	}

	/**
	 * Get the file extension for the formatter.
	 *
	 * @since 1.0.0
	 *
	 * @return string File extension.
	 */
	public function get_file_extension(): string {

		return 'pdf';
	}
}
