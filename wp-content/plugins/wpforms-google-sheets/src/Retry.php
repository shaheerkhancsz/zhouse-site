<?php
namespace WPFormsGoogleSheets;

use WPForms\Admin\Notice;
use WPForms\Pro\Providers\RetryHandler;

/**
 * Google Sheets Retry Handler class.
 *
 * @since 2.6.0
 */
class Retry extends RetryHandler {

	/**
	 * Get the retry slug.
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	protected function get_slug(): string {

		return 'google_sheets';
	}

	/**
	 * Get the retry link label.
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	protected function get_label(): string {

		return esc_html__( 'Resend to Google Sheets', 'wpforms-google-sheets' );
	}

	/**
	 * Check if the provider is enabled for this form.
	 *
	 * @since 2.6.0
	 *
	 * @param array $form_data Form data.
	 *
	 * @return bool
	 */
	protected function is_enabled( array $form_data ): bool {

		// Check if Google Sheets are enabled for this form.
		return ! empty( $form_data['providers'][ Plugin::SLUG ] );
	}

	/**
	 * Check if there are failed connections that still exist in the form.
	 *
	 * @since 2.6.0
	 *
	 * @param int   $entry_id  Entry ID.
	 * @param array $form_data Form data.
	 *
	 * @return bool
	 */
	protected function has_failed_connections( int $entry_id, array $form_data ): bool {

		$failed_connections = $this->get_failed_items( $entry_id );

		if ( empty( $failed_connections ) ) {
			return false;
		}

		// Get current connection IDs from form settings.
		$current_connection_ids = array_column( $form_data['providers'][ Plugin::SLUG ], 'id' );

		// Check if any failed connection IDs still exist in the current form's settings.
		foreach ( $failed_connections as $failed_connection_id ) {
			if ( in_array( $failed_connection_id, $current_connection_ids, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Execute the actual retry logic.
	 *
	 * @since 2.6.0
	 *
	 * @param int $entry_id Entry ID.
	 *
	 * @return int Number of successfully resent items.
	 */
	protected function retry( int $entry_id ): int {

		$provider = wpforms_google_sheets()->get( 'provider' );

		if ( ! $provider ) {
			return 0;
		}

		$process = $provider->get_process();

		if ( ! $process || ! method_exists( $process, 'resend_failed_connections' ) ) {
			return 0;
		}

		return $process->resend_failed_connections( $entry_id );
	}

	/**
	 * Display success notice.
	 *
	 * @since 2.6.0
	 *
	 * @param int $count Number of resent items.
	 */
	protected function display_success_notice( int $count ): void {

		Notice::success(
			sprintf(
				/* translators: %d - number of connections. */
				_n( '%d connection was resent.', '%d connections were resent.', $count, 'wpforms-google-sheets' ),
				$count
			)
		);
	}
}
