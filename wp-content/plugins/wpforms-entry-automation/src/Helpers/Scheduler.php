<?php

namespace WPFormsEntryAutomation\Helpers;

use DateTime;

/**
 * Scheduling utilities.
 *
 * @since 1.0.0
 */
class Scheduler {

	/**
	 * Get formatted time.
	 *
	 * @since 1.0.0
	 *
	 * @param array $schedule Schedule data.
	 *
	 * @return string
	 */
	public static function get_formatted_time( array $schedule ): string {

		if ( ! empty( $schedule['queue'] ) ) {
			return esc_html__( 'Immediately after previous task', 'wpforms-entry-automation' );
		}

		$frequency = $schedule['frequency'] ?? '';
		$time      = date_i18n( get_option( 'time_format' ), strtotime( $schedule['time'] ) );

		if ( in_array( $frequency, [ 'first', 'last' ], true ) ) {
			/* translators: %1$s - first/last, %2$s - time. */
			return sprintf( __( 'Every %1$s day of the month at %2$s', 'wpforms-entry-automation' ), $frequency, $time );
		}

		$days = self::get_prepared_days_string( $schedule, $frequency );

		if ( empty( $days ) ) {
			return '';
		}

		if ( $frequency === 'month' ) {
			/* translators: %1$s - days of the month, %2$s - time. */
			return sprintf( __( 'Every month on the %1$s at %2$s', 'wpforms-entry-automation' ), $days, $time );
		}

		if ( strtolower( $days ) === __( 'every day', 'wpforms-entry-automation' ) || strtolower( $days ) === __( 'every week day', 'wpforms-entry-automation' ) ) {
			/* translators: %1$s - days of the week, %2$s - time. */
			return sprintf( __( '%1$s at %2$s', 'wpforms-entry-automation' ), $days, $time );
		}

		/* translators: %1$s - days of the week, %2$s - time. */
		return sprintf( __( 'Every %1$s at %2$s', 'wpforms-entry-automation' ), $days, $time );
	}

	/**
	 * Get prepared days string.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $schedule  Schedule data.
	 * @param string $frequency Frequency.
	 *
	 * @return string
	 */
	private static function get_prepared_days_string( array $schedule, string $frequency ): string {

		$days = array_filter( $schedule['days'] ?? [] );
		$time = $schedule['time'];

		if ( empty( $days ) || empty( $time ) ) {
			return '';
		}

		$days = self::get_days( $days, $frequency );

		return implode( ', ', array_map( 'ucfirst', $days ) );
	}

	/**
	 * Get summary formatted time.
	 *
	 * @since 1.0.0
	 *
	 * @param array $schedule Schedule data.
	 *
	 * @return string
	 */
	public static function get_summary_formatted_time( array $schedule ): string {

		if ( ! empty( $schedule['queue'] ) ) {
			return __( 'and will run immediately after the previous task', 'wpforms-entry-automation' );
		}

		$frequency = $schedule['frequency'] ?? '';

		$days = array_filter( $schedule['days'] ?? [] );
		$days = self::get_days( $days, $frequency, true );
		$days = self::get_string_list( $days, true );

		$time = date_i18n( get_option( 'time_format' ), strtotime( $schedule['time'] ) );

		if ( $frequency === 'month' ) {
			/* translators: %1$s - days of the month, %2$s - time. */
			return sprintf( __( '%1$s of each month at %2$s', 'wpforms-entry-automation' ), $days, $time );
		}

		if ( in_array( $frequency, [ 'first', 'last' ], true ) ) {
			/* translators: %1$s - first or last day of the month, %2$s - time. */
			return sprintf( __( 'the %1$s day of the month at %2$s', 'wpforms-entry-automation' ), $frequency, $time );
		}

		/* translators: %1$s - days of the week, %2$s - time. */
		return sprintf( __( '%1$s at %2$s', 'wpforms-entry-automation' ), $days, $time );
	}

	/**
	 * Get the list in a string format.
	 *
	 * @since 1.0.0
	 *
	 * @param array $items     List of items.
	 * @param bool  $is_timing Whether the items are related to timing.
	 *
	 * @return string
	 */
	public static function get_string_list( array $items, bool $is_timing = false ): string {

		$items_count = count( $items );

		$_items = implode( ', ', $items );

		// Check if string contains `every`.
		if ( $_items === __( 'every day', 'wpforms-entry-automation' ) || $_items === __( 'every week day', 'wpforms-entry-automation' ) ) {
			return $_items;
		}

		if ( $is_timing ) {
			$_items = sprintf( '%1$s %2$s', __( 'on', 'wpforms-entry-automation' ), $_items );
		}

		// Replace the last comma with 'and'.
		if ( $items_count > 2 ) {
			return preg_replace( '/, ([^,]+)$/', ', ' . __( 'and', 'wpforms-entry-automation' ) . ' $1', $_items );
		}

		return preg_replace( '/, ([^,]+)$/', ' ' . __( 'and', 'wpforms-entry-automation' ) . ' $1', $_items );
	}

	/**
	 * Get formatted days.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $days      Days.
	 * @param string $frequency Frequency.
	 * @param bool   $full_day  Full day.
	 *
	 * @return array
	 */
	public static function get_days( array $days, string $frequency, bool $full_day = false ): array {

		if ( $frequency === 'month' ) {
			return array_map(
				static function ( $day ) {

					return $day . self::get_day_suffix( $day );
				},
				$days
			);
		}

		if ( count( $days ) === 7 ) {
			return [ __( 'every day', 'wpforms-entry-automation' ) ];
		}

		if ( count( $days ) === 5 && ! in_array( 'sat', $days, true ) && ! in_array( 'sun', $days, true ) ) {
			return [ __( 'every week day', 'wpforms-entry-automation' ) ];
		}

		if ( $full_day ) {
			return array_map(
				static function ( $day ) {

					return DateTime::createFromFormat( 'D', $day )->format( 'l' );
				},
				$days
			);
		}

		return array_map( 'ucfirst', $days );
	}

	/**
	 * Get status labels.
	 *
	 * @since 1.0.0
	 *
	 * @param array $filters Filters.
	 *
	 * @return array
	 */
	public static function get_statuses( array $filters ): array {

		$statuses = $filters['statuses'] ?? [];

		return array_map( 'ucfirst', $statuses );
	}

	/**
	 * Get day suffix.
	 *
	 * @since 1.0.0
	 *
	 * @param int $day Day of the month.
	 *
	 * @return string
	 */
	private static function get_day_suffix( int $day ): string {

		switch ( $day ) {
			case 1:
				$suffix = 'st';
				break;

			case 2:
				$suffix = 'nd';
				break;

			case 3:
				$suffix = 'rd';
				break;

			default:
				$suffix = 'th';
		}

		return $suffix;
	}

	/**
	 * Get time format options.
	 * This method generates a list of time format options in 30-minute intervals.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_time_format_options(): array {

		$options = [];
		$time    = strtotime( '12:00 AM' );

		for ( $i = 0; $i < 48; $i++ ) {
			$options[] = [
				'value' => gmdate( 'g:i A', $time ), // Value always in 12-hour format.
				'label' => gmdate( get_option( 'time_format' ), $time ), // Label in the site's time format.
			];

			$time = strtotime( '+30 minutes', $time );
		}

		return $options;
	}
}
