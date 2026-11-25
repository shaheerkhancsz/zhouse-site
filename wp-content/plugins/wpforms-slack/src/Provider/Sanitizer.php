<?php

namespace WPFormsSlack\Provider;

use WPFormsSlack\Plugin;

/**
 * Class for sanitizing different data.
 *
 * @since 1.0.0
 */
class Sanitizer {

	/**
	 * Sanitize connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	public function connection( array $connection_data ): array {

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'duplicate' ) {
			return $connection_data;
		}

		$connection_data = wp_parse_args(
			$connection_data,
			[
				'id'         => '',
				'account_id' => '',
				'action'     => '',
			]
		);

		$connection_data['id']         = sanitize_text_field( $connection_data['id'] );
		$connection_data['account_id'] = sanitize_text_field( $connection_data['account_id'] );
		$connection_data['action']     = sanitize_text_field( $connection_data['action'] );

		if ( $connection_data['action'] === 'send_direct_message' ) {
			return $this->sanitize_direct_message( $connection_data );
		}

		if ( $connection_data['action'] === 'send_channel_message' ) {
			return $this->sanitize_channel_message( $connection_data );
		}

		if ( $connection_data['action'] === 'set_reminder' ) {
			return $this->sanitize_set_reminder( $connection_data );
		}

		return $connection_data;
	}

	/**
	 * Sanitize direct message.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_direct_message( array $connection_data ): array {

		$connection_data = wp_parse_args(
			$connection_data,
			[
				'users'   => [],
				'message' => '',
			]
		);

		$connection_data['users'] = $this->sanitize_array( $connection_data, 'users' );

		return $connection_data;
	}

	/**
	 * Sanitize channel message.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_channel_message( array $connection_data ): array {

		$connection_data = wp_parse_args(
			$connection_data,
			[
				'channels' => [],
				'message'  => '',
			]
		);

		$connection_data['channels'] = $this->sanitize_array( $connection_data, 'channels' );

		return $connection_data;
	}

	/**
	 * Sanitize set reminder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_set_reminder( array $connection_data ): array {

		$connection_data = wp_parse_args(
			$connection_data,
			[
				'time'    => '',
				'message' => '',
			]
		);

		$connection_data['time'] = $this->sanitize_time( $connection_data['time'] );

		return $connection_data;
	}

	/**
	 * When saving data in the builder, the fields with multiple values (e.g. users) will keep only the last value.
	 * For instance, the `providers[slack][123][users][]` with values `1`, `3` and `5` will be saved with the `5` value.
	 * That's why we use `$_POST` directly instead of `$connection_data` here.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $connection_data Connection data.
	 * @param string $key             Array key.
	 *
	 * @return array
	 */
	private function sanitize_array( array $connection_data, string $key ): array {

		$connection_id = $connection_data['id'];

		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$form_post = ! empty( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : [];

		return wpforms_chain( $form_post )
			->map(
				static function ( $post_pair ) use ( $connection_id, $key ) {
					$field_name = $key;

					$provider_slug = Plugin::SLUG;

					if (
						empty( $post_pair['name'] ) ||
						$post_pair['name'] !== "providers[$provider_slug][$connection_id][$field_name][]"
					) {
						return '';
					}

					return $post_pair['value'];
				}
			)
			->array_filter()
			->array_values()
			->value();
	}

	/**
	 * Sanitize time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $time Time.
	 *
	 * @return int|string
	 */
	private function sanitize_time( string $time ) {

		$time = sanitize_text_field( wp_unslash( $time ) );

		// Return the parsed time if it's valid and in the future.
		return is_numeric( $time ) ? (int) $time : $time;
	}
}
