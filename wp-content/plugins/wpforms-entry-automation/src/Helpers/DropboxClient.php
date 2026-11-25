<?php

namespace WPFormsEntryAutomation\Helpers;

use WPFormsDropbox\Api\Auth;
use WPFormsDropbox\Api\Http\Response;
use WPFormsDropbox\Plugin as DropboxPlugin;
use WPFormsDropbox\Api\Http\Request;
use WPFormsDropbox\Api\Api;

/**
 * Class Client creates connections between addon and the Dropbox API.
 *
 * @since 1.0.0
 */
class DropboxClient {

	/**
	 * API content URL.
	 *
	 * @since 1.0.0
	 */
	private const API_CONTENT_URL = 'https://content.dropboxapi.com/2/';

	/**
	 * Create a new connection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return Api|null
	 */
	public function new_api( string $account_id ): ?Api {

		$request = $this->new_request( $account_id );

		if ( ! $request ) {
			return null;
		}

		// If the token is valid, return the API connection.
		return new Api( $request );
	}

	/**
	 * Create a new request instance for the account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return Request|null
	 */
	public function new_request( string $account_id ): ?Request {

		$access_token = $this->get_access_token( $account_id );

		if ( empty( $access_token ) ) {
			return null;
		}

		// Create a request instance with the token.
		return new Request( $access_token );
	}

