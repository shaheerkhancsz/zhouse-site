<?php
/**
 * Entry automation form fields list template.
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wpforms-choices-list">
	<div class="checkbox-item">
		<input
			type="checkbox"
			value="select-all"
			id="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-form_field_0-item"
			class="select-all-checkbox screen-reader-text"
		>
		<label for="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-form_field_0-item">Select All</label>
	</div>
	<#
	const selectedFormFields = data.connection.form_field?.map?.( String ) ?? [];
	_.each( data.formFields, function( field, index ) {
		var itemIndex = `${index + 1}-${ data.connection.id }`;
	#>
	<div class="checkbox-item">
		<input
			type="checkbox"
			name="settings[{{data.provider}}][{{data.connection.id}}][form_field][{{index}}]"
			id="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-form_field_{{itemIndex}}-item"
			value="{{field.id}}"
			class="item-checkbox screen-reader-text"
		<# if ( selectedFormFields.includes( field.id.toString() ) ) { #> checked="checked" <# } #>
		>
		<label for="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-form_field_{{itemIndex}}-item">
			<# if ( field.label ) { #>
				{{ field.label }}
			<# } else { #>
				<?php esc_attr_e( 'Field #' ); ?>{{field.id}}
			<# } #>
		</label>
	</div>
	<# } ) #>
</div>
