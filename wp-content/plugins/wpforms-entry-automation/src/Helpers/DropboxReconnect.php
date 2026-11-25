<?php

namespace WPFormsEntryAutomation\Helpers;

use WPFormsDropbox\Plugin as DropboxPlugin;
use WPFormsEntryAutomation\Plugin;

/**
 * Handles the reconnection process for Dropbox accounts in WPForms.
 *
 * This class is responsible for managing Dropbox account reconnections by storing
 * temporary data, handling AJAX requests, updating provider options, and replacing
 * account IDs as needed.
 *
 * @since 1.0.0
 */
class DropboxReconnect {

	/**
	 * Transient name used for storing temporary reconnection data.
	 *
	 * @since 1.0.0
	 */
	private const TRANSIENT_RECONNECT = 'wpforms_entry_automation_dropbox_reconnect';

	/**
	 * Account ID that needs to be replaced in provider options.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $account_id_for_replacing = '';

	/**
	 * Account ID that will replace the old account ID in provider options.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $account_id_replacement = '';

	/**
	 * Registers hooks for the Dropbox provider.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		$slug = Plugin::SLUG;

		add_filter( "wpforms_builder_ajax_{$slug}_dropbox_reconnect", [ $this, 'ajax_dropbox_reconnect' ] );
		add_filter( "wpforms_builder_ajax_{$slug}_dropbox_load_accounts_scopes", [ $this, 'ajax_get_accounts_with_scopes' ] );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset(
			$_GET['dropbox_connect'],
			$_GET['state'],
			$_GET['account_id'],
			$_GET['access_token'],
			$_GET['refresh_token'],
			$_GET['expires_in']
		) ) {
			return;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Reconnect a Dropbox account during the auth process.
		// See \WPFormsDropbox\Api\Auth::process_auth.
		add_action( 'admin_init', [ $this, 'reconnect_account' ], 0 );
		add_action( 'admin_init', [ $this, 'replace_account_id' ], 11 );
		add_filter( 'wpforms_update_providers_options', [ $this, 'update_scope_for_account' ], 10, 4 );
	}

	/**
	 * 1. Check whether the account_id in GET params equals to stored account_id.
	 * 2. If yes, remove the account from the provider options.
	 *     2.1 Handle saving providers options.
	 *     2.2 Set the account ID for replacing.
	 *     2.3 In the replace_account_id method do the replacement.
	 *     2.4 Clear the transient.
	 * 3. If no, do nothing.
	 *
	 * This method is called during the auth process.
	 * It is used to reconnect the account with new scopes.
	 *
	 * @since 1.0.0
	 */
	public function reconnect_account(): void { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$reconnect = get_transient( self::TRANSIENT_RECONNECT );
		$reconnect = ! empty( $reconnect ) ? json_decode( $reconnect, true ) : [];

		if ( ! $reconnect ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$account_id = ! empty( $_GET['account_id'] ) ? sanitize_text_field( wp_unslash( $_GET['account_id'] ) ) : '';

		if ( $account_id !== $reconnect['account_id'] ) {
			return;
		}

		$this->account_id_replacement = $reconnect['key'];

		// Clear the current account.
		wpforms_dropbox()->get( 'account' )->remove( $reconnect['key'] );
		// Handle saving providers options.
		add_filter( 'wpforms_update_providers_options', [ $this, 'save_account_id_for_replacing' ], 10, 4 );
	}

	/**
	 * Saves the account ID for replacing and modifies provider options if applicable.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $providers The array of provider configurations.
	 * @param string $provider  The slug of the current provider being processed.
	 * @param array  $options   The options for the current provider.
	 * @param string $id        The ID to be saved for replacing.
	 *
	 * @return array
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function save_account_id_for_replacing( $providers, $provider, $options, $id ): array { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( $provider !== DropboxPlugin::SLUG && ! empty( $options ) ) {
			return (array) $providers;
		}

		// We can't replace the new account ID here since it should be used to finish the native Dropbox addon OAuth process.
		// Instead, we save the account ID for replacing and modify the provider options later.
		$this->account_id_for_replacing = $id;

		remove_filter( 'wpforms_update_providers_options', [ $this, 'save_account_id_for_replacing' ] );

		return (array) $providers;
	}

	/**
	 * Replaces the account ID being used with a new account ID in the provider options.
	 *
	 * @since 1.0.0
	 */
	public function replace_account_id(): void {

		// Early exit if no account ID needs replacing.
		if ( ! $this->account_id_for_replacing ) {
			return;
		}

		$providers = wpforms_get_providers_options();
		$provider  =& $providers[ DropboxPlugin::SLUG ];
		// Get the account details for the account being replaced.
		$account = $provider[ $this->account_id_for_replacing ];
		// Set the new account ID.
		$account['key'] = $this->account_id_replacement;

		// Remove old account ID.
		unset( $provider[ $this->account_id_for_replacing ] );

		// Add an account with a new ID.
		$provider[ $this->account_id_replacement ] = $account;

		update_option( 'wpforms_providers', $providers );
		delete_transient( self::TRANSIENT_RECONNECT );
	}

	/**
	 * Update the scope for all new Dropbox accounts.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $providers The list of providers.
	 * @param string $provider  The current provider slug.
	 * @param mixed  $options   Additional options, if any.
	 * @param string $id        The account ID to update.
	 *
	 * @return array Updated list of providers.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function update_scope_for_account( $providers, $provider, $options, $id ): array {

		$providers = (array) $providers;

		if ( $provider !== DropboxPlugin::SLUG && ! empty( $options ) ) {
			return $providers;
		}

		$provider =& $providers[ DropboxPlugin::SLUG ];

		// Save the current timestamp for tracking scope updates.
		$provider[ $id ]['updated_scopes'] = time();

		return $providers;
	}

	/**
	 * Handle AJAX request to reconnect a Dropbox account with new scopes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function ajax_dropbox_reconnect(): array {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$account_id = ! empty( $_POST['accountId'] ) ? sanitize_text_field( wp_unslash( $_POST['accountId'] ) ) : '';

		if ( empty( $account_id ) ) {
			return [
				'error_msg' => esc_html__( 'Account ID is required.', 'wpforms-entry-automation' ),
			];
		}

		$account = wpforms_dropbox()->get( 'account' )->get( $account_id );

		set_transient(
			self::TRANSIENT_RECONNECT,
			wp_json_encode(
				[
					'account_id' => $account['account_id'],
					'key'        => $account['key'],
				]
			),
			HOUR_IN_SECONDS * 3
		);

		return [];
	}

	/**
	 * Retrieves a list of accounts along with their updated scopes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Returns an array containing the accounts and their updated scopes.
	 */
	public function ajax_get_accounts_with_scopes(): array {

		$accounts          = wpforms_get_providers_options( DropboxPlugin::SLUG );
		$prepared_accounts = array_map(
			static function ( $account ) {

				return [
					'updated_scopes' => (bool) ( $account['updated_scopes'] ?? 0 ),
				];
			},
			$accounts
		);

		return [
			'accounts' => $prepared_accounts,
		];
	}
}
