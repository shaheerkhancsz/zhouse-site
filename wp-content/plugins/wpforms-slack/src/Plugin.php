<?php

namespace WPFormsSlack;

use stdClass;
use WPForms\Providers\Loader as ProvidersLoader;
use WPFormsSlack\Api\Client;
use WPFormsSlack\Api\Auth;
use WPFormsSlack\Provider\Sanitizer;
use WPFormsSlack\Provider\Core;
use WPFormsSlack\Provider\Account;
use WPFormsSlack\Tasks\ProcessActionTask;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Provider slug.
	 *
	 * @since 1.0.0
	 */
	const SLUG = 'slack';

	/**
	 * Account class.
	 *
	 * @since 1.0.0
	 *
	 * @var Account
	 */
	private $account;

	/**
	 * Client class.
	 *
	 * @since 1.0.0
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Sanitizer class.
	 *
	 * @since 1.0.0
	 *
	 * @var Sanitizer
	 */
	private $sanitizer;

	/**
	 * Plugin constructor.
	 * This method is empty and private, so others can't initialize a new instance of it.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Initialize plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public function init(): Plugin {

		$this->load_dependencies();
		$this->hooks();

		return $this;
	}

	/**
	 * Plugin hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {

		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'register' ], 10, 2 );
	}

	/**
	 * All the actual plugin loading is done here.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {

		$this->account   = new Account();
		$this->client    = new Client();
		$this->sanitizer = new Sanitizer();

		( new Auth() )->hooks();
		( new ProcessActionTask() )->hooks();

		ProvidersLoader::get_instance()->register(
			Core::get_instance()
		);
	}

	/**
	 * Get the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {

		static $instance;

		if ( ! $instance ) {
			$instance = ( new self() )->init();
		}

		return $instance;
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
	 * Register addon location.
	 *
	 * @since 1.0.0
	 *
	 * @param string $located  Template location.
	 * @param string $template Template.
	 *
	 * @return string
	 */
	public function register( $located, string $template ): string {

		// Checking if `$template` is an absolute path and passed from this plugin.
		if (
			strpos( $template, WPFORMS_SLACK_PATH ) === 0 &&
			is_readable( $template )
		) {
			return $template;
		}

		return (string) $located;
	}
}
