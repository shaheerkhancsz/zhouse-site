<?php
/**
 * CSV / PDF downloads ( admin-post.php?action=zbr_export ).
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZBR_Export {

	public static function init() {
		add_action( 'admin_post_zbr_export', array( __CLASS__, 'handle' ) );
	}

	public static function handle() {
		if ( ! current_user_can( ZBR_Data::capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to download booking reports.', 'zhouse-booking-reports' ), 403 );
		}
		check_admin_referer( 'zbr_export' );

		if ( ! ZBR_Data::is_ready() ) {
			wp_die( 'Booking Calendar tables were not found.' );
		}

		$request = wp_unslash( $_GET );
		$filters = ZBR_Data::sanitize_filters( $request );
		$records = ZBR_Data::get_records( $filters );
		$report  = ZBR_Report::build( $records, $filters );
		$format  = isset( $request['format'] ) ? sanitize_key( $request['format'] ) : 'csv';
		$name    = self::file_base( $filters );

		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		switch ( $format ) {
			case 'pdf':
				self::pdf( $records, $report, $filters, $name . '.pdf' );
				break;
			case 'summary':
				self::csv_summary( $report, $filters, $name . '_summary.csv' );
				break;
			default:
				self::csv_bookings( $records, $name . '_bookings.csv' );
		}
		exit;
	}

	/** e.g. booking-report_clifton_acme_2026-10-01_to_2026-10-31 */
	private static function file_base( array $f ) {
		$parts = array( 'booking-report' );
		if ( '' !== $f['branch'] ) {
			$parts[] = sanitize_title( ZBR_Data::branch_options()[ $f['branch'] ] );
		}
		if ( '' !== $f['company'] ) {
			$parts[] = sanitize_title( $f['company'] );
		}
		$parts[] = $f['from'] . '_to_' . $f['to'];
		return implode( '_', array_filter( $parts ) );
	}

	/* ------------------------------------------------------------------ */
	/* CSV                                                                 */
	/* ------------------------------------------------------------------ */

	private static function csv_open( $filename ) {
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $filename ) . '"' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM so Excel shows names correctly.
		return $out;
	}

	private static function csv_row( $out, array $row ) {
		fputcsv( $out, array_map( array( __CLASS__, 'csv_safe' ), $row ), ',', '"', '' );
	}

	/** Stop spreadsheet formula injection (=, +, -, @) while keeping phone numbers like +92 300 1234567. */
	public static function csv_safe( $value ) {
		if ( ! is_string( $value ) || '' === $value ) {
			return $value;
		}
		if ( preg_match( '/^[=+\-@\t\r]/', $value ) && ! preg_match( '/^[+\-]?[\d\s().\-]+$/', $value ) ) {
			return "'" . $value;
		}
		return $value;
	}

	public static function csv_bookings( array $records, $filename ) {
		// Extra form fields (anything beyond the standard ones) become their own columns.
		$extra_keys = array();
		foreach ( $records as $r ) {
			foreach ( array_keys( $r['extra'] ) as $k ) {
				$extra_keys[ $k ] = true;
			}
		}
		$extra_keys = array_keys( $extra_keys );

		$out = self::csv_open( $filename );

		$header = array(
			'Booking ID', 'Status', 'Branch', 'Room', 'Meeting Date', 'Weekday', 'End Date', 'Start Time', 'End Time', 'Duration (hours)', 'Full Day',
			'First Name', 'Last Name', 'Company', 'Email', 'Phone', 'Persons', 'Booked On',
			'Approved By', 'Approved By Email', 'Approved At', 'Cancelled By', 'Cancelled At', 'Admin Notes',
		);
		foreach ( $extra_keys as $k ) {
			$header[] = ZBR_Data::field_label( $k );
		}
		self::csv_row( $out, $header );

		foreach ( $records as $r ) {
			$row = array(
				$r['id'],
				ucfirst( $r['status'] ),
				$r['branch'],
				$r['resource'],
				$r['date'],
				'' !== $r['date'] ? ZBR_Data::fmt_date( $r['date'], 'l' ) : '',
				$r['days'] > 1 ? $r['date_end'] : '',
				ZBR_Data::fmt_time( $r['start'] ),
				ZBR_Data::fmt_time( $r['end'] ),
				null !== $r['hours'] ? (float) $r['hours'] : '',
				$r['full_day'] ? 'Yes' : 'No',
				$r['first_name'],
				$r['last_name'],
				$r['company'],
				$r['email'],
				$r['phone'],
				$r['persons'] ? $r['persons'] : '',
				ZBR_Data::fmt_datetime( $r['created'], 'Y-m-d' ),
				$r['approved_by'],
				$r['approved_by_email'],
				ZBR_Data::fmt_datetime( $r['approved_at'], 'Y-m-d' ),
				$r['cancelled_by'],
				ZBR_Data::fmt_datetime( $r['cancelled_at'], 'Y-m-d' ),
				$r['remark'],
			);
			foreach ( $extra_keys as $k ) {
				$row[] = isset( $r['extra'][ $k ] ) ? $r['extra'][ $k ] : '';
			}
			self::csv_row( $out, $row );
		}
		fclose( $out );
	}

	public static function csv_summary( array $report, array $f, $filename ) {
		$t   = $report['totals'];
		$out = self::csv_open( $filename );

		self::csv_row( $out, array( get_bloginfo( 'name' ) . ' - Meeting Room Booking Summary' ) );
		self::csv_row( $out, array( 'Filters', ZBR_Data::describe_filters( $f ) ) );
		self::csv_row( $out, array( 'Generated', ZBR_Data::fmt_datetime( wp_date( 'Y-m-d H:i:s' ) ) ) );
		self::csv_row( $out, array() );

		self::csv_row( $out, array( 'TOTALS' ) );
		$lines = array(
			array( 'Bookings', $t['bookings'] ),
			array( 'Approved', $t['approved'] ),
			array( 'Pending', $t['pending'] ),
			array( 'Cancelled', $t['cancelled'] ),
			array( 'Booked hours', $t['hours'] ),
			array( 'Average duration (hours)', $t['avg_hours'] ),
			array( 'Full-day bookings', $t['full_day'] ),
			array( 'Attendees (persons)', $t['persons'] ),
			array( 'Distinct companies', $t['companies'] ),
			array( 'Distinct customers', $t['customers'] ),
		);
		foreach ( $lines as $line ) {
			self::csv_row( $out, $line );
		}

		foreach ( ZBR_Report::sections( $report ) as $key => $title ) {
			if ( empty( $report[ $key ] ) ) {
				continue;
			}
			self::csv_row( $out, array() );
			self::csv_row( $out, array( strtoupper( $title ) ) );
			self::csv_row( $out, array( 'Name', 'Bookings', 'Approved', 'Pending', 'Cancelled', 'Hours', 'Persons', 'Share of bookings %' ) );
			foreach ( $report[ $key ] as $row ) {
				self::csv_row(
					$out,
					array(
						$row['label'],
						$row['bookings'],
						$row['approved'],
						$row['pending'],
						$row['cancelled'],
						$row['hours'],
						$row['persons'],
						$t['bookings'] ? round( $row['bookings'] / $t['bookings'] * 100, 1 ) : 0,
					)
				);
			}
		}
		fclose( $out );
	}

	/* ------------------------------------------------------------------ */
	/* PDF                                                                 */
	/* ------------------------------------------------------------------ */

	public static function pdf( array $records, array $report, array $f, $filename ) {
		require_once ZBR_DIR . 'includes/class-zbr-pdf.php';

		$pdf = ZBR_PDF::make( $report, $records, $f );

		nocache_headers();
		$pdf->Output( 'D', sanitize_file_name( $filename ) );
	}
}
