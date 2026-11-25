<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$folder_name_label   = __( 'Dropbox Folder Name', 'wpforms-entry-automation' );
$folder_name_tooltip = __( 'Name of the folder where your exported files will be stored in Dropbox.', 'wpforms-entry-automation' );
?>
<# const folderName = data.connection?.dropbox?.folder_name || data.formName; #>
<div class="wpforms-panel-field">
	<label for="wpforms-builder-{{data.provider}}-{{data.connection.id}}-dropbox-folder-name"><?php echo esc_html( $folder_name_label ); ?><span class="required">*</span><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $folder_name_tooltip ); ?>"></i>
	</label>
	<input
		type="text"
		id="wpforms-builder-{{data.provider}}-{{data.connection.id}}-dropbox-folder-name"
		name="settings[{{data.provider}}][{{data.connection.id}}][dropbox][folder_name]"
		value="{{folderName}}"
		class="wpforms-required"
	>
</div>
<?php
$folder_path_label   = __( 'Dropbox Folder to Store Data', 'wpforms-entry-automation' );
$folder_path_tooltip = __( 'Select the location in your Dropbox account where files should be stored.', 'wpforms-entry-automation' );
?>
<div class="wpforms-panel-field wpforms-entry-automation-dropbox-folder-path">
	<label for="wpforms-builder-{{data.provider}}-{{data.connection.id}}-dropbox-path">
		<?php echo esc_html( $folder_path_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $folder_path_tooltip ); ?>"></i>
	</label>
	<select
		class=""
		name="settings[{{data.provider}}][{{data.connection.id}}][dropbox][path]"
		id="wpforms-builder-{{data.provider}}-{{data.connection.id}}-dropbox-path"
	>
		<# _.each( data.folders[ folderName ], function( label, path ) { #>
			<# let indent = path.split('/').length - 2;
			let dashedLabel = `${'-'.repeat(indent)} ${label}`; #>
			<option value="{{ path }}" <# if ( data.connection?.dropbox?.path === path ) { #>selected="selected"<# } #>>
				{{ dashedLabel }}
			</option>
		<# } ); #>

		<# if ( data.folders[ folderName ] === undefined ) { #>
			<option value="/" selected><?php esc_html_e( 'Root', 'wpforms-entry-automation' ); ?></option>
		<# }; #>
	</select>
</div><!-- /.wpforms-builder-provider-connection-fields -->
