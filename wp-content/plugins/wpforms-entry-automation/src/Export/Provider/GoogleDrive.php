<?php

namespace WPFormsEntryAutomation\Export\Provider;

use WPForms\Helpers\File;
use WPFormsEntryAutomation\Export\FormatterManager; // phpcs:ignore WPForms.PHP.UseStatement.UnusedUseStatement
use WPFormsGoogleDrive\Api\Client;
use WPFormsGoogleDrive\Provider\Settings\FormBuilder as GoogleDriveFormBuilder;
use WPFormsGoogleDrive\Api\Http\Request;

/**
 * Google Drive provider implementation.
 *
 * @since 1.0.0
 */
class GoogleDrive extends Provider implements AddonProviderInterface {

	/**
	 * Get a human-readable title of the provider.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider title.
	 */
	public function get_title(): string {

		return esc_html__( 'Google Drive', 'wpforms-entry-automation' );
	}

	/**
	 * Checks whether the required addon is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the addon is activated, false otherwise.
	 */
	public function is_activated(): bool {

		return wpforms_is_addon_initialized( 'google-drive' );
	}

	/**
	 * Retrieve addon data for Dropbox.
	 *
	 * @since 1.0.0
	 *
	 * @return array Addon data for Dropbox.
	 */
	public function get_data(): array {

		return wpforms()->obj( 'addons' )->get_addon( 'google-drive' );
	}

	/**
	 * Retrieve custom templates.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of custom template identifiers.
	 */
	public function get_custom_templates(): array {

		if ( ! $this->is_activated() ) {
			return [];
		}

		return [
			'export-to/google',
			'export-to/google-folder',
		];
	}

	/**
	 * Deliver formatted entries via Google Drive.
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

		$client = $this->get_api_client( $connection['google']['account_id'] ?? '' );

		if ( ! $client ) {
			wpforms_log(
				'Entry Automation - Google Drive client not found',
				[
					'account_id' => $connection['google']['account_id'] ?? '',
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);

			return false;
		}

		// Get Google Drive settings.
		$request_args = $this->get_request_args( $entries_file_path, $connection );

		// Upload a file to Google Drive.
		$file_id = $client->upload_file( $request_args );

		wpforms_entry_automation()->get( 'tasks' )->update(
			$connection['task_id'],
			[
				'destination' => $file_id,
				'file'        => $request_args['name'],
			]
		);

		if ( empty( $file_id ) ) {
			wpforms_log(
				'Entry Automation - Google Drive upload failed',
				[
					'file_id' => $file_id,
					'file'    => $request_args['name'],
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);
		}

		return ! empty( $file_id );
	}

	/**
	 * Checks if a file exists on Google Drive using the provided file ID and connection details.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  The name of the file to check for existence.
	 * @param array  $task_data  An associative array containing task-specific data, including the file ID.
	 * @param array  $connection An associative array containing connection details, including the Google account ID.
	 *
	 * @return bool Returns true if the file exists, otherwise false.
	 */
	public function is_file_exist( string $file_name, array $task_data, array $connection ): bool {

		$file_id = $task_data['destination'] ?? '';

		// If the file_id is empty, return false immediately.
		if ( empty( $file_id ) ) {
			return false;
		}

		// Get the client using the account ID from task data.
		$client = $this->get_api_client( $connection['google']['account_id'] ?? '' );

		if ( ! $client ) {
			return false;
		}

		// Create a Request object with the client's access token.
		$request = new Request( $client->get_access_token() );

		// Construct the API URL to get file metadata.
		$url = 'https://www.googleapis.com/drive/v3/files/' . $file_id;

		// Send a GET request to check if the file exists.
		$response = $request->get( $url, [ 'fields' => 'id,name' ] );

		// Return true if the file exists.
		return ! $response->has_errors();
	}

