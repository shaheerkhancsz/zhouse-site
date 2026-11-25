<?php

namespace WPFormsEntryAutomation;

/**
 * Class Sanitizer.
 *
 * @since 1.0.0
 */
class Sanitizer {

	/**
	 * Sanitize connection data and form data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data to sanitize.
	 * @param array $form_data       Form data to process during sanitization.
	 *
	 * @return array
	 */
	public function sanitize( array $connection_data, array $form_data ): array {

		if ( ! empty( $connection_data['is_invalid'] ) ) {
			unset( $connection_data['is_invalid'] );

			return $connection_data;
		}

		$connection_data = $this->sanitize_basic_fields( $connection_data );
		$connection_data = $this->sanitize_form_fields( $connection_data );
		$connection_data = $this->sanitize_additional_fields( $connection_data );
		$connection_data = $this->sanitize_filters( $connection_data );

		wpforms_entry_automation()->get( 'delivery_manager' )->sanitize_connection_data( $connection_data, $form_data );

		return $this->sanitize_schedule( $connection_data );
	}

	/**
	 * Sanitize basic connection data fields.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added one_entry_per_page sanitization.
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_basic_fields( array $connection_data ): array {

		$connection_data = wp_parse_args(
			$connection_data,
			[
				'name'               => '',
				'action'             => '',
				'file_name'          => '',
				'on_duplicate'       => '',
				'export_to'          => '',
				'format'             => '',
				'entries_number'     => 'all',
				'one_entry_per_page' => '0',
			]
		);

		$connection_data['name']               = sanitize_text_field( $connection_data['name'] );
		$connection_data['action']             = $this->sanitize_action( $connection_data['action'] );
		$connection_data['file_name']          = sanitize_text_field( $connection_data['file_name'] );
		$connection_data['on_duplicate']       = $this->sanitize_on_duplicate( $connection_data['on_duplicate'] );
		$connection_data['export_to']          = sanitize_text_field( $connection_data['export_to'] );
		$connection_data['format']             = sanitize_text_field( $connection_data['format'] );
		$connection_data['entries_number']     = $this->sanitize_entries_number( $connection_data['entries_number'] );
		$connection_data['one_entry_per_page'] = $connection_data['one_entry_per_page'] === '1' ? '1' : '0';

		return $connection_data;
	}

	/**
	 * Sanitize form fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_form_fields( array $connection_data ): array {

		if ( ! isset( $connection_data['form_field'] ) || ! is_array( $connection_data['form_field'] ) ) {
			$connection_data['form_field'] = [];

			return $connection_data;
		}

		$connection_data['form_field'] = array_values( array_map( 'absint', $connection_data['form_field'] ) );

		return $connection_data;
	}

	/**
	 * Sanitize additional fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_additional_fields( array $connection_data ): array {

		if ( ! isset( $connection_data['additional_field'] ) || ! is_array( $connection_data['additional_field'] ) ) {
			$connection_data['additional_field'] = [];

			return $connection_data;
		}

		$connection_data['additional_field'] = array_values( array_map( 'sanitize_key', $connection_data['additional_field'] ) );

		return $connection_data;
	}

	/**
	 * Sanitize action.
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action.
	 *
	 * @return string
	 */
	private function sanitize_action( string $action ): string {

		return in_array( $action, [ 'export', 'delete' ], true ) ? $action : '';
	}

	/**
	 * Sanitize on_duplicate.
	 *
	 * @since 1.0.0
	 *
	 * @param string $on_duplicate On duplicate action.
	 *
	 * @return string
	 */
	private function sanitize_on_duplicate( string $on_duplicate ): string {

		return in_array( $on_duplicate, [ 'inc', 'over', 'add' ], true ) ? $on_duplicate : 'inc';
	}

	/**
	 * Sanitize entries_number.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entries_number Entries number.
	 *
	 * @return string
	 */
	private function sanitize_entries_number( string $entries_number ): string {

		return in_array( $entries_number, [ 'all', 'new' ], true ) ? $entries_number : 'all';
	}

