<?php
/**
 * Plugin Name:       WPForms Entry Automation
 * Plugin URI:        https://wpforms.com
 * Description:       Entry Automation integration with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.4.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpforms-entry-automation
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
 *
 * @since     1.0.0
 * @author    WPForms
 * @package   WPFormsEntryAutomation
 * @license   GPL-2.0+
 * @copyright Copyright (c) 2025, WPForms LLC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WPFormsEntryAutomation\Install;
use WPFormsEntryAutomation\Plugin;

/**
 * Plugin version.
 *
 * @since 1.0.0
 */
const WPFORMS_ENTRY_AUTOMATION_VERSION = '1.4.0';

/**
 * Plugin FILE.
 *
 * @since 1.0.0
 */
const WPFORMS_ENTRY_AUTOMATION_FILE = __FILE__;

/**
 * Plugin PATH.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_ENTRY_AUTOMATION_PATH', plugin_dir_path( WPFORMS_ENTRY_AUTOMATION_FILE ) );

/**
 * Plugin URL.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_ENTRY_AUTOMATION_URL', plugin_dir_url( WPFORMS_ENTRY_AUTOMATION_FILE ) );

/**
 * Load the plugin files.
 *
 * @since 1.0.0
 */
function wpforms_entry_automation_load() {

	$requirements = [
		'file'    => WPFORMS_ENTRY_AUTOMATION_FILE,
		'wpforms' => '2.0.1',
	];

	if ( ! function_exists( 'wpforms_requirements' ) || ! wpforms_requirements( $requirements ) ) {
		return;
	}

	wpforms_entry_automation();
}

add_action( 'wpforms_loaded', 'wpforms_entry_automation_load' );

/**
 * Get the instance of the `\WPFormsEntryAutomation\Plugin` class.
 * This function is useful for quickly grabbing data used throughout the plugin.
 *
 * @since 1.0.0
 *
 * @return Plugin
 */
function wpforms_entry_automation(): Plugin {

	return Plugin::get_instance();
}

require_once WPFORMS_ENTRY_AUTOMATION_PATH . 'vendor/autoload.php';

// Load installation things immediately for a reason how activation hook works.
( new Install() )->hooks();
