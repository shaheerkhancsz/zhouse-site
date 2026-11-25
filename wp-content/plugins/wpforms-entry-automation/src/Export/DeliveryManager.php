<?php

namespace WPFormsEntryAutomation\Export;

use WPFormsEntryAutomation\Export\Provider\AddonProviderInterface;
use WPFormsEntryAutomation\Export\Provider\Dropbox;
use WPFormsEntryAutomation\Export\Provider\Email;
use WPFormsEntryAutomation\Export\Provider\FTP;
use WPFormsEntryAutomation\Export\Provider\GoogleDrive;
use WPFormsEntryAutomation\Plugin;

/**
 * Class DeliveryManager.
 *
 * @since 1.0.0
 */
class DeliveryManager {

	/**
	 * Available delivery methods.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $delivery_methods;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->delivery_methods = [
			'email'   => new Email(),
			'ftp'     => new FTP(),
			'dropbox' => new Dropbox(),
			'google'  => new GoogleDrive(),
		];
	}

	/**
	 * Executes the hooks for all registered delivery methods.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		$methods = $this->get_delivery_methods();

		foreach ( $methods as $method ) {
			$method->hooks();
		}
	}

	/**
	 * Resolve submitted connection data the current user isn't allowed to change.
	 *
	 * Every provider sees every connection, so data belonging to one destination can't be
	 * smuggled in through another.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form_data Form data being saved.
	 * @param array $prev_form Previously stored form data.
	 *
	 * @return array
	 */
	public function restrict_connections( array $form_data, array $prev_form ): array {

		$connections = $form_data['settings'][ Plugin::SLUG ] ?? [];

		if ( ! is_array( $connections ) ) {
			return $form_data;
		}

		$prev_connections = (array) ( $prev_form['settings'][ Plugin::SLUG ] ?? [] );
		$delivery_methods = $this->get_delivery_methods();

		foreach ( $connections as $connection_id => $connection ) {
			if ( ! is_array( $connection ) ) {
				continue;
			}

			$prev_connection = (array) ( $prev_connections[ $connection_id ] ?? [] );

			foreach ( $delivery_methods as $delivery_method ) {
				$connection = $delivery_method->restrict_connection( $connection, $prev_connection );
			}

			$form_data['settings'][ Plugin::SLUG ][ $connection_id ] = $connection;
		}

		return $form_data;
	}

	/**
	 * Get available delivery methods.
	 *
	 * @since 1.0.0
	 *
	 * @return Provider\Provider[]
	 */
	public function get_delivery_methods(): array {

		/**
		 * Filter the available delivery methods.
		 *
		 * @since 1.0.0
		 *
		 * @param array $delivery_methods Delivery methods.
		 */
		return (array) apply_filters( 'wpforms_entry_automation_export_delivery_manager_methods', $this->delivery_methods );
	}

	/**
	 * Get addons data for education modal.
	 *
	 * @since 1.0.0
	 */
	public function get_builder_data(): array {

		$education_addons = [];
		$activated_addons = [];
		$labels           = [];

		foreach ( $this->get_delivery_methods() as $key => $provider ) {
			$labels[ $key ] = $provider->get_title();

			if ( ! is_a( $provider, AddonProviderInterface::class ) ) {
				continue;
			}

			$education_addons[ $key ] = $this->prepare_addon_data( $provider->get_data() );

			if ( $provider->is_activated() ) {
				$activated_addons[] = $key;
			}
		}

		return [
			'labels'          => $labels,
			'educationAddons' => $education_addons,
			'activatedAddons' => $activated_addons,
		];
	}

	/**
	 * Prepare addon data for education modal.
	 *
	 * @since 1.0.0
	 *
	 * @param array $addon Addon data.
	 *
	 * @return array Prepared addon data.
	 */
	private function prepare_addon_data( array $addon ): array {

		if ( ! isset( $addon['slug'] ) && ! isset( $addon['name'] ) ) {
			return [];
		}

		return [
			'name'        => $addon['name'],
			'slug'        => $addon['slug'],
			'action'      => $addon['action'],
			'path'        => $addon['path'],
			'url'         => $addon['url'],
			'nonce'       => $addon['nonce'],
			'video'       => $addon['video'],
			'license'     => $addon['license_level'],
			'utm-content' => $addon['utm_content'],
			'message'     => $addon['message'],
		];
	}

	/**
	 * Checks if a given delivery method is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method The delivery method to validate.
	 *
	 * @return bool True if the delivery method is valid, false otherwise.
	 */
	public function is_valid_delivery_method( string $method ): bool {

		$methods = $this->get_delivery_methods();

		return isset( $methods[ $method ] );
	}

