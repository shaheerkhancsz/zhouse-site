<?php

namespace WPFormsSlack\Api;

use WPFormsSlack\Api\Http\Request;
use WPFormsSlack\Api\Http\Response;

/**
 * WPForms Slack API class.
 *
 * @since 1.0.0
 */
class Api {

	/**
	 * URL for the Slack Objects API.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const API_OBJECTS_URL = 'https://slack.com/api/';

	/**
	 * Request instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Request
	 */
	private $request;

	/**
	 * API constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Request $request Request.
	 */
	public function __construct( Request $request ) {

		$this->request = $request;
	}

	/**
	 * Send a message to a user or channel.
	 *
	 * @since 1.0.0
	 *
	 * @param string $recipient_id User or Channel ID.
	 * @param string $message      Message.
	 *
	 * @return Response
	 */
	public function send_message( string $recipient_id, string $message ): Response {

		$body = [
			'channel' => $recipient_id,
			'text'    => $message,
		];

		return $this->request->post( self::API_OBJECTS_URL . 'chat.postMessage', $body );
	}

	/**
	 * Set a reminder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The reminder text.
	 * @param string $time The time to set the reminder.
	 *
	 * @return Response
	 */
	public function set_reminder( string $text, string $time ): Response {

		$body = [
			'text' => $text,
			'time' => $time,
		];

		return $this->request->post( self::API_OBJECTS_URL . 'reminders.add', $body );
	}

	/**
	 * Get a list of users.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added pagination support.
	 *
	 * @return array
	 */
	public function get_users(): array {

		$users  = [];
		$cursor = '';

		do {
			$cursor = $this->get_next_users_cursor( $users, [ 'cursor' => $cursor ] );

		} while ( $cursor );

		return $users;
	}

	/**
	 * Update the user's array with the next page of users.
	 * Also returns the cursor for the next request.
	 *
	 * @since 1.1.0
	 *
	 * @param array $users The array to store the users.
	 * @param array $args  The arguments for the request.
	 *
	 * @return string
	 */
	private function get_next_users_cursor( array &$users, array $args ): string {

		// Make the API request.
		$response = $this->request->get( self::API_OBJECTS_URL . 'users.list', $args );

		if ( $response->has_errors() ) {
			return '';
		}

		$property_data = $response->get_body();

		if ( ! $property_data['ok'] ) {
			return '';
		}

		// Filter out bot and deleted/deactivated users.
		foreach ( $property_data['members'] as $member ) {
			if ( ! $member['is_bot'] && $member['id'] !== 'USLACKBOT' && ! $member['deleted'] ) {
				$users[ $member['id'] ] = $member['real_name'];
			}
		}

		// Return the cursor for the next request.
		return $property_data['response_metadata']['next_cursor'] ?? '';
	}

	/**
	 * Get a list of public channels.
	 *
	 * @since 1.1.0
	 *
	 * @param string $type The type of channels to fetch: 'public_channel' or 'private_channel'.
	 *
	 * @return array
	 */
	private function get_channels_by_type( string $type ): array {

		$channels = [];
		$cursor   = '';

		$allowed_types = [ 'public_channel', 'private_channel' ];

		if ( ! in_array( $type, $allowed_types, true ) ) {
			return $channels; // Return an empty array if the type is invalid.
		}

		do {
			$cursor = $this->get_next_channels_cursor( $channels, [ 'cursor' => $cursor ], $type );
		} while ( $cursor );

		return $channels;
	}

	/**
	 * Fetch the next page of channels of the given type and update the channels array.
	 *
	 * @since 1.1.0
	 *
	 * @param array  $channels The array to store channel id => name pairs.
	 * @param array  $args     The arguments for the API request.
	 * @param string $type     The type of channel: 'public_channel' or 'private_channel'.
	 *
	 * @return string
	 */
	private function get_next_channels_cursor( array &$channels, array $args, string $type ): string {

		$args['types']            = $type;
		$args['limit']            = 1000; // Slack max per page.
		$args['exclude_archived'] = true; // Do not include archived channels.

		return $this->fetch_channels( $args, $channels );
	}

	/**
	 * Fetch channels from Slack API and update the channels array.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args     The arguments for the API request.
	 * @param array $channels The array to store channel id => name pairs.
	 *
	 * @return string
	 */
	private function fetch_channels( array $args, array &$channels ): string {
		// Make the API request.
		$response = $this->request->get( self::API_OBJECTS_URL . 'conversations.list', $args );

		if ( $response->has_errors() ) {
			return '';
		}

		$property_data = $response->get_body();

		if ( ! $this->is_body_valid( $property_data ) ) {
			return '';
		}

		if ( empty( $property_data['channels'] ) ) {
			return '';
		}

		// Merge this page of channels.
		foreach ( $property_data['channels'] as $channel ) {
			$channels[ $channel['id'] ] = [
				'name'       => $channel['name'] ?? '',
				'is_private' => ( $channel['is_private'] ?? false ),
			];
		}

		// Return the cursor for the next request.
		return $property_data['response_metadata']['next_cursor'] ?? '';
	}

	/**
	 * Get a list of channels.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added pagination support.
	 * @since 1.1.0 Added private channels support.
	 *
	 * @return array
	 */
	public function get_channels(): array {

		$public_channels  = $this->get_channels_by_type( 'public_channel' );
		$private_channels = $this->get_channels_by_type( 'private_channel' );

		return array_merge( $public_channels, $private_channels );
	}

	/**
	 * Check if the response body is valid.
	 *
	 * @since 1.1.0
	 *
	 * @param array $body Response body.
	 *
	 * @return bool
	 */
	private function is_body_valid( array $body ): bool {

		if ( ! empty( $body['ok'] ) && (bool) $body['ok'] === true ) {
			return true;
		}

		$error = $body['error'] ?? '';

		// Handle missing scope error.
		if ( $error === 'missing_scope' ) {

			$user_token = $this->request->get_user_token();

			$account_id = wpforms_slack()->get( 'account' )->get_account_id_by_user_token( $user_token );

			if ( ! empty( $account_id ) ) {
				wpforms_slack()->get( 'account' )->update_token_status( $account_id, true );
				wpforms_slack()->get( 'account' )->update_token_status_error( $account_id, $error );
			}

			return false;
		}

		return true;
	}

	/**
	 * Get team name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $team_id The user ID.
	 *
	 * @return string
	 */
	public function get_team_name( string $team_id ): string {

		$response = $this->request->get( self::API_OBJECTS_URL . 'team.info', [ 'user' => $team_id ] );

		if ( $response->has_errors() ) {
			return '';
		}

		$property_data = $response->get_body();

		if ( ! $property_data['ok'] ) {
			return '';
		}

		return $property_data['team']['name'];
	}
}
