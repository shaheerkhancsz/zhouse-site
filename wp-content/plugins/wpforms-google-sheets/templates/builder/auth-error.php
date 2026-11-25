<?php
/**
 * Auth notice template.
 *
 * @since 1.0.0
 * @since 2.7.0 Added the administrator-contact variant shown when no re-auth URL is available.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-alert wpforms-alert-danger wpforms-alert-dismissible">
	<div class="wpforms-alert-message">
		<p>
			<# if ( data.reAuthUrl ) { #>
				<?php esc_html_e( 'Your Google account connection has expired. Please reconnect your account.', 'wpforms-google-sheets' ); ?>
			<# } else { #>
				<?php esc_html_e( 'Your Google account connection has expired. Please contact your site administrator to reconnect the account.', 'wpforms-google-sheets' ); ?>
			<# } #>
		</p>
	</div>

	<# if ( data.reAuthUrl ) { #>
		<div class="wpforms-alert-buttons">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpforms_render( WPFORMS_GOOGLE_SHEETS_PATH . 'templates/auth/sign-in' );
			?>
			<input type="hidden" value="{{ data.reAuthUrl }}">
		</div>
	<# } #>
</div>
