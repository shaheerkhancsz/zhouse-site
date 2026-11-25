<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WPFormsEntryAutomation\Plugin;

wpforms_panel_field(
	'text',
	Plugin::SLUG,
	'file_name',
	[],
	esc_html__( 'File Name', 'wpforms-entry-automation' ) . '<span class="required">*</span>',
	[
		'field_name'  => 'settings[{{data.provider}}][{{data.connection.id}}][file_name]',
		'smarttags'   => [
			'type' => 'other',
		],
		'input_class' => 'wpforms-smart-tags-enabled wpforms-required',
		'input_id'    => 'wpforms-entry-automation-file-name-{{data.connection.id}}',
		'value'       => '{{data.connection.file_name}}',
		'tooltip'     => esc_html__( 'Enter the file name. Consider that it may be different in the result', 'wpforms-entry-automation' ),
	]
);

$file_format_label   = __( 'File Format', 'wpforms-entry-automation' );
$file_format_tooltip = __( 'Choose file format. CSV and XLSX are the most common.', 'wpforms-entry-automation' );

$note = sprintf(
	wp_kses( /* translators: %s - link to the WPForms.com doc article. */
		__( '<strong>Heads up!</strong> Some email providers have limits on attachment file size. If exporting large files, your notifications may not be delivered. <a href="%s" target="_blank" rel="noopener noreferrer">Learn More</a>', 'wpforms-entry-automation' ),
		[
			'a'      => [
				'href'   => [],
				'rel'    => [],
				'target' => [],
			],
			'strong' => [],
		]
	),
	esc_url(
		wpforms_utm_link(
			'https://wpforms.com/docs/attaching-files-to-form-notification-emails/',
			'Builder Notifications',
			'Form Attachments Documentation'
		)
	)
);
?>
<div class="wpforms-panel-field">
	<label for="wpforms-entry-automation-file-format-{{data.connection.id}}"><?php echo esc_html( $file_format_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $file_format_tooltip ); ?>"></i></label>
	<select
		name="settings[{{data.provider}}][{{data.connection.id}}][format]"
		id="wpforms-entry-automation-file-format-{{data.connection.id}}"
		class="wpforms-entry-automation-file-format"
	>
		<# _.each( data.fileFormats, function( fieldLabel, fieldValue ) { #>
			<option value="{{ fieldValue }}" <# if (data.connection.format === fieldValue) { #>selected="selected"<# } #>>
				{{ fieldLabel }}
			</option>
		<# } ) #>
	</select>
</div>

<?php
$one_entry_per_page_label   = __( 'Display One Entry Per Page', 'wpforms-entry-automation' );
$one_entry_per_page_tooltip = __( 'When enabled, each entry will start on a new page in the exported PDF file.', 'wpforms-entry-automation' );
?>
<div class="wpforms-entry-automation-one-entry-per-page-wrap wpforms-panel-field"
	<# if ( data.connection.format !== 'pdf' ) { #>style="display:none;"<# } #>
>
	<span class="wpforms-toggle-control">
		<input
			type="checkbox"
			id="wpforms-entry-automation-one-entry-per-page-{{data.connection.id}}"
			name="settings[{{data.provider}}][{{data.connection.id}}][one_entry_per_page]"
			value="1"
			<# if ( data.connection.one_entry_per_page === '1' ) { #>checked="checked"<# } #>
		>
		<label class="wpforms-toggle-control-icon"
			for="wpforms-entry-automation-one-entry-per-page-{{data.connection.id}}">
		</label>
		<label for="wpforms-entry-automation-one-entry-per-page-{{data.connection.id}}"
			class="wpforms-toggle-control-label"><?php echo esc_html( $one_entry_per_page_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $one_entry_per_page_tooltip ); ?>"></i></label>
	</span>
</div>

<?php
$export_to_label   = __( 'Export To', 'wpforms-entry-automation' );
$export_to_tooltip = __( 'Choose export destination.', 'wpforms-entry-automation' );
?>

