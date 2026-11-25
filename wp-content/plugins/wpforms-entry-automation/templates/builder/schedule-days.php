<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<# _.each( data.frequencyDaysLabels, function( field, fieldValue ) { #>
	<# isCurrent = data.frequencyValue === fieldValue; #>
	<label data-frequensy-days-label="{{ fieldValue }}" class="{{ isCurrent ? '' : 'wpforms-hidden' }}">
		{{ field.label }}<i class="fa fa-question-circle-o wpforms-help-tooltip" title="{{ field.tooltip }}"></i>
	</label>
<# } ) #>
<div class="wpforms-chocolate-choices"></div>
