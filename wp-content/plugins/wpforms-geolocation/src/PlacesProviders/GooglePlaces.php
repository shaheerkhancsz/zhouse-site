<?php

namespace WPFormsGeolocation\PlacesProviders;

use WPFormsGeolocation\Admin\Settings\Settings;
use WPFormsGeolocation\Admin\Settings\GooglePlaces as SettingsGooglePlaces;

/**
 * Class GooglePlaces.
 *
 * @since 2.0.0
 */
class GooglePlaces implements IPlacesProvider {

	/**
	 * Init Google Places provider.
	 *
	 * @since 2.0.0
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
	 * @since 2.13.0
	 */
	private function hooks(): void {

		add_action( 'wpforms_frontend_js', [ $this, 'enqueue_api_assets' ], 8 );

		add_action( 'wpforms_frontend_css', [ $this, 'enqueue_styles' ] );
		add_action( 'wpforms_frontend_js', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_styles( $forms ) {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-geolocation-google-places',
			WPFORMS_GEOLOCATION_URL . "assets/css/wpforms-geolocation-google{$min}.css",
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

		$this->enqueue_api_map_assets();
	}

	/**
	 * Enqueue provider map API assets.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_api_map_assets(): void {

		static $loaded = false;

		if ( $loaded ) {
			return;
		}
		?>
		<script>
			(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
				key: "<?php echo esc_attr( wpforms_setting( Settings::SLUG . '-' . SettingsGooglePlaces::SLUG . '-api-key' ) ); ?>",
				v: "weekly",
			});
		</script>
		<?php
		$loaded = true;
	}

	/**
	 * Enqueue provider autocomplete API assets.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_api_autocomplete_assets(): void {

		$this->enqueue_api_map_assets();
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.0.0
	 *
	 * @param array $forms List of forms.
	 */
	public function enqueue_scripts( $forms ) {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-geolocation-google-places',
			WPFORMS_GEOLOCATION_URL . "assets/js/wpforms-geolocation-google-api{$min}.js",
			[ 'jquery' ],
			WPFORMS_GEOLOCATION_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-geolocation-google-places',
			'wpforms_geolocation_google_places',
			[
				'l10n' => [
					'apiError' => $this->get_api_error_message(),
				],
			]
		);
	}

	/**
	 * Determine whether the Google Places provider is active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_active() {

		return (bool) wpforms_setting( Settings::SLUG . '-' . SettingsGooglePlaces::SLUG . '-api-key' );
	}

	/**
	 * Get an API error message.
	 *
	 * @since 2.12.0
	 *
	 * @return string
	 */
	private function get_api_error_message(): string {

		return sprintf(
			/* translators: %1$s - Google Cloud Console link. */
			esc_html__( 'This API key is not authorized to use this service or API. Please check the API restrictions settings of your API key in the %1$s to ensure that all of the APIs and services you need to use are correctly specified in the list of enabled APIs.', 'wpforms-geolocation' ),
			'<a href="https://console.cloud.google.com/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Google Cloud Console', 'wpforms-geolocation' ) . '</a>'
		);
	}
}
