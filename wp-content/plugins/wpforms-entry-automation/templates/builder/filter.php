<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wpforms-panel-field wpforms-entry-automation-filter-field">
	<label for="wpforms-entry-automation-{{ data.connection.id }}-filter-field">
		<?php esc_html_e( 'Filter', 'wpforms-entry-automation' ); ?>
	</label>

	<div class="wpforms-entry-automation-filter-field-controls">
		<select
			id="wpforms-entry-automation-{{ data.connection.id }}-filter-field"
			name="settings[{{data.provider}}][{{ data.connection.id }}][filters][field]"
		>
			<# currentField = data.connection?.filters?.field; #>
			<optgroup label="<?php esc_html_e( 'Form Fields', 'wpforms-entry-automation' ); ?>">
				<option value="">
					<?php esc_html_e( 'Any Field', 'wpforms-entry-automation' ); ?>
				</option>
				<# _.each( data.fields, function( field, key ) { #>
					<option
						value="{{ field.id }}"
						<# if ( field.id.toString() === currentField?.toString() ) { #>selected="selected"<# } #>
					>
						<# if ( ! _.isUndefined( field.label ) && field.label.toString().trim() !== '' ) { #>
							{{ field.label.toString().trim() }}
						<# } else { #>
							{{ wpforms_builder.field + ' #' + key }}
						<# } #>
					</option>
				<# } ); #>
			</optgroup>

			<optgroup label="<?php esc_html_e( 'Advanced Options', 'wpforms-entry-automation' ); ?>">
				<# _.each( data.advancedFilterFields, function( field, key ) { #>
					<option
						value="{{ key }}"
						<# if ( key.toString() === currentField?.toString() ) { #>selected="selected"<# } #>
					>
						{{ field }}
					</option>
				<# } ); #>
			</optgroup>
		</select>

		<select
			class="wpforms-entry-automation-filter-operator"
			id="wpforms-entry-automation-{{ data.connection.id }}-filter-operator"
			name="settings[{{data.provider}}][{{ data.connection.id }}][filters][operator]"
		>
			<# currentOperator = data.connection?.filters?.operator; #>
			<# _.each( data.operators, function( operatorLabel, operator ) { #>
				<option
					value="{{ operator }}"
					<# if ( operator === currentOperator ) { #>selected="selected"<# } #>
				>
					{{ operatorLabel }}
				</option>
			<# } ); #>
		</select>
		<input
			type="text"
			name="settings[{{data.provider}}][{{ data.connection.id }}][filters][value]"
			value="{{data.connection.filters?.value}}"
		>
	</div>

</div>

<?php
$status_label   = __( 'Status', 'wpforms-entry-automation' );
$status_tooltip = __( 'Select which form entry statuses should be included in this task.', 'wpforms-entry-automation' );
?>
<div class="wpforms-panel-field wpforms-entry-automation-filter-status-field">
	<label for="wpforms-entry-automation-{{ data.connection.id }}-filter-status">
		<?php echo esc_html( $status_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $status_tooltip ); ?>"></i>
	</label>

	<select
		class="wpforms-entry-automation-filter-status choicesjs-select wpforms-required"
		id="wpforms-entry-automation-{{ data.connection.id }}-filter-status"
		name="settings[{{data.provider}}][{{ data.connection.id }}][filters][statuses][]"
		multiple
	>
		<# connectionData = data.connection?.filters?.statuses ?? [] #>
		<option value="" disabled>
			<?php esc_html_e( 'Select status', 'wpforms-entry-automation' ); ?>
		</option>
		<# _.each( data.statuses, function( statusLabel, status ) {
				selected = _.find( connectionData, ( statusItem ) => ( statusItem === status ) ) #>
				<option value="{{ status }}" <# if ( ! _.isEmpty( connectionData ) && selected ) { #> selected<# } #> >
					{{ statusLabel }}
				</option>
		<# } ) #>
	</select>
</div>
