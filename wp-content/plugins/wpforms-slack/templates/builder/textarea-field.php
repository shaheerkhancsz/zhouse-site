<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection-block">
	<div class="wpforms-panel-field wpforms-panel-field-textarea">
		<label for="wpforms-builder-slack-provider-{{ data.connection.id }}-message">
			{{ data.field.label }}<# if ( data.field.required ) { #><span class="required">*</span><# } #>
		</label>
		<textarea
			id="wpforms-builder-slack-provider-{{ data.connection.id }}-message"
			name="providers[{{ data.provider.slug }}][{{ data.connection.id }}][{{ data.name }}]"
			class="wpforms-required wpforms-smart-tags-enabled"
			placeholder="<?php esc_attr_e( 'Enter a message', 'wpforms-slack' ); ?>"
			rows="4"
			data-type="all"
			data-fields><# if ( _.has( data.connection, 'message' ) && ! _.isEmpty( data.connection.message ) ){ #>{{ data.connection.message }}<# }#></textarea>
			<# if ( _.has( data.field, 'description' ) ) { #>
				<p class="description">{{ data.field.description }}</p>
			<# } #>
	</div>
</div>
