<?php

namespace WPFormsSlack\Provider\Actions;

use RuntimeException;

/**
 * Class SendChannelMessageAction.
 *
 * @since 1.0.0
 */
class SendChannelMessageAction extends Action {

	/**
	 * Run action.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException Invalid response.
	 */
	public function run() {

		$message  = $this->field_mapper->get_required_field_value( 'message' );
		$channels = $this->field_mapper->get_not_required_field_value( 'channels' );

		if ( wpforms_is_empty_string( $message ) ) {
			return;
		}

		if ( empty( $channels ) ) {
			return;
		}

		$message = wpforms_process_smart_tags( $message, $this->form_data, $this->fields, $this->field_mapper->get_entry_id(), 'slack-channel-message' );
		$message = html_entity_decode( $message, ENT_QUOTES, 'UTF-8' );

		foreach ( $channels as $channel ) {
			$send_channel_message = $this->api->send_message( $channel, $message );

			if ( $send_channel_message->has_errors() ) {
				throw new RuntimeException( esc_html( $send_channel_message->get_response_message() ), 400 );
			}
		}
	}
}
