<?php

namespace WPFormsSlack\Provider;

use RuntimeException;

/**
 * Field Mapper class.
 *
 * @since 1.0.0
 */
class FieldMapper {

	/**
	 * Connection data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $connection_data;

	/**
	 * Entry ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $entry_id;

	/**
	 * FieldMapper constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param int   $entry_id        Entry ID.
	 */
	public function __construct( array $connection_data, int $entry_id ) {

		$this->connection_data = $connection_data;
		$this->entry_id        = $entry_id;
	}

	/**
	 * Get required field value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Field name.
	 *
	 * @return string|int|array
	 *
	 * @throws RuntimeException The Required argument is missing.
	 */
	public function get_required_field_value( string $name ) {

		if ( ! isset( $this->connection_data[ $name ] ) || wpforms_is_empty_string( trim( $this->connection_data[ $name ] ) ) ) {
			throw new RuntimeException(
				sprintf(
					'The "%1$s" required field is missing',
					esc_html( $name )
				),
				400
			);
		}

		return $this->connection_data[ $name ];
	}

	/**
	 * Get not required field value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Field name.
	 *
	 * @return string|int|array
	 */
	public function get_not_required_field_value( string $name ) {

		return $this->connection_data[ $name ] ?? '';
	}

	/**
	 * Get current entry ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_entry_id(): int {

		return $this->entry_id;
	}
}