	/**
	 * Downloads a file from Google Drive using the provided file ID and connection details.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An associative array containing task-specific data used during the download operation.
	 * @param array $connection An associative array containing connection details, including the Google account ID.
	 *
	 * @return string The path to the downloaded temporary file if successful, or an empty string on failure.
	 */
	public function download_file( array $task_data, array $connection ): string {

		$file_id = $task_data['destination'] ?? '';

		if ( empty( $file_id ) ) {
			return '';
		}

		$client = $this->get_api_client( $connection['google']['account_id'] ?? '' );

		if ( ! $client ) {
			return '';
		}

		// Create a Request object with the client's access token.
		$request = new Request( $client->get_access_token() );

		// Construct the API URL for the file download.
		$url = 'https://www.googleapis.com/drive/v3/files/' . $file_id;

		// Send a GET request to download the file content.
		$response = $request->get( $url, [ 'alt' => 'media' ] );

		// Here we should abort the process or continue uploading the regular file.
		if ( $response->has_errors() ) {
			return '';
		}

		/**
		 * Formatter manager instance.
		 *
		 * @var FormatterManager $formatter_manager
		 */
		$formatter_manager = wpforms_entry_automation()->get( 'formatter_manager' );
		// Create a temporary file to store the downloaded content.
		$tmp_file = trailingslashit( $formatter_manager->get_private_dir() ) . sanitize_file_name( $task_data['file_name'] );
		// Write the file content to the temporary file.
		$content = $response->get_file();
		$result  = File::put_contents( $tmp_file, $content );

		return $result === false ? '' : $tmp_file;
	}

	/**
	 * Deletes a file from Google Drive using the provided file ID and connection details.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An associative array containing task-specific data used during the delete operation.
	 * @param array $connection An associative array containing connection details, including the Google account ID.
	 */
	public function delete_file( array $task_data, array $connection ): void {

		$file_id = $task_data['destination'] ?? '';

		if ( empty( $file_id ) ) {
			return;
		}

		$client = $this->get_api_client( $connection['google']['account_id'] ?? '' );

		if ( ! $client ) {
			return;
		}

		// Create a Request object with the client's access token.
		$request = new Request( $client->get_access_token() );
		// Send a DELETE request to the API to delete the file.
		// It's okay, even if it fails.
		$request->delete(
			'https://www.googleapis.com/drive/v3/files/' . $file_id
		);
	}

	/**
	 * Sanitizes connection data for export destinations and processes Google connection specifics if applicable.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data An associative array representing the connection data to be sanitized. It may be updated during the process.
	 * @param array $form_data        An associative array containing form data related to the connection.
	 */
	public function sanitize_connection( array &$connection_data, array $form_data ): void {

		$connection_data['google']               = $connection_data['google'] ?? [];
		$connection_data['google']['account_id'] = sanitize_text_field( $connection_data['google']['account_id'] ?? '' );

		$this->sanitize_folder( $connection_data );
		$this->create_folder( $connection_data, $form_data );
	}

	/**
	 * Sanitizes folder-related data within the provided connection data array.
	 * Adjusts and validates the folder type and appropriately sanitizes or removes folder-specific fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data An associative array containing Google connection data.
	 *                                Includes folder settings that will be sanitized and updated.
	 */
	private function sanitize_folder( array &$connection_data ): void {

		$folder_type = $connection_data['google']['folder_type'] ?? '';
		$folder_type = in_array( $folder_type, [ 'new', 'existing' ], true ) ? $folder_type : 'new';

		$connection_data['google']['folder_type'] = $folder_type;

		if ( $folder_type === 'new' ) {
			$connection_data['google']['folder_name'] = sanitize_text_field( $connection_data['google']['folder_name'] ?? '' );

			unset( $connection_data['google']['folder_id'] );
		} else {
			$connection_data['google']['folder_id'] = sanitize_text_field( $connection_data['google']['folder_id'] ?? '' );

			unset( $connection_data['google']['folder_name'] );
		}
	}

	/**
	 * Determine whether the current user can work with an existing folder.
	 *
	 * The Google Picker needs the site-wide OAuth access token in the browser, so it is
	 * limited to the users who administer the connected account.
	 *
	 * @since 1.4.0
	 *
	 * @return bool
	 */
	public static function current_user_can_select_existing_folder(): bool {

		return wpforms_current_user_can();
	}

