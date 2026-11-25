<?php

namespace WPFormsGeolocation\Forms;

/**
 * Editing field entries.
 *
 * @since 2.13.0
 */
class EntriesEdit extends \WPForms\Pro\Forms\Fields\Base\EntriesEdit {

	/**
	 * Constructor.
	 *
	 * @since 2.13.0
	 */
	public function __construct() {

		parent::__construct( 'map' );
	}

	/**
	 * Display the field on the Edit Entry page.
	 *
	 * @since 2.13.0
	 *
	 * @param array $entry_field Entry field data.
	 * @param array $field       Field data and settings.
	 * @param array $form_data   Form data and settings.
	 */
	public function field_display( $entry_field, $field, $form_data ): void { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( isset( $entry_field['value_raw'] ) && ! wpforms_is_empty_string( $entry_field['value_raw'] ) ) {
			foreach ( $field['properties']['inputs'] as $input_id => $input_properties ) {
				$field['properties']['inputs'][ $input_id ]['default'] = (int) $input_id === (int) $entry_field['value_raw'];
			}
		}

		add_filter( 'wpforms_geolocation_forms_field_location_list_label', [ $this, 'change_location_label' ], 10, 2 );

		parent::field_display( $entry_field, $field, $form_data );

		remove_filter( 'wpforms_geolocation_forms_field_location_list_label', [ $this, 'change_location_label' ], 10 );
	}

	/**
	 * Updates the location label based on provided choice attributes.
	 *
	 * @since        2.13.0
	 *
	 * @param string $label  The initial label value.
	 * @param array  $choice An associative array containing location data such as 'name' and 'address'.
	 *
	 * @return string
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function change_location_label( $label, $choice ): string {

		$label_parts = [];

		if ( ! wpforms_is_empty_string( $choice['name'] ) ) {
			$label_parts[] = $choice['name'];
		}

		if ( ! wpforms_is_empty_string( $choice['address'] ) ) {
			$label_parts[] = $choice['address'];
		}

		return esc_html( implode( ' &mdash; ', $label_parts ) );
	}
}