	/**
	 * Sanitize statuses.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_statuses( array $connection_data ): array {

		if ( empty( $connection_data['filters']['statuses'] ) || ! is_array( $connection_data['filters']['statuses'] ) ) {
			return [];
		}

		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$form_post     = ! empty( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : [];
		$connection_id = $connection_data['id'] ?? '';

		return wpforms_chain( $form_post )
			->map(
				static function ( $post_pair ) use ( $connection_id ) {
					$provider_slug = Plugin::SLUG;

					if (
						empty( $post_pair['name'] ) ||
						$post_pair['name'] !== "settings[$provider_slug][$connection_id][filters][statuses][]"
					) {
						return '';
					}

					return $post_pair['value'];
				}
			)
			->array_filter()
			->array_values()
			->value();
	}

	/**
	 * Sanitize filters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_filters( array $connection_data ): array {

		if ( empty( $connection_data['filters'] ) || ! is_array( $connection_data['filters'] ) ) {
			$connection_data['filters'] = [];

			return $connection_data;
		}

		$filters = $connection_data['filters'];
		$filters = wp_parse_args(
			$filters,
			[
				'field'    => '',
				'operator' => '',
				'value'    => '',
				'statuses' => [],
			]
		);

		$filters                    = [
			'field'    => is_numeric( $filters['field'] ) ? absint( $filters['field'] ) : sanitize_key( $filters['field'] ),
			'operator' => sanitize_text_field( $filters['operator'] ),
			'value'    => sanitize_text_field( $filters['value'] ),
			'statuses' => $this->sanitize_statuses( $connection_data ),
		];
		$connection_data['filters'] = $filters;

		return $connection_data;
	}

	/**
	 * Sanitize schedule.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 *
	 * @return array
	 */
	private function sanitize_schedule( array $connection_data ): array {

		if ( empty( $connection_data['schedule'] ) || ! is_array( $connection_data['schedule'] ) ) {
			$connection_data['schedule'] = $this->get_default_schedule();

			return $connection_data;
		}

		$schedule = $connection_data['schedule'];
		$schedule = wp_parse_args(
			$schedule,
			[
				'start'     => '',
				'end'       => '',
				'frequency' => '',
				'time'      => '',
				'days'      => [],
			]
		);

		// Start date - if empty, set to today.
		$schedule['start'] = ! empty( $schedule['start'] ) ?
			sanitize_text_field( $schedule['start'] ) :
			gmdate( 'Y-m-d' );

		// End date - can be empty.
		$schedule['end'] = ! empty( $schedule['end'] ) ?
			sanitize_text_field( $schedule['end'] ) :
			'';

		// Frequency.
		$valid_frequencies     = [ 'week', 'month', 'first', 'last' ];
		$schedule['frequency'] = in_array( $schedule['frequency'], $valid_frequencies, true ) ?
			$schedule['frequency'] :
			'week';

		// Days.
		$schedule['days'] = $this->sanitize_schedule_days(
			$schedule['days'],
			$schedule['frequency']
		);

		// Time.
		$schedule['time'] = sanitize_text_field( $schedule['time'] );

		$connection_data['schedule'] = $schedule;

		return $connection_data;
	}

	/**
	 * Sanitize schedule days based on frequency.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $days      Days array.
	 * @param string $frequency Schedule frequency.
	 *
	 * @return array
	 */
	private function sanitize_schedule_days( $days, string $frequency ): array {

		if ( ! is_array( $days ) || in_array( $frequency, [ 'first', 'last' ], true ) ) {
			return [];
		}

		if ( $frequency === 'month' ) {
			// For monthly frequency, days should be numeric.
			$sanitized_days = array_map( 'absint', $days );
		} else {
			// For weekly frequency, days should be keys like 'mon', 'tue', etc.
			$sanitized_days = array_map( 'sanitize_key', $days );
		}

		return array_values( array_filter( $sanitized_days ) );
	}

	/**
	 * Get default schedule settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_default_schedule(): array {

		return [
			'start'     => gmdate( 'Y-m-d' ),
			'end'       => '',
			'frequency' => 'week',
			'days'      => [],
			'time'      => '',
		];
	}
}
