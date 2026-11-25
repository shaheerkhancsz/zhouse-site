<?php

namespace WPFormsSlack\Api\Http;

/**
 * Wrapper class to parse responses.
 *
 * @since 1.0.0
 */
class Response {

	/**
	 * Input data.
	 *
	 * @since 1.0.0
	 *
	 * @var array|\WP_Error
	 */
	private $input;

	/**
	 * Response constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array|\WP_Error $input The response data.
	 */
	public function __construct( $input ) {

		$this->input = $input;
	}

	/**
	 * Retrieve only the response code from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return int The response code as an integer.
	 */
	public function get_response_code(): int {

		return absint( wp_remote_retrieve_response_code( $this->input ) );
	}

	/**
	 * Retrieve only the response message from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return string The response message.
	 */
	public function get_response_message(): string {

		$body = $this->get_body();

		if ( ! empty( $body['message'] ) ) {
			return $body['message'];
		}

		$message = wp_remote_retrieve_response_message( $this->input );

		return ! empty( $message ) ? $message : __( 'Response error', 'wpforms-slack' );
	}

	/**
	 * Retrieve only the body from the raw response.
	 *
	 * @since 1.0.0
	 *
	 * @return array The body of the response.
	 */
	public function get_body(): array {

		return (array) json_decode( wp_remote_retrieve_body( $this->input ), true );
	}

	/**
	 * Whether we received errors in the response.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if response has errors.
	 */
	public function has_errors(): bool {

		$code = $this->get_response_code();

		return $code < 200 || $code > 299;
	}
}
