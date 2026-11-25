<?php

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/* translators: %s: Connection ID. */
$connection_title = __( 'Connection ID: %s', 'wpforms-entry-automation' );

?>
<# const connectionTitle = '<?php echo esc_html( $connection_title ); ?>'.replace( '%s', data.connection.id ); #>
<div class="wpforms-builder-settings-block wpforms-builder-entity-connection" data-connection_id="{{ data.connection.id }}" data-block-type="entry_automation" data-block-id="{{ data.connection.id }}">
	<input type="hidden" class="wpforms-builder-entity-connection-id" name="settings[{{data.provider}}][{{ data.connection.id }}][id]" value="{{ data.connection.id }}">

	<div class="wpforms-builder-settings-block-header">
		<div class="wpforms-builder-settings-block-name-holder">
			<span class="wpforms-builder-entity-connection-block-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M32 288c-17.7 0-32 14.3-32 32s14.3 32 32 32l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 288zm0-128c-17.7 0-32 14.3-32 32s14.3 32 32 32l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 160z"/></svg>
			</span>
			<span class="wpforms-builder-settings-block-name wpforms-builder-entity-connection-title" title="{{ connectionTitle }}">
				{{ data.connection.name }}
			</span>

			<div class="wpforms-builder-settings-block-name-edit">
				<input
					type="text"
					name="settings[{{data.provider}}][{{ data.connection.id }}][name]"
					value="{{ data.connection.name }}"
					class="wpforms-builder-entity-connection-title-input"
				>
			</div>
			<button class="wpforms-builder-settings-block-edit" title="<?php esc_attr_e( 'Edit', 'wpforms-entry-automation' ); ?>"><i class="fa fa-pencil"></i></button>
		</div>

		<div class="wpforms-builder-settings-block-actions">
			<# if ( data.connection.status === '1' ) { #>
			<?php
				wpforms_connection_status_button(
					'{{data.connection.id}}',
					'settings[{{data.provider}}][{{ data.connection.id }}][status]',
					true
				);
			?>
			<# } else { #>
			<?php
				wpforms_connection_status_button(
					'{{data.connection.id}}',
					'settings[{{data.provider}}][{{ data.connection.id }}][status]',
					false
				);
			?>
			<# } #>

			<button class="wpforms-builder-entity-connection-clone" title="<?php esc_attr_e( 'Clone', 'wpforms-entry-automation' ); ?>">
				<i class="fa fa-copy"></i>
			</button>

			<button class="wpforms-builder-entity-connection-delete" title="<?php esc_attr_e( 'Delete', 'wpforms-entry-automation' ); ?>" type="button">
				<i class="fa fa-trash-o"></i>
			</button>

			<button class="wpforms-builder-settings-block-toggle" title="<?php esc_attr_e( 'Open / Close', 'wpforms-entry-automation' ); ?>">
				<i class="fa <# if ( data.connection?.state === 'closed' ) { #> fa-chevron-circle-down <# } else { #> fa-chevron-circle-up <# } #>"></i>
			</button>
		</div>
	</div>

	<div class="wpforms-builder-settings-block-content" <# if ( data.connection.state === 'closed' ) { #> style="display: none;" <# } #>>
		<div class="wpforms-entry-automation-task-summary {{ data.connection.summary ? '' : 'wpforms-hidden' }}" id="wpforms-entry-automation-task-summary-{{ data.connection.id }}">
			<div class="wpforms-entry-automation-task-summary-icon">
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M128 0c13.3 0 24 10.7 24 24l0 40 144 0 0-40c0-13.3 10.7-24 24-24s24 10.7 24 24l0 40 40 0c35.3 0 64 28.7 64 64l0 16 0 48 0 256c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 192l0-48 0-16C0 92.7 28.7 64 64 64l40 0 0-40c0-13.3 10.7-24 24-24zM400 192L48 192l0 256c0 8.8 7.2 16 16 16l320 0c8.8 0 16-7.2 16-16l0-256zM329 297L217 409c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47 95-95c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
				</span>
			</div>
			<div class="wpforms-entry-automation-task-summary-text">
				<span class="wpforms-entry-automation-task-summary-text-title">
					<?php esc_html_e( 'Task Summary', 'wpforms-entry-automation' ); ?>
				</span>
				<span class="wpforms-entry-automation-task--summary-text-value">
					{{ data.connection.summary }}
				</span>
			</div>
		</div>
		<div class="wpforms-panel-field wpforms-entry-automation-task-type">
			<div class="wpforms-larger-icon-choice">
				<input
					type="radio"
					id="wpforms-entry-automation-task-type-{{data.connection.id}}-export"
					name="settings[{{data.provider}}][{{data.connection.id}}][action]"
					value="export"
					<# if ( data.connection.action === 'export' ) { #> checked="checked" <# } #>
				>
				<label for="wpforms-entry-automation-task-type-{{data.connection.id}}-export">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM216 232l0 102.1 31-31c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-72 72c-9.4 9.4-24.6 9.4-33.9 0l-72-72c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l31 31L168 232c0-13.3 10.7-24 24-24s24 10.7 24 24z"/></svg>
					<span><?php esc_html_e( 'Export Entries', 'wpforms-entry-automation' ); ?></span>
				</label>
			</div>

			<div class="wpforms-larger-icon-choice">
				<input
					type="radio"
					id="wpforms-entry-automation-task-type-{{data.connection.id}}-delete"
					name="settings[{{data.provider}}][{{data.connection.id}}][action]"
					value="delete"
					<# if ( data.connection.action === 'delete' ) { #> checked="checked" <# } #>
				>
				<label for="wpforms-entry-automation-task-type-{{data.connection.id}}-delete">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M170.5 51.6L151.5 80l145 0-19-28.4c-1.5-2.2-4-3.6-6.7-3.6l-93.7 0c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80 368 80l48 0 8 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-8 0 0 304c0 44.2-35.8 80-80 80l-224 0c-44.2 0-80-35.8-80-80l0-304-8 0c-13.3 0-24-10.7-24-24S10.7 80 24 80l8 0 48 0 13.8 0 36.7-55.1C140.9 9.4 158.4 0 177.1 0l93.7 0c18.7 0 36.2 9.4 46.6 24.9zM80 128l0 304c0 17.7 14.3 32 32 32l224 0c17.7 0 32-14.3 32-32l0-304L80 128zm80 64l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg>
					<span><?php esc_html_e( 'Delete Entries', 'wpforms-entry-automation' ); ?></span>
				</label>
			</div>
		</div>

		<div class="wpforms-entry-automation-task-type-options"></div>
	</div>

</div>
