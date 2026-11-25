<?php

namespace WPFormsSlack\Provider\Actions;

use RuntimeException;

/**
 * Class SetReminderAction.
 *
 * @since 1.0.0
 */
class SetReminderAction extends Action {

	/**
	 * Run action.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException Invalid response.
	 */
	public function run() {

		$message = $this->field_mapper->get_required_field_value( 'message' );

		if ( wpforms_is_empty_string( $message ) ) {
			return;
		}

		$time = sanitize_text_field( $this->field_mapper->get_required_field_value( 'time' ) );

		if ( empty( $time ) ) {
			return;
		}

		$message = html_entity_decode( wpforms_process_smart_tags( $message, $this->form_data, $this->fields, $this->field_mapper->get_entry_id(), 'slack-reminder-message' ) );

		$set_reminder = $this->api->set_reminder( $message, $time );

		if ( $set_reminder->has_errors() ) {
			throw new RuntimeException( esc_html( $set_reminder->get_response_message() ), 400 );
		}
	}
}
