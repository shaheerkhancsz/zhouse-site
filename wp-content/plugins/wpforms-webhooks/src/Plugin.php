<?php

namespace WPFormsWebhooks;

use WPFormsWebhooks\Admin\Entry;

/**
 * Webhooks plugin class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * FormBuilder instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WPFormsWebhooks\Admin\FormBuilder
	 */
	public $form_builder;

	/**
	 * Settings instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WPFormsWebhooks\Admin\Settings
	 */
	public $settings;

	/**
	 * Entry instance.
	 *
	 * @since 1.6.0
	 *
	 * @var Entry
	 */
	public $entry;

	/**
	 * Process instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \WPFormsWebhooks\Process
	 */
	public $process;

	/**
	 * Constructor method.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance() {

		static $instance;

		if ( ! $instance ) {
			$instance = new self();

			$instance->init();
		}

		return $instance;
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->hooks();

		return $this;
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {

		add_action( 'wpforms_loaded', [ $this, 'init_components' ], 15 );
		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'templates' ], 10, 4 );
	}

	/**
	 * Init components.
	 *
	 * @since 1.0.0
	 */
	public function init_components() {

		if (
			wpforms_is_admin_page( 'builder' ) ||
			wp_doing_ajax()
		) {
			$this->form_builder = new Admin\FormBuilder();
			$this->settings     = new Admin\Settings();

			$this->form_builder->init();
			$this->settings->init();
		}

		// Initialize entry management for entry details page.
		if ( wpforms_is_admin_page( 'entries', 'details' ) ) {
			$this->entry = new Entry();

			$this->entry->init();
		}

		$this->process = new Process();
		$this->process->init();
	}

	/**
	 * Load the addon updater.
	 *
	 * @since 1.0.0
	 * @deprecated 1.5.0
	 *
	 * @todo Remove with core 1.9.2
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		_deprecated_function( __METHOD__, '1.5.0 of the WPForms Webhooks plugin' );

		new \WPForms_Updater(
			[
				'plugin_name' => 'WPForms Webhooks',
				'plugin_slug' => 'wpforms-webhooks',
				'plugin_path' => plugin_basename( WPFORMS_WEBHOOKS_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_WEBHOOKS_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_WEBHOOKS_VERSION,
				'key'         => $key,
			]
		);
	}

	/**
	 * Change a template location.
	 *
	 * @since 1.0.0
	 *
	 * @param string $located  Template location.
	 * @param string $template Template.
	 * @param array  $args     Arguments.
	 * @param bool   $extract  Extract arguments.
	 *
	 * @return string
	 */
	public function templates( $located, $template, $args, $extract ) {

		// Checking if `$template` is an absolute path and passed from this plugin.
		if (
			( 0 === strpos( $template, WPFORMS_WEBHOOKS_PATH ) ) &&
			is_readable( $template )
		) {
			return $template;
		}

		return $located;
	}

	/**
	 * Retrieve available request methods.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_available_methods() {

		return [
			'get'    => 'GET',
			'post'   => 'POST',
			'put'    => 'PUT',
			'patch'  => 'PATCH',
			'delete' => 'DELETE',
		];
	}

	/**
	 * Retrieve available request formats.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_available_formats() {

		return [
			'json' => esc_html__( 'JSON', 'wpforms-webhooks' ),
			'form' => esc_html__( 'FORM', 'wpforms-webhooks' ),
		];
	}
}
