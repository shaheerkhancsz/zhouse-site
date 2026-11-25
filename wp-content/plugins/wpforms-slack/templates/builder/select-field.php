<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection-block">
	<label for="wpforms-builder-slack-provider-{{ data.connection.id }}-{{data.name}}">{{ data.field.label }}<# if ( data.field.required ) { #><span class="required">*</span><# } #></label>
	<select
		class="wpforms-builder-slack-provider-connection-{{data.name}} <# if ( data.field.multiple ) { #> choicesjs-select<# } #><# if ( data.field.map ) { #> wpforms-field-map-select<# } #><# if ( data.field.required ) { #> wpforms-required<# } #>"
		id="wpforms-builder-slack-provider-{{ data.connection.id }}-{{data.name}}"
		name="providers[{{ data.provider.slug }}][{{ data.connection.id }}][{{ data.name }}]<# if ( data.field.multiple ) { #>[]<# } #>"
		<# if ( data.field.map ) { #>
			data-field-map-allowed="{{ data.field.map }}"
			data-field-map-placeholder="<?php esc_html_e( '--- Select Form Field ---', 'wpforms-slack' ); ?>"
		<# } #>
		<# if ( data.field.multiple ) { #>multiple<# } #>
	>
		<# connectionData = _.isArray( data.connection[data.name] ) ? data.connection[data.name] : [ data.connection[data.name] ]; #>
		<option value="" <# if ( data.field.multiple ) { #>disabled<# } #> >{{ data.field.placeholder }}</option>
		<# options = typeof data.field.options !== 'undefined' ? data.field.options : data.options; #>
		<# _.each( options, function( formField, key ) {
				selected = _.isArray( data.connection[data.name] ) ?
				_.find( connectionData, function( item ) { return item === key; } ) :
				+data.connection[data.name] === +key;

				var iconClass = '';
				if ( data.name === 'channels' ) {
					var isPrivate = formField.is_private;
					iconClass = isPrivate? 'fa-lock': 'fa-hashtag';
				}
				var label = _.isObject( formField ) ? formField.name : formField;
				#>
					<option value="{{ key }}" data-custom-properties='{"icon": "{{ iconClass }}"}' <# if ( ! _.isEmpty( connectionData ) && selected ) { #> selected<# } #>>
						{{ _.isObject( formField ) ? formField.name : formField }}
					</option>
			<# } ) #>
	</select>
</div>