	/**
	 * Drop an existing folder selection the current user isn't allowed to make.
	 *
	 * Only the folder already stored for that connection and account survives, so anything
	 * else falls back to a folder the addon creates.
	 *
	 * @since 1.4.0
	 *
	 * @param array $connection      Connection data being saved.
	 * @param array $prev_connection Previously stored data of the same connection.
	 *
	 * @return array
	 */
	public function restrict_connection( array $connection, array $prev_connection ): array {

		if ( ! is_array( $connection['google'] ?? null ) || self::current_user_can_select_existing_folder() ) {
			return $connection;
		}

		$connection['google'] = $this->restrict_folder(
			$connection['google'],
			(array) ( $prev_connection['google'] ?? [] )
		);

		return $connection;
	}

	/**
	 * Keep an already stored folder or fall back to creating a new one.
	 *
	 * @since 1.4.0
	 *
	 * @param array $google      Google connection data being saved.
	 * @param array $prev_google Previously stored data of the same connection.
	 *
	 * @return array
	 */
	private function restrict_folder( array $google, array $prev_google ): array {

		$folder_id = (string) ( $google['folder_id'] ?? '' );

		// A folder that is already stored for this account was picked by someone allowed to pick it.
		if (
			$folder_id !== '' &&
			$folder_id === (string) ( $prev_google['folder_id'] ?? '' ) &&
			( $google['account_id'] ?? '' ) === ( $prev_google['account_id'] ?? '' )
		) {
			return $google;
		}

		if ( $folder_id === '' && ( $google['folder_type'] ?? '' ) !== 'existing' ) {
			return $google;
		}

		$google['folder_type'] = 'new';

		unset( $google['folder_id'] );

		return $google;
	}

	/**
	 * Create a folder in Google Drive.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data Google Drive connection data.
	 * @param array $form_data        Form data used to generate the folder.
	 */
	public function create_folder( array &$connection_data, array $form_data ): void {

		$google_form_builder = $this->get_google_drive_form_builder();
		$google              = $connection_data['google'] ?? [];
		$export_to           = $connection_data['export_to'] ?? '';

		if (
			! $google_form_builder ||
			$export_to !== 'google' ||
			(
				! empty( $google['folder_type'] ) &&
				$google['folder_type'] !== 'new'
			)
		) {
			return;
		}

		$google_form_builder->create_folder( $google, $form_data );
		$connection_data['google'] = $google;
	}

	/**
	 * Retrieve the Google Drive form builder instance.
	 *
	 * This method attempts to fetch the form builder instance for Google Drive integration
	 * if the associated function exists.
	 *
	 * @since 1.0.0
	 *
	 * @return GoogleDriveFormBuilder|null The Google Drive form builder instance or null if not available.
	 */
	private function get_google_drive_form_builder(): ?GoogleDriveFormBuilder {

		if ( ! $this->is_activated() ) {
			return null;
		}

		return wpforms_google_drive()->get( 'core' )->get_form_builder();
	}

	/**
	 * Build and retrieve request arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entries_file_path Path to the entries file.
	 * @param array  $connection        Connection configuration array.
	 *
	 * @return array List of request arguments.
	 */
	private function get_request_args( string $entries_file_path, array $connection ): array {

		// FileInfo PHP extension (ext-fileinfo) is safely loaded by the Google Drive addon.
		$mime_type = mime_content_type( $entries_file_path );
		$extension = strtolower( pathinfo( $entries_file_path, PATHINFO_EXTENSION ) );

		if ( $mime_type === 'text/plain' && $extension === 'csv' ) {
			$mime_type = 'application/csv';
		}

		return [
			'name'      => basename( $entries_file_path ),
			'path'      => $entries_file_path,
			'mime'      => $mime_type,
			'folder_id' => $connection['google']['folder_id'] ?? '',
		];
	}

	/**
	 * Retrieve an API client for the provided account ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id The account ID for which the API client is to be retrieved.
	 *
	 * @return Client|null The API client instance if available, or null if not found.
	 */
	private function get_api_client( string $account_id ): ?Client {

		if ( ! $this->is_activated() ) {
			return null;
		}

		$account = wpforms_google_drive()->get( 'account' );

		if ( ! $account ) {
			return null;
		}

		return $account->get_client_by_id( $account_id );
	}
}
