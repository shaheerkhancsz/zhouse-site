<?php

namespace WPFormsEntryAutomation\Export\Provider;

use WPForms\Helpers\File;
use WPFormsDropbox\Api\Api;
use WPFormsEntryAutomation\Helpers\DropboxReconnect;
use RuntimeException;

/**
 * Dropbox provider implementation.
 *
 * @since 1.0.0
 */
class Dropbox extends Provider implements AddonProviderInterface {

	/**
	 * Instance of DropboxReconnect class for handling reconnection functionality.
	 * We may use it in the future to disable this logic, when it will be implemented in the Dropbox addon itself.
	 *
	 * @since 1.0.0
	 *
	 * @var DropboxReconnect
	 */
	private $reconnect;

	/**
	 * Constructor method for initializing the object.
	 * Initializes the DropboxReconnect instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->reconnect = new DropboxReconnect();
	}

	/**
	 * Registers the necessary hooks for the Dropbox reconnect functionality.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		$this->reconnect->hooks();
	}

	/**
	 * Get a human-readable title of the provider.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider title.
	 */
	public function get_title(): string {

		return esc_html__( 'Dropbox', 'wpforms-entry-automation' );
	}

	/**
	 * Checks whether the required addon is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the addon is activated, false otherwise.
	 */
	public function is_activated(): bool {

		return wpforms_is_addon_initialized( 'dropbox' );
	}

