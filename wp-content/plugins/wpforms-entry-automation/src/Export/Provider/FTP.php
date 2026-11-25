<?php

namespace WPFormsEntryAutomation\Export\Provider;

use WP_Filesystem_FTPext;
use WPFormsEntryAutomation\Plugin;
use WPForms\Helpers\File;

/**
 * FTP provider implementation.
 *
 * @since 1.0.0
 */
class FTP extends Provider {

	/**
	 * Placeholder returned to the builder in place of the stored password.
	 *
	 * The real password is never sent back to the client. When the builder
	 * submits this placeholder unchanged, the previously stored password is
	 * kept as-is.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const PASSWORD_PLACEHOLDER = 'wpforms-entry-automation-ftp-password-unchanged';

	/**
	 * Get a human-readable title of the provider.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider title.
	 */
	public function get_title(): string {

		return esc_html__( 'FTP Server', 'wpforms-entry-automation' );
	}

	/**
	 * Registers hooks for handling FTP connection testing via AJAX.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hooks(): void {

		$namespace_slug = Plugin::SLUG;
		// Test FTP connection.
		add_filter( "wpforms_builder_ajax_{$namespace_slug}_test_ftp_connection", [ $this, 'test_ftp_connection' ], 10, 2 );
	}

	/**
	 * Deliver formatted entries via FTP.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entries_file_path Formatted entries.
	 * @param array  $connection        Connection data.
	 * @param array  $form_data         Form data.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function deliver( string $entries_file_path, array $connection, array $form_data ): bool {
		// Get FTP settings.
		$ftp_settings = $this->get_ftp_settings( $connection );

		// Upload a file to the FTP server.
		$result = $this->upload_to_ftp( $entries_file_path, $ftp_settings );

		// Clean up a temporary file.
		wp_delete_file( $entries_file_path );

		if ( ! $result ) {
			wpforms_log(
				'Entry Automation - FTP upload failed',
				[
					'ftp_settings' => $ftp_settings,
					'file_path'    => $entries_file_path,
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);
		}

		return $result;
	}

	/**
	 * Sanitize connection data based on the export method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Reference to the connection data array to sanitize.
	 * @param array $form_data       Form data array used for sanitization logic.
	 */
	public function sanitize_connection( array &$connection_data, array $form_data ): void {

		$connection_data['ftp'] = $connection_data['ftp'] ?? [];

		$this->sanitize_connection_fields( $connection_data['ftp'] );
	}

	/**
	 * Sanitize FTP connection data fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$ftp_data Connection data array with fields to sanitize. Fields include:
	 *                         - 'host' (string): The host address.
	 *                         - 'port' (int): The port number.
	 *                         - 'password' (string): The password.
	 *                         - 'path' (string): The file path.
	 */
	private function sanitize_connection_fields( array &$ftp_data ): void {

		$port = (int) ( $ftp_data['port'] ?? '' );

		$ftp_data['host']     = sanitize_text_field( $ftp_data['host'] ?? '' );
		$ftp_data['port']     = ! empty( $port ) ? absint( $port ) : '';
		$ftp_data['password'] = sanitize_text_field( $ftp_data['password'] ?? '' );
		$ftp_data['path']     = sanitize_text_field( $ftp_data['path'] ?? '' );
	}

	/**
	 * Get FTP settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data Task data.
	 *
	 * @return array FTP settings.
	 */
	private function get_ftp_settings( array $task_data ): array {

		$ftp = $task_data['ftp'] ?? [];

		if ( empty( $ftp ) ) {
			return [];
		}

		if ( empty( $ftp['host'] ) || empty( $ftp['username'] ) || empty( $ftp['password'] ) ) {
			return [];
		}

		return [
			'host'     => $ftp['host'],
			'port'     => ! empty( $ftp['port'] ) ? absint( $ftp['port'] ) : 21,
			'username' => $ftp['username'],
			'password' => $ftp['password'],
			'path'     => ! empty( $ftp['path'] ) ? $ftp['path'] : '/',
		];
	}

