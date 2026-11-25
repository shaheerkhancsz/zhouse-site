<?php

namespace WPFormsEntryAutomation\Export\Provider;

use WPForms\Emails\Helpers as EmailHelpers;
use WPForms\Emails\Mailer;
use WPForms_WP_Emails;
use WPFormsEntryAutomation\Plugin;

/**
 * Email provider implementation.
 *
 * @since 1.0.0
 */
class Email extends Provider {

	/**
	 * Get a human-readable title of the provider.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider title.
	 */
	public function get_title(): string {

		return esc_html__( 'Email', 'wpforms-entry-automation' );
	}

	/**
	 * Deliver formatted entries via email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entries_file_path Formatted entries.
	 * @param array  $connection        Connection data.
	 * @param array  $form_data         Form data.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function deliver( string $entries_file_path, array $connection, array $form_data ): bool {

		$template_name  = EmailHelpers::get_current_template_name();
		$template_class = EmailHelpers::get_current_template_class( $template_name );

		$template = ( new $template_class() );

		$field_template = '';

		if ( method_exists( $template, 'get_field_template' ) ) {
			$field_template = $template->get_field_template();
		}

		if ( $template_name === 'default' ) {
			$legacy_template = new WPForms_WP_Emails();

			ob_start();

			$legacy_template->get_template_part( 'field', $template_name );

			$field_template = ob_get_clean();
		}

		// Get email settings.
		$to_email = $this->get_to_email( $connection, $form_data );
		$subject  = $this->get_subject( $form_data['settings']['form_title'] );
		$message  = $this->get_message( $connection, $form_data, $field_template );

		$args = [
			'body' => [
				'message' => $message,
			],
		];

		$template->set_args( $args );

		$emails = explode( ',', $to_email );
		$emails = array_map( 'sanitize_email', $emails );
		$emails = array_filter( $emails );

		if ( empty( $emails ) ) {
			wpforms_log(
				'Entry Automation - No email address provided',
				[
					'to_email' => $to_email,
					'subject'  => $subject,
					'message'  => $message,
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);

			return false;
		}

		// Send email.
		$result = $this->send_email( $emails, $subject, $template, $entries_file_path );

		// Cleanup attachment.
		wp_delete_file( $entries_file_path );

		if ( ! $result ) {
			wpforms_log(
				'Entry Automation - Email delivery failed',
				[
					'to_email'  => $to_email,
					'subject'   => $subject,
					'message'   => $message,
					'file_path' => $entries_file_path,
				],
				[
					'type'    => 'error',
					'form_id' => absint( $form_data['id'] ),
				]
			);
		}

		return $result;
	}

	/**
	 * Checks if a file with the given name exists using the provided connection and task data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  The name of the file to check for existence.
	 * @param array  $task_data  An array containing task-specific data used during the operation.
	 * @param array  $connection An array containing the connection details required to perform the operation.
	 *
	 * @return bool Returns true if the file exists, otherwise false.
	 */
	public function is_file_exist( string $file_name, array $task_data, array $connection ): bool {

		return false;
	}

	/**
	 * Sanitizes the provided connection data based on the form data.
	 *
	 * Removes email-related data if the export destination is not set to email.
	 * If the export destination is email, ensures the email address is sanitized.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data An array containing the connection data to be sanitized. Passed by reference.
	 * @param array $form_data        An array of form data that may influence the sanitization process.
	 */
	public function sanitize_connection( array &$connection_data, array $form_data ): void {

		$connection_data['email']            = $connection_data['email'] ?? [];
		$connection_data['email']['address'] = $connection_data['email']['address'] ?? '';
		$connection_data['email']['address'] = sanitize_text_field( $connection_data['email']['address'] );
	}

	/**
	 * Get the recipient email address.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data Task data.
	 * @param array $form_data Form data.
	 *
	 * @return string Recipient email address.
	 */
	private function get_to_email( array $task_data, array $form_data ): string {

		return wpforms_process_smart_tags( $task_data['email']['address'], $form_data );
	}

