<?php

namespace WPFormsSlack\Provider;

use RuntimeException;
use WPFormsSlack\Plugin;
use WPFormsSlack\Api\Auth;

/**
 * Class Account.
 *
 * @since 1.0.0
 */
class Account {

	/**
	 * Get all Slack accounts.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_all(): array {

		$options = wpforms_get_providers_options();

		return ! empty( $options[ Plugin::SLUG ] ) ? $options[ Plugin::SLUG ] : [];
	}

	/**
	 * Get a specific Slack account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return array
	 */
	public function get( string $account_id ): array {

		$accounts = $this->get_all();

		return $accounts[ $account_id ] ?? [];
	}

	/**
	 * Get an account ID by user token.
	 *
	 * @since 1.1.0
	 *
	 * @param string $user_token User token.
	 *
	 * @return string
	 */
	public function get_account_id_by_user_token( string $user_token ): string {

		$accounts = $this->get_all();

		foreach ( $accounts as $id => $account ) {
			if ( $account['user_token'] === $user_token ) {
				return $id;
			}
		}

		return '';
	}

	/**
	 * Save a new account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Access token.
	 *
	 * @return array
	 *
	 * @throws RuntimeException Invalid key or account has already existed.
	 */
	public function add( array $data ): array {

		if ( ! isset( $data['access_token'], $data['user_token'] ) ) {
			throw new RuntimeException(
				esc_html__( 'Access token and user token are required.', 'wpforms-slack' ),
				400
			);
		}

		$access_token   = sanitize_text_field( wp_unslash( $data['access_token'] ) );
		$user_token     = sanitize_text_field( wp_unslash( $data['user_token'] ) );
		$authed_user_id = sanitize_text_field( wp_unslash( $data['authed_user_id'] ) );

		try {
			// Check if the account already exists.
			if ( $this->exists( $access_token, $user_token ) ) {
				throw new RuntimeException(
					esc_html__( 'A Slack account with these credentials has already been added. Please try connecting a different account or verify the existing account details.', 'wpforms-slack' ),
					400
				);
			}

			// Generate a unique key.
			$key = uniqid( '', true );

			// Prepare the options for saving.
			$options = [
				'access_token'   => $access_token,
				'user_token'     => $user_token,
				'authed_user_id' => $authed_user_id,
				'token__invalid' => false,
				'date'           => time(),
			];

			wpforms_update_providers_options( Plugin::SLUG, $options, $key );

			$options['key'] = $key;

			return $options;

		} catch ( RuntimeException $e ) {
			set_transient( 'wpforms_slack_error_message', $e->getMessage(), 30 ); // Transient lasts for 30 seconds.

			return [];
		}
	}

	/**
	 * Update an account.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 * @param array  $data       Data to update.
	 *
	 * @return array
	 */
	public function update( string $account_id, array $data ): array {

		$account = $this->get( $account_id );

		if ( empty( $account ) ) {
			return [];
		}

		if ( ! isset( $data['access_token'], $data['user_token'], $data['authed_user_id'] ) ) {
			return [];
		}

		// Case when a user tries to refresh a token for another account.
		if ( $account['authed_user_id'] !== $data['authed_user_id'] ) {
			$error = esc_html__( 'A Slack account with these credentials has already been added. Please try connecting a different account or verify the existing account details.', 'wpforms-slack' );

			set_transient( 'wpforms_slack_error_message', $error, 30 ); // Transient lasts for 30 seconds.

			return [];
		}

		$account = array_merge( $account, array_map( 'sanitize_text_field', array_map( 'wp_unslash', $data ) ) );

		wpforms_update_providers_options( Plugin::SLUG, $account, $account_id );

		return $account;
	}

	/**
	 * Check if account with these tokens already exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $access_token Access token.
	 * @param string $user_token   User token.
	 *
	 * @return bool
	 */
	private function exists( string $access_token, string $user_token ): bool {

		$options = wpforms_get_providers_options( Plugin::SLUG );
		$bot     = array_column( $options, 'access_token' );
		$user    = array_column( $options, 'user_token' );

		return in_array( $access_token, $bot, true ) || in_array( $user_token, $user, true );
	}

	/**
	 * Get expired accounts.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_invalid_accounts(): array {

		$expired = [];

		foreach ( $this->get_all() as $key => $account ) {

			if ( empty( $account['token__invalid'] ) ) {
				continue;
			}

			$expired[ $key ] = [
				'label'         => $account['label'] ?? '',
				'account_error' => $account['account_error'] ?? '',
			];
		}

		return $expired;
	}

	/**
	 * Update the token status.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $account_id Account ID.
	 * @param boolean $status     Is token invalid.
	 */
	public function update_token_status( $account_id, $status ) {

		$account = wpforms_slack()->get( 'account' )->get( $account_id );

		// Skip the same statuses.
		if ( (bool) $account['token__invalid'] === (bool) $status ) {
			return;
		}

		$account['token__invalid'] = (bool) $status;

		$this->update( $account_id, $account );
	}

	/**
	 * Update the token status error code.
	 *
	 * @since 1.1.0
	 *
	 * @param string $account_id Account ID.
	 * @param string $error      Error message.
	 */
	public function update_token_status_error( string $account_id, string $error ): void {

		$account = wpforms_slack()->get( 'account' )->get( $account_id );

		if ( empty( $account ) ) {
			return;
		}

		$account['account_error'] = $error;

		$this->update( $account_id, $account );
	}

	/**
	 * Get the notice message for an expired account.
	 *
	 * @since 1.1.0
	 *
	 * @param string $account_id Account ID.
	 * @param array  $account    Account data.
	 *
	 * @return string
	 */
	public function get_expired_account_notice_message( string $account_id, array $account ): string {

		return sprintf(
			wp_kses( /* translators: %1$s - Account label. %2$s - reauthorization URL. */
				__( 'Heads up! The Slack account %1$s connection is expired. Tokens must be refreshed. <a href="%2$s" target="_blank" rel="noopener noreferrer">Refresh</a>', 'wpforms-slack' ),
				[
					'a' => [
						'rel'    => [],
						'href'   => [],
						'target' => [],
					],
				]
			),
			'<strong>' . esc_html( $account['label'] ) . '</strong>',
			esc_url( Auth::get_authorization_url( $account_id ) )
		);
	}

	/**
	 * Get the notice message for an account missing required scopes.
	 *
	 * @since 1.1.0
	 *
	 * @param string $account_id Account ID.
	 * @param array  $account    Account data.
	 *
	 * @return string
	 */
	public function get_missing_scope_account_notice_message( string $account_id, array $account ): string {

		return sprintf(
			wp_kses( /* translators: %1$s - Account label. %2$s - reauthorization URL. */
				__( 'The Slack account %1$s connection is missing required scopes. <a href="%2$s" target="_blank" rel="noopener noreferrer">Reauthorize</a>', 'wpforms-slack' ),
				[
					'a' => [
						'rel'    => [],
						'href'   => [],
						'target' => [],
					],
				]
			),
			'<strong>' . esc_html( $account['label'] ) . '</strong>',
			esc_url( Auth::get_authorization_url( $account_id ) )
		);
	}
}
