<?php /**
 * @version 1.0
 * @package  Booking Calendar
 * @category Booking Forms Templates
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2024-10-08
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit, if accessed directly           //FixIn: 10.6.2.1


// ---------------------------------------------------------------------------------------------------------------------
//  Templates 	for 	"Booking  Form"
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Get Booking Form Template - HTML content with Shortcodes
 *
 * @param string $template_name
 *
 * @return string
 */
function wpbc_get__booking_form__template( $template_name ) {

	$booking_form = '';

	switch ( $template_name ) {

		case 'standard':

            $booking_form = '[calendar] \n\
<div class="standard-form"> \n\
 <p>'.__('First Name (required)' ,'booking').':<br />[text* name] </p> \n\
 <p>'.__('Last Name (required)' ,'booking').':<br />[text* secondname] </p> \n\
 <p>'.__('Email (required)' ,'booking').':<br />[email* email] </p> \n\
 <p>'.__('Phone' ,'booking').':<br />[text phone] </p> \n\
 <p>'.__('Adults' ,'booking').':<br />[selectbox visitors "1" "2" "3" "4"] </p> \n\
 <p>'.__('Children' ,'booking').':<br />[selectbox children "0" "1" "2" "3"] </p> \n\
 <p>'.__('Details' ,'booking').':<br /> [textarea details] </p> \n\
 <p>[checkbox* term_and_condition use_label_element "'.__('I Accept term and conditions' ,'booking').'"] </p> \n\
 <p>[captcha]</p> \n\
 <p>[submit class:btn "'.__('Send' ,'booking').'"]</p> \n\
</div>';
            break;


		case '2_columns':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div" style="padding: 1em 0;"> \n\
	<r> \n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l> <div style="clear:both;width:100%"></div> \n\
			[textarea details] </c> \n\
	</r> \n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\	
	<p>[submit "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
	        break;


		case '2_columns_times_2_hours':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
      Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
<div class="wpbc__form__div" style="padding: 1em 0;"> \n\
    <r> \n\
		<c> <l>' . __( 'Select Date', 'booking' ) . ':</l><br />[calendar] </c> \n\
		<c> <l>' . __( 'Select Times', 'booking' ) . '*:</l><br /> \n\
			[selectbox* rangetime "10:00 AM - 12:00 PM@@10:00 - 12:00" "12:00 PM - 02:00 PM@@12:00 - 14:00" "02:00 PM - 04:00 PM@@14:00 - 16:00" "04:00 PM - 06:00 PM@@16:00 - 18:00" "06:00 PM - 08:00 PM@@18:00 - 20:00"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n\
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l> <div style="clear:both;width:100%"></div> \n\
			[textarea details] </c> \n\
	</r> \n\
	<p>[submit "' . __( 'Send', 'booking' ) . '"]</p> \n\
</div>';
	        break;


		case '2_columns_times_30_minutes_wizard':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step1"> \n\ 
		<r> \n\
			<c> <l>' . __( 'Select Date', 'booking' ) . ':</l><br />[calendar] </c> \n\ 
			<c> <l>' . __( 'Select Times', 'booking' ) . '*:</l><br /> \n\
				[selectbox rangetime "09:00 - 09:30" "09:30 - 10:00" "10:00 - 10:30" "10:30 - 11:00" "11:00 - 11:30" "11:30 - 12:00" "12:00 - 12:30" "12:30 - 13:00" "13:00 - 13:30" "13:30 - 14:00" "14:00 - 14:30" "14:30 - 15:00" "15:00 - 15:30" "15:30 - 16:00" "16:00 - 16:30" "16:30 - 17:00"] </c> \n\ 
		</r><hr> \n\ 
		<r> \n\
			<div class="wpbc__field" style="justify-content: flex-end"> \n\
     			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_2"> \n\ 
				Continue to step 2 \n\ 
				</a> \n\
			</div> \n\ 
		</r> \n\ 
</div> \n\
<div class="wpbc_wizard_step wpbc__form__div wpbc_wizard_step2 wpbc_wizard_step_hidden" style="clear:both"> \n\ 
	<r> \n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c> \n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c> \n\ 
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c> \n\ 
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c> \n\ 
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c> \n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c> \n\ 
	</r> \n\
	<r> \n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><spacer></spacer> \n\ 
			[textarea details] </c> \n\ 
	</r> \n\
	<spacer>height:10px;</spacer> \n\ 
	<hr> \n\
	<r> \n\
		<div class="wpbc__field" style="justify-content: flex-end"> \n\
			<a class="wpbc_button_light wpbc_wizard_step_button wpbc_wizard_step_1">Back to step 1</a>&nbsp;&nbsp;&nbsp; \n\ 
			[submit "' . __( 'Send', 'booking' ) . '"] \n\ 
		</div> \n\ 
	</r> \n\
</div>';
	        break;


		case '2_columns_hint_cost_nights':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c><div class="form-hints">\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>  \n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</div></c>\n\
	</r>\n\
	<hr> \n\
	<r>\n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Address (required)', 'booking' ) . ':</l><br />[text* address] </c>\n\
		<c> <l>' . __( 'City (required)', 'booking' ) . ':</l><br />[text* city] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Post code (required)', 'booking' ) . ':</l><br />[text* postcode] </c>\n\
		<c> <l>' . __( 'Country (required)', 'booking' ) . ':</l><br />[country] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr> \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;


		case '2_columns_hint_availability':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>  \n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
		</p></c>\n\ 
		<c><l>' . __( 'Availability', 'booking' ) . ':</l><spacer></spacer>[capacity_hint]</c> \n\   
	</r>\n\
	<hr>  \n\
	<r>\n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;


		case '2_columns_times_30_minutes_hints_coupon':

			$booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar] \n\
<r>\n\
	<c>  <l>' . __( 'Select Times', 'booking' ) . '*:</l><br />\n\
		[selectbox rangetime "9:00 AM - 9:30 AM@@09:00 - 09:30"  "9:30 AM - 10:00 AM@@09:30 - 10:00"  "10:00 AM - 10:30 AM@@10:00 - 10:30"  "10:30 AM - 11:00 AM@@10:30 - 11:00"  "11:00 AM - 11:30 AM@@11:00 - 11:30"  "11:30 AM - 12:00 PM@@11:30 - 12:00"  "12:00 PM - 12:30 PM@@12:00 - 12:30"  "12:30 PM - 1:00 PM@@12:30 - 13:00"  "1:00 PM - 1:30 PM@@13:00 - 13:30"  "1:30 PM - 2:00 PM@@13:30 - 14:00"  "2:00 PM - 2:30 PM@@14:00 - 14:30"  "2:30 PM - 3:00 PM@@14:30 - 15:00"  "3:00 PM - 3:30 PM@@15:00 - 15:30"  "3:30 PM - 4:00 PM@@15:30 - 16:00"  "4:00 PM - 4:30 PM@@16:00 - 16:30"  "4:30 PM - 5:00 PM@@16:30 - 17:00"  "5:00 PM - 5:30 PM@@17:00 - 17:30"  "5:30 PM - 6:00 PM@@17:30 - 18:00"  "6:00 PM - 6:30 PM@@18:00 - 18:30"]\n\
	</c>\n\
</r>\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c>
			<div>\n\
				' . __( 'Date', 'booking' ) . ': <strong>[selected_dates_hint]</strong><br> \n\  
				' . __( 'Times', 'booking' ) . ': <strong>[start_time_hint] - [end_time_hint]</strong><br>  \n\  
				' . __( 'Total cost', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
			</div>\n\ 
		</c>\n\ 
	</r>\n\ 
	<hr>  \n\
	<r>\n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;


		case '2_columns_hints_coupon':

            $booking_form  ='<!--  Simple HTML shortcodes in the form (check more at "Generate Tag" section): \n\
Row: <r>...</r> | Columns: <c>...</c> | Labels: <l>...</l> | Spacer: <spacer></spacer> --> \n\
[calendar]\n\
<div class="wpbc__form__div">\n\
	<r>\n\
		<c>
			<div>\n\
				' . __( 'Date', 'booking' ) . ': <strong>[selected_dates_hint]</strong><br> \n\  
				' . __( 'Times', 'booking' ) . ': <strong>[start_time_hint] - [end_time_hint]</strong><br>  \n\  
				' . __( 'Total cost', 'booking' ) . ': <strong>[cost_hint]</strong> \n\
			</div>\n\ 
		</c>\n\ 
	</r>\n\ 
	<hr>  \n\
	<r>\n\
		<c> <l>' . __( 'First Name (required)', 'booking' ) . ':</l><br />[text* name] </c>\n\
		<c> <l>' . __( 'Last Name (required)', 'booking' ) . ':</l><br />[text* secondname] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Email (required)', 'booking' ) . ':</l><br />[email* email] </c>\n\
		<c> <l>' . __( 'Phone', 'booking' ) . ':</l><br />[text phone] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Adults', 'booking' ) . ':</l><br />[selectbox visitors "1" "2" "3" "4" "5"] </c>\n\
		<c> <l>' . __( 'Children', 'booking' ) . ':</l><br />[selectbox children "0" "1" "2" "3"] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Details', 'booking' ) . ':</l><div style="clear:both;width:100%"></div>\n\
			[textarea details] </c>\n\
	</r>\n\
	<r>\n\
		<c> <l>' . __( 'Coupon (required)', 'booking' ) . ':</l><br />[coupon coupon] </c>\n\
	</r>\n\
	<div style="margin-top:10px;clear:both;"></div>\n\
	<r>\n\
		<c> [checkbox* term_and_condition use_label_element "' . __( 'I Accept term and conditions', 'booking' ) . '"] </c>\n\
		<c> [captcha] </c>\n\
	</r>\n\
	<r>\n\
		<c><p>\n\
			' . __( 'Dates', 'booking' ) . ': [selected_short_timedates_hint]  ([nights_number_hint] - ' . __( 'night(s)', 'booking' ) . ')<br>\n\
			' . __( 'Full cost of the booking', 'booking' ) . ': <strong>[cost_hint]</strong> <br>\n\
		</p></c>\n\
	</r> <hr>  \n\
	<r>\n\
		<c> <p>[submit "Send"]</p> </c> \n\
	</r> \n\
</div>';
	        break;
	}

	return $booking_form;
}


/**
 * Get "Content of booking fields data" - HTML content with Shortcodes
 *
 * @param string $template_name
 *
 * @return string
 */
function wpbc_get__booking_data__template( $template_name ) {

	$booking_form = '';

	switch ( $template_name ) {

		case 'standard':
		case '2_columns':

            $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
            break;

		case '2_columns_times_2_hours':
		case '2_columns_times_30_minutes_wizard':

            $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('Times' ,'booking').'</b>:      <f>[rangetime]</f><br/> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
			break;

		case '2_columns_hint_cost_nights':

            $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Address' ,'booking').'</b>:    <f>[address]</f><br/> \n\
    <b>'. __('City' ,'booking').'</b>:       <f>[city]</f><br/> \n\
    <b>'. __('Post code' ,'booking').'</b>:  <f>[postcode]</f><br/> \n\
    <b>'. __('Country' ,'booking').'</b>:    <f>[country]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
	        break;

		case '2_columns_hint_availability':
		case '2_columns_hints_coupon':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Coupon' ,'booking').'</b>:     <f>[coupon]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
	        break;

		case '2_columns_times_30_minutes_hints_coupon':
            $booking_form = '<div class="standard-content-form"> \n\
    <b>'. __('Times' ,'booking').'</b>:      <f>[rangetime]</f><br/> \n\
    <b>'. __('First Name' ,'booking').'</b>: <f>[name]</f><br/> \n\
    <b>'. __('Last Name' ,'booking').'</b>:  <f>[secondname]</f><br/> \n\
    <b>'. __('Email' ,'booking').'</b>:      <f>[email]</f><br/> \n\
    <b>'. __('Phone' ,'booking').'</b>:      <f>[phone]</f><br/> \n\
    <b>'. __('Adults' ,'booking').'</b>:     <f>[visitors]</f><br/> \n\
    <b>'. __('Children' ,'booking').'</b>:   <f>[children]</f><br/> \n\
    <b>'. __('Coupon' ,'booking').'</b>:     <f>[coupon]</f><br/> \n\
    <b>'. __('Details' ,'booking').'</b>:    <f>[details]</f> \n\
</div>';
 	        break;
	}

	return $booking_form;
}