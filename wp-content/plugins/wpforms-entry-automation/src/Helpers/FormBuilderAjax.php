<?php

namespace WPFormsEntryAutomation\Helpers;

/**
 * Handles AJAX requests in the form builder.
 *
 * @since 1.0.0
 */
class FormBuilderAjax {

	/**
	 * Represents the namespace slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $namespace_slug;

	/**
	 * Class constructor for initializing the object with a namespace slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $namespace_slug The namespace slug to be assigned.
	 */
	public function __construct( string $namespace_slug ) {

		$this->namespace_slug = $namespace_slug;
	}

	/**
	 * Handles AJAX requests for the form builder.
	 * Registers an AJAX action specific to the builder namespace.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {

		// Process builder AJAX requests.
		add_action( "wp_ajax_wpforms_builder_ajax_{$this->namespace_slug}", [ $this, 'process_ajax' ] );
	}

	/**
	 * Process the Builder AJAX requests.
	 *
	 * @since 1.0.0
	 */
	public function process_ajax(): void {

		$this->verify_request();

		$form_id   = $this->get_form_id();
		$task      = $this->get_ajax_task();
		$revisions = wpforms()->obj( 'revisions' );
		$revision  = $revisions ? $revisions->get_revision() : null;

		if ( $revision ) {
			// Setup form data based on the revision_id that we got from AJAX request.
			$form_data = wpforms_decode( $revision->post_content );
		} else {
			// Setup form data based on the ID that we got from AJAX request.
			$form_handler = wpforms()->obj( 'form' );
			$form_data    = $form_handler ? $form_handler->get( $form_id, [ 'content_only' => true ] ) : [];
		}

		// Do not allow proceeding further, as form_id may be incorrect.
		if ( empty( $form_data ) ) {
			wp_send_json_error( $this->get_default_error() );
		}

		$this->process_form_data( $task, $form_data );
	}

	/**
	 * Processes the form data and handles the AJAX response.
	 *
	 * Applies a filter to process the form data and responds with a success
	 * or error message based on the data returned from the filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $task      The AJAX task to be processed.
	 * @param array  $form_data The form data array to be processed.
	 */
	private function process_form_data( string $task, array $form_data ): void {

		/**
		 * Runs when an ajax task successfully verified.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed $response  The response array.
		 * @param array $form_data The form data array.
		 */
		$data = apply_filters( "wpforms_builder_ajax_{$this->namespace_slug}_{$task}", null, $form_data ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

		if ( ! empty( $data['error_msg'] ) ) {
			wp_send_json_error( [ 'error_msg' => $data['error_msg'] ] );
		}

		if ( $data !== null ) {
			wp_send_json_success( $data );
		}

		wp_send_json_error( $this->get_default_error() );
	}

	/**
	 * Verifies the incoming request by performing security and permission checks.
	 *
	 * @since 1.0.0
	 */
	private function verify_request(): void {
		// Run a security check.
		check_ajax_referer( 'wpforms-builder', 'nonce' );

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission to perform this action.', 'wpforms-entry-automation' ),
				]
			);
		}

		if (
			empty( $_POST['id'] ) ||
			empty( $_POST['task'] )
		) {
			wp_send_json_error( $this->get_default_error() );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! wpforms_current_user_can( 'edit_form_single', absint( $_POST['id'] ) ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission to edit this form.', 'wpforms-entry-automation' ),
				]
			);
		}
	}

	/**
	 * Retrieves and validates the form ID from the POST request.
	 *
	 * @since 1.0.0
	 *
	 * @return int The sanitized form ID.
	 */
	private function get_form_id(): int {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$form_id = absint( $_POST['id'] ?? '' );

		if ( ! $form_id ) {
			wp_send_json_error( $this->get_default_error() );
		}

		return $form_id;
	}

	/**
	 * Retrieves the AJAX task from the incoming request.
	 *
	 * This method sanitizes and validates the task parameter from the POST request.
	 * If the task parameter is not provided or invalid, it sends a JSON error response.
	 *
	 * @since 1.0.0
	 *
	 * @return string The sanitized task value from the request.
	 */
	private function get_ajax_task(): string {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$task = sanitize_key( $_POST['task'] ?? '' );

		if ( ! $task ) {
			wp_send_json_error( $this->get_default_error() );
		}

		return $task;
	}

	/**
	 * Retrieves the default error message to be used for AJAX request failures.
	 *
	 * @since 1.0.0
	 *
	 * @return array An associative array containing the default error message.
	 */
	private function get_default_error(): array {

		return [
			'error' => esc_html__(
				'Something went wrong while performing an AJAX request.',
				'wpforms-entry-automation'
			),
		];
	}
}