<div class="wpforms-panel-field">
	<label for="wpforms-entry-automation-export-to-{{data.connection.id}}"><?php echo esc_html( $export_to_label ); ?><span class="required">*</span><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $export_to_tooltip ); ?>"></i></label>
	<select
		name="settings[{{data.provider}}][{{data.connection.id}}][export_to]"
		id="wpforms-entry-automation-export-to-{{data.connection.id}}"
		class="wpforms-entry-automation-export-to"
	>
		<# _.each( data.exportDestinations, function( fieldLabel, fieldValue ) { #>
			<option
					value="{{ fieldValue }}"
					<# if (data.connection.export_to === fieldValue) { #>selected="selected"<# } #>
					<# if (data.deliveryAddons.hasOwnProperty( fieldValue )) { #>
						<# _.each( data.deliveryAddons[fieldValue], function( value, param ) { #>
							data-{{ param }}="{{ value }}"
						<# } ) #>
					<# if ( ! data.activatedAddons.includes( fieldValue ) ) { #>class="education-modal"<# } #>
					<# } #>
				>
				{{ fieldLabel }}
			</option>
		<# } ) #>
	</select>
	<p class="note wpforms-entry-automation-email-note-{{data.connection.id}} wpforms-hidden">
		<?php echo wp_kses_post( $note ); ?>
	</p>
</div>

<div class="wpforms-entry-automation-task-export-to-options"></div>
<div class="wpforms-entry-automation-on-duplicate-wrap"></div>

<?php
$form_fields_label   = __( 'Form Fields', 'wpforms-entry-automation' );
$form_fields_tooltip = __( 'Select which form fields should be included in the export file.', 'wpforms-entry-automation' );

ob_start();
?>
<div class="wpforms-panel-field">
	<label for="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-form_field_0-item">
		<?php echo esc_html( $form_fields_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $form_fields_tooltip ); ?>"></i>
	</label>

	<div class="wpforms-entry-automation-form-fields-list-wrap"></div>
</div>

<?php
$additional_fields_label   = __( 'Additional Information', 'wpforms-entry-automation' );
$additional_fields_tooltip = __( 'Select additional entry information to include in the export file.', 'wpforms-entry-automation' );
?>
<div id="wpforms-panel-field-entry_automation-additional_fields-wrap" class="wpforms-panel-field">
	<label for="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-additional_field_0-item">
		<?php echo esc_html( $additional_fields_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $additional_fields_tooltip ); ?>"></i>
	</label>

	<div class="wpforms-choices-list">
		<div class="checkbox-item">
			<input
				type="checkbox"
				value="select-all"
				id="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-additional_field_0-item"
				class="select-all-checkbox screen-reader-text"
			>
			<label for="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-additional_field_0-item">Select All</label>
		</div>
		<#
		let counter = 0;
		_.each( data.additionalFields, function( fieldLabel, fieldValue ) {
			var itemIndex = `${ fieldValue }-${ data.connection.id }`;
		#>
		<div class="checkbox-item">
			<input
				type="checkbox"
				name="settings[{{data.provider}}][{{data.connection.id}}][additional_field][{{counter}}]"
				id="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-additional_field_{{itemIndex}}-item"
				value="{{fieldValue}}"
				class="item-checkbox screen-reader-text"
				<# if ( data.connection.additional_field?.includes?.( fieldValue ) ) { #> checked="checked" <# } #>
			>
			<label for="wpforms-choices-list-{{data.provider}}-{{data.connection.id}}-additional_field_{{itemIndex}}-item">{{fieldLabel}}</label>
		</div>
		<#
		counter++;
		} ) #>
	</div>
</div>

<div class="wpforms-entry-automation-filter-options"></div>

<?php
$export_label   = __( 'Export', 'wpforms-entry-automation' );
$export_tooltip = __( 'Choose how many entries to include in each export file.', 'wpforms-entry-automation' );
?>
<div class="wpforms-panel-field">
	<label for="wpforms-entry-automation-entries-number-{{data.connection.id}}">
		<?php echo esc_html( $export_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $export_tooltip ); ?>"></i>
	</label>
	<select
		name="settings[{{data.provider}}][{{data.connection.id}}][entries_number]"
		id="wpforms-entry-automation-entries-number-{{data.connection.id}}"
	>
		<# _.each( data.entriesNumberOptions, function( fieldLabel, fieldValue ) { #>
			<option value="{{ fieldValue }}" <# if (data.connection.entries_number === fieldValue) { #>selected="selected"<# } #>>
				{{ fieldLabel }}
			</option>
		<# } ) #>
	</select>
</div>
<?php

wpforms_panel_fields_group(
	ob_get_clean(),
	[
		'borders' => [ 'top' ],
		'class'   => 'wpforms-builder-entry-automation-group',
		'title'   => esc_html__( 'Entry Information', 'wpforms-entry-automation' ),
	]
);

printf( '<div class="wpforms-entry-automation-schedule-options"></div>' );
