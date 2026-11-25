<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection-block wpforms-builder-slack-provider-{{ data.name }}">
	<div class="wpforms-builder-provider-connection-setting">
		<label for="wpforms-builder-slack-provider-{{ data.connection.id }}-{{data.name}}">{{ data.field.label }}<# if ( data.field.required ) { #><span class="required">*</span><# } #></label>
		<input
			type="text"
			class="wpforms-builder-slack-provider-connection-{{data.name}} <# if ( data.field.required ) { #> wpforms-required <# } #>"
			id="wpforms-builder-slack-provider-{{ data.connection.id }}-{{data.name}}"
			name="providers[{{ data.provider.slug }}][{{ data.connection.id }}][{{ data.name }}]"
			value="{{ data.connection.time }}"
		>
		<# if ( _.has( data.field, 'description' ) ) { #>
			<p class="description">{{{ data.field.description }}}</p>
		<# } #>
	</div>
</div>
