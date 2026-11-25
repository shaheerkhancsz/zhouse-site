<?php
/**
 * Plugin Name:       WPForms Slack
 * Plugin URI:        https://wpforms.com
 * Description:       Slack integration with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.1.1
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpforms-slack
 * Domain Path:       /languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <https://www.gnu.org/licenses/>.
 */

use WPFormsSlack\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
/**
 * Define the plugin version.
 *
 * @since 1.0.0
 */
const WPFORMS_SLACK_VERSION = '1.1.1';

/**
 * Define the plugin file path.
 *
 * @since 1.0.0
 */
const WPFORMS_SLACK_FILE = __FILE__;

/**
 * Define the plugin directory path.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_SLACK_PATH', plugin_dir_path( WPFORMS_SLACK_FILE ) );

/**
 * Define the plugin directory URL.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_SLACK_URL', plugin_dir_url( WPFORMS_SLACK_FILE ) );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 */
function wpforms_slack_load() {

	$requirements = [
		'file'    => WPFORMS_SLACK_FILE,
		'wpforms' => '1.9.5',
	];

	if ( ! function_exists( 'wpforms_requirements' ) || ! wpforms_requirements( $requirements ) ) {
		return;
	}

	// Initialize the plugin.
	wpforms_slack();
}
add_action( 'wpforms_loaded', 'wpforms_slack_load' );

/**
 * Get the instance of the `\WPFormsSlack\Plugin` class.
 *
 * @since 1.0.0
 *
 * @return Plugin
 */
function wpforms_slack(): Plugin {

	require_once WPFORMS_SLACK_PATH . 'vendor/autoload.php';

	return Plugin::get_instance();
}
