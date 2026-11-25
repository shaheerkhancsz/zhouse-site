<?php
// Exit if accessed directly.

use WPFormsEntryAutomation\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'ftp-host',
	[],
	__( 'FTP Host', 'wpforms-entry-automation' ) . '<span class="required">*</span>',
	[
		'field_name'  => 'settings[{{data.provider}}][{{data.connection.id}}][ftp][host]',
		'tooltip'     => __( 'The FTP host address.', 'wpforms-entry-automation' ),
		'value'       => '{{data.connection?.ftp?.host}}',
		'class'       => 'wpforms-entry-automation-ftp-field',
		'input_class' => 'wpforms-required',
		'input_id'    => 'wpforms-entry-automation-ftp-host-{{data.connection.id}}',
	]
);

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'ftp-port',
	[],
	__( 'Port', 'wpforms-entry-automation' ),
	[
		'field_name' => 'settings[{{data.provider}}][{{data.connection.id}}][ftp][port]',
		'tooltip'    => __( 'The port number for the FTP connection.', 'wpforms-entry-automation' ),
		'type'       => 'number',
		'value'      => '{{data.connection?.ftp?.port}}',
		'class'      => 'wpforms-entry-automation-ftp-field',
		'input_id'   => 'wpforms-entry-automation-ftp-port-{{data.connection.id}}',
	]
);

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'ftp-username',
	[],
	__( 'Username', 'wpforms-entry-automation' ) . '<span class="required">*</span>',
	[
		'field_name'  => 'settings[{{data.provider}}][{{data.connection.id}}][ftp][username]',
		'tooltip'     => __( 'The username for the FTP connection.', 'wpforms-entry-automation' ),
		'value'       => '{{data.connection?.ftp?.username}}',
		'class'       => 'wpforms-entry-automation-ftp-field',
		'input_class' => 'wpforms-required',
		'input_id'    => 'wpforms-entry-automation-ftp-username-{{data.connection.id}}',
	]
);

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'ftp-password',
	[],
	__( 'Password', 'wpforms-entry-automation' ) . '<span class="required">*</span>',
	[
		'field_name'  => 'settings[{{data.provider}}][{{data.connection.id}}][ftp][password]',
		'tooltip'     => __( 'The password for the FTP connection.', 'wpforms-entry-automation' ),
		'type'        => 'password',
		'value'       => '{{data.connection?.ftp?.password}}',
		'class'       => 'wpforms-entry-automation-ftp-field',
		'input_class' => 'wpforms-required',
		'input_id'    => 'wpforms-entry-automation-ftp-password-{{data.connection.id}}',
	]
);

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'ftp-path',
	[],
	__( 'Path', 'wpforms-entry-automation' ),
	[
		'field_name' => 'settings[{{data.provider}}][{{data.connection.id}}][ftp][path]',
		'tooltip'    => __( 'The path on the FTP server where the file will be uploaded.', 'wpforms-entry-automation' ),
		'value'      => '{{data.connection?.ftp?.path}}',
		'class'      => 'wpforms-entry-automation-ftp-field wpforms-entry-automation-ftp-path',
		'input_id'   => 'wpforms-entry-automation-ftp-path-{{data.connection.id}}',
	]
);

?>

<div class="wpforms-panel-field wpforms-panel-field-ftp-connection-buttons">
	<button type="button" class="wpforms-btn wpforms-btn-blue wpforms-btn-sm wpforms-entry-automation-ftp-connection-test" data-connection-id="{{data.connection.id}}">
		<?php esc_html_e( 'Test Connection', 'wpforms-entry-automation' ); ?>
	</button>
	<button type="button" class="wpforms-btn wpforms-btn-red-borders wpforms-btn-sm wpforms-entry-automation-ftp-connection-remove wpforms-entry-automation-ftp-connection-remove-{{data.connection.id}} {{ data.connection?.ftp?.host ? '' : 'wpforms-hidden' }} " data-connection-id="{{data.connection.id}}">
		<?php esc_html_e( 'Remove Connection', 'wpforms-entry-automation' ); ?>
	</button>
</div>