	/**
	 * Retrieve the content of a specified file from Dropbox.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID associated with Dropbox.
	 * @param string $file_id    File path or ID in the Dropbox account.
	 *
	 * @return string
	 */
	public function get_file( string $account_id, string $file_id ): string {

		$access_token = $this->get_access_token( $account_id );

		// Get default parameters (without allowing them to be overwritten).
		$options = $this->get_default_parameters( $access_token );

		$options['headers'] = array_merge(
			$options['headers'],
			[
				'Content-Type'    => 'application/octet-stream',
				'Dropbox-API-Arg' => wp_json_encode( [ 'path' => $file_id ] ),
			]
		);
		$options['method']  = 'POST';
		$url                = self::API_CONTENT_URL . 'files/download';

		// Retrieve the raw response from a safe HTTP request.
		$http_response = wp_safe_remote_request( $url, $options );
		$response      = new Response( $http_response );

		if ( ! $response->has_errors() ) {
			return wp_remote_retrieve_body( $http_response );
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

		return '';
	}

	/**
	 * Generate the authorization URL with optional query parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_params Optional. Additional query parameters to include in the redirect URI.
	 *
	 * @return string
	 */
	public static function get_authorization_url( array $query_params = [] ): string {

		$data = [
			'redirect_uri' => wpforms_current_url(),
		];

		// Set additional parameters.
		if ( ! empty( $query_params ) ) {
			$data['redirect_uri'] = add_query_arg( $query_params, $data['redirect_uri'] );
		}

		$state = rawurlencode( base64_encode( wp_json_encode( $data ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		return add_query_arg(
			[
				'action' => 'init',
				'state'  => $state,
			],
			Auth::get_redirect_uri()
		);
	}

	/**
	 * Retrieve folders from Dropbox accounts based on connections and form data.
	 *
	 * Collects folder information for each account from provided connections and organizes it
	 * based on the folder settings and form-specific data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connections Array of Dropbox connection data.
	 * @param array $form_data   Form data including settings and metadata.
	 *
	 * @return array
	 */
	public function get_folders( array $connections, array $form_data ): array {

		$folders = [];

		// Collect folders from connections.
		foreach ( $connections as $connection_data ) {
			if ( empty( $connection_data['dropbox']['folder_name'] ) || empty( $connection_data['dropbox']['account_id'] ) ) {
				continue;
			}

			$account_id = $connection_data['dropbox']['account_id'];

			if ( isset( $folders[ $account_id ] ) ) {
				continue;
			}

			$connection = $this->new_api( $account_id );

			if ( ! $connection ) {
				continue;
			}

			$folders[ $account_id ] = $this->get_subfolders( $connection_data, $form_data, $connection );
		}

		return $folders;
	}

	/**
	 * Retrieves a list of subfolders based on provided connection and form data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data containing folder information.
	 * @param array $form_data       Form data that may include form title information.
	 * @param Api   $connection      Connection instance used for folder operations.
	 *
	 * @return array List of subfolders derived from the provided data.
	 */
	private function get_subfolders( array $connection_data, array $form_data, Api $connection ): array {
		// Add a connection folder.
		$connection_folder = $connection_data['dropbox']['folder_name'];
		// Add the main form folder.
		$form_title = $form_data['settings']['form_title'] ?? '';
		$root_label = [ '/' => __( 'Root', 'wpforms-entry-automation' ) ];
		$subfolders = $this->add_subfolders( [], $connection_folder, $connection, $root_label );

		if ( ! wpforms_is_empty_string( $form_title ) ) {
			$subfolders = $this->add_subfolders( $subfolders, $form_title, $connection, $root_label );
		}

		return $subfolders;
	}

	/**
	 * Adds subfolders to the provided list based on the specified folder name and connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $subfolders  Existing list of subfolders to be updated.
	 * @param string $folder_name Name of the folder to add subfolders for.
	 * @param Api    $connection  Connection instance used to retrieve subfolder data.
	 * @param array  $root_label  Label array representing the root folder structure.
	 *
	 * @return array Updated list of subfolders including the new folder and its subfolders.
	 */
	private function add_subfolders( array $subfolders, string $folder_name, Api $connection, array $root_label ): array {

		$clean_folder_name     = wpforms_dropbox()->get( 'helper' )->prepare_folder_name( $folder_name );
		$formatted_folder_name = sprintf( '/%s', $clean_folder_name );

		$subfolders[ $clean_folder_name ] = array_merge(
			$root_label,
			$connection->get_subfolders( $formatted_folder_name )
		);

		return $subfolders;
	}

	/**
	 * Get a valid access token for the account.
	 * In case the token is expired, refresh it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return string
	 */
	private function get_access_token( string $account_id ): string {

		if ( ! wpforms_is_addon_initialized( DropboxPlugin::SLUG ) ) {
			return '';
		}

		$accounts = wpforms_get_providers_options( DropboxPlugin::SLUG );

		if ( empty( $accounts[ $account_id ]['access_token'] ) ) {
			return '';
		}

		$account = wpforms_dropbox()->get( 'account' );

		if ( $account->is_access_token_expired( $account_id ) === true ) {
			return $this->get_refreshed_account_token( $account_id ) ?? '';
		}

		return $account->get( $account_id )['access_token'] ?? '';
	}

	/**
	 * Get refreshed account data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return string
	 */
	private function get_refreshed_account_token( string $account_id ): string {

		$account      = wpforms_dropbox()->get( 'account' );
		$account_data = $account->get( $account_id );
		$response     = Auth::refresh_access_token( $account_data['refresh_token'] );

		if ( ! isset( $response['success'] ) || (bool) $response['success'] === false ) {

			wpforms_log(
				__( 'Dropbox API: Error refreshing Dropbox access token.', 'wpforms-entry-automation' ),
				! empty( $response['error'] ) ? esc_html( $response['error'] ) : '',
				[
					'type' => [
						'provider',
						'error',
					],
				]
			);

			return '';
		}

		$access_token = ! empty( $response['access_token'] ) ? $response['access_token'] : '';
		$expires_in   = ! empty( $response['expires_in'] ) ? $response['expires_in'] : 0;

		$account_data = $account->update(
			$account_id,
			[
				'access_token'            => $access_token,
				'access_token_expires_at' => $account->get_access_token_expiration_time( $expires_in ),
			]
		);

		return $account_data['access_token'] ?? '';
	}

	/**
	 * Retrieve the default parameters for an API request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $access_token The access token used for authorization.
	 *
	 * @return array
	 */
	private function get_default_parameters( string $access_token ): array {

		return [
			'timeout' => 120,
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
				'Accept'        => 'application/json',
				'Connection'    => 'keep-alive',
				'Content-Type'  => 'application/json',
			],
		];
	}
}
