<?php

namespace WPFormsEntryAutomation\Export\Provider;

/**
 * Abstract Provider class for delivery methods.
 *
 * @since 1.0.0
 */
abstract class Provider {

	/**
	 * Deliver formatted entries.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entries_file_path Formatted entries.
	 * @param array  $connection        Connection data.
	 * @param array  $form_data         Form data.
	 *
	 * @return bool True on success, false on failure.
	 */
	abstract public function deliver( string $entries_file_path, array $connection, array $form_data ): bool;

	/**
	 * Downloads a file using the provided file ID and connection details.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An array containing task-specific data used during the download operation.
	 * @param array $connection The connection details required to access the file.
	 *
	 * @return string The contents of the downloaded file.
	 */
	abstract public function download_file( array $task_data, array $connection ): string;

	/**
	 * Deletes a file with the specified ID using the provided connection.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An array containing task-specific data used during the delete operation.
	 * @param array $connection An array containing the connection details required to perform the delete operation.
	 */
	abstract public function delete_file( array $task_data, array $connection ): void;

	/**
	 * Checks if the specified file exists using the provided connection and task data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  The name of the file to check for existence.
	 * @param array  $task_data  An array of task-specific data used during the operation.
	 * @param array  $connection An array containing the connection details required to check file existence.
	 *
	 * @return bool True if the file exists, false otherwise.
	 */
	abstract public function is_file_exist( string $file_name, array $task_data, array $connection ): bool;

	/**
	 * Get a human-readable title of the provider.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider title.
	 */
	abstract public function get_title(): string;

	/**
	 * Retrieves the list of custom templates.
	 *
	 * @since 1.0.0
	 *
	 * @return array The custom templates.
	 */
	public function get_custom_templates(): array {

		return [];
	}

	/**
	 * Sanitizes the connection data by processing and validating the provided form data.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data Reference to the connection data array that will be sanitized.
	 * @param array $form_data        An array of form data used for sanitizing the connection details.
	 */
	public function sanitize_connection( array &$connection_data, array $form_data ): void {
	}

	/**
	 * Resolve submitted connection data the current user isn't allowed to change.
	 *
	 * Runs for every connection before it is sanitized, whatever destination the connection
	 * was saved with, and has to stay idempotent across repeated saves.
	 *
	 * @since 1.4.0
	 *
	 * @param array $connection      Connection data being saved.
	 * @param array $prev_connection Previously stored data of the same connection.
	 *
	 * @return array
	 */
	public function restrict_connection( array $connection, array $prev_connection ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		return $connection;
	}

	/**
	 * Registers or executes hooks for the implementation.
	 *
	 * @since 1.0.0
	 */
	public function hooks(): void {}

	/**
	 * Increment the file name by appending a timestamp.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name The original file name.
	 *
	 * @return string The incremented file name with a timestamp.
	 */
	public static function increment_file_name( string $file_name ): string {

		return pathinfo( $file_name, PATHINFO_FILENAME ) . '-' . time() . '.' . pathinfo( $file_name, PATHINFO_EXTENSION );
	}
}
