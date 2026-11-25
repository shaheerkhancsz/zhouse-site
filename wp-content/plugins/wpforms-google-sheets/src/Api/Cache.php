<?php

namespace WPFormsGoogleSheets\Api;

use WPForms\Helpers\Transient;

/**
 * Cache Google Sheets request results.
 *
 * @since 1.0.0
 * @deprecated 2.2.0
 */
class Cache {

	/**
	 * Prepare cache key.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args List of arguments.
	 *
	 * @return string
	 */
	private function key( $args ) {

		return 'google_sheets_' . hash( 'adler32', implode( '_', $args ) );
	}

	/**
	 * Get cached data.
	 *
	 * @since 1.0.0
	 * @deprecated 2.2.0
	 *
	 * @param string ...$args List of arguments.
	 *
	 * @return array
	 */
	public function get( ...$args ) {

		_deprecated_function( __METHOD__, '2.2.0 of the WPForms Google Sheets addon' );

		$data = Transient::get( $this->key( $args ) );

		return is_array( $data ) ? $data : [];
	}

	/**
	 * To cache data.
	 *
	 * @since 1.0.0
	 * @deprecated 2.2.0
	 *
	 * @param array  $value   Data for caching.
	 * @param int    $minutes Expiration of cache in minutes.
	 * @param string ...$args List of arguments.
	 */
	public function set( $value, $minutes, ...$args ) {

		_deprecated_function( __METHOD__, '2.2.0 of the WPForms Google Sheets addon' );

		if ( empty( $value ) || ! is_array( $value ) ) {
			return;
		}

		Transient::set(
			$this->key( $args ),
			$value,
			$minutes * MINUTE_IN_SECONDS
		);
	}

	/**
	 * Delete cache.
	 *
	 * @since 1.0.0
	 * @deprecated 2.2.0
	 *
	 * @param string ...$args List of arguments.
	 */
	public function delete( ...$args ) {

		_deprecated_function( __METHOD__, '2.2.0 of the WPForms Google Sheets addon' );

		Transient::delete( $this->key( $args ) );
	}

	/**
	 * Drop all cache.
	 *
	 * @since 1.0.0
	 * @deprecated 2.2.0
	 */
	public function delete_all() {

		_deprecated_function( __METHOD__, '2.2.0 of the WPForms Google Sheets addon' );

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options
				WHERE option_name LIKE %s OR option_name LIKE %s",
				'_wpforms_transient_google_sheets_%',
				'_wpforms_transient_timeout_google_sheets_%'
			)
		);
	}
}
