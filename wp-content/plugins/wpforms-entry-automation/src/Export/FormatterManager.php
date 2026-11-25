<?php

namespace WPFormsEntryAutomation\Export;

use Exception;
use WPForms\Helpers\File;
use WPFormsEntryAutomation\Export\Formatter\CSV;
use WPFormsEntryAutomation\Export\Formatter\Formatter;
use WPFormsEntryAutomation\Export\Formatter\JSON;
use WPFormsEntryAutomation\Export\Formatter\PDF;
use WPFormsEntryAutomation\Export\Formatter\XLSX;
use WPFormsEntryAutomation\Plugin;

/**
 * Class FormatterManager.
 *
 * @since 1.0.0
 */
class FormatterManager {

	/**
	 * Option holding the random segment of the private export directory name.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	private const PRIVATE_DIR_OPTION = 'wpforms_entry_automation_export_dir';

	/**
	 * How long a file left behind by a failed delivery is kept, in seconds.
	 *
	 * @since 1.4.0
	 *
	 * @var int
	 */
	private const PRIVATE_FILE_TTL = DAY_IN_SECONDS;

	/**
	 * Available format types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $formats;

	/**
	 * Temporary directory path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $temp_dir = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->formats = [
			'csv'  => new CSV( $this ),
			'xlsx' => new XLSX( $this ),
			'json' => new JSON( $this ),
			'pdf'  => new PDF( $this ),
		];
	}

	/**
	 * Get available format types.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_formats(): array {

		/**
		 * Filter the available format types.
		 *
		 * @since 1.0.0
		 *
		 * @param array $formats Format types.
		 */
		return (array) apply_filters( 'wpforms_entry_automation_export_formatter_manager_get_formats', $this->formats );
	}

	/**
	 * Get available format labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of format keys and their display labels.
	 */
	public function get_formats_labels(): array {

		$labels = [];

		foreach ( $this->get_formats() as $key => $formatter ) {
			$labels[ $key ] = $formatter->get_title();
		}

		return $labels;
	}

	/**
	 * Get a specific formatter instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format Format type.
	 *
	 * @return Formatter|null Formatter instance or null if not found.
	 */
	public function get_formatter( string $format ): ?Formatter {

		$formats = $this->get_formats();

		return $formats[ $format ] ?? null;
	}

	/**
	 * Format entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $task_data          Task data.
	 * @param array  $entries            Entries data.
	 * @param array  $form_data          Form data.
	 * @param string $existing_file_path Existing file path for appending, if any.
	 *
	 * @return string File path or empty string on failure.
	 */
	public function format_entries( array $task_data, array $entries, array $form_data, string $existing_file_path = '' ): string { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$connection = $form_data['settings'][ Plugin::SLUG ][ $task_data['connection_id'] ?? '' ] ?? [];
		$format     = $connection['format'] ?? '';
		$formatter  = $this->get_formatter( $format );

		if ( ! $formatter ) {
			return '';
		}

		// Write straight into the private directory instead of the guessable export root. The
		// filter is scoped to this call so a manual export from the Tools page, which is
		// downloaded through the admin handler, keeps using core's own directory.
		add_filter( 'wpforms_pro_admin_entries_export_file_get_tmpdir', [ $this, 'filter_export_tmpdir' ] );

		try {
			return $formatter->format(
				$entries,
				$task_data,
				$form_data,
				$connection,
				$existing_file_path
			);
		} finally {
			remove_filter( 'wpforms_pro_admin_entries_export_file_get_tmpdir', [ $this, 'filter_export_tmpdir' ] );
		}
	}

	/**
	 * Point core's export writers at the private directory.
	 *
	 * @since 1.4.0
	 *
	 * @param string|mixed $export_path Absolute path to core's export directory.
	 *
	 * @return string
	 */
	public function filter_export_tmpdir( $export_path ): string {

		$private_dir = $this->get_private_dir();

		// get_private_dir() falls back to the base temp dir when it cannot create its own,
		// and returning that would be the same directory core already picked.
		return $private_dir !== '' ? $private_dir : (string) $export_path;
	}

	/**
	 * Directory the export files live in while they wait to be delivered.
	 *
	 * The exports have to stay under the uploads directory, so the directory name carries a
	 * random segment: the delivered file keeps its readable name ( every provider takes the
	 * name from the file's basename ) while its URL cannot be guessed. That matters because
	 * the `.htaccess` guard is ignored by nginx, and these files are written by a scheduled
	 * task with nobody watching.
	 *
	 * @since 1.4.0
	 *
	 * @return string Private directory path, or the base temp dir when it cannot be created.
	 */
	public function get_private_dir(): string {

		$base    = $this->get_temp_dir();
		$segment = $this->get_dir_segment();

		if ( $segment === '' ) {
			return $base;
		}

		$private_dir = wp_normalize_path( trailingslashit( $base ) . $segment );

		if ( ! File::exists( $private_dir ) && ! File::mkdir( $private_dir ) ) {
			return $base;
		}

		wpforms_create_index_html_file( $private_dir );
		$this->remove_stale_private_files( $private_dir );

		return $private_dir;
	}

	/**
	 * Random segment of the private directory name, generated once and reused afterwards.
	 *
	 * @since 1.4.0
	 *
	 * @return string Random segment, or an empty string when it cannot be generated.
	 */
	private function get_dir_segment(): string {

		$segment = (string) get_option( self::PRIVATE_DIR_OPTION, '' );

		if ( $segment !== '' ) {
			return $segment;
		}

		try {
			$segment = bin2hex( random_bytes( 16 ) );
		} catch ( Exception $e ) {
			wpforms_log(
				'Entry Automation export',
				'The private export directory name could not be generated: ' . $e->getMessage(),
				[ 'type' => [ 'error' ] ]
			);

			return '';
		}

		update_option( self::PRIVATE_DIR_OPTION, $segment, false );

		return $segment;
	}

	/**
	 * Delete files a failed delivery left behind in the private directory.
	 *
	 * Core's export cleanup task only sweeps the export directory itself, so the private
	 * directory has to take care of its own leftovers.
	 *
	 * @since 1.4.0
	 *
	 * @param string $private_dir Private directory path.
	 */
	private function remove_stale_private_files( string $private_dir ): void {

		$files = File::dirlist( $private_dir );

		if ( empty( $files ) ) {
			return;
		}

		$expired = time() - self::PRIVATE_FILE_TTL;

		foreach ( $files as $file ) {
			if ( ( $file['type'] ?? '' ) !== 'f' || ( $file['name'] ?? '' ) === 'index.html' ) {
				continue;
			}

			$modified = (int) ( $file['lastmodunix'] ?? 0 );

			// Not every transport reports a timestamp - the FTP dirlist carries none at all - so a
			// file whose age cannot be established is kept. Deleting it would take out the export
			// that is about to be delivered, since this runs right before the file is written.
			if ( $modified === 0 || $modified > $expired ) {
				continue;
			}

			File::delete( trailingslashit( $private_dir ) . $file['name'] );
		}
	}

	/**
	 * Tmp files directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string Temporary files directory path.
	 */
	private function get_base_temp_dir(): string {

		$upload_dir  = wpforms_upload_dir();
		$upload_path = $upload_dir['path'];

		$export_path = trailingslashit( $upload_path ) . 'export';

		if ( ! file_exists( $export_path ) ) {
			wp_mkdir_p( $export_path );
		}

		// Check if the .htaccess exists in the upload directory, if not - create it.
		wpforms_create_upload_dir_htaccess_file();

		// Check if the index.html exists in the directories, if not - create it.
		wpforms_create_index_html_file( $upload_path );
		wpforms_create_index_html_file( $export_path );

		// Check if the .htaccess exists in the export directory, if not - create it.
		wpforms_create_export_dir_htaccess_file( $export_path );

		// Normalize slashes for Windows.
		return wp_normalize_path( $export_path );
	}

	/**
	 * Set the temporary directory path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $temp_dir Path to the temporary directory.
	 */
	public function set_temp_dir( string $temp_dir ): void {

		$this->temp_dir = $temp_dir;
	}

	/**
	 * Retrieve the temporary directory.
	 *
	 * @since 1.0.0
	 *
	 * @return string Path to the temporary directory.
	 */
	public function get_temp_dir(): string {

		if ( ! $this->temp_dir ) {
			$this->set_temp_dir( $this->get_base_temp_dir() );
		}

		return $this->temp_dir;
	}
}
