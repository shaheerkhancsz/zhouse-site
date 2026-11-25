<?php

namespace WPFormsGeolocation\PlacesProviders;

/**
 * Interface IPlacesProvider.
 *
 * @since 2.0.0
 */
interface IPlacesProvider {

	/**
	 * Init provider.
	 *
	 * @since 2.0.0
	 */
	public function init();

	/**
	 * Check filling settings for this provider.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 */
	public function is_active();

	/**
	 * Enqueue provider styles.
	 *
	 * @since 2.3.0
	 *
	 * @param array $forms List of forms.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 */
	public function enqueue_styles( $forms );

	/**
	 * Enqueue provider scripts.
	 *
	 * @since 2.3.0
	 *
	 * @param array $forms List of forms.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 */
	public function enqueue_scripts( $forms );

	/**
	 * Enqueue provider API assets.
	 *
	 * @since 2.13.0
	 *
	 * @param array $forms List of forms.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function enqueue_api_assets( $forms ): void;

	/**
	 * Enqueue provider map API assets.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_api_map_assets(): void;

	/**
	 * Enqueue provider autocomplete API assets.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_api_autocomplete_assets(): void;
}
