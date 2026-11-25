<?php

namespace WPFormsSlack\Api\Http;

/**
 * Wrapper class for HTTP requests.
 *
 * @since 1.0.0
 */
class Request {

	/**
	 * Access token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * User token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $user_token;

	/**
	 * Request constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $access_token Access token.
	 * @param string $user_token   User token.
	 */
	public function __construct( string $access_token, string $user_token ) {

		$this->access_token = $access_token;
		$this->user_token   = $user_token;
	}

	/**
	 * Send a GET request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Arguments for the request.
	 *
	 * @return Response
	 */
	public function get( string $url, array $args = [] ): Response {

		$url = add_query_arg( $args, $url );

		return $this->request( 'GET', $url, [] );
	}

	/**
	 * Send a POST request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Arguments for the request.
	 *
	 * @return Response
	 */
	public function post( string $url, array $args = [] ): Response {

		$args = ! empty( $args ) ? [ 'body' => $args ] : [];

		return $this->request( 'POST', $url, $args );
	}

	/**
	 * Send a request based on method (main interface).
	 *
	 * @since 1.0.0
	 *
	 * @param string $method Request method.
	 * @param string $url    Request URL.
	 * @param array  $args   Options for the request.
	 *
	 * @return Response
	 */
	public function request( string $method, string $url, array $args ): Response {

		// Get default parameters (without allowing them to be overwritten).
		$options = $this->get_default_parameters();

		$options['method'] = $method;

		// Merge only the body from $args if it exists, but do not allow overwriting default parameters.
		if ( isset( $args['body'] ) ) {
			$options['body'] = wp_json_encode( $args['body'] ); // Always encode the body as JSON.
		}

		// Check if the request is for reminders.add and set specific Authorization.
		if ( strpos( $url, 'reminders.add' ) !== false ) {
			$options['headers']['Authorization'] = 'Bearer ' . $this->user_token; // Use specific user token for reminders.add.
		}

		/**
		 * Filter a request options array before it's sent.
		 *
		 * @since 1.0.0
		 *
		 * @param array   $options  Request options.
		 * @param string  $method   Request method.
		 * @param string  $url      Request URL.
		 * @param Request $instance Instance of Request class.
		 */
		$options = (array) apply_filters( 'wpforms_slack_api_http_request_options', $options, $method, $url, $this );

		// Retrieve the raw response from a safe HTTP request.
		return new Response( wp_safe_remote_request( $url, $options ) );
	}

	/**
	 * Retrieve the access token.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_user_token(): string {

		return $this->user_token ?? '';
	}

	/**
	 * Retrieve default parameters for request.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_default_parameters(): array {

		/**
		 * Filter the request timeout.
		 *
		 * @since 1.0.0
		 *
		 * @param int $timeout Request timeout.
		 */
		$timeout = (int) apply_filters( 'wpforms_slack_api_http_request_timeout', 15 );

		return [
			'method'  => 'GET',
			'timeout' => $timeout,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->access_token,
				'Accept'        => 'application/json',
				'Connection'    => 'keep-alive',
				'Content-Type'  => 'application/json; charset=utf-8',
			],
		];
	}
}
