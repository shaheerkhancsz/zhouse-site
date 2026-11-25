<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wpforms-panel-field wpforms-builder-google-drive-provider-folder-type">
	<label for="wpforms-builder-google-drive-folder-field-{{ data.connection.id }}">
		<?php esc_html_e( 'Google Drive Folder', 'wpforms-entry-automation' ); ?><span class="required">*</span>
	</label>

	<div class="wpforms-builder-entry-automation-radio-buttons<# if ( ! _.isEmpty( data.connection?.google?.folder_id ) ) { #> wpforms-hidden<# } #>">
		<label>
			<input type="radio" value="new" name="settings[{{data.provider}}][{{ data.connection.id }}][google][folder_type]" class="js-wpforms-builder-google-drive-provider-connection-folder-type wpforms-builder-google-drive-folder-type"<# if ( _.isEmpty( data.connection?.google?.folder_type ) || data.connection?.google?.folder_type === 'new' ) { #> checked <# } #>>
			<?php esc_html_e( 'Create New', 'wpforms-entry-automation' ); ?>
		</label>
		<label>
			<input type="radio" value="existing" name="settings[{{data.provider}}][{{ data.connection.id }}][google][folder_type]" class="js-wpforms-builder-google-drive-provider-connection-folder-type wpforms-builder-google-drive-folder-type"<# if ( data.connection?.google?.folder_type === 'existing' ) { #> checked <# } #>>
			<?php esc_html_e( 'Select Existing', 'wpforms-entry-automation' ); ?>
		</label>
	</div>
</div>

<div class="wpforms-panel-field wpforms-builder-google-drive-provider-folder-id<# if ( _.isEmpty( data.connection?.google?.folder_type ) || data.connection?.google?.folder_type !== 'existing' ) { #> wpforms-hidden<# } #>">
	<input type="hidden"
			name="settings[{{data.provider}}][{{ data.connection.id }}][google][folder_id]"
			value="{{ data.connection?.google?.folder_id }}"
			class="js-wpforms-builder-google-drive-provider-connection-folder-id"
	>
	<div class="wpforms-builder-provider-connection-block-field wpforms-builder-provider-connection-block-field-existing-empty js-wpforms-builder-provider-connection-block-field-existing-empty<# if ( ! _.isEmpty( data.connection?.google?.folder_id ) ) { #> wpforms-hidden<# } #>">
		<# if ( data.canSelectExistingFolder ) { #>
			<button type="button"
					class="js-wpforms-builder-google-drive-provider-connection-folder-id-choose wpforms-btn wpforms-btn-sm wpforms-btn-blue-borders">
				<?php esc_html_e( 'Select Folder', 'wpforms-entry-automation' ); ?>
			</button>
		<# } else { #>
			<div class="wpforms-alert wpforms-alert-warning wpforms-alert-nomargin">
				<h4><?php esc_html_e( 'Administrator Permissions Required', 'wpforms-entry-automation' ); ?></h4>
				<p><?php esc_html_e( 'You don\'t have permission to connect this form to an existing folder, but you can create a new one.', 'wpforms-entry-automation' ); ?></p>
			</div>
		<# } #>
	</div>
	<div class="wpforms-builder-provider-connection-block-field wpforms-builder-provider-connection-block-field-existing-not-empty js-wpforms-builder-provider-connection-block-field-existing-not-empty<# if ( _.isEmpty( data.connection?.google?.folder_id ) ) { #> wpforms-hidden<# } #>">
		<a href="#"
			class="js-wpforms-builder-google-drive-provider-connection-folder-id-view wpforms-btn wpforms-btn-sm wpforms-btn-blue-borders"
			target="_blank"
			rel="noopener noreferrer">
			<?php esc_html_e( 'View Folder on Drive', 'wpforms-entry-automation' ); ?>
		</a>
		<button type="button"
				class="wpforms-builder-google-drive-connection-remove wpforms-btn wpforms-btn-sm wpforms-btn-red-borders">
			<?php esc_html_e( 'Remove Connection', 'wpforms-entry-automation' ); ?>
		</button>
	</div>
</div>

<div class="wpforms-panel-field wpforms-builder-google-drive-provider-folder-name<# if ( ! _.isEmpty( data.connection?.google?.folder_type ) && data.connection?.google?.folder_type !== 'new'  ) { #> wpforms-hidden<# } #>">
	<label for="wpforms-builder-google-drive-folder-name-field-{{ data.connection.id }}">
		<?php esc_html_e( 'Google Drive Folder Name', 'wpforms-entry-automation' ); ?><span class="required">*</span>
	</label>

	<input id="wpforms-builder-google-drive-folder-name-field-{{ data.connection.id }}"
			type="text"
			class="js-wpforms-builder-google-drive-provider-connection-folder-name"
			name="settings[{{data.provider}}][{{ data.connection.id }}][google][folder_name]"
			placeholder="{{ data.formName }}"
			data-default="{{ data.formName }}"
			value="{{ data.formName }}">
</div>