	/**
	 * Tests the FTP connection based on the provided settings.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added the $form_data parameter to resolve the password placeholder.
	 *
	 * @param mixed $response  The initial response or fallback response if the connection fails.
	 * @param array $form_data The current form data, used to look up the stored password
	 *                         when the client submits the unchanged-password placeholder.
	 *
	 * @return bool|mixed True if the FTP connection is successful or the original response on failure.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function test_ftp_connection( $response, $form_data = [] ) {

		$settings      = wp_unslash( $_POST['settings'] ?? [] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
		$connection_id = sanitize_text_field( wp_unslash( $_POST['connectionId'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$settings      = $this->resolve_password( $settings, $form_data, $connection_id );

		if ( $this->check_ftp_connection( $settings ) ) {
			return true;
		}

		return $response;
	}

	/**
	 * Resolve the unchanged-password placeholder back to the stored password.
	 *
	 * The builder never receives the real password (see FormBuilder::mask_ftp_password()),
	 * so testing an unmodified connection resubmits the placeholder. Swap it back for the
	 * previously stored password so the test can run without the user retyping it.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $settings      Submitted FTP settings.
	 * @param array  $form_data     Current form data.
	 * @param string $connection_id Connection ID the settings belong to.
	 *
	 * @return array Settings with the real password in place.
	 */
	private function resolve_password( array $settings, array $form_data, string $connection_id ): array {

		if ( ( $settings['password'] ?? '' ) !== self::PASSWORD_PLACEHOLDER ) {
			return $settings;
		}

		$settings['password'] = $form_data['settings'][ Plugin::SLUG ][ $connection_id ]['ftp']['password'] ?? '';

		return $settings;
	}

	/**
	 * Initialize FTP connection using the WP_Filesystem_FTPext class directly.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ftp_settings FTP settings.
	 *
	 * @return object|bool FTP object on success, false on failure.
	 */
	private function init_ftp_connection( array $ftp_settings ) {

		// Include required files for WP_Filesystem_FTPext.
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-ftpext.php';

		// Prepare credentials for FTP connection.
		$credentials = [
			'hostname' => $ftp_settings['host'],
			'port'     => (int) $ftp_settings['port'],
			'username' => $ftp_settings['username'],
			'password' => $ftp_settings['password'],
		];

		// Initialize WP_Filesystem_FTPext directly.
		$ftp = new WP_Filesystem_FTPext( $credentials );

		// Try to connect.
		$connected = $ftp->connect();

		return $connected ? $ftp : false;
	}

	/**
	 * Check FTP connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $ftp_settings FTP settings.
	 *
	 * @return bool True if connection successful, false otherwise.
	 */
	private function check_ftp_connection( array $ftp_settings ): bool {

		// Validate FTP settings.
		if ( empty( $ftp_settings['host'] ) || empty( $ftp_settings['username'] ) || empty( $ftp_settings['password'] ) ) {
			return false;
		}

		// Initialize FTP connection.
		$ftp = $this->init_ftp_connection( $ftp_settings );

		if ( ! $ftp ) {
			return false;
		}

		// Check if we can access the target directory.
		$list = $ftp->dirlist( $ftp_settings['path'] );

		if ( $list === false ) {
			// If the directory is empty, create it.
			return $this->create_remote_directory( $ftp, $ftp_settings['path'] );
		}

		return true;
	}

