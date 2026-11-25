<?php
/**
 * Aggregates booking records into totals and breakdowns.
 *
 * @package Zhouse_Booking_Reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZBR_Report {

	/**
	 * @param array[] $records From ZBR_Data::get_records().
	 * @param array   $f       Sanitized filters.
	 */
	public static function build( array $records, array $f ) {
		$totals = array(
			'bookings'  => 0,
			'approved'  => 0,
			'pending'   => 0,
			'cancelled' => 0,
			'hours'     => 0.0,
			'timed'     => 0,
			'full_day'  => 0,
			'persons'   => 0,
			'companies' => 0,
			'customers' => 0,
			'branches'  => 0,
			'avg_hours' => 0.0,
		);

		$companies   = array();
		$customers   = array();
		$branches    = array();
		$by_branch   = array();
		$by_room     = array();
		$by_company  = array();
		$by_period   = array();
		$by_weekday  = array();
		$by_hour     = array();
		$by_approver = array();
		$period_mode = self::period_mode( $f );

		// Branches in settings order (empty ones are dropped later).
		foreach ( ZBR_Data::branch_options() as $id => $name ) {
			$by_branch[ $id ] = self::row( $name );
		}

		// Weekdays in the site's week order.
		$sow = (int) get_option( 'start_of_week', 1 );
		for ( $i = 0; $i < 7; $i++ ) {
			$n                = ( ( $sow + $i ) % 7 ) ?: 7; // ISO-8601: 1 = Mon ... 7 = Sun.
			$by_weekday[ $n ] = self::row( gmdate( 'l', strtotime( "Sunday +{$n} days" ) ) );
		}

		foreach ( $records as $r ) {
			$totals['bookings']++;
			$totals[ $r['status'] ]++;

			$hours = null !== $r['hours'] ? (float) $r['hours'] : 0.0;
			if ( null !== $r['hours'] ) {
				$totals['hours'] += $hours;
				$totals['timed']++;
			}
			if ( $r['full_day'] ) {
				$totals['full_day']++;
			}
			$totals['persons'] += $r['persons'];

			$has_company = ZBR_Data::NO_COMPANY_KEY !== $r['company_key'];
			if ( $has_company ) {
				$companies[ $r['company_key'] ] = true;
			}
			$customer_key = strtolower( '' !== $r['email'] ? $r['email'] : $r['name'] );
			if ( '' !== $customer_key ) {
				$customers[ $customer_key ] = true;
			}
			if ( 'none' !== $r['branch_id'] ) {
				$branches[ $r['branch_id'] ] = true;
			}

			self::add( $by_branch, $r['branch_id'], $r['branch'], $r, $hours );
			self::add( $by_room, 'r' . $r['resource_id'], $r['resource'], $r, $hours );
			self::add( $by_company, $has_company ? $r['company_key'] : "\xff", $has_company ? $r['company'] : ZBR_Data::NO_COMPANY, $r, $hours );

			$period_date = ( 'created' === $f['basis'] && '' !== $r['created'] ) ? substr( $r['created'], 0, 10 ) : $r['date'];
			if ( '' !== $period_date ) {
				if ( 'month' === $period_mode ) {
					$key   = substr( $period_date, 0, 7 );
					$label = ZBR_Data::fmt_date( $key . '-01', 'F Y' );
				} else {
					$key   = $period_date;
					$label = ZBR_Data::fmt_date( $period_date, 'D, d M Y' );
				}
				self::add( $by_period, $key, $label, $r, $hours );
			}

			if ( '' !== $r['date'] ) {
				$n = (int) ZBR_Data::fmt_date( $r['date'], 'N' );
				self::add( $by_weekday, $n, $by_weekday[ $n ]['label'], $r, $hours );
			}

			if ( '' !== $r['start'] ) {
				$h = (int) substr( $r['start'], 0, 2 );
				self::add( $by_hour, $h, ZBR_Data::fmt_hour( $h ) . ' - ' . ZBR_Data::fmt_hour( $h + 1 ), $r, $hours );
			}

			if ( '' !== $r['approved_by'] ) {
				self::add( $by_approver, strtolower( $r['approved_by'] ), $r['approved_by'], $r, $hours );
			}
		}

		$totals['hours']     = round( $totals['hours'], 2 );
		$totals['companies'] = count( $companies );
		$totals['customers'] = count( $customers );
		$totals['branches']  = count( $branches );
		$totals['avg_hours'] = $totals['timed'] ? round( $totals['hours'] / $totals['timed'], 2 ) : 0.0;

		$by_branch = array_filter(
			$by_branch,
			static function ( $row ) {
				return $row['bookings'] > 0;
			}
		);
		uasort( $by_room, array( __CLASS__, 'sort_by_bookings' ) );
		uasort( $by_company, array( __CLASS__, 'sort_by_hours' ) );
		uasort( $by_approver, array( __CLASS__, 'sort_by_bookings' ) );
		ksort( $by_period );
		ksort( $by_hour );

		return array(
			'totals'      => $totals,
			'period_mode' => $period_mode,
			'by_branch'   => array_values( $by_branch ),
			'by_room'     => array_values( $by_room ),
			'by_company'  => array_values( $by_company ),
			'by_approver' => array_values( $by_approver ),
			'by_period'   => array_values( $by_period ),
			'by_weekday'  => array_values( $by_weekday ),
			'by_hour'     => array_values( $by_hour ),
		);
	}

	/** Daily breakdown for ranges up to ~2 months, monthly otherwise. */
	public static function period_mode( array $f ) {
		$days = ( strtotime( $f['to'] . ' UTC' ) - strtotime( $f['from'] . ' UTC' ) ) / DAY_IN_SECONDS;
		return $days <= 62 ? 'day' : 'month';
	}

	/** Breakdown titles, in report order. */
	public static function sections( array $report ) {
		return array(
			'by_branch'   => 'Bookings by branch',
			'by_company'  => 'Bookings by company',
			'by_room'     => 'Bookings by room',
			'by_approver' => 'Approved by (staff)',
			'by_period'   => 'month' === $report['period_mode'] ? 'Bookings by month' : 'Bookings by day',
			'by_weekday'  => 'Busiest days of the week',
			'by_hour'     => 'Peak start times',
		);
	}

	/** Subtotals per group, keyed by group label (for grouped booking lists). */
	public static function group_totals( array $records, $group ) {
		$out = array();
		foreach ( $records as $r ) {
			$label = ZBR_Data::group_label( $r, $group );
			if ( ! isset( $out[ $label ] ) ) {
				$out[ $label ] = self::row( $label );
			}
			self::add( $out, $label, $label, $r, null !== $r['hours'] ? (float) $r['hours'] : 0.0 );
		}
		return $out;
	}

	private static function row( $label ) {
		return array(
			'label'     => $label,
			'bookings'  => 0,
			'approved'  => 0,
			'pending'   => 0,
			'cancelled' => 0,
			'hours'     => 0.0,
			'persons'   => 0,
		);
	}

	private static function add( array &$bucket, $key, $label, array $r, $hours ) {
		if ( ! isset( $bucket[ $key ] ) ) {
			$bucket[ $key ] = self::row( $label );
		}
		$bucket[ $key ]['bookings']++;
		$bucket[ $key ][ $r['status'] ]++;
		$bucket[ $key ]['hours']    = round( $bucket[ $key ]['hours'] + $hours, 2 );
		$bucket[ $key ]['persons'] += $r['persons'];
	}

	private static function sort_by_bookings( $a, $b ) {
		return array( $b['bookings'], $b['hours'] ) <=> array( $a['bookings'], $a['hours'] );
	}

	private static function sort_by_hours( $a, $b ) {
		return array( $b['hours'], $b['bookings'] ) <=> array( $a['hours'], $a['bookings'] );
	}
}
