<?php
// Exit if accessed directly.

use WPForms\Providers\Provider\Status;
use WPFormsGoogleDrive\Plugin as GoogleDrivePlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_configured = Status::init( GoogleDrivePlugin::SLUG )->is_configured();

if ( ! $is_configured ) :
?>
<div class="wpforms-panel-field">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpforms_render( WPFORMS_GOOGLE_DRIVE_PATH . 'templates/sign-in' );
	?>
</div>
<?php
	else :
?>
<# if ( ! data.connection?.google?.account_id && data.showAddConnectionButton ) { #>
	<div class="wpforms-panel-field">
		<button
			type="button"
			class="wpforms-builder-inner-provider-connection-add google wpforms-btn wpforms-btn-sm wpforms-btn-blue"
		>
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path opacity="0.5" d="M15.25 8.1875C15.25 12.625 12.2188 15.75 7.75 15.75C3.4375 15.75 0 12.3125 0 8C0 3.71875 3.4375 0.25 7.75 0.25C9.8125 0.25 11.5938 1.03125 12.9375 2.28125L10.8125 4.3125C8.0625 1.65625 2.9375 3.65625 2.9375 8C2.9375 10.7188 5.09375 12.9062 7.75 12.9062C10.8125 12.9062 11.9688 10.7188 12.125 9.5625H7.75V6.90625H15.125C15.1875 7.3125 15.25 7.6875 15.25 8.1875Z" fill="white"/>
			</svg>
			<span><?php esc_html_e( 'Add New Connection', 'wpforms-entry-automation' ); ?></span>
		</button>
	</div>
<# } else { #>
<div class="wpforms-panel-field wpforms-builder-google-drive-provider-accounts">
	<label for="js-wpforms-builder-google-drive-provider-{{ data.connection.id }}-account"><?php esc_html_e( 'Google Account', 'wpforms-entry-automation' ); ?><span class="required">*</span></label>

	<select id="js-wpforms-builder-google-drive-provider-{{ data.connection.id }}-account" class="js-wpforms-builder-google-drive-provider-connection-account wpforms-required" name="settings[{{data.provider}}][{{ data.connection.id }}][google][account_id]"<# if ( _.isEmpty( data.accounts ) ) { #> disabled<# } #>>
		<option value="" selected disabled><?php esc_html_e( 'Select Account', 'wpforms-entry-automation' ); ?></option>

		<# _.each( data.accounts, function ( account, account_id ) { #>
			<option value="{{ account_id }}"
				<# if ( account_id === data.connection?.google?.account_id ) { #> selected<# } #>>
				{{ account }}
			</option>
		<# } ); #>
	</select>
</div>

<div class="js-wpforms-builder-google-drive-provider-account-fields wpforms-builder-google-drive-provider-account-fields<# if ( _.isEmpty( data.connection?.google?.account_id ) ) { #> wpforms-hidden<# } #>">
	<div class="wpforms-builder-google-drive-folder-fields"></div>

	<div class="wpforms-panel-field <# if ( ! _.isEmpty( data.connection?.google?.folder_id ) ) { #>wpforms-hidden<# } #>" id="wpforms-google-drive-connection-remove-{{ data.connection.id }}">
		<button type="button"
				class="wpforms-builder-google-drive-connection-remove wpforms-btn wpforms-btn-sm wpforms-btn-red-borders">
			<?php esc_html_e( 'Remove Connection', 'wpforms-entry-automation' ); ?>
		</button>
	</div>
</div>
<# }#>
<?php
	endif;