	/**
	 * Get a specific delivery method instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method Delivery method key.
	 *
	 * @return Provider\Provider Delivery method instance.
	 */
	public function get_delivery( string $method ): Provider\Provider {

		$delivery_methods = $this->get_delivery_methods();

		return $delivery_methods[ $method ];
	}

	/**
	 * Retrieves custom templates from all available delivery method providers.
	 * Gathers and merges custom templates provided by the delivery method providers.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of custom templates.
	 */
	public function get_custom_templates(): array {

		$delivery_methods = $this->get_delivery_methods();
		$templates        = [];

		foreach ( $delivery_methods as $provider ) {
			$templates[] = $provider->get_custom_templates();
		}

		return array_merge( ...$templates );
	}

	/**
	 * Deliver entries.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $connection Connection data.
	 * @param string $file_path  File path.
	 * @param array  $form_data  Form data.
	 *
	 * @return bool
	 */
	public function deliver_entries( array $connection, string $file_path, array $form_data ): bool {

		$method = $connection['export_to'] ?? '';

		if ( ! $this->is_valid_delivery_method( $method ) ) {
			return false;
		}

		return $this->get_delivery( $method )->deliver( $file_path, $connection, $form_data );
	}

	/**
	 * Sanitizes the connection data using defined delivery methods.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data Reference to the connection data that will be sanitized.
	 * @param array $form_data        The form data used for sanitization.
	 */
	public function sanitize_connection_data( array &$connection_data, array $form_data ): void {

		$delivery_methods = $this->get_delivery_methods();
		$export_to        = $connection_data['export_to'] ?? '';
		$delivery_method  = $delivery_methods[ $export_to ] ?? null;

		if ( ! ( $delivery_method instanceof Provider\Provider ) ) {
			return;
		}

		// If the delivery method is an addon, check if it's activated.
		if ( $delivery_method instanceof AddonProviderInterface && ! $delivery_method->is_activated() ) {
			$this->set_default_connection_data( $connection_data );

			return;
		}

		// Do the main sanitization.
		$delivery_method->sanitize_connection( $connection_data, $form_data );
	}

	/**
	 * Manages an existing file based on the provided task data and connection details.
	 * Handles scenarios such as overwriting the file, appending to it, or doing nothing.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  The name of the file to manage.
	 * @param array  $task_data  The data related to the task, including destination and export method.
	 * @param array  $connection Connection details, including handling for duplicate file scenarios.
	 *
	 * @return array An array containing the file path or name based on the management action taken.
	 */
	public function manage_existing_file( string $file_name, array $task_data, array $connection ): array {

		$on_duplicate = $connection['on_duplicate'] ?? 'inc';
		$method       = $this->get_delivery( $task_data['export_to'] );

		// Return incremented file name if the file already exists.
		if ( $on_duplicate === 'inc' ) {
			return [
				'file_name' => $method::increment_file_name( $file_name ),
			];
		}

		// Download file: return the existing file name.
		if ( $on_duplicate === 'add' ) {
			return [
				'file_path' => $method->download_file( $task_data, $connection ),
			];
		}

		// We should skip overwriting (delete and add a new file) file at this point
		// since we can't be sure that the new file will be uploaded successfully.
		return [];
	}

	/**
	 * Manages an existing file after performing a task.
	 *
	 * This method handles the process of overwriting a file by deleting the existing file
	 * and uploading a new one, based on the specified connection settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $task_data  An array containing task-specific data.
	 * @param array $connection An array containing connection-related settings.
	 */
	public function manage_existing_file_after( array $task_data, array $connection ): void {

		$on_duplicate = $connection['on_duplicate'] ?? 'inc';

		if ( ! in_array( $on_duplicate, [ 'over', 'add' ], true ) ) {
			return;
		}

		$method = $this->get_delivery( $task_data['export_to'] );
		// Overwrite file: delete the existing one and upload a new one.
		$method->delete_file( $task_data, $connection );
	}

	/**
	 * Check if the file already exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name  The name of the file to check.
	 * @param array  $task_data  Task-specific data used during the check.
	 * @param array  $connection Connection details.
	 *
	 * @return bool True if the file exists, false otherwise.
	 */
	public function is_file_exist( string $file_name, array $task_data, array $connection ): bool {

		$method = $task_data['export_to'] ?? '';
		$method = $this->get_delivery( $method );

		return $method->is_file_exist( $file_name, $task_data, $connection );
	}

	/**
	 * Sets the default connection data by initializing export settings and email structure.
	 *
	 * @since 1.0.0
	 *
	 * @param array &$connection_data A reference to an array where the default connection data will be set.
	 */
	private function set_default_connection_data( array &$connection_data ): void {

		$connection_data['export_to'] = 'email';
		$connection_data['email']     = [
			'address' => '',
		];
	}
}
