<?php

namespace WPFormsEntryAutomation;

use stdClass;
use WPFormsEntryAutomation\Admin\FormBuilder;
use WPFormsEntryAutomation\Admin\Tools;
use WPFormsEntryAutomation\Database\Tasks;
use WPFormsEntryAutomation\Export\DeliveryManager;
use WPFormsEntryAutomation\Export\FormatterManager;
use WPFormsEntryAutomation\Helpers\DropboxClient;
use WPFormsEntryAutomation\Helpers\Scheduler;
use WPFormsEntryAutomation\Tasks\ProcessActionTask;
use WPFormsEntryAutomation\Tasks\TaskManager;

/**
 * Main class.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 */
	public const SLUG = 'entry_automation';

	/**
	 * Tasks instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Tasks
	 */
	private $tasks;

	/**
	 * Action tasks instance.
	 *
	 * @since 1.0.0
	 *
	 * @var ProcessActionTask
	 */
	private $action_tasks;

	/**
	 * Sanitizer instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Sanitizer
	 */
	private $sanitizer;

	/**
	 * Scheduler instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Scheduler
	 */
	private $scheduler;

	/**
	 * DeliveryManager instance.
	 *
	 * @since 1.0.0
	 *
	 * @var DeliveryManager
	 */
	private $delivery_manager;

	/**
	 * FormatterManager instance.
	 *
	 * @since 1.0.0
	 *
	 * @var FormatterManager
	 */
	private $formatter_manager;

	/**
	 * Builder instance.
	 *
	 * @since 1.0.0
	 *
	 * @var FormBuilder
	 */
	private $builder;

	/**
	 * Dropbox client instance.
	 *
	 * @since 1.0.0
	 *
	 * @var DropboxClient
	 */
	private $dropbox_client;

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {

		$this->tasks             = new Tasks();
		$this->action_tasks      = new ProcessActionTask();
		$this->sanitizer         = new Sanitizer();
		$this->scheduler         = new Scheduler();
		$this->delivery_manager  = new DeliveryManager();
		$this->formatter_manager = new FormatterManager();
		$this->builder           = new FormBuilder();
		$this->dropbox_client    = new DropboxClient();
	}

	/**
	 * Get a single instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance(): ?Plugin {

		static $instance = null;

		if ( ! $instance ) {
			$instance = new Plugin();

			$instance->init();
		}

		return $instance;
	}

	/**
	 * Initialize plugin.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks(): void {

		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'register' ], 10, 2 );

		$this->builder->hooks();
		$this->delivery_manager->hooks();
		$this->action_tasks->hooks();

		// Initialize admin components.
		( new Tools() )->hooks();

		// Initialize task manager.
		( new TaskManager() )->hooks();
	}

	/**
	 * Get property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $property_name Property name.
	 *
	 * @return mixed
	 */
	public function get( string $property_name ) {

		return property_exists( $this, $property_name ) ? $this->{$property_name} : new stdClass();
	}

	/**
	 * Register an addon location.
	 *
	 * @since 1.0.0
	 *
	 * @param string $located  Template location.
	 * @param string $template Template.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function register( $located, string $template ): string {

		// Checking if `$template` is an absolute path and passed from this plugin.
		if (
			strpos( $template, WPFORMS_ENTRY_AUTOMATION_PATH ) === 0 &&
			is_readable( $template )
		) {
			return $template;
		}

		return (string) $located;
	}
}
