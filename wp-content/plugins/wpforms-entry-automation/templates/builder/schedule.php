<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$queue_label   = __( 'Run After Previous Task', 'wpforms-entry-automation' );
$queue_tooltip = __( 'Run this task after another task has completed execution.', 'wpforms-entry-automation' );

ob_start();
?>
<div class="wpforms-entry-automation-schedule-queue-settings {{ data.connection?.schedule?.queue ? '' : 'wpforms-hidden' }}">
	<input type="hidden" name="settings[{{data.provider}}][{{data.connection.id}}][schedule][queue_notice_display]" value="{{data.connection?.schedule?.queue_notice_display ?? '1' }}" class="wpforms-entry-automation-schedule-queue-notice-display"/>

	<# if ( data.connection?.schedule?.queue_notice_display !== '0' ) { #>
	<?php
		printf(
			'<div class="wpforms-alert wpforms-alert-info wpforms-entry-automation-alert wpforms-entry-automation-schedule-notice">
				<div class="wpforms-entry-automation-alert-content">
					<h4>%2$s</h4>
					<p>%3$s</p>
					<p>%4$s</p>
				</div>
				<button type="button" class="wpforms-dismiss-button" title="%1$s" data-connection-id="{{data.connection.id}}"></button>
			</div>',
			esc_html__( 'Dismiss this notice.', 'wpforms-entry-automation' ),
			esc_html__( 'Run Tasks in Sequence', 'wpforms-entry-automation' ),
			esc_html__( 'To avoid conflicts and keep your data safe, it’s recommended that you run tasks in sequence.', 'wpforms-entry-automation' ),
			esc_html__( 'This task has been added to the queue and will run after the previous task has completed.', 'wpforms-entry-automation' )
		);
	?>
	<# } #>
</div>

<div class="wpforms-panel-field wpforms-entry-automation-schedule-queue {{ data.connection?.isFirst ? 'wpforms-hidden' : '' }}">
	<span class="wpforms-toggle-control " >
		<input type="checkbox" id="wpforms-entry-automation-schedule-queue-{{data.connection.id}}" name="settings[{{data.provider}}][{{data.connection.id}}][schedule][queue]" class="wpforms-entry-automation-schedule-queue-toggle" value="1" <# if ( data.connection.schedule?.queue ) { #> checked="checked" <# } #> />
		<label class="wpforms-toggle-control-icon" for="wpforms-entry-automation-schedule-queue-{{data.connection.id}}"></label>
		<label for="wpforms-entry-automation-schedule-queue-{{data.connection.id}}" class="wpforms-toggle-control-label"><?php echo esc_html( $queue_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $queue_tooltip ); ?>"></i></label>
	</span>
</div>

<div class="wpforms-entry-automation-schedule-settings {{ data.connection?.schedule?.queue ? 'wpforms-hidden' : '' }}">
	<div class="wpforms-panel-field wpforms-entry-automation-schedule-range">
	<?php
	$start_date_label   = __( 'Start Date', 'wpforms-entry-automation' );
	$start_date_tooltip = __( 'Choose when this automated task should begin running.', 'wpforms-entry-automation' );
	?>
	<div class="wpforms-entry-automation-schedule-start-wrap wpforms-entry-automation-schedule-item-wrap">
		<label for="wpforms-entry-automation-schedule-start-{{data.connection.id}}">
			<?php echo esc_html( $start_date_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $start_date_tooltip ); ?>"></i>
		</label>

		<div class="wpforms-entry-automation-datepicker-wrap">
			<input
				type="text"
				id="wpforms-entry-automation-schedule-start-{{data.connection.id}}"
				name="settings[{{data.provider}}][{{data.connection.id}}][schedule][start]"
				value="{{data.connection.schedule?.start}}"
				data-type="start"
				data-id="{{data.connection.id}}"
			>
		</div>
	</div>

	<?php
	$end_date_label   = __( 'End Date', 'wpforms-entry-automation' );
	$end_date_tooltip = __( 'Optional. Choose when this automated task should stop running.', 'wpforms-entry-automation' );
	?>
	<div class="wpforms-entry-automation-schedule-end-wrap wpforms-entry-automation-schedule-item-wrap">
		<label for="wpforms-entry-automation-schedule-end-{{data.connection.id}}">
			<?php echo esc_html( $end_date_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $end_date_tooltip ); ?>"></i>
		</label>

		<div class="wpforms-entry-automation-datepicker-wrap">
			<input
				type="text"
				id="wpforms-entry-automation-schedule-end-{{data.connection.id}}"
				name="settings[{{data.provider}}][{{data.connection.id}}][schedule][end]"
				value="{{data.connection.schedule?.end}}"
				data-type="end"
				data-id="{{data.connection.id}}"
				placeholder="<?php esc_html_e( 'None', 'wpforms-entry-automation' ); ?>"
			>
		</div>
	</div>
</div>

	<?php
	$frequency_label   = __( 'Frequency', 'wpforms-entry-automation' );
	$frequency_tooltip = __( 'How often this task should run.', 'wpforms-entry-automation' );
	?>
	<div class="wpforms-panel-field wpforms-entry-automation-schedule-frequency-wrap">
		<label for="wpforms-entry-automation-schedule-frequency-{{data.connection.id}}">
			<?php echo esc_html( $frequency_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $frequency_tooltip ); ?>"></i>
		</label>
		<select
			name="settings[{{data.provider}}][{{data.connection.id}}][schedule][frequency]"
			id="wpforms-entry-automation-schedule-frequency-{{data.connection.id}}"
			class="wpforms-entry-automation-schedule-frequency"
		>
			<# _.each( data.frequencyOptions, function( fieldLabel, fieldValue ) { #>
				<option value="{{ fieldValue }}" <# if (data.connection?.schedule?.frequency === fieldValue) { #>selected="selected"<# } #>>
					{{ fieldLabel }}
				</option>
			<# } ) #>
		</select>
	</div>

	<div class="wpforms-panel-field wpforms-entry-automation-schedule-days"></div>

	<?php
	$time_label   = __( 'Time', 'wpforms-entry-automation' );
	$time_tooltip = __( 'Select the time when this task should run.', 'wpforms-entry-automation' );
	?>
	<div class="wpforms-panel-field wpforms-entry-automation-schedule-time">
		<label for="wpforms-entry-automation-schedule-time-{{data.connection.id}}">
			<?php echo esc_html( $time_label ); ?><i class="fa fa-question-circle-o wpforms-help-tooltip" title="<?php echo esc_attr( $time_tooltip ); ?>"></i>
		</label>

		<div class="wpforms-entry-automation-schedule-time-wrap">
			<select
				name="settings[{{data.provider}}][{{data.connection.id}}][schedule][time]"
				class="wpforms-entry-automation-schedule-time"
				id="wpforms-entry-automation-schedule-time-{{data.connection.id}}"
			>
				<# _.each( data.timeFormatOptions, function( field, index ) { #>
					<option value="{{ field.value }}" <# if (data.connection?.schedule?.time === field.value) { #>selected="selected"<# } #>>
						{{ field.label }}
					</option>
				<# } ) #>
			</select>
		</div>
	</div>
</div>

<?php
wpforms_panel_fields_group(
	ob_get_clean(),
	[
		'borders' => [ 'top' ],
		'class'   => 'wpforms-builder-entry-automation-group',
		'title'   => esc_html__( 'Schedule', 'wpforms-entry-automation' ),
	]
);
