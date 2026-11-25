<?php
/**
 * Data access: reads Booking Calendar tables and turns each booking into a flat record.
 *
 * Booking Calendar storage reminders:
 *  - wp_booking.form            "text^name1^John~email^email1^john@x.com~..."  (field name + resource ID suffix)
 *  - wp_booking.booking_type    booking resource (room) ID
 *  - wp_booking.trash           1 = cancelled / in trash
 *  - wp_booking.remark          admin notes; Booking Calendar also appends an action log here, e.g.
 *                               "[2026-10-05 14:12] Approved by: Ali Khan (ali@x.com)"
 *  - wp_bookingdates            one row per date; the SECONDS of the time mark its meaning:
 *                               '10:00:01' = start time, '12:00:02' = end time, '00:00:00' = full day
 *  - wp_bookingdates.approved   1 = approved, 0 = pending
 *
 * Times are kept internally as 24-hour 'HH:MM' (for sorting) and shown in 12-hour format.
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZBR_Data {

	const STATUSES = array(
		'active'    => 'Approved + Pending',
		'approved'  => 'Approved only',
		'pending'   => 'Pending only',
		'cancelled' => 'Cancelled / Trash',
		'any'       => 'All (incl. cancelled)',
	);

	const BASES = array(
		'meeting' => 'Meeting date',
		'created' => 'Date booking was made',
	);

	/** Booking-list grouping. URL value 'none' = no grouping (stored internally as ''). Default: branch. */
	const GROUPS = array(
		'branch'  => 'Branch',
		'company' => 'Company',
		'none'    => 'No grouping',
	);

	/** Company key used for bookings without a company name. */
	const NO_COMPANY_KEY = '_none';

	const UNASSIGNED = 'Unassigned';
	const NO_COMPANY = '(No company given)';

	/** Aliases for the fields the report needs. First match wins. */
	const FIELD_ALIASES = array(
		'first_name' => array( 'name', 'firstname', 'first_name', 'fname' ),
		'last_name'  => array( 'secondname', 'lastname', 'last_name', 'surname', 'lname' ),
		'email'      => array( 'email', 'your-email' ),
		'phone'      => array( 'phone', 'mobile', 'tel', 'telephone' ),
		'company'    => array( 'companyname', 'company', 'organization', 'organisation' ),
		'persons'    => array( 'numberofperson', 'numberofpersons', 'persons', 'visitors', 'guests', 'attendees' ),
	);

	/** Form fields that are never exported as "extra" CSV columns. */
	const SKIP_EXTRA = array( 'starttime', 'endtime', 'rangetime', 'durationtime', 'captcha_input', 'wpdev_captcha_challenge', 'branch', 'location' );

	/** Booking Calendar's action-log lines in the notes (English). */
	const NOTE_ACTIONS = array(
		'Approved by:' => 'approved',
		'Declined by:' => 'pending',
		'Trashed by:'  => 'cancelled',
		'Restored by:' => 'restored',
		'Deleted by:'  => 'deleted',
	);

	private static $resources       = null;
	private static $columns         = array();
	private static $company_options = array();

	/* ------------------------------------------------------------------ */
	/* Environment                                                         */
	/* ------------------------------------------------------------------ */

	public static function booking_calendar_active() {
		return defined( 'WP_BK_VERSION_NUM' );
	}

	public static function table_exists( $name ) {
		global $wpdb;
		$table = $wpdb->prefix . $name;
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	/** Booking Calendar tables exist (even if the plugin is temporarily inactive). */
	public static function is_ready() {
		return self::table_exists( 'booking' ) && self::table_exists( 'bookingdates' );
	}

	public static function has_column( $table, $column ) {
		global $wpdb;
		$key = $table . '.' . $column;
		if ( ! isset( self::$columns[ $key ] ) ) {
			$found                 = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}{$table} LIKE %s", $column ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			self::$columns[ $key ] = ( null !== $found );
		}
		return self::$columns[ $key ];
	}

	/**
	 * Who may see reports: the same role Booking Calendar requires for its Bookings
	 * page (Settings > General > "User permissions"), falling back to admins.
	 */
	public static function capability() {
		$cap = 'manage_options';
		if ( function_exists( 'get_bk_option' ) && class_exists( 'WPBC_Admin_Menus' ) ) {
			$role = get_bk_option( 'booking_user_role_booking' );
			if ( is_string( $role ) && isset( WPBC_Admin_Menus::$capability[ $role ] ) ) {
				$cap = WPBC_Admin_Menus::$capability[ $role ];
			}
		}
		return apply_filters( 'zbr_capability', $cap );
	}

	/** @return array resource_id => title */
	public static function get_resources() {
		global $wpdb;
		if ( null === self::$resources ) {
			self::$resources = array();
			if ( self::table_exists( 'bookingtypes' ) ) {
				$rows = $wpdb->get_results( "SELECT booking_type_id, title FROM {$wpdb->prefix}bookingtypes ORDER BY booking_type_id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				foreach ( (array) $rows as $row ) {
					self::$resources[ (int) $row['booking_type_id'] ] = self::clean( $row['title'] );
				}
			}
			if ( empty( self::$resources ) ) {
				self::$resources[1] = 'Default';
			}
		}
		return self::$resources;
	}

	/** Branch filter options: configured branches + Unassigned. */
	public static function branch_options() {
		$out = ZBR_Settings::branches();
		$out['none'] = self::UNASSIGNED;
		return $out;
	}

	/** Company options (key => label) found for the current filters, set by get_records(). */
	public static function company_options() {
		return self::$company_options;
	}

	/* ------------------------------------------------------------------ */
	/* Filters                                                             */
	/* ------------------------------------------------------------------ */

	public static function presets() {
		return array(
			'today'      => 'Today',
			'yesterday'  => 'Yesterday',
			'this_week'  => 'This week',
			'last_week'  => 'Last week',
			'this_month' => 'This month',
			'last_month' => 'Last month',
			'this_year'  => 'This year',
			'last_year'  => 'Last year',
			'custom'     => 'Custom range',
		);
	}

	/** @return array|null [ 'Y-m-d', 'Y-m-d' ] */
	public static function preset_range( $preset ) {
		$now = new DateTimeImmutable( 'now', wp_timezone() );
		$sow = (int) get_option( 'start_of_week', 1 ); // 0 = Sunday, 1 = Monday.

		switch ( $preset ) {
			case 'today':
				$from = $now;
				$to   = $now;
				break;
			case 'yesterday':
				$from = $now->modify( '-1 day' );
				$to   = $from;
				break;
			case 'this_week':
			case 'last_week':
				$diff = ( (int) $now->format( 'w' ) - $sow + 7 ) % 7;
				$from = $now->modify( "-{$diff} days" );
				if ( 'last_week' === $preset ) {
					$from = $from->modify( '-7 days' );
				}
				$to = $from->modify( '+6 days' );
				break;
			case 'this_month':
				$from = $now->modify( 'first day of this month' );
				$to   = $now->modify( 'last day of this month' );
				break;
			case 'last_month':
				$from = $now->modify( 'first day of last month' );
				$to   = $now->modify( 'last day of last month' );
				break;
			case 'this_year':
				$from = $now->setDate( (int) $now->format( 'Y' ), 1, 1 );
				$to   = $now->setDate( (int) $now->format( 'Y' ), 12, 31 );
				break;
			case 'last_year':
				$from = $now->setDate( (int) $now->format( 'Y' ) - 1, 1, 1 );
				$to   = $now->setDate( (int) $now->format( 'Y' ) - 1, 12, 31 );
				break;
			default:
				return null;
		}
		return array( $from->format( 'Y-m-d' ), $to->format( 'Y-m-d' ) );
	}

	/**
	 * @param array $src Unslashed request array ( wp_unslash( $_GET ) ).
	 */
	public static function sanitize_filters( array $src ) {
		$str    = static function ( $key ) use ( $src ) {
			return ( isset( $src[ $key ] ) && is_string( $src[ $key ] ) ) ? $src[ $key ] : '';
		};
		$preset = array_key_exists( $str( 'range' ), self::presets() ) ? $str( 'range' ) : '';
		$from   = self::valid_date( $str( 'from' ) );
		$to     = self::valid_date( $str( 'to' ) );

		if ( '' === $preset ) {
			$preset = ( $from || $to ) ? 'custom' : 'this_month';
		}

		if ( 'custom' !== $preset ) {
			list( $from, $to ) = self::preset_range( $preset );
		} else {
			if ( ! $from && ! $to ) {
				list( $from, $to ) = self::preset_range( 'this_month' );
			}
			$from = $from ? $from : $to;
			$to   = $to ? $to : $from;
		}
		if ( $from > $to ) {
			list( $from, $to ) = array( $to, $from );
		}

		$company = trim( sanitize_text_field( $str( 'company' ) ) );
		$group   = $str( 'group' );
		if ( '' === $group || ! isset( self::GROUPS[ $group ] ) ) {
			$group = 'branch';
		}

		return array(
			'range'    => $preset,
			'from'     => $from,
			'to'       => $to,
			'basis'    => isset( self::BASES[ $str( 'basis' ) ] ) ? $str( 'basis' ) : 'meeting',
			'status'   => isset( self::STATUSES[ $str( 'status' ) ] ) ? $str( 'status' ) : 'active',
			'resource' => isset( $src['resource'] ) ? absint( $src['resource'] ) : 0,
			'branch'   => isset( self::branch_options()[ $str( 'branch' ) ] ) ? $str( 'branch' ) : '',
			'company'  => '' === $company ? '' : ( self::NO_COMPANY_KEY === $company ? $company : self::company_key( $company ) ),
			'group'    => 'none' === $group ? '' : $group,
			's'        => trim( sanitize_text_field( $str( 's' ) ) ),
		);
	}

	/** Filters as URL query args (used for pagination and export links). */
	public static function query_args( array $f ) {
		$args = array(
			'range'  => $f['range'],
			'from'   => $f['from'],
			'to'     => $f['to'],
			'basis'  => $f['basis'],
			'status' => $f['status'],
		);
		foreach ( array( 'resource', 'branch', 'company', 's' ) as $key ) {
			if ( ! empty( $f[ $key ] ) ) {
				$args[ $key ] = $f[ $key ];
			}
		}
		$args['group'] = self::group_param( $f );
		return $args;
	}

	/** Grouping as used in URLs / the select box ('none' when not grouped). */
	public static function group_param( array $f ) {
		return '' === $f['group'] ? 'none' : $f['group'];
	}

	/** Human readable description of the active filters. */
	public static function describe_filters( array $f ) {
		$parts   = array( self::BASES[ $f['basis'] ] . ': ' . self::fmt_date( $f['from'] ) . ' - ' . self::fmt_date( $f['to'] ) );
		$parts[] = 'Status: ' . self::STATUSES[ $f['status'] ];
		if ( '' !== $f['branch'] ) {
			$parts[] = 'Branch: ' . self::branch_options()[ $f['branch'] ];
		}
		if ( $f['resource'] ) {
			$res     = self::get_resources();
			$parts[] = 'Room: ' . ( isset( $res[ $f['resource'] ] ) ? $res[ $f['resource'] ] : '#' . $f['resource'] );
		}
		if ( '' !== $f['company'] ) {
			$opts    = self::$company_options;
			$parts[] = 'Company: ' . ( isset( $opts[ $f['company'] ] ) ? $opts[ $f['company'] ] : $f['company'] );
		}
		if ( '' !== $f['s'] ) {
			$parts[] = 'Search: "' . $f['s'] . '"';
		}
		if ( '' !== $f['group'] ) {
			$parts[] = 'Grouped by ' . strtolower( self::GROUPS[ $f['group'] ] );
		}
		return implode( ' | ', $parts );
	}

	public static function valid_date( $value ) {
		if ( ! is_string( $value ) || ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m ) ) {
			return '';
		}
		return checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ? $value : '';
	}

	/* ------------------------------------------------------------------ */
	/* Query                                                               */
	/* ------------------------------------------------------------------ */

	/**
	 * Load bookings matching the filters, as flat records.
	 * Sorted by group (when grouping) and then by meeting date/time.
	 *
	 * @return array[]
	 */
	public static function get_records( array $f ) {
		global $wpdb;
		$bk = $wpdb->prefix . 'booking';
		$dt = $wpdb->prefix . 'bookingdates';

		self::$company_options = array();

		$where = array();
		$args  = array();

		if ( 'created' === $f['basis'] ) {
			$col     = self::has_column( 'booking', 'creation_date' ) ? 'creation_date' : 'modification_date';
			$where[] = "bk.{$col} BETWEEN %s AND %s";
		} else {
			$where[] = "EXISTS ( SELECT 1 FROM {$dt} AS d WHERE d.booking_id = bk.booking_id AND d.booking_date BETWEEN %s AND %s )";
		}
		$args[] = $f['from'] . ' 00:00:00';
		$args[] = $f['to'] . ' 23:59:59';

		if ( 'cancelled' === $f['status'] ) {
			$where[] = 'bk.trash = 1';
		} elseif ( 'any' !== $f['status'] ) {
			$where[] = 'bk.trash = 0';
		}

		if ( $f['resource'] > 0 ) {
			$where[] = 'bk.booking_type = %d';
			$args[]  = $f['resource'];
		}

		if ( '' !== $f['s'] ) {
			$like   = '%' . $wpdb->esc_like( $f['s'] ) . '%';
			$conds  = array( 'bk.form LIKE %s' );
			$args[] = $like;
			if ( self::has_column( 'booking', 'remark' ) ) {
				$conds[] = 'bk.remark LIKE %s';
				$args[]  = $like;
			}
			if ( ctype_digit( $f['s'] ) ) {
				$conds[] = 'bk.booking_id = %d';
				$args[]  = (int) $f['s'];
			}
			$where[] = '( ' . implode( ' OR ', $conds ) . ' )';
		}

		$sql  = "SELECT bk.* FROM {$bk} AS bk WHERE " . implode( ' AND ', $where ) . ' ORDER BY bk.booking_id ASC';
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $rows ) ) {
			return array();
		}

		// Dates and our own action log, for all bookings, in chunks (IDs are integers, safe to inline).
		$ids   = array_map( 'intval', wp_list_pluck( $rows, 'booking_id' ) );
		$dates = array();
		foreach ( array_chunk( $ids, 500 ) as $chunk ) {
			$in  = implode( ',', $chunk );
			$res = $wpdb->get_results( "SELECT booking_id, booking_date, approved FROM {$dt} WHERE booking_id IN ({$in}) ORDER BY booking_date ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			foreach ( (array) $res as $r ) {
				$dates[ (int) $r['booking_id'] ][] = $r;
			}
		}
		$logs = ZBR_Log::get_for( $ids );

		$resources = self::get_resources();
		$records   = array();
		foreach ( $rows as $row ) {
			$id     = (int) $row['booking_id'];
			$record = self::build_record(
				$row,
				isset( $dates[ $id ] ) ? $dates[ $id ] : array(),
				$resources,
				isset( $logs[ $id ] ) ? $logs[ $id ] : array()
			);

			if ( 'approved' === $f['status'] && 'approved' !== $record['status'] ) {
				continue;
			}
			if ( 'pending' === $f['status'] && 'pending' !== $record['status'] ) {
				continue;
			}
			if ( '' !== $f['branch'] && $record['branch_id'] !== $f['branch'] ) {
				continue;
			}
			$records[] = $record;
		}

		// Company list for the dropdown reflects every other filter, then the company filter applies.
		foreach ( $records as $r ) {
			if ( ! isset( self::$company_options[ $r['company_key'] ] ) ) {
				self::$company_options[ $r['company_key'] ] = self::NO_COMPANY_KEY === $r['company_key'] ? self::NO_COMPANY : $r['company'];
			}
		}
		uasort( self::$company_options, 'strnatcasecmp' );

		if ( '' !== $f['company'] ) {
			$records = array_values(
				array_filter(
					$records,
					static function ( $r ) use ( $f ) {
						return $r['company_key'] === $f['company'];
					}
				)
			);
		}

		self::sort_records( $records, $f['group'] );

		return apply_filters( 'zbr_records', $records, $f );
	}

	/** Sort by group (branch order from settings / company A-Z) then meeting date and time. */
	public static function sort_records( array &$records, $group ) {
		$branch_order = array_flip( array_keys( self::branch_options() ) );
		usort(
			$records,
			static function ( $a, $b ) use ( $group, $branch_order ) {
				$ga = '';
				$gb = '';
				if ( 'branch' === $group ) {
					$ga = isset( $branch_order[ $a['branch_id'] ] ) ? sprintf( '%04d', $branch_order[ $a['branch_id'] ] ) : '9999' . $a['branch'];
					$gb = isset( $branch_order[ $b['branch_id'] ] ) ? sprintf( '%04d', $branch_order[ $b['branch_id'] ] ) : '9999' . $b['branch'];
				} elseif ( 'company' === $group ) {
					$ga = self::NO_COMPANY_KEY === $a['company_key'] ? "\xff" : $a['company_key'];
					$gb = self::NO_COMPANY_KEY === $b['company_key'] ? "\xff" : $b['company_key'];
				}
				return array( $ga, $a['date'], $a['start'], $a['id'] ) <=> array( $gb, $b['date'], $b['start'], $b['id'] );
			}
		);
	}

	/** Label of the group a record belongs to. */
	public static function group_label( array $r, $group ) {
		if ( 'branch' === $group ) {
			return $r['branch'];
		}
		if ( 'company' === $group ) {
			return self::NO_COMPANY_KEY === $r['company_key'] ? self::NO_COMPANY : $r['company'];
		}
		return '';
	}

	/* ------------------------------------------------------------------ */
	/* Record building                                                     */
	/* ------------------------------------------------------------------ */

	public static function build_record( array $row, array $date_rows, array $resources, array $log_events = array() ) {
		$resource_id = isset( $row['booking_type'] ) ? (int) $row['booking_type'] : 1;
		$fields      = self::parse_form( isset( $row['form'] ) ? (string) $row['form'] : '', $resource_id );
		$times       = self::compute_times( $date_rows );

		// Fallback to typed form values when the dates carry no times (e.g. imported bookings).
		if ( '' === $times['start'] ) {
			$times = self::times_from_form( $fields, $times );
		}

		if ( ! empty( $row['trash'] ) ) {
			$status = 'cancelled';
		} else {
			$status = empty( $date_rows ) ? 'pending' : 'approved';
			foreach ( $date_rows as $d ) {
				if ( empty( $d['approved'] ) ) {
					$status = 'pending';
					break;
				}
			}
		}

		$first   = self::pick( $fields, 'first_name' );
		$last    = self::pick( $fields, 'last_name' );
		$persons = self::pick( $fields, 'persons' );
		$persons = preg_match( '/\d+/', $persons, $m ) ? (int) $m[0] : 0;
		$company = self::pick( $fields, 'company' );

		$created = '';
		if ( ! empty( $row['creation_date'] ) && '0000-00-00 00:00:00' !== $row['creation_date'] ) {
			$created = $row['creation_date'];
		} elseif ( ! empty( $row['modification_date'] ) ) {
			$created = $row['modification_date'];
		}

		list( $note_events, $notes ) = self::parse_notes( isset( $row['remark'] ) ? (string) $row['remark'] : '' );
		$approval                    = self::approval_info( $status, array_merge( $note_events, $log_events ) );

		list( $branch_id, $branch ) = self::resolve_branch( $fields, $resource_id );

		$extra = array();
		foreach ( $fields as $name => $value ) {
			if ( in_array( $name, self::SKIP_EXTRA, true ) || self::is_alias( $name ) || ZBR_Settings::get()['branch_field'] === $name ) {
				continue;
			}
			$extra[ $name ] = $value;
		}

		return array_merge(
			array(
				'id'          => (int) $row['booking_id'],
				'resource_id' => $resource_id,
				'resource'    => isset( $resources[ $resource_id ] ) ? $resources[ $resource_id ] : 'Resource #' . $resource_id,
				'branch_id'   => $branch_id,
				'branch'      => $branch,
				'status'      => $status,
				'date'        => $times['date'],
				'date_end'    => $times['date_end'],
				'days'        => $times['days'],
				'start'       => $times['start'],
				'end'         => $times['end'],
				'hours'       => $times['hours'],
				'full_day'    => $times['full_day'],
				'first_name'  => $first,
				'last_name'   => $last,
				'name'        => trim( $first . ' ' . $last ),
				'email'       => self::pick( $fields, 'email' ),
				'phone'       => self::pick( $fields, 'phone' ),
				'company'     => $company,
				'company_key' => self::company_key( $company ),
				'persons'     => $persons,
				'created'     => $created,
				'remark'      => $notes,
				'extra'       => $extra,
			),
			$approval
		);
	}

	/**
	 * Matching key for company names: case, spaces and punctuation are ignored, so
	 * "Social Sharing", "SOCIAL-SHARING" and "socialsharing" count as one company.
	 */
	public static function company_key( $company ) {
		$company = (string) $company;
		$company = function_exists( 'mb_strtolower' ) ? mb_strtolower( $company, 'UTF-8' ) : strtolower( $company );
		$key     = preg_replace( '/[^\p{L}\p{N}]+/u', '', $company );
		if ( null === $key ) { // Invalid UTF-8.
			$key = preg_replace( '/[^a-z0-9]+/', '', $company );
		}
		return '' === $key ? self::NO_COMPANY_KEY : $key;
	}

	/** @return array [ branch_id, branch label ] */
	private static function resolve_branch( array $fields, $resource_id ) {
		$s        = ZBR_Settings::get();
		$branches = $s['branches'];

		foreach ( array_unique( array( $s['branch_field'], 'branch', 'location' ) ) as $field ) {
			if ( isset( $fields[ $field ] ) && '' !== $fields[ $field ] ) {
				$value = $fields[ $field ];
				$vkey  = self::company_key( $value );
				// Exact match first, then a short value inside a branch name ("DHA" -> "Z Signature – DHA").
				foreach ( $branches as $id => $name ) {
					if ( self::company_key( $name ) === $vkey ) {
						return array( $id, $name );
					}
				}
				foreach ( $branches as $id => $name ) {
					if ( strlen( $vkey ) >= 3 && false !== strpos( self::company_key( $name ), $vkey ) ) {
						return array( $id, $name );
					}
				}
				return array( 'x' . substr( md5( self::company_key( $value ) ), 0, 8 ), $value );
			}
		}

		if ( isset( $s['resource_branch'][ $resource_id ] ) && isset( $branches[ $s['resource_branch'][ $resource_id ] ] ) ) {
			$id = $s['resource_branch'][ $resource_id ];
			return array( $id, $branches[ $id ] );
		}
		return array( 'none', self::UNASSIGNED );
	}

	/**
	 * Split Booking Calendar notes into action-log events and the admin's own notes.
	 *
	 * @return array [ events[], notes string ]
	 */
	public static function parse_notes( $remark ) {
		$events = array();
		$notes  = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $remark ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			if ( ! preg_match( '/^\[(\d{4}-\d{2}-\d{2}) (\d{1,2}):(\d{2})\]\s*(.*)$/', $line, $m ) ) {
				$notes[] = $line;
				continue;
			}
			foreach ( self::NOTE_ACTIONS as $prefix => $action ) {
				if ( 0 === stripos( $m[4], $prefix ) ) {
					$who   = trim( substr( $m[4], strlen( $prefix ) ) );
					$name  = $who;
					$email = '';
					if ( preg_match( '/^(.*?)\s*\(([^()]*@[^()]*)\)\s*$/', $who, $mm ) ) {
						$name  = trim( $mm[1] );
						$email = trim( $mm[2] );
					}
					$events[] = array(
						'action'     => $action,
						'user_name'  => '' !== $name ? $name : $email,
						'user_email' => $email,
						'created_at' => sprintf( '%s %02d:%s:00', $m[1], $m[2], $m[3] ),
						'source'     => 'notes',
					);
					break;
				}
			}
			// Other "[date] ..." lines are system log entries, not admin notes.
		}
		return array( $events, implode( ' / ', $notes ) );
	}

	/**
	 * Who approved (and when), who cancelled (and when), from the combined event list.
	 */
	private static function approval_info( $status, array $events ) {
		usort(
			$events,
			static function ( $a, $b ) {
				return strcmp( $a['created_at'], $b['created_at'] );
			}
		);

		$approve = null;
		$cancel  = null;
		foreach ( $events as $e ) {
			if ( 'approved' === $e['action'] || 'auto_approved' === $e['action'] ) {
				$approve = $e;
			} elseif ( 'pending' === $e['action'] ) {
				$approve = null;
			} elseif ( 'cancelled' === $e['action'] ) {
				$cancel = $e;
			} elseif ( 'restored' === $e['action'] ) {
				$cancel = null;
			}
		}

		$info = array(
			'approved_by'       => '',
			'approved_by_email' => '',
			'approved_at'       => '',
			'cancelled_by'      => '',
			'cancelled_at'      => '',
		);

		if ( 'pending' !== $status && $approve ) {
			$name = $approve['user_name'];
			if ( 'auto_approved' === $approve['action'] ) {
				$name = '' === $name ? 'Auto-approved' : $name . ' (admin booking)';
			}
			$info['approved_by']       = '' !== $name ? $name : 'Unknown user';
			$info['approved_by_email'] = $approve['user_email'];
			$info['approved_at']       = $approve['created_at'];
		} elseif ( 'approved' === $status ) {
			$info['approved_by'] = 'Not recorded';
		}

		if ( 'cancelled' === $status && $cancel ) {
			$info['cancelled_by'] = '' !== $cancel['user_name'] ? $cancel['user_name'] : 'Unknown user';
			$info['cancelled_at'] = $cancel['created_at'];
		}
		return $info;
	}

	/**
	 * "text^name1^John~email^email1^a@b.com" -> [ 'name' => 'John', 'email' => 'a@b.com' ]
	 */
	public static function parse_form( $form, $resource_id ) {
		$out = array();
		if ( '' === $form ) {
			return $out;
		}
		$rid = (string) $resource_id;

		foreach ( explode( '~', $form ) as $chunk ) {
			$parts = explode( '^', $chunk, 3 );
			if ( count( $parts ) < 3 ) {
				continue;
			}
			list( $type, $name, $value ) = $parts;

			if ( '[]' === substr( $name, -2 ) ) {
				$name = substr( $name, 0, -2 );
			}
			if ( '' !== $rid && strlen( $name ) > strlen( $rid ) && substr( $name, -strlen( $rid ) ) === $rid ) {
				$name = substr( $name, 0, -strlen( $rid ) );
			}
			if ( '' === $name ) {
				continue;
			}

			$value = self::clean( $value );
			if ( 'checkbox' === $type ) {
				$value = ( 'true' === $value ) ? 'Yes' : ( ( 'false' === $value ) ? '' : $value );
			}

			if ( ! isset( $out[ $name ] ) || '' === $out[ $name ] ) {
				$out[ $name ] = $value;
			} elseif ( '' !== $value ) {
				$out[ $name ] .= ', ' . $value;
			}
		}
		return $out;
	}

	/**
	 * Work out dates, start/end and duration from wp_bookingdates rows.
	 */
	public static function compute_times( array $date_rows ) {
		$days = array();
		foreach ( $date_rows as $r ) {
			$parts = explode( ' ', trim( (string) $r['booking_date'] ) );
			$day   = $parts[0];
			$hms   = array_map( 'intval', array_pad( explode( ':', isset( $parts[1] ) ? $parts[1] : '00:00:00' ), 3, 0 ) );
			if ( ! isset( $days[ $day ] ) ) {
				$days[ $day ] = array( 'start' => null, 'end' => null, 'midnight' => false );
			}
			$minutes = $hms[0] * 60 + $hms[1];
			if ( 1 === $hms[2] ) {
				$days[ $day ]['start'] = $minutes;
			} elseif ( 2 === $hms[2] ) {
				$days[ $day ]['end'] = $minutes;
			} elseif ( 0 === $minutes ) {
				$days[ $day ]['midnight'] = true;
			}
		}
		// Booking Calendar saves a 12:00 AM start time as '00:00:00' (not '00:00:01').
		foreach ( $days as $day => $d ) {
			if ( null === $d['start'] && null !== $d['end'] && $d['midnight'] ) {
				$days[ $day ]['start'] = 0;
			}
		}
		ksort( $days );
		$keys = array_keys( $days );

		$out = array(
			'date'     => $keys ? $keys[0] : '',
			'date_end' => $keys ? $keys[ count( $keys ) - 1 ] : '',
			'days'     => count( $keys ),
			'start'    => '',
			'end'      => '',
			'hours'    => null,
			'full_day' => false,
		);
		if ( ! $keys ) {
			return $out;
		}

		$has_time = false;
		$complete = true;
		foreach ( $days as $d ) {
			if ( null !== $d['start'] || null !== $d['end'] ) {
				$has_time = true;
			}
			if ( null === $d['start'] || null === $d['end'] ) {
				$complete = false;
			}
		}

		if ( ! $has_time ) {
			$out['full_day'] = true;
			return $out;
		}

		$first = $days[ $out['date'] ];
		$last  = $days[ $out['date_end'] ];

		if ( $complete ) {
			// Same time slot on every booked day (single-day bookings land here).
			$minutes = 0;
			foreach ( $days as $d ) {
				$minutes += max( 0, $d['end'] - $d['start'] );
			}
			$out['start'] = self::fmt_minutes( $first['start'] );
			$out['end']   = self::fmt_minutes( $first['end'] );
			$out['hours'] = round( $minutes / 60, 2 );
		} elseif ( null !== $first['start'] && null !== $last['end'] && $out['days'] > 1 ) {
			// Continuous: check-in time on first day until check-out time on last day.
			$span         = (int) round( ( strtotime( $out['date_end'] . ' 00:00:00 UTC' ) - strtotime( $out['date'] . ' 00:00:00 UTC' ) ) / DAY_IN_SECONDS );
			$minutes      = $span * 1440 + $last['end'] - $first['start'];
			$out['start'] = self::fmt_minutes( $first['start'] );
			$out['end']   = self::fmt_minutes( $last['end'] );
			$out['hours'] = round( max( 0, $minutes ) / 60, 2 );
		} else {
			$out['start'] = null !== $first['start'] ? self::fmt_minutes( $first['start'] ) : '';
			$out['end']   = null !== $last['end'] ? self::fmt_minutes( $last['end'] ) : '';
		}
		return $out;
	}

	private static function times_from_form( array $fields, array $times ) {
		$start = '';
		$end   = '';
		if ( ! empty( $fields['rangetime'] ) && preg_match_all( '/(\d{1,2}):(\d{2})\s*([ap]\.?m\.?)?/i', $fields['rangetime'], $m, PREG_SET_ORDER ) && count( $m ) >= 2 ) {
			$start = self::to_minutes( $m[0] );
			$end   = self::to_minutes( $m[1] );
		} else {
			if ( ! empty( $fields['starttime'] ) && preg_match( '/(\d{1,2}):(\d{2})\s*([ap]\.?m\.?)?/i', $fields['starttime'], $m ) ) {
				$start = self::to_minutes( $m );
			}
			if ( ! empty( $fields['endtime'] ) && preg_match( '/(\d{1,2}):(\d{2})\s*([ap]\.?m\.?)?/i', $fields['endtime'], $m ) ) {
				$end = self::to_minutes( $m );
			}
		}
		if ( '' === $start ) {
			return $times;
		}
		$times['start']    = self::fmt_minutes( $start );
		$times['full_day'] = false;
		if ( '' !== $end && $end > $start ) {
			$times['end']   = self::fmt_minutes( $end );
			$times['hours'] = round( ( $end - $start ) / 60 * max( 1, $times['days'] ), 2 );
		}
		return $times;
	}

	/** Regex match [ full, h, mm, am/pm? ] -> minutes after midnight. */
	private static function to_minutes( array $m ) {
		$h      = (int) $m[1];
		$suffix = isset( $m[3] ) ? strtolower( $m[3][0] ?? '' ) : '';
		if ( 'p' === $suffix && $h < 12 ) {
			$h += 12;
		} elseif ( 'a' === $suffix && 12 === $h ) {
			$h = 0;
		}
		return $h * 60 + (int) $m[2];
	}

	/* ------------------------------------------------------------------ */
	/* Helpers                                                             */
	/* ------------------------------------------------------------------ */

	private static function pick( array $fields, $key ) {
		foreach ( self::FIELD_ALIASES[ $key ] as $alias ) {
			if ( isset( $fields[ $alias ] ) && '' !== $fields[ $alias ] ) {
				return $fields[ $alias ];
			}
		}
		return '';
	}

	private static function is_alias( $name ) {
		foreach ( self::FIELD_ALIASES as $aliases ) {
			if ( in_array( $name, $aliases, true ) ) {
				return true;
			}
		}
		return false;
	}

	public static function clean( $value ) {
		return trim( html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES, 'UTF-8' ) );
	}

	public static function fmt_minutes( $minutes ) {
		return sprintf( '%02d:%02d', intdiv( (int) $minutes, 60 ), (int) $minutes % 60 );
	}

	/** '14:30' -> '2:30 PM' */
	public static function fmt_time( $hhmm ) {
		if ( ! preg_match( '/^(\d{1,2}):(\d{2})/', (string) $hhmm, $m ) ) {
			return '';
		}
		$h = (int) $m[1] % 24;
		return sprintf( '%d:%s %s', ( 0 === $h % 12 ) ? 12 : $h % 12, $m[2], $h < 12 ? 'AM' : 'PM' );
	}

	/** Hour 0-23 -> '9 AM' */
	public static function fmt_hour( $h ) {
		$h = (int) $h % 24;
		return ( ( 0 === $h % 12 ) ? 12 : $h % 12 ) . ( $h < 12 ? ' AM' : ' PM' );
	}

	/** '10:00 AM - 12:00 PM', 'Full day' or '' */
	public static function time_range( array $r ) {
		if ( $r['full_day'] ) {
			return 'Full day';
		}
		return trim( self::fmt_time( $r['start'] ) . ' - ' . self::fmt_time( $r['end'] ), ' -' );
	}

	/** '2026-10-05 14:12:00' -> '05 Oct 2026, 2:12 PM' */
	public static function fmt_datetime( $sql, $date_format = 'd M Y' ) {
		if ( ! is_string( $sql ) || strlen( $sql ) < 16 ) {
			return '';
		}
		return self::fmt_date( substr( $sql, 0, 10 ), $date_format ) . ', ' . self::fmt_time( substr( $sql, 11, 5 ) );
	}

	public static function fmt_hours( $hours ) {
		if ( null === $hours ) {
			return '';
		}
		return rtrim( rtrim( number_format( (float) $hours, 2, '.', '' ), '0' ), '.' );
	}

	public static function fmt_date( $ymd, $format = 'd M Y' ) {
		$d = DateTimeImmutable::createFromFormat( '!Y-m-d', substr( (string) $ymd, 0, 10 ) );
		return $d ? $d->format( $format ) : (string) $ymd;
	}

	public static function field_label( $name ) {
		$labels = apply_filters(
			'zbr_field_labels',
			array(
				'details' => 'Details',
				'address' => 'Address',
				'city'    => 'City',
				'country' => 'Country',
			)
		);
		return isset( $labels[ $name ] ) ? $labels[ $name ] : ucwords( str_replace( array( '_', '-' ), ' ', $name ) );
	}
}