	/**
	 * Retrieve addon data for Dropbox.
	 *
	 * @since 1.0.0
	 *
	 * @return array Addon data for Dropbox.
	 */
	public function get_data(): array {

		return wpforms()->obj( 'addons' )->get_addon( 'dropbox' );
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
			'export-to/dropbox',
			'export-to/dropbox-fields',
		];
	}

	/**
	 * Deliver formatted entries via Dropbox.
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

		$account_id = $connection['dropbox']['account_id'] ?? '';
		$api        = wpforms_entry_automation()->get( 'dropbox_client' )->new_api( $account_id );

		if ( ! $api ) {
			wpforms_log(
				'Entry Automation - Dropbox API instance is not available',
				[
					'account_id' => $account_id,
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);

			return false;
		}

		try {
			$dropbox_path  = $this->get_full_path( $api, $connection, basename( $entries_file_path ) );
			$uploaded_file = $api->upload( $entries_file_path, $dropbox_path );
		} catch ( RuntimeException $exception ) {
			return false;
		}

		if ( empty( $uploaded_file['id'] ) || empty( $uploaded_file['path_display'] ) ) {
			wpforms_log(
				'Entry Automation - Dropbox upload failed',
				[
					'file_path' => $entries_file_path,
					'path'      => $dropbox_path,
					'response'  => $uploaded_file,
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);

			return false;
		}

		$shared_link = $api->create_shared_link( $uploaded_file['path_display'] );

		$destination = wp_json_encode(
            [
				'id'  => $uploaded_file['id'],
				'url' => $shared_link['url'] ?? '',
            ]
        );

		wpforms_entry_automation()->get( 'tasks' )->update( $connection['task_id'], [ 'destination' => $destination ] );

		return true;
	}

	/**
	 * Checks if the file with the specified name exists using the provided task data and connection information.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  The name of the file to check.
	 * @param array  $task_data  Task data that may include additional file information.
	 * @param array  $connection An array containing the connection details required to check file existence.
	 *
	 * @return bool True if the file exists, false otherwise.
	 */
	public function is_file_exist( string $file_name, array $task_data, array $connection ): bool {

		$file_id = $this->get_destination_file_id( $task_data );

		if ( empty( $file_id ) ) {
			return false;
		}

		$account_id = $connection['dropbox']['account_id'] ?? '';
		$api        = wpforms_entry_automation()->get( 'dropbox_client' )->new_api( $account_id );

		if ( ! $api ) {
			return false;
		}

		$request = wpforms_entry_automation()->get( 'dropbox_client' )->new_request( $account_id );

		if ( ! $request ) {
			return false;
		}

		// Check if the file exists using the Dropbox API.
		$body = wp_json_encode( [ 'path' => $file_id ] );
		$url  = Api::API_ACCOUNT_URL . 'files/get_metadata';

		try {
			$response = $request->post( $url, $body );
		} catch ( RuntimeException $exception ) {
			return false;
		}

		// If there are no errors in the response, the file exists.
		return ! $response->has_errors();
	}

	/**
	 * Downloads a file using the provided file ID and connection details.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  Task data containing the file ID to be downloaded.
	 * @param array $connection The connection details required to access the file.
	 *
	 * @return string The contents of the downloaded file.
	 */
	public function download_file( array $task_data, array $connection ): string {

		$file_id = $this->get_destination_file_id( $task_data );

		if ( empty( $file_id ) ) {
			return '';
		}

		$account_id        = $connection['dropbox']['account_id'] ?? '';
		$file_content      = wpforms_entry_automation()->get( 'dropbox_client' )->get_file( $account_id, $file_id );
		$formatter_manager = wpforms_entry_automation()->get( 'formatter_manager' );
		// Create a temporary file path. Use basename for safety.
		$tmp_file = trailingslashit( $formatter_manager->get_private_dir() ) . sanitize_file_name( $task_data['file_name'] );
		// Write the file content to the temporary file.
		$result = File::put_contents( $tmp_file, $file_content );

		return $result === false ? '' : $tmp_file;
	}

	/**
	 * Deletes a file with the specified ID using the provided connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  Task data.
	 * @param array $connection An array containing the connection details required to perform the delete operation.
	 */
	public function delete_file( array $task_data, array $connection ): void {

		$file_id = $this->get_destination_file_id( $task_data );

		if ( empty( $file_id ) ) {
			return;
		}

		$account_id = $connection['dropbox']['account_id'] ?? '';
		$request    = wpforms_entry_automation()->get( 'dropbox_client' )->new_request( $account_id );

		if ( ! $request ) {
			return;
		}

		// Prepare the request body for the Dropbox API delete endpoint.
		$body = wp_json_encode( [ 'path' => $file_id ] );
		$url  = Api::API_ACCOUNT_URL . 'files/delete_v2';

		try {
			// Make the POST request to delete the file.
			$response = $request->post( $url, $body );
		} catch ( RuntimeException $exception ) {
			return;
		}

		if ( ! $response->has_errors() ) {
			return;
		}

		// Log errors if any.
		wpforms_log(
			'Request to Dropbox API was failed',
			[
				'message' => $response->get_body(),
				'request' => [
					'method' => 'POST',
					'url'    => $url,
				],
			],
			[
				'type' => [ 'provider', 'error' ],
			]
		);
	}

	/**
	 * Sanitizes the connection data for the specified export service.
	 * Ensures the connection data is properly formatted and sanitized for
	 * Dropbox when the export service is set to 'dropbox'. Removes connection
	 * details for unsupported export services.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data An array of connection data that will be sanitized in place.
	 * @param array $form_data       An array containing form-related data.
	 */
	public function sanitize_connection( array &$connection_data, array $form_data ): void {

		$connection_data['dropbox'] = $connection_data['dropbox'] ?? [];

		$this->sanitize_connection_fields( $connection_data['dropbox'], $form_data );
	}

	/**
	 * Retrieves the destination file ID from the task data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data Task data containing destination details.
	 *
	 * @return string The destination file ID.
	 */
	private function get_destination_file_id( array $task_data ): string {

		$destination = $task_data['destination'] ?? '';
		$destination = wpforms_is_json( $destination ) ? json_decode( $destination, true ) : [];
		$file_id     = $destination['id'] ?? '';

		return (string) $file_id;
	}

	/**
	 * Sanitizes Dropbox connection fields and updates the folder name if not set.
	 *
	 * @since 1.0.0
	 *
	 * @param array $dropbox_data Reference to the Dropbox data array containing connection fields to be sanitized.
	 * @param array $form_data    Array containing form data, used to populate the folder name if it is empty.
	 */
	private function sanitize_connection_fields( array &$dropbox_data, array $form_data ): void {

		$dropbox_data['account_id']  = sanitize_text_field( $dropbox_data['account_id'] ?? '' );
		$dropbox_data['folder_name'] = $this->sanitize_folder_name( $dropbox_data['folder_name'] ?? '', $form_data );
		$dropbox_data['path']        = sanitize_text_field( $dropbox_data['path'] ?? '' );

		if ( empty( $dropbox_data['folder_name'] ) ) {
			$dropbox_data['folder_name'] = $form_data['settings']['form_title'] ?? '';
		}
	}

	/**
	 * Constructs the full path of a file within the specified folder and connection.
	 *
	 * @since 1.0.0
	 *
	 * @param Api    $api        An instance of the Api class used to retrieve necessary data.
	 * @param array  $connection An array containing connection details, including folder path information.
	 * @param string $file_name  The name of the file for which the full path is to be constructed.
	 *
	 * @return string The full path of the file, or an empty string if the main folder is not found.
	 */
	private function get_full_path( Api $api, array $connection, string $file_name ): string {

		$main_folder = $this->get_main_folder( $api, $connection );

		if ( empty( $main_folder ) ) {
			return '';
		}

		$folder = $connection['dropbox']['path'] ?? '';

		if ( $folder === '/' ) {
			$folder = $main_folder;
		}

		return $folder . '/' . $file_name;
	}

	/**
	 * Retrieves the main folder from Dropbox based on the provided connection details.
	 * If the folder does not exist, it creates the folder using the API.
	 *
	 * @since 1.0.0
	 *
	 * @param Api   $api        An instance of the API class used to interact with Dropbox.
	 * @param array $connection An array containing Dropbox connection details, including the target folder name.
	 *
	 * @return string The path of the main folder in Dropbox.
	 */
	private function get_main_folder( Api $api, array $connection ): string {

		$folder_name = sprintf( '/%s', wpforms_dropbox()->get( 'helper' )->prepare_folder_name( $connection['dropbox']['folder_name'] ) );

		return $api->folder_exists( $folder_name ) ? $folder_name : $api->create_folder( $folder_name );
	}

	/**
	 * Sanitizes and prepares a folder name based on the provided input and form data.
	 *
	 * Ensures the folder name is secure, clean, and compliant with naming conventions.
	 * If no folder name is provided, it falls back to the form title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $folder_name The input folder name to be sanitized.
	 * @param array  $form_data   Form data containing settings such as the form title.
	 *
	 * @return string The sanitized folder name.
	 */
	private function sanitize_folder_name( string $folder_name, array $form_data ): string {

		$folder_name = sanitize_text_field( wpforms_dropbox()->get( 'helper' )->prepare_folder_name( $folder_name ) );

		if ( ! isset( $form_data['settings']['form_title'] ) || ! wpforms_is_empty_string( $folder_name ) ) {
			return $folder_name;
		}

		// Form Name is used as a fallback.
		return sanitize_text_field(
			wpforms_dropbox()->get( 'helper' )->prepare_folder_name( $form_data['settings']['form_title'] )
		);
	}
}
