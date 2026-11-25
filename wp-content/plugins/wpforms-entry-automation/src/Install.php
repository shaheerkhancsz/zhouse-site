<?php

namespace WPFormsEntryAutomation;

use WP_Site;

/**
 * Addon install.
 *
 * @since 1.0.0
 */
class Install {

	/**
	 * Add hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		register_activation_hook( WPFORMS_ENTRY_AUTOMATION_FILE, [ $this, 'install' ] );

		register_deactivation_hook( WPFORMS_ENTRY_AUTOMATION_FILE, [ $this, 'uninstall' ] );

		add_action( 'wp_initialize_site', [ $this, 'new_multisite_blog' ], 10, 2 );
	}

	/**
	 * Perform certain actions on plugin activation.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|mixed $network_wide Whether to enable the plugin for all sites in the network or just the current site.
	 *                                 Multisite only.
	 *                                 Default is false.
	 *
	 * @noinspection DisconnectedForeachInstructionInspection
	 */
	public function install( $network_wide = false ): void {

		// Normal single site.
		if ( ! ( $network_wide && is_multisite() ) ) {
			$this->run();

			return;
		}

		// Multisite - go through each subsite and run the installer.
		$sites = get_sites(
			[
				'fields' => 'ids',
				'number' => 0,
			]
		);

		foreach ( $sites as $blog_id ) {
			switch_to_blog( $blog_id );
			$this->run();
			restore_current_blog();
		}
	}

	/**
	 * Perform certain actions on plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public function uninstall(): void {

		// Cancel all scheduled tasks.
		// Task::cancel() cannot be used here because the WPForms plugin could already be deactivated.
		if ( ! function_exists( 'as_unschedule_all_actions' ) ) {
			return;
		}

		as_unschedule_all_actions( 'wpforms_entry_automation_process_action_task' );
		as_unschedule_all_actions( 'wpforms_entry_automation_action_create_tasks' );
	}

	/**
	 * When a new site is created in multisite, see if we are network activated,
	 * and if so run the installer.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site $new_site New site object.
	 * @param array   $args     Arguments for the initialization.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function new_multisite_blog( $new_site, $args ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		if ( is_plugin_active_for_network( plugin_basename( WPFORMS_ENTRY_AUTOMATION_FILE ) ) ) {
			switch_to_blog( $new_site->blog_id );
			$this->run();
			restore_current_blog();
		}
	}

	/**
	 * Run the actual installer.
	 *
	 * @since 1.0.0
	 */
	private function run(): void {

		if ( ! $this->table_exist() ) {
			$this->create_table();
		}
	}

	/**
	 * Check if the table exists.
	 *
	 * @since 1.0.0
	 */
	private function table_exist(): bool {

		global $wpdb;

		$table_name = $wpdb->prefix . 'wpforms_entry_automation_tasks';

		$sql = "SHOW TABLES LIKE '$table_name'";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->get_var( $sql );

		return $result === $table_name;
	}

	/**
	 * Create the table.
	 *
	 * @since 1.0.0
	 */
	private function create_table(): void {

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;

		$table           = $wpdb->prefix . 'wpforms_entry_automation_tasks';
		$charset_collate = $wpdb->get_charset_collate();

		dbDelta(
			"CREATE TABLE $table (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				form_id BIGINT UNSIGNED NOT NULL,
				action_id BIGINT UNSIGNED DEFAULT NULL,
				connection_id VARCHAR(50) NOT NULL,
				connection_name VARCHAR(255) NOT NULL,
				type VARCHAR(50) DEFAULT NULL,
				export_to VARCHAR(50) DEFAULT NULL,
				destination LONGTEXT DEFAULT NULL,
				file VARCHAR(255) DEFAULT NULL,
				status VARCHAR(20) NOT NULL DEFAULT 'active',
				parent_id VARCHAR(50) DEFAULT NULL,
				schedule LONGTEXT DEFAULT NULL,
				last_run DATETIME DEFAULT NULL,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				PRIMARY KEY  (id),
				KEY form_id (form_id),
				KEY action_id (action_id),
				KEY type (type(32)),
				KEY status (status(16)),
				KEY connection_id (connection_id(32)),
				KEY parent_id (parent_id(32))
			) $charset_collate;"
		);
	}
}
