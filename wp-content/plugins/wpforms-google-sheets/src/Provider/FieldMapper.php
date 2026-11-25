<?php

namespace WPFormsGoogleSheets\Provider;

/**
 * FieldMapper class.
 *
 * @since 1.0.0
 */
class FieldMapper {

	/**
	 * Formulas start characters.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	const FORMULAS_START_CHARS = [ '=', '-', '+', '@', "\t", "\r" ];

	/**
	 * Prepare row for a spreadsheet.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $fields          Submitted fields.
	 * @param array $form_data       Form data and settings.
	 * @param int   $entry_id        Entry ID.
	 *
	 * @return array
	 */
	public function prepare_row( $connection_data, $fields, $form_data, $entry_id ) {

		if ( empty( $connection_data['custom_fields'] ) ) {
			return [];
		}

		$keys        = array_keys( $connection_data['custom_fields'] );
		$last_number = $this->convert_column_to_number( end( $keys ) );
		$row         = array_fill( 0, $last_number, '' );

		foreach ( $connection_data['custom_fields'] as $column_name => $column_value ) {
			$value = $this->get_custom_field_value( $column_value, $form_data, $fields, $entry_id );

			$row[ $this->convert_column_to_number( $column_name ) ] = $value;
		}

		// Clean up the 1st column if no entry was created.
		if ( empty( $entry_id ) ) {
			$row[0] = '';
		}

		return $row;
	}

	/**
	 * Get custom field value.
	 *
	 * @since 2.2.0
	 *
	 * @param string|int $column_value Field ID or custom value.
	 * @param array      $form_data    Form data and settings.
	 * @param array      $fields       Submitted fields.
	 * @param int        $entry_id     Entry ID.
	 *
	 * @return string
	 */
	private function get_custom_field_value( $column_value, $form_data, $fields, $entry_id ): string {

		// Field IDs stored as integer, Custom Values as string.
		if ( ! is_string( $column_value ) ) {

			$value = $this->get_field_value( $column_value, $form_data, $fields, $entry_id );

			/**
			 * Holds a specific value used for processing or configuration in the application.
			 *
			 * @since 2.6.0
			 *
			 * @param string     $value     The variable that stores the desired data or configuration.
			 * @param string|int $field_id  Field ID.
			 * @param array      $form_data Form data and settings.
			 * @param array      $fields    Submitted fields.
			 * @param int        $entry_id  Entry ID.
			 */
			$value = (string) apply_filters( 'wpforms_google_sheets_provider_field_mapper_field_value', $value, $column_value, $form_data, $fields, $entry_id );

			return $this->escape_formulas( html_entity_decode( $value ) );
		}

		// We allow using formulas for custom values.
		// But the formula shouldn't be passed as a smart tag value.
		if ( in_array( $column_value[0], self::FORMULAS_START_CHARS, true ) ) {
			return html_entity_decode( wpforms_process_smart_tags( $column_value, $form_data, $fields, $entry_id, 'google-sheets-custom-value' ) );
		}

		return $this->escape_formulas( html_entity_decode( wpforms_process_smart_tags( $column_value, $form_data, $fields, $entry_id, 'google-sheets-custom-value' ) ) );
	}

	/**
	 * Get field value.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $column_value Field ID or custom value.
	 * @param array      $form_data    Form data and settings.
	 * @param array      $fields       Submitted fields.
	 * @param int        $entry_id     Entry ID.
	 *
	 * @return string
	 */
	private function get_field_value( $column_value, $form_data, $fields, $entry_id ): string {

		if ( $this->use_smart_tag( $column_value, $fields ) ) {
			$field_smart_tag = '{field_id="' . $column_value . '"}';

			return wpforms_process_smart_tags( $field_smart_tag, $form_data, $fields, $entry_id, 'google-sheets-field-value' );
		}

		if ( ! isset( $fields[ $column_value ] ) ) {
			return '';
		}

		if ( wpforms_payment_has_quantity( $fields[ $column_value ], $form_data ) ) {
			return wpforms_payment_format_quantity( $fields[ $column_value ] );
		}

		return $fields[ $column_value ]['value'];
	}

	/**
	 * Determine if the field should be processed as a smart tag.
	 *
	 * @since 2.4.0
	 *
	 * @param int   $field_id Field ID.
	 * @param array $fields   List of fields.
	 *
	 * @return bool
	 */
	private function use_smart_tag( $field_id, array $fields ): bool {

		if ( ! isset( $fields[ $field_id ] ) ) {
			return false;
		}

		// Check if the field is a Repeated field.
		if ( wpforms_is_repeated_field( $field_id, $fields ) ) {
			return true;
		}

		// Check if the field is a File Upload or Camera field.
		// Smart tags for these fields should be processed to get the protected file URL.
		if ( in_array( $fields[ $field_id ]['type'], [ 'file-upload', 'camera' ], true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Escaping formulas for a cell.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text Cell text.
	 *
	 * @return string
	 */
	private function escape_formulas( $text ) {

		if ( ! in_array( substr( (string) $text, 0, 1 ), self::FORMULAS_START_CHARS, true ) ) {
			return $text;
		}

		return "'" . $text;
	}

	/**
	 * Convert column name to number.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column_name Column name e.g. A, B, ..., AAA.
	 *
	 * @return int
	 */
	private function convert_column_to_number( $column_name ) {

		$alphabet        = array_flip( range( 'A', 'Z' ) );
		$alphabet_length = count( $alphabet );

		$letters = str_split( $column_name );
		$number  = 0;
		$i       = 0;

		while ( $letters ) {
			$letter = array_pop( $letters );

			$number += ( $alphabet[ $letter ] + 1 ) * ( $alphabet_length ** $i );

			$i ++;
		}

		$number --;

		return (int) $number;
	}

	/**
	 * Prepare column names.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection_data Connection data.
	 * @param array $filled_headings Filled headings in the spreadsheet.
	 * @param array $form_data       Form data and settings.
	 *
	 * @return array
	 */
	public function prepare_headings( $connection_data, $filled_headings, $form_data ) {

		if ( empty( $connection_data['custom_fields'] ) ) {
			return [];
		}

		$columns = [];

		foreach ( $connection_data['custom_fields'] as $column_name => $value ) {
			$number = $this->convert_column_to_number( $column_name );

			if ( ! isset( $filled_headings[ $number ] ) ) {
				$columns[ $column_name ] = sanitize_text_field( $this->get_field_label( $value, $column_name, $form_data ) );
			}
		}

		return $columns;
	}

	/**
	 * Get field label.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $column_value Field ID or custom value.
	 * @param string     $column_name  Column name e.g. A, B, ..., AAA.
	 * @param array      $form_data    Form data and settings.
	 *
	 * @return string
	 */
	private function get_field_label( $column_value, $column_name, $form_data ) {

		if ( $column_name === 'A' ) {
			return __( 'Entry ID', 'wpforms-google-sheets' );
		}

		if ( is_string( $column_value ) ) {
			return sprintf( /* translators: %s is a column name. */
				__( 'Column %s', 'wpforms-google-sheets' ),
				$column_name
			);
		}

		if ( ! isset( $form_data['fields'][ $column_value ]['label'] ) || wpforms_is_empty_string( $form_data['fields'][ $column_value ]['label'] ) ) {
			return sprintf( /* translators: %d is a field id. */
				__( 'Field %d', 'wpforms-google-sheets' ),
				$column_value
			);
		}

		return $form_data['fields'][ $column_value ]['label'];
	}
}
