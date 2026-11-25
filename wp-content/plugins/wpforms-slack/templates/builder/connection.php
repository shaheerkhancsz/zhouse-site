<?php

// Exit if accessed directly.
use WPFormsSlack\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connection" data-connection_id="{{ data.connection.id }}">
	<input type="hidden" class="wpforms-builder-provider-connection-id"
		name="providers[{{ data.provider }}][{{ data.connection.id }}][id]"
		value="{{ data.connection.id }}">

	<div class="wpforms-builder-provider-connection-title">
		{{ data.connection.name }}
		<button class="wpforms-builder-provider-connection-delete js-wpforms-builder-provider-connection-delete" type="button">
			<i class="fa fa-trash-o"></i>
		</button>
		<input type="hidden"
			id="wpforms-builder-slack-provider-{{ data.connection.id }}-name"
			name="providers[{{ data.provider }}][{{ data.connection.id }}][name]"
			value="{{ data.connection.name }}">
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-slack-provider-accounts">
		<label for="js-wpforms-builder-slack-provider-{{ data.connection.id }}-account"><?php esc_html_e( 'Select Account', 'wpforms-slack' ); ?><span class="required">*</span></label>

		<select id="js-wpforms-builder-slack-provider-{{ data.connection.id }}-account" class="js-wpforms-builder-slack-provider-connection-account wpforms-required" name="providers[{{ data.provider }}][{{ data.connection.id }}][account_id]"<# if ( _.isEmpty( data.accounts ) ) { #> disabled<# } #>>
		<option value="" selected disabled><?php esc_html_e( '--- Select ---', 'wpforms-slack' ); ?></option>

		<# _.each( data.accounts, function ( account, account_id ) { #>
		<option value="{{ account_id }}" data-option_id="{{ account['option_id'] }}"
		<# if ( account_id === data.connection.account_id ) { #> selected<# } #>>
		{{ account.label }}
		</option>
		<# } ); #>
		</select>
	</div>

	<div class="wpforms-builder-provider-connection-block wpforms-builder-slack-provider-actions">
		<label for="js-wpforms-builder-slack-provider-{{ data.connection.id }}-action"><?php esc_html_e( 'Action to Perform', 'wpforms-slack' ); ?><span class="required">*</span></label>

		<select class="js-wpforms-builder-slack-provider-connection-action wpforms-required"
			id="wpforms-builder-slack-provider-{{ data.connection.id }}-action"
		<# if ( _.isEmpty( data.connection.account_id ) ) { #>disabled<# } #>
		name="providers[<?php echo esc_attr( Plugin::SLUG ); ?>][{{ data.connection.id }}][action]">

		<option value="" selected disabled><?php esc_html_e( '--- Select Action ---', 'wpforms-slack' ); ?></option>

		<# _.each( data.actions, function( label, name ) { #>
		<option value="{{ name }}"<# if ( name === data.connection.action ) { #> selected<# } #>>
		{{ label }}
		</option>
		<# } ); #>
		</select>
	</div>

	<!-- Here is where sub-templates will put its compiled HTML. -->
	<div class="wpforms-builder-slack-provider-actions-data"></div>
	{{{ data.conditional }}}
</div>
