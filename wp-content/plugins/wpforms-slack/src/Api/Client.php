<?php

namespace WPFormsSlack\Api;

use WPFormsSlack\Plugin;
use WPFormsSlack\Api\Http\Request;

/**
 * Class Client creates connections between addon and the Slack API.
 *
 * @since 1.0.0
 */
class Client {

	/**
	 * Create a new connection.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Account ID.
	 *
	 * @return Api|null
	 */
	public function new_connection( string $account_id ) {

		$accounts = wpforms_get_providers_options( Plugin::SLUG );

		if ( empty( $accounts[ $account_id ]['access_token'] ) ) {
			return null;
		}

		$access_token = $accounts[ $account_id ]['access_token'];
		$user_token   = $accounts[ $account_id ]['user_token'] ?? '';

		// Create a request instance with the token.
		$request = new Request( $access_token, $user_token );

		// Validate the token.
		if ( ! $this->validate_token( $request, $account_id ) ) {
			return null;
		}

		// If the token is valid, return the API connection.
		return new Api( $request );
	}

	/**
	 * Validate the API token using Slack's `auth.test` method.
	 *
	 * @since 1.0.0
	 *
	 * @param Request $request    The request object.
	 * @param string  $account_id Account ID.
	 *
	 * @return bool True if the token is valid, false otherwise.
	 */
	private function validate_token( Request $request, string $account_id ): bool {

		$response = $request->post( Api::API_OBJECTS_URL . 'auth.test' );
		$data     = $response->get_body();

		// If the token is invalid, handle the error.
		if ( ! isset( $data['ok'] ) || $data['ok'] === false ) {
			if ( ! empty( $data['error'] ) ) {
				wpforms_log(
					esc_html__( 'Slack authentication error', 'wpforms-slack' ),
					sprintf( /* translators: %s is the account ID. */
						esc_html__( 'Slack API token is invalid for account ID: %s', 'wpforms-slack' ),
						esc_html( $account_id )
					),
					[
						'type' => [
							'provider',
							'error',
						],
					]
				);

				// Set token as invalid.
				wpforms_slack()->get( 'account' )->update_token_status( $account_id, true );
				wpforms_slack()->get( 'account' )->update_token_status_error( $account_id, 'invalid_auth' );

				return false;
			}
		}

		return true;
	}
}
