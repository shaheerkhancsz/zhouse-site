<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$if_exist_label   = __( 'If File Already Exists', 'wpforms-entry-automation' );
$if_exist_tooltip = __( 'Increment file name will affect only file name. Overwrite file will replace the old exported file with entries. Add Entries to File will extend the existing file.', 'wpforms-entry-automation' );
?>
<# if ( data.exportTo !== 'email' ) {
	if ( [ 'json', 'pdf' ].includes( data.fileFormat ) ) {
		delete data.onDuplicateOptions.add;
	} #>
	<div class="wpforms-panel-field">
		<label for="wpforms-entry-automation-on-duplicate-{{data.connection.id}}"><?php echo esc_html( $if_exist_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $if_exist_tooltip ); ?>"></i></label>
		<select
			name="settings[{{data.provider}}][{{data.connection.id}}][on_duplicate]"
			id="wpforms-entry-automation-on-duplicate-{{data.connection.id}}"
			class="wpforms-entry-automation-on-duplicate"
		>
			<# _.each( data.onDuplicateOptions, function( fieldLabel, fieldValue ) { #>
				<option value="{{ fieldValue }}" <# if (data.currentValue === fieldValue) { #>selected="selected"<# } #>>
					{{ fieldLabel }}
				</option>
			<# } ) #>
		</select>
	</div>
<# } #>
