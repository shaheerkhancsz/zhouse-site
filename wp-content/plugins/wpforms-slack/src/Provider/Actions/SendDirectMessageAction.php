<?php

namespace WPFormsSlack\Provider\Actions;

use RuntimeException;

/**
 * Class SendMessageAction.
 *
 * @since 1.0.0
 */
class SendDirectMessageAction extends Action {

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

		$users = $this->field_mapper->get_not_required_field_value( 'users' );

		if ( empty( $users ) ) {
			return;
		}

		$message = html_entity_decode( wpforms_process_smart_tags( $message, $this->form_data, $this->fields, $this->field_mapper->get_entry_id(), 'slack-direct-message' ), ENT_QUOTES, 'UTF-8' );

		foreach ( $users as $user ) {
			$send_direct_message = $this->api->send_message( $user, $message );

			if ( $send_direct_message->has_errors() ) {
				throw new RuntimeException( esc_html( $send_direct_message->get_response_message() ), 400 );
			}
		}
	}
}
