<?php

namespace WPFormsSlack\Api;

use WPFormsSlack\Plugin;

/**
 * Class Authorization.
 *
 * @since 1.0.0
 */
class Auth {

	/**
	 * WPForms website URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const WPFORMS_URL = 'https://wpforms.com';

	/**
	 * Slack connect endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLACK_CONNECT_ENDPOINT = '/oauth/slack-connect';

	/**
	 * Nonce action for the OAuth flow.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wpforms_slack_oauth';

	/**
	 * Auth constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Register all hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'process_auth' ] );
	}

	/**
	 * Get authorization url to begin OAuth flow.
	 * If account_id is provided, it will be used to update the account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return string
	 */
	public static function get_authorization_url( string $account_id = '' ): string {

		$data = [
			'redirect_uri' => wpforms_current_url(),
		];

		// If account_id is provided, this is for cases when we update the refresh token.
		if ( ! empty( $account_id ) ) {
			$data['account_id'] = $account_id;
		}

		// Anti-CSRF nonce round-tripped through the state payload and verified in the OAuth callback.
		$data['nonce'] = wp_create_nonce( self::NONCE_ACTION );

		return add_query_arg(
			[
				'action' => 'init',
				'state'  => rawurlencode( base64_encode( wp_json_encode( $data ) ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			],
			self::get_redirect_uri()
		);
	}

	/**
	 * Get redirect URI.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_redirect_uri(): string {

		if ( defined( 'WPFORMS_SLACK_TEST_CONNECT_SERVER' ) && WPFORMS_SLACK_TEST_CONNECT_SERVER ) {
			return WPFORMS_SLACK_TEST_CONNECT_SERVER . self::SLACK_CONNECT_ENDPOINT;
		}

		return self::WPFORMS_URL . self::SLACK_CONNECT_ENDPOINT;
	}

	/**
	 * Process authorization.
	 *
	 * @since 1.0.0
	 */
	public function process_auth() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! wpforms_current_user_can( 'create_forms' ) || wp_doing_ajax() ) {
			return;
		}

		// Check all required params.
		if ( ! isset( $_GET['slack_connect'], $_GET['state'] ) ) {
			return;
		}

		// We're using base64_decode here to safely decode the state parameter from the Slack OAuth flow.
		// This is required to process the OAuth request and is not used for obfuscation.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode, WordPress.Security.NonceVerification.Recommended
		$state_data   = json_decode( base64_decode( sanitize_text_field( wp_unslash( $_GET['state'] ) ) ), true );
		$redirect_uri = isset( $state_data['redirect_uri'] ) ? esc_url_raw( urldecode( $state_data['redirect_uri'] ) ) : '';

		if ( ! is_array( $state_data ) ) {
			return;
		}

		// Verify the anti-CSRF nonce carried in the state payload before persisting any account.
		if ( empty( $state_data['nonce'] ) || ! wp_verify_nonce( $state_data['nonce'], self::NONCE_ACTION ) ) {
			return;
		}

		// In case if user pressed Cancel on authorization tab.
		if ( isset( $_GET['error_message'] ) ) {

			// Redirect to the original URI, no account creation.
			if ( ! empty( $redirect_uri ) ) {
				wp_safe_redirect( $redirect_uri );
				exit;
			}
		}

		if ( ! isset( $_GET['access_token'], $_GET['authed_user_id'], $_GET['user_token'] ) ) {
			return;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$account_id = $state_data['account_id'] ?? '';

		// Prepare the data to store in the account.
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$data = [
			'access_token'   => $_GET['access_token'],
			'user_token'     => $_GET['user_token'],
			'authed_user_id' => $_GET['authed_user_id'],
			'token__invalid' => false,
		];
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		// Update or add account information based on the presence of account ID.
		$account = $this->update_account( $account_id, $data );

		// If the connection is valid, set the account label.
		$this->set_account_label( $account, sanitize_text_field( wp_unslash( $_GET['authed_user_id'] ) ) );

		// Redirect to the original URI, ensuring safe URL redirection.
		if ( ! empty( $redirect_uri ) ) {
			wp_safe_redirect( $redirect_uri );
			exit;
		}
	}

	/**
	 * Update or add an account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 * @param array  $data       Account data.
	 *
	 * @return array
	 */
	private function update_account( string $account_id, array $data ): array {

		$account = ! empty( $account_id ) ? wpforms_slack()->get( 'account' )->get( $account_id ) : null;

		if ( ! empty( $account ) ) {
			return wpforms_slack()->get( 'account' )->update( $account_id, $data );
		}

		return wpforms_slack()->get( 'account' )->add( $data );
	}

	/**
	 * Set the account label.
	 * Helper method as the label is not returned in the initial auth request.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $account        Account data.
	 * @param string $authed_user_id Authed user ID.
	 */
	private function set_account_label( array $account, string $authed_user_id ) {

		$connection = ! empty( $account['key'] ) ? wpforms_slack()->get( 'client' )->new_connection( $account['key'] ) : null;

		if ( $connection !== null ) {
			$account['label'] = $connection->get_team_name( $authed_user_id );

			wpforms_update_providers_options( Plugin::SLUG, $account, $account['key'] );
		}
	}
}
