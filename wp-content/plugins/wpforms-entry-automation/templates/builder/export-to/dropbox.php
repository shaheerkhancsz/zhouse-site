<?php

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WPForms\Providers\Provider\Status;
use WPFormsDropbox\Plugin as DropboxPlugin;

$is_configured = Status::init( DropboxPlugin::SLUG )->is_configured();

if ( ! $is_configured ) :
?>
<div class="wpforms-panel-field">
	<button type="button" class="wpforms-builder-inner-provider-account-add dropbox wpforms-btn wpforms-btn-sm wpforms-btn-blue">
        <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.5" d="M8.25 2.65625L4.125 5.28125L8.25 7.90625L4.125 10.5625L0 7.90625L4.125 5.25L0 2.65625L4.125 0L8.25 2.65625ZM4.09375 11.375L8.21875 8.75L12.3438 11.375L8.21875 14L4.09375 11.375ZM8.25 7.90625L12.375 5.25L8.25 2.65625L12.3438 0L16.5 2.65625L12.3438 5.28125L16.5 7.90625L12.3438 10.5625L8.25 7.90625Z" fill="white"/>
        </svg>
		<span><?php esc_html_e( 'Add New Account', 'wpforms-entry-automation' ); ?></span>
	</button>
</div>
<?php else : ?>
    <# if ( ! data.connection?.dropbox?.account_id && data.showAddConnectionButton ) { #>
        <div class="wpforms-panel-field">
            <button
                    type="button"
                    class="wpforms-builder-inner-provider-connection-add dropbox wpforms-btn wpforms-btn-sm wpforms-btn-blue"
            >
                <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.5" d="M8.25 2.65625L4.125 5.28125L8.25 7.90625L4.125 10.5625L0 7.90625L4.125 5.25L0 2.65625L4.125 0L8.25 2.65625ZM4.09375 11.375L8.21875 8.75L12.3438 11.375L8.21875 14L4.09375 11.375ZM8.25 7.90625L12.375 5.25L8.25 2.65625L12.3438 0L16.5 2.65625L12.3438 5.28125L16.5 7.90625L12.3438 10.5625L8.25 7.90625Z" fill="white"/>
                </svg>
                <span><?php esc_html_e( 'Add New Connection', 'wpforms-entry-automation' ); ?></span>
            </button>
        </div>
    <# } else { #>
        <?php
        $account_label   = __( 'Dropbox Account', 'wpforms-entry-automation' );
        $account_tooltip = __( 'Select which Dropbox account to use for this export task.', 'wpforms-entry-automation' );
        ?>
        <div class="wpforms-panel-field wpforms-builder-dropbox-provider-accounts">
            <label for="js-wpforms-builder-dropbox-provider-{{ data.connection.id }}-account"><?php echo esc_html( $account_label ); ?><span class="required">*</span><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $account_tooltip ); ?>"></i></label>

            <select id="js-wpforms-builder-dropbox-provider-{{ data.connection.id }}-account" class="js-wpforms-builder-dropbox-provider-connection-account wpforms-required" name="settings[{{data.provider}}][{{ data.connection.id }}][dropbox][account_id]"<# if ( _.isEmpty( data.accounts ) ) { #> disabled<# } #>>
                <option value="" selected disabled><?php esc_html_e( '--- Select ---', 'wpforms-entry-automation' ); ?></option>

                <# _.each( data.accounts, function ( account, account_id ) { #>
                    <option value="{{ account_id }}" data-option_id="{{ account['option_id'] }}"
                        <# if ( account_id === data.connection?.dropbox?.account_id ) { #> selected<# } #>>
                        {{ account }}
                    </option>
                <# } ); #>
            </select>
        </div>

        <!-- Here is where sub-templates will put its compiled HTML. -->
        <div class="wpforms-builder-dropbox-provider-actions-data"></div>

        <div class="wpforms-panel-field">
            <button type="button"
                    class="wpforms-builder-dropbox-connection-remove wpforms-btn wpforms-btn-sm wpforms-btn-red-borders">
                <?php esc_html_e( 'Remove Connection', 'wpforms-entry-automation' ); ?>
            </button>
        </div>
    <# }#>
<?php endif; ?>
