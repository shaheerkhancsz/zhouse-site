<?php

namespace WPFormsGeolocation\PlacesProviders;

use WPFormsGeolocation\Admin\Settings\Settings;
use WPFormsGeolocation\Admin\Settings\MapboxSearch as SettingsMapboxSearch;

/**
 * Class MapboxSearch.
 *
 * @since 2.3.0
 */
class MapboxSearch implements IPlacesProvider {

	/**
	 * Init Mapbox Search provider.
	 *
	 * @since 2.3.0
	 */
	public function init() {

		if ( ! $this->is_active() ) {
			return;
		}

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.3.0
	 */
	private function hooks(): void {

		add_action( 'wpforms_frontend_js', [ $this, 'enqueue_api_assets' ], 8 );

		add_action( 'wpforms_frontend_css', [ $this, 'enqueue_styles' ] );
		add_action( 'wpforms_frontend_js', [ $this, 'enqueue_scripts' ] );
		add_filter( 'wpforms_geolocation_front_fields_get_autocomplete_settings', [ $this, 'modify_autocomplete_settings' ], 9, 2 );
		add_filter( 'wpforms_geolocation_front_fields_get_map_settings', [ $this, 'modify_map_settings' ], 9, 2 );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 2.3.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_styles( $forms ) {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-geolocation-mapbox',
			WPFORMS_GEOLOCATION_URL . "assets/css/wpforms-geolocation-mapbox{$min}.css",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Enqueue provider API assets.
	 *
	 * @since 2.13.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_api_assets( $forms ): void {

		if ( $this->has_map( $forms ) ) {
			$this->enqueue_api_map_assets();
		}

		$this->enqueue_api_autocomplete_assets();
	}

	/**
	 * Enqueue provider map API assets.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_api_map_assets(): void {

		wp_enqueue_style(
			'wpforms-mapbox-gl',
			'https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.css',
			[],
			'2.9.1'
		);

		wp_enqueue_script(
			'wpforms-mapbox-gl',
			'https://api.mapbox.com/mapbox-gl-js/v2.9.1/mapbox-gl.js',
			[],
			'2.9.1',
			true
		);
	}

	/**
	 * Enqueue provider autocomplete API assets.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_api_autocomplete_assets(): void {

		wp_enqueue_script(
			'wpforms-mapbox-search',
			'https://api.mapbox.com/search-js/v1.0.0-beta.19/web.js',
			[],
			'1.0.0-beta19',
			true
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.3.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_scripts( $forms ) {

		$min  = wpforms_get_min_suffix();
		$deps = [ 'jquery', 'wpforms-mapbox-search' ];

		if ( $this->has_map( $forms ) ) {
			$deps[] = 'wpforms-mapbox-gl';
		}

		wp_enqueue_script(
			'wpforms-geolocation-mapbox-api',
			WPFORMS_GEOLOCATION_URL . "assets/js/wpforms-geolocation-mapbox-api{$min}.js",
			$deps,
			WPFORMS_GEOLOCATION_VERSION,
			true
		);
	}

	/**
	 * Determine whether the `Address Autocomplete` and `Display Map` options are enabled for one of the page's forms.
	 *
	 * @since 2.3.0
	 *
	 * @param array $forms List of forms.
	 *
	 * @return bool
	 */
	private function has_map( $forms ): bool {

		static $has_map;

		if ( $has_map !== null ) {
			return $has_map;
		}

		foreach ( $forms as $form ) {
			foreach ( $form['fields'] as $field ) {
				if ( ! empty( $field['enable_address_autocomplete'] ) && ! empty( $field['display_map'] ) ) {
					$has_map = true;

					return true;
				}
			}
		}

		$has_map = false;

		return false;
	}

	/**
	 * Determine if the account is active.
	 *
	 * @since 2.3.0
	 *
	 * @return bool
	 */
	public function is_active() {

		return ! empty( self::get_access_token() );
	}

	/**
	 * Get access token.
	 *
	 * @since 2.3.0
	 * @since 2.13.0 Make the method public.
	 *
	 * @return string
	 */
	public static function get_access_token(): string {

		return (string) wpforms_setting( Settings::SLUG . '-' . SettingsMapboxSearch::SLUG . '-access-token', '' );
	}

	/**
	 * Modify autocomplete settings.
	 *
	 * @since 2.3.0
	 *
	 * @param array $autocomplete_settings Autocomplete settings.
	 * @param array $forms                 Forms.
	 *
	 * @return array
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function modify_autocomplete_settings( $autocomplete_settings, $forms ) {

		$autocomplete_settings['access_token'] = self::get_access_token();
		$autocomplete_settings['language']     = substr( get_locale(), 0, 2 );

		return $autocomplete_settings;
	}

	/**
	 * Modify map settings.
	 *
	 * @since 2.3.0
	 *
	 * @param array $map_settings Map settings.
	 * @param array $forms        Forms.
	 *
	 * @return array
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function modify_map_settings( $map_settings, $forms ) {

		$map_settings['style']               = 'mapbox://styles/mapbox/streets-v12';
		$map_settings['cooperativeGestures'] = true;

		return $map_settings;
	}
}