	/**
	 * Get an email subject.
	 *
	 * @since 1.0.0
	 *
	 * @param string $form_name Name of the form.
	 *
	 * @return string Email subject.
	 */
	private function get_subject( string $form_name ): string {

		return sprintf(
			/* translators: %1$s - task name. */
			esc_html__( 'Entries Export: %1$s', 'wpforms-entry-automation' ),
			$form_name
		);
	}

	/**
	 * Get an email message.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $connection     Connection data.
	 * @param array  $form_data      Form data.
	 * @param string $field_template Template for the fields in the email.
	 *
	 * @return string Email message.
	 */
	private function get_message( array $connection, array $form_data, string $field_template ): string {

		$message = esc_html__( 'Please find attached the entries export.', 'wpforms-entry-automation' );
		$summary = $form_data['settings'][ Plugin::SLUG ][ $connection['id'] ]['summary'] ?? '';

		// If the connection has a parent ID, use the parent's schedule and summary.
		if ( ! empty( $connection['parent_id'] ) ) {
			$connection['schedule'] = $this->get_root_task_schedule( $connection, $form_data );

			$summary = wpforms_entry_automation()->get( 'builder' )->get_connection_summary( $connection );
		}

		$fields = [
			[
				'name'  => esc_html__( 'Form', 'wpforms-entry-automation' ),
				'value' => wpforms_process_smart_tags( '{form_name}', $form_data ),
				'type'  => 'text',
			],
			[
				'name'  => esc_html__( 'Date', 'wpforms-entry-automation' ),
				'value' => date_i18n( 'F jS, Y' ),
				'type'  => 'text',
			],
			[
				'name'  => esc_html__( 'Task Summary', 'wpforms-entry-automation' ),
				'value' => $summary,
				'type'  => 'text',
			],
		];

		foreach ( $fields as $field ) {
			$message .= str_replace(
				[ '{field_type}', '{field_name}', '{field_value}' ],
				[ $field['type'], $field['name'], $field['value'] ],
				$field_template
			);
		}

		return $message;
	}

	/**
	 * Retrieves the schedule for the root task by recursively checking the parent tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param array $connection An array representing the current task data. This is passed by reference to track the schedule and parent ID.
	 * @param array $form_data  An array containing form-related data, including task settings and configurations.
	 *
	 * @return array The schedule of the root task. Returns an empty array if no schedule is found.
	 */
	private function get_root_task_schedule( array $connection, array $form_data ): array {
		// If there's no parent_id, this is the root task, so return its schedule.
		if ( empty( $connection['parent_id'] ) ) {
			return $connection['schedule'] ?? [];
		}

		// Get the parent task data.
		$parent_id   = $connection['parent_id'];
		$parent_task = $form_data['settings'][ Plugin::SLUG ][ $parent_id ] ?? null;

		// If a parent task not found, return the current schedule as fallback.
		if ( empty( $parent_task ) ) {
			return $connection['schedule'] ?? [];
		}

		// Recursively find the root task.
		return $this->get_root_task_schedule( $parent_task, $form_data );
	}

	/**
	 * Send email with an attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $emails     Recipient email addresses.
	 * @param string $subject    Email subject.
	 * @param object $template   Email template.
	 * @param string $attachment Attachment file path.
	 *
	 * @return bool True on success, false on failure.
	 */
	private function send_email( array $emails, string $subject, object $template, string $attachment ): bool {

		$mailer = ( new Mailer() )
			->template( $template )
			->subject( $subject )
			->to_email( $emails );

		$mailer->__set( 'attachments', [ $attachment ] );

		return $mailer->send();
	}

	/**
	 * Downloads a file using the provided file ID and connection details.
	 * Email provider does not support file downloads.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An array containing task-specific data used during the download operation.
	 * @param array $connection The connection details required to access the file.
	 *
	 * @return string The contents of the downloaded file.
	 */
	public function download_file( array $task_data, array $connection ): string {

		return '';
	}

	/**
	 * Deletes a file with the specified ID using the provided connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An array containing task-specific data used during the delete operation.
	 * @param array $connection An array containing the connection details required to perform the delete operation.
	 */
	public function delete_file( array $task_data, array $connection ): void {
		// Email provider does not support file deletion.
	}
}
