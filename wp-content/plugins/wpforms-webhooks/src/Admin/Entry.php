<?php

namespace WPFormsWebhooks\Admin;

use WPForms\Admin\Notice;
use WPForms_Entries_Single;
use WPFormsWebhooks\Process;

/**
 * Entry management class for Webhooks resend functionality.
 *
 * @since 1.6.0
 */
class Entry {

	/**
	 * Meta-key for storing failed webhook attempts.
	 *
	 * @since 1.6.0
	 */
	public const FAILED_WEBHOOKS_META = 'webhooks_failed';

	/**
	 * Meta-key for resending failed webhook attempts.
	 *
	 * @since 1.6.0
	 */
	public const RESEND_FAILED_WEBHOOKS_META = 'webhooks_failed_resend';

	/**
	 * Initialize.
	 *
	 * @since 1.6.0
	 */
	public function init(): void {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.6.0
	 */
	private function hooks(): void {

		add_action( 'wpforms_entry_details_init', [ $this, 'maybe_display_resend_notice' ] );
		add_filter( 'wpforms_entry_details_sidebar_actions_link', [ $this, 'add_resend_action' ], 10, 3 );
	}

	/**
	 * Add resend action to the entry sidebar.
	 *
	 * @since 1.6.0
	 *
	 * @param array  $actions   Current sidebar actions.
	 * @param object $entry     Entry object.
	 * @param array  $form_data Form data.
	 *
	 * @return array Modified actions array.
	 */
	public function add_resend_action( $actions, object $entry, array $form_data ): array {

		$actions = (array) $actions;

		// Return early if webhooks are not enabled or no failed webhooks exist.
		if ( ! $this->has_failed_webhooks( $form_data, $entry ) ) {
			return $actions;
		}

		$log_status           = (bool) wpforms_setting( 'logs-enable' );
		$description_template = $log_status
			/* translators: %s - URL to the WPForms Logs page. */
			? __( 'Check your <a href="%s">Logs</a> to see why webhooks are failing for this entry.', 'wpforms-webhooks' )
			/* translators: %s - URL to the WPForms Logs page. */
			: __( 'Enable <a href="%s">Logs</a> to see why webhooks are failing for this entry.', 'wpforms-webhooks' );

		$description = sprintf(
			wp_kses(
				$description_template,
				[
					'a' => [
						'href' => [],
					],
				]
			),
			esc_url( admin_url( 'admin.php?page=wpforms-tools&view=logs' ) )
		);

		return wpforms_array_insert(
			$actions,
			[
				'webhooks_resend' => [
					'url'         => $this->get_resend_url( $entry->entry_id ),
					'label'       => esc_html__( 'Resend Failed Webhooks', 'wpforms-webhooks' ),
					'icon'        => 'dashicons-external',
					'description' => $description,
				],
			],
			'print',
			'before'
		);
	}

	/**
	 * Check if there are failed webhooks for this entry.
	 *
	 * @since 1.6.0
	 *
	 * @param array  $form_data Form data.
	 * @param object $entry     Entry object.
	 *
	 * @return bool True if failed webhooks exist, false otherwise.
	 */
	private function has_failed_webhooks( array $form_data, object $entry ): bool {

		// Check if webhooks are enabled for this form.
		if (
			empty( $form_data['settings']['webhooks'] ) ||
			! isset( $form_data['settings']['webhooks_enable'] ) ||
			! wp_validate_boolean( $form_data['settings']['webhooks_enable'] )
		) {
			return false;
		}

		$entry_id   = (int) $entry->entry_id;
		$entry_meta = wpforms()->obj( 'entry_meta' );

		if ( ! $entry_meta ) {
			return false;
		}

		// Get failed webhooks from entry_meta.
		$failed_meta = $entry_meta->get_meta(
			[
				'entry_id' => $entry_id,
				'type'     => self::FAILED_WEBHOOKS_META,
				'number'   => 1,
			]
		);

		if ( empty( $failed_meta ) || empty( $failed_meta[0]->data ) ) {
			return false;
		}

		$failed_webhooks = json_decode( $failed_meta[0]->data, true );

		if ( empty( $failed_webhooks ) || ! is_array( $failed_webhooks ) ) {
			return false;
		}

		// Get current webhook IDs from form settings.
		$current_webhook_ids = array_column( $form_data['settings']['webhooks'], 'id' );

		// Check if any failed webhook IDs still exist in the current form's webhooks.
		foreach ( $failed_webhooks as $failed_webhook_id ) {
			if ( in_array( $failed_webhook_id, $current_webhook_ids, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate the resend URL for a specific entry.
	 *
	 * @since 1.6.0
	 *
	 * @param int $entry_id The ID of the entry for which to generate the resend URL.
	 *
	 * @return string The generated resend URL.
	 */
	private function get_resend_url( int $entry_id ): string {

		return add_query_arg(
			[
				Process::RESEND_ACTION => '1',
				'_wpnonce'             => wp_create_nonce( Process::RESEND_ACTION ),
			],
			$this->get_url( $entry_id )
		);
	}

	/**
	 * Generate a URL for viewing entry details.
	 *
	 * @since 1.6.0
	 *
	 * @param int $entry_id Entry ID.
	 *
	 * @return string The generated URL.
	 */
	private function get_url( int $entry_id ): string {

		return add_query_arg(
			[
				'page'     => 'wpforms-entries',
				'view'     => 'details',
				'entry_id' => $entry_id,
			],
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Display resend notice after processing.
	 *
	 * @since 1.6.0
	 *
	 * @param WPForms_Entries_Single $entry_single Entry single instance.
	 */
	public function maybe_display_resend_notice( WPForms_Entries_Single $entry_single ): void {

		$entry_meta = wpforms()->obj( 'entry_meta' );

		if ( ! $entry_meta ) {
			return;
		}

		$existing_record = $entry_meta->get_meta(
			[
				'entry_id' => $entry_single->entry->entry_id,
				'type'     => self::RESEND_FAILED_WEBHOOKS_META,
				'number'   => 1,
			]
		);

		if ( empty( $existing_record ) || empty( $existing_record[0] ) ) {
			return;
		}

		$resend_data = json_decode( $existing_record[0]->data, true );

		// Delete the temporary meta record.
		$entry_meta->delete( $existing_record[0]->id );

		if ( empty( $resend_data['count'] ) ) {
			return;
		}

		$count = (int) $resend_data['count'];

		Notice::success(
			sprintf(
				/* translators: %d - number of webhooks. */
				_n( '%d webhook was resent.', '%d webhooks were resent.', $count, 'wpforms-webhooks' ),
				$count
			)
		);
	}
}
