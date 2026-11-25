<?php

namespace WPFormsEntryAutomation\Export\Formatter;

use WPFormsEntryAutomation\Export\FormatterManager;

/**
 * Abstract formatter class.
 *
 * @since 1.0.0
 */
abstract class Formatter {

	/**
	 * Formatter manager instance.
	 *
	 * @since 1.0.0
	 *
	 * @var FormatterManager
	 */
	protected $manager;

	/**
	 * Constructor for initializing the FormatterManager.
	 *
	 * @since 1.0.0
	 *
	 * @param FormatterManager $manager Instance of the FormatterManager.
	 */
	public function __construct( FormatterManager $manager ) {

		$this->manager = $manager;
	}

	/**
	 * Format the entries based on the provided data.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $entries         Entries to be formatted.
	 * @param array  $task_data       Task data related to the formatting process.
	 * @param array  $form_data       Form data associated with the entries.
	 * @param array  $connection      Connection data for the formatting context.
	 * @param string $exist_file_path Optional. Existing file path, if applicable.
	 *
	 * @return string Temporary file name containing the generated data.
	 */
	public function format( array $entries, array $task_data, array $form_data, array $connection, string $exist_file_path = '' ): string {

		if ( empty( $entries ) ) {
			return '';
		}

		// Make sure the core export settings are initialized before formatting.
		$this->maybe_init_entries_export();

		// Format the entries based on the specific implementation.
		return $this->format_entries( $entries, $form_data, $task_data, $connection, $exist_file_path );
	}

	/**
	 * Make sure the core entries export settings are initialized.
	 *
	 * Action Scheduler may dispatch a task via WP-Cron, its async loopback request,
	 * WP-CLI, or the admin Scheduled Actions screen. Depending on the context, the
	 * core Export::init() either bailed early (leaving the settings empty) or built
	 * the settings before the provider addons registered their export hooks — so
	 * the settings are (re)initialized here, right before formatting.
	 *
	 * @since 1.3.0
	 */
	private function maybe_init_entries_export(): void {

		$export = wpforms()->obj( 'entries_export' );

		if ( $export && method_exists( $export, 'maybe_init_settings' ) ) {
			$export->maybe_init_settings();
		}
	}

	/**
	 * Format entries based on provided data and parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $entries         Entry data to be formatted.
	 * @param array  $form_data       Data related to the form.
	 * @param array  $task_data       Information about the task being executed.
	 * @param array  $connection      Connection details for processing.
	 * @param string $exist_file_path Optional existing file path for output or reference.
	 */
	abstract protected function format_entries( array $entries, array $form_data, array $task_data, array $connection, string $exist_file_path = '' ): string;

	/**
	 * Get the formatter's human-readable title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Formatter title.
	 */
	abstract public function get_title(): string;

	/**
	 * Get the file extension for the formatter.
	 *
	 * @since 1.0.0
	 *
	 * @return string File extension.
	 */
	abstract public function get_file_extension(): string;

	/**
	 * Get the file name for the formatted entries.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name      File name.
	 * @param array  $form_data Form data.
	 *
	 * @return string Formatted file name.
	 */
	public function get_file_name( string $name, array $form_data ): string {

		if ( empty( $name ) ) {
			$name = __( 'Entries', 'wpforms-entry-automation' );
		}

		return sprintf( '%1$s.%2$s', sanitize_file_name( wpforms_process_smart_tags( $name, $form_data ) ), $this->get_file_extension() );
	}
}
