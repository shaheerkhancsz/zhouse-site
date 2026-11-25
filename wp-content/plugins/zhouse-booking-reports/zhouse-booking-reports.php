<?php
/**
 * Plugin Name:       Zhouse Booking Reports
 * Description:       Reporting for Booking Calendar meeting-room bookings: branch-wise and company-wise reports, who approved each booking, and CSV / PDF downloads. Adds "Booking Calendar > Reports".
 * Version:           1.2.1
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            Z Houses
 * License:           GPLv2 or later
 * Text Domain:       zhouse-booking-reports
 *
 * This is a stand-alone add-on. It READS the Booking Calendar tables (wp_booking,
 * wp_bookingdates, wp_bookingtypes) and never modifies them. Its only own data is the
 * settings option and an approval log table (wp_zbr_booking_log).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZBR_VERSION', '1.2.1' );
define( 'ZBR_FILE', __FILE__ );
define( 'ZBR_DIR', plugin_dir_path( __FILE__ ) );
define( 'ZBR_URL', plugin_dir_url( __FILE__ ) );

require_once ZBR_DIR . 'includes/class-zbr-settings.php';
require_once ZBR_DIR . 'includes/class-zbr-log.php';
require_once ZBR_DIR . 'includes/class-zbr-data.php';
require_once ZBR_DIR . 'includes/class-zbr-report.php';
require_once ZBR_DIR . 'includes/class-zbr-admin.php';
require_once ZBR_DIR . 'includes/class-zbr-export.php';

register_activation_hook( __FILE__, array( 'ZBR_Log', 'install' ) );

add_action(
	'plugins_loaded',
	static function () {
		ZBR_Settings::init();
		ZBR_Log::init();
		ZBR_Admin::init();
		ZBR_Export::init();
	}
);
