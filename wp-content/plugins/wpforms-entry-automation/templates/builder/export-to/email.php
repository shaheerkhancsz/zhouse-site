<?php
// Exit if accessed directly.

use WPFormsEntryAutomation\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$note = sprintf(
	'<p class="note">%s</p>',
	esc_html__( 'You can add multiple email addresses, separated by commas.', 'wpforms-entry-automation' )
);

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'email',
	[],
	__( 'Email Address', 'wpforms-entry-automation' ) . '<span class="required">*</span>',
	[
		'field_name'  => 'settings[{{data.provider}}][{{data.connection.id}}][email][address]',
		'smarttags'   => [
			'type' => 'other',
		],
		'input_class' => 'wpforms-smart-tags-enabled wpforms-required',
		'value'       => '{{data.connection.email?.address}}',
		'tooltip'     => esc_html__( 'Enter the email address.', 'wpforms-entry-automation' ),
		'after'       => $note,
	]
);