	/**
	 * Upload a file to the FTP server.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path    File path.
	 * @param array  $ftp_settings FTP settings.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function upload_to_ftp( string $file_path, array $ftp_settings ): bool {

		// Check if a file exists.
		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		// Get file contents.
		$file_contents = file_get_contents( $file_path );

		if ( $file_contents === false ) {
			return false;
		}

		// Initialize FTP connection.
		$ftp = $this->init_ftp_connection( $ftp_settings );

		if ( ! $ftp ) {
			return false;
		}

		$remote_path = rtrim( $ftp_settings['path'], '/' ) . '/';

		// Prepare a remote path.
		$remote_file = $remote_path . basename( $file_path );

		// Check if a file already exists on the server.
		if ( $ftp->size( $remote_file ) ) {
			// If a file exists, delete it.
			$ftp->delete( $remote_file, false, 'f' );
		}

		// Create a directory if it doesn't exist.
		$this->create_remote_directory( $ftp, $remote_path );

		// Upload file.
		return $ftp->put_contents( $remote_file, $file_contents );
	}

	/**
	 * Check if a file exists on the FTP server.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  Name of the file to check.
	 * @param array  $task_data  Task-specific data.
	 * @param array  $connection Connection details.
	 *
	 * @return bool True if the file exists, false otherwise.
	 */
	public function is_file_exist( string $file_name, array $task_data, array $connection ): bool {

		// Get FTP settings.
		$ftp_settings = $this->get_ftp_settings( $connection );

		// Initialize FTP connection.
		$ftp = $this->init_ftp_connection( $ftp_settings );

		if ( ! $ftp ) {
			return false;
		}

		$remote_path = rtrim( $ftp_settings['path'], '/' ) . '/';

		// Prepare a remote path.
		$remote_file = $remote_path . $file_name;

		return $ftp->size( $remote_file );
	}

	/**
	 * Create directories recursively on the remote server.
	 *
	 * @since 1.0.0
	 *
	 * @param object $ftp      WP_Filesystem_FTPext object.
	 * @param string $dir_path Directory path to create.
	 *
	 * @return bool True on success, false on failure.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private function create_remote_directory( $ftp, string $dir_path ): bool {

		// If the directory already exists, return true.
		if ( $ftp->is_dir( $dir_path ) ) {
			return true;
		}

		// Create the parent directory first.
		$parent_dir = dirname( $dir_path );

		if ( $parent_dir !== $dir_path && ! $ftp->is_dir( $parent_dir ) ) {
			$this->create_remote_directory( $ftp, $parent_dir );
		}

		// Create the directory.
		return $ftp->mkdir( $dir_path );
	}

	/**
	 * Download a file from the FTP server.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  Task data.
	 * @param array $connection Connection details.
	 *
	 * @return string Local file path or empty string on failure.
	 */
	public function download_file( array $task_data, array $connection ): string {

		// Get the FTP settings from connection details.
		$ftp_settings = $this->get_ftp_settings( $connection );

		// Initialize FTP connection.
		$ftp = $this->init_ftp_connection( $ftp_settings );

		if ( ! $ftp ) {
			return '';
		}

		// Prepare a remote file path.
		$remote_path = rtrim( $ftp_settings['path'], '/' ) . '/';
		$remote_file = $remote_path . $task_data['file_name'];

		$wp_filesystem = File::get_filesystem();
		$copied_file   = $ftp->get_contents( $remote_file );

		// Create a temporary file for download. It keeps its readable name for the re-upload,
		// so it goes to the private directory rather than a guessable path under uploads.
		$local_file = trailingslashit( wpforms_entry_automation()->get( 'formatter_manager' )->get_private_dir() ) . sanitize_file_name( $task_data['file_name'] );

		$result = $wp_filesystem->put_contents( $local_file, $copied_file );

		return $result === false ? '' : $local_file;
	}

	/**
	 * Delete file from FTP server.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  Task data.
	 * @param array $connection Connection details.
	 */
	public function delete_file( array $task_data, array $connection ): void {

		// Get the FTP settings from connection details.
		$ftp_settings = $this->get_ftp_settings( $connection );

		// Initialize FTP connection.
		$ftp = $this->init_ftp_connection( $ftp_settings );

		if ( ! $ftp ) {
			return;
		}

		// Prepare a remote file path.
		$remote_path = rtrim( $ftp_settings['path'], '/' ) . '/';
		$remote_file = $remote_path . $task_data['file_name'];

		// Delete the file.
		$ftp->delete( $remote_file );
	}
}
