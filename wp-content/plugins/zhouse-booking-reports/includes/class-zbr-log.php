<?php
/**
 * Approval / cancellation log.
 *
 * Booking Calendar only writes "who approved" into the booking notes, and only while
 * its "Log booking actions" option is on. To keep a reliable record we also listen to
 * Booking Calendar's own hooks and store each action in wp_zbr_booking_log:
 *   who (WordPress user) did what (approved / set pending / cancelled / restored) and when.
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZBR_Log {

	const DB_VERSION = '1';

	/** Avoid double rows when Booking Calendar fires a legacy and a new hook in one request. */
	private static $seen = array();

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'zbr_booking_log';
	}

	public static function init() {
		// Approve / set pending (new Bookings page, legacy Ajax, auto-approve on create).
		add_action( 'wpbc_booking_action__approved', array( __CLASS__, 'on_approved' ), 20, 2 );
		add_action( 'wpbc_booking_approved', array( __CLASS__, 'on_approved' ), 20, 2 );
		// Trash (= cancel) / restore.
		add_action( 'wpbc_booking_action__trash', array( __CLASS__, 'on_trash' ), 20, 2 );
		add_action( 'wpbc_booking_trash', array( __CLASS__, 'on_trash' ), 20, 2 );
		// Permanent delete.
		add_action( 'wpbc_booking_action__delete', array( __CLASS__, 'on_delete' ), 20, 1 );
		add_action( 'wpbc_booking_delete', array( __CLASS__, 'on_delete' ), 20, 1 );

		add_action( 'admin_init', array( __CLASS__, 'maybe_install' ) );
	}

	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$table   = self::table();
		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL auto_increment,
				booking_id bigint(20) unsigned NOT NULL,
				action varchar(20) NOT NULL default '',
				user_id bigint(20) unsigned NOT NULL default 0,
				user_name varchar(200) NOT NULL default '',
				user_email varchar(200) NOT NULL default '',
				created_at datetime NOT NULL,
				PRIMARY KEY  (id),
				KEY booking_id (booking_id)
			) {$charset};"
		);
		update_option( 'zbr_db_version', self::DB_VERSION, false );
	}

	public static function maybe_install() {
		if ( self::DB_VERSION !== get_option( 'zbr_db_version' ) ) {
			self::install();
		}
	}

	/* ------------------------------------------------------------------ */
	/* Hooks                                                               */
	/* ------------------------------------------------------------------ */

	public static function on_approved( $ids, $is_approved = 1 ) {
		$approved = ( 1 === (int) $is_approved );

		if ( self::is_creating_booking() ) {
			// New booking: only interesting when it was approved immediately (auto-approve).
			if ( $approved && ! self::is_editing_booking() ) {
				self::record( $ids, 'auto_approved', ! self::is_from_admin() );
			}
			return;
		}
		self::record( $ids, $approved ? 'approved' : 'pending' );
	}

	public static function on_trash( $ids, $is_trash = 1 ) {
		self::record( $ids, ( 1 === (int) $is_trash ) ? 'cancelled' : 'restored' );
	}

	public static function on_delete( $ids ) {
		self::record( $ids, 'deleted' );
	}

	/* ------------------------------------------------------------------ */

	private static function request_action() {
		return isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	}

	private static function is_creating_booking() {
		return 'wpbc_ajx_booking__create' === self::request_action();
	}

	private static function is_editing_booking() {
		// phpcs:ignore WordPress.Security.NonceVerification
		return ! empty( $_REQUEST['calendar_request_params']['booking_hash'] );
	}

	private static function is_from_admin() {
		$ref = wp_get_referer();
		return $ref && false !== strpos( $ref, '/wp-admin/' );
	}

	/**
	 * @param int|string|array $ids       Booking ID, CSV of IDs or array.
	 * @param string           $action    approved | auto_approved | pending | cancelled | restored | deleted
	 * @param bool             $anonymous Record without a user (visitor's auto-approved booking).
	 */
	private static function record( $ids, $action, $anonymous = false ) {
		global $wpdb;

		$ids = is_array( $ids ) ? $ids : explode( ',', (string) $ids );
		$ids = array_filter( array_map( 'absint', $ids ) );
		if ( empty( $ids ) ) {
			return;
		}
		self::maybe_install();

		$user  = $anonymous ? null : wp_get_current_user();
		$uid   = ( $user && $user->exists() ) ? (int) $user->ID : 0;
		$name  = $uid ? trim( $user->first_name . ' ' . $user->last_name ) : '';
		$name  = ( '' === $name && $uid ) ? $user->display_name : $name;
		$email = $uid ? $user->user_email : '';
		$now   = current_time( 'mysql' );

		foreach ( $ids as $id ) {
			$key = $id . '|' . $action;
			if ( isset( self::$seen[ $key ] ) ) {
				continue;
			}
			self::$seen[ $key ] = true;

			$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				self::table(),
				array(
					'booking_id' => $id,
					'action'     => $action,
					'user_id'    => $uid,
					'user_name'  => $name,
					'user_email' => $email,
					'created_at' => $now,
				),
				array( '%d', '%s', '%d', '%s', '%s', '%s' )
			);
		}
	}

	/* ------------------------------------------------------------------ */
	/* Read                                                                */
	/* ------------------------------------------------------------------ */

	/** @return array booking_id => [ events oldest first ] */
	public static function get_for( array $ids ) {
		global $wpdb;
		$out = array();
		if ( empty( $ids ) || ! ZBR_Data::table_exists( 'zbr_booking_log' ) ) {
			return $out;
		}
		$table = self::table();
		foreach ( array_chunk( array_map( 'intval', $ids ), 500 ) as $chunk ) {
			$in   = implode( ',', $chunk );
			$rows = $wpdb->get_results( "SELECT booking_id, action, user_id, user_name, user_email, created_at FROM {$table} WHERE booking_id IN ({$in}) ORDER BY created_at ASC, id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			foreach ( (array) $rows as $r ) {
				$r['source']                         = 'log';
				$out[ (int) $r['booking_id'] ][] = $r;
			}
		}
		return $out;
	}
}
