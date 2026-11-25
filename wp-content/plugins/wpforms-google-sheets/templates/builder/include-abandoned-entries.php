<?php
/**
 * Include abandoned entries toggle.
 *
 * @since 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="wpforms-panel-field-{{ data.provider }}-include_abandoned_entries-wrap-{{ data.connection_id }}" class="wpforms-panel-field  wpforms-panel-field-toggle">
	<span class="wpforms-toggle-control">
		<input
			type="checkbox"
			id="wpforms-panel-field-{{ data.provider }}-include_abandoned_entries-{{ data.connection_id }}"
			name="providers[{{ data.provider }}][{{ data.connection_id }}][include_abandoned_entries]"
			value="1"
			<# if ( data.isChecked ) { #>
				checked="checked"
			<# } #>
		>
		<label class="wpforms-toggle-control-icon" for="wpforms-panel-field-{{ data.provider }}-include_abandoned_entries-{{ data.connection_id }}"></label>
		<label for="wpforms-panel-field-{{ data.provider }}-include_abandoned_entries-{{ data.connection_id }}" class="wpforms-toggle-control-label">
			<?php esc_html_e( 'Include Abandoned Entries', 'wpforms-google-sheets' ); ?>
		</label>
	</span>
</div>
