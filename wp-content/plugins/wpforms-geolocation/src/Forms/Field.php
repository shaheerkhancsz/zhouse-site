<?php

namespace WPFormsGeolocation\Forms;

use WPForms\Forms\Fields\Addons\Map\Field as FieldLite;
use WPForms\Pro\Forms\Fields\Layout\Helpers;
use WPForms\SmartTags\SmartTag\SmartTag;
use WPFormsGeolocation\Admin\Settings\Settings;
use WPFormsGeolocation\PlacesProviders;
use WPFormsGeolocation\PlacesProviders\ProvidersFactory;

/**
 * Class Field.
 *
 * @since 2.13.0
 */
class Field extends FieldLite {

	/**
	 * Icon font sizes for markers.
	 *
	 * @since 2.13.0
	 */
	private const ICON_MARKER_SIZES = [
		'small'  => 12,
		'medium' => 18,
		'large'  => 24,
	];

	/**
	 * Determine if the provider is selected and has configured credentials.
	 *
	 * @since 2.13.0
	 *
	 * @var bool
	 */
	private $is_provider_configured;

	/**
	 * Provider instance.
	 *
	 * @since 2.13.0
	 *
	 * @var PlacesProviders\IPlacesProvider|null
	 */
	private $provider;

	/**
	 * Primary class constructor.
	 *
	 * @since 2.13.0
	 *
	 * @param bool $init Pass false to allow shortcutting the whole initialization, if needed.
	 */
	public function __construct( $init = true ) {

		$this->display_field_options_notice = false;
		$settings                           = new Settings();

		$provider = ( new ProvidersFactory( $settings ) )->get_current_provider();

		$this->provider               = $provider;
		$this->is_provider_configured = $provider && $provider->is_active();

		parent::__construct( $init );
	}

	/**
	 * Define field hooks.
	 *
	 * @since 2.13.0
	 */
	protected function common_hooks(): void { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		parent::common_hooks();

		add_action( 'wpforms_frontend_js', [ $this, 'frontend_js' ] );
		add_action( 'wpforms_frontend_css', [ $this, 'frontend_css' ] );

		add_action( 'wpforms_builder_enqueues', [ $this, 'enqueue_builder_styles' ] );
		add_action( 'wpforms_builder_enqueues', [ $this, 'enqueue_builder_scripts' ] );

		add_filter( 'wpforms_get_form_fields_allowed', [ $this, 'add_to_allowed_form_fields' ] );
		add_filter( 'wpforms_field_data', [ $this, 'set_label_id' ], 10 );
		add_filter( "wpforms_field_properties_$this->type", [ $this, 'field_properties' ], 5, 3 );
		add_filter( 'wpforms_builder_field_button_attributes', [ $this, 'field_button_attributes' ], 10, 2 );

		add_filter( 'wpforms_pro_admin_entries_export_ajax_get_entry_fields_data_field', [ $this, 'export_entry_field_data' ] );
		add_filter( "wpforms_pro_admin_entries_edit_field_object_$this->type", [ $this, 'register_edit_field_object' ] );
		add_filter( "wpforms_pro_admin_entries_edit_is_field_displayable_$this->type", [ $this, 'is_displayed' ], 10, 3 );
		add_filter( "wpforms_pro_admin_entries_print_is_field_displayable_$this->type", [ $this, 'is_displayed' ], 10, 3 );
		add_filter( 'wpforms_pro_admin_entries_edit_field_editable', [ $this, 'is_editable' ], 10, 3 );
		add_filter( 'wpforms_html_field_value', [ $this, 'replace_link' ], 10, 4 );
		add_filter( 'wpforms_html_field_value', [ $this, 'append_entry_thumbnail' ], 11, 4 );
		add_filter( 'wpforms_google_sheets_provider_field_mapper_field_value', [ $this, 'google_sheets_value' ], 10, 4 );
		add_filter( 'wpforms_smarttags_process_field_id_value', [ $this, 'google_sheets_smart_tags_value' ], 10, 6 );
		add_filter( 'wpforms_save_form_args', [ $this, 'save_user_settings' ], 10, 2 );
	}

	/**
	 * Add the field to the allowed list of fields.
	 *
	 * @since 2.13.0
	 *
	 * @param array $fields Allowed fields list.
	 *
	 * @return array
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function add_to_allowed_form_fields( $fields ): array {

		$fields = (array) $fields;

		$fields[] = $this->type;

		return $fields;
	}

	/**
	 * Ask for the addon setup if the provider is not configured.
	 *
	 * @since 2.13.0
	 * @since 2.14.0 Asks for the addon setup via the `configure` action instead of reporting an incompatibility.
	 */
	public function admin_init_pro_field(): void {

		parent::admin_init_pro_field();

		if ( $this->is_provider_configured ) {
			return;
		}

		$this->addon_edu_data['slug'] = 'wpforms-geolocation';

		// Released core versions know nothing about the `configure` action, so they keep receiving the old one.
		if ( ! method_exists( $this, 'is_addon_edu_action_supported' ) || ! $this->is_addon_edu_action_supported( 'configure' ) ) {
			$this->addon_edu_data['action'] = 'incompatible';

			return;
		}

		// The addon is up to date; it just has no configured Places Provider, so ask for setup, not an update.
		$this->addon_edu_data['action']  = 'configure';
		$this->addon_edu_data['message'] = self::get_provider_not_configured_strings()['description'];
	}

	/**
	 * Enqueue builder styles.
	 *
	 * @since 2.13.0
	 */
	public function enqueue_builder_styles(): void {

		if ( ! $this->is_provider_configured ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-map-field',
			WPFORMS_GEOLOCATION_URL . "assets/css/wpforms-geolocation-map-field$min.css",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Enqueue builder scripts.
	 *
	 * @since 2.13.0
	 *
	 * @noinspection HtmlUnknownTarget
	 */
	public function enqueue_builder_scripts(): void {

		$min = wpforms_get_min_suffix();

		if ( $this->provider ) {
			$this->provider->enqueue_api_map_assets();
			$this->provider->enqueue_api_autocomplete_assets();
		}

		wp_enqueue_script(
			'wpforms-map-field',
			WPFORMS_GEOLOCATION_URL . "assets/js/wpforms-geolocation-map-field$min.js",
			[ 'jquery' ],
			WPFORMS_GEOLOCATION_VERSION,
			true
		);

		wp_enqueue_script(
			'wpforms-map-field-builder',
			WPFORMS_GEOLOCATION_URL . "assets/js/admin/wpforms-geolocation-map-field-builder$min.js",
			[ 'jquery', 'wpforms-map-field' ],
			WPFORMS_GEOLOCATION_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-map-field-builder',
			'wpforms_geolocation_map_field',
			$this->get_builder_strings()
		);
	}

	/**
	 * Get strings describing that the Places Provider is not configured yet.
	 *
	 * @since 2.14.0
	 *
	 * @return array
	 * @noinspection HtmlUnknownTarget
	 */
	public static function get_provider_not_configured_strings(): array {

		return [
			'title'       => esc_html__( 'API Connection Required', 'wpforms-geolocation' ),
			'description' => wp_kses(
				sprintf( /* translators: %s - link to the WPForms Geolocation Settings page. */
					__( 'To proceed, please go to the <a href="%1$s" target="_blank" rel="noopener noreferrer">WPForms Settings > Geolocation page</a>, choose a Places Provider and set credentials for it.', 'wpforms-geolocation' ),
					esc_url(
						add_query_arg(
							[
								'page' => 'wpforms-settings',
								'view' => 'geolocation',
							],
							admin_url( 'admin.php' )
						)
					)
				),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'rel'    => [],
					],
				]
			),
		];
	}

	/**
	 * Get builder strings.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_builder_strings(): array {

		$strings = [
			'provider_not_configured'             => self::get_provider_not_configured_strings(),
			'incompatible_with_disabled_dragging' => wp_kses(
				__( 'This option can’t be enabled while <strong>Disable Dragging</strong> is active.', 'wpforms-geolocation' ),
				[
					'strong' => [],
				]
			),
			'allow_location_selection_disabled'   => wp_kses(
				__( 'This option requires <strong>Show List of Locations</strong> and at least two locations.', 'wpforms-geolocation' ),
				[
					'strong' => [],
				]
			),
			'zoom_disabled'                       => wp_kses(
				__( 'This option is unavailable when multiple locations are set. The map automatically adjusts the zoom to display all locations.', 'wpforms-geolocation' ),
				[
					'strong' => [],
				]
			),
			'thumbnail_in_entry_disabled'         => wp_kses(
				__( 'This option requires <strong>Show in Entry</strong> to be enabled.', 'wpforms-geolocation' ),
				[
					'strong' => [],
				]
			),
			'detection_not_supported'             => esc_html__( 'Geolocation detection is not supported by your browser.', 'wpforms-geolocation' ),
			'unable_retrieve_location'            => esc_html__( 'We couldn\'t retrieve your location.', 'wpforms-geolocation' ),
			'detection_denied'                    => esc_html__( 'Detection of your location is denied.', 'wpforms-geolocation' ),
			'api_error'                           => esc_html__( 'This page can\'t load Google Maps correctly. Check the Google Places API key in WPForms settings.', 'wpforms-geolocation' ),
			'default_search_radius'               => self::DEFAULT_SEARCH_RADIUS,
		];

		if ( $this->get_active_provider_slug() === 'mapbox-search' ) {
			$strings['settings']['access_token'] = PlacesProviders\MapboxSearch::get_access_token();
		}

		$strings['settings']['bounds'] = $this->get_bounds_settings();

		/**
		 * Allow modifying builder strings.
		 *
		 * @since 2.13.0
		 *
		 * @param array $strings I18n strings.
		 */
		return (array) apply_filters( 'wpforms_geolocation_forms_field_builder_strings', $strings );
	}

	/**
	 * Determine if the field is disabled and the provider is configured.
	 *
	 * @since 2.13.0
	 */
	protected function is_disabled_field(): bool {

		return parent::is_disabled_field() || ! $this->is_provider_configured;
	}

	/**
	 * Create the button for the 'Add Fields' tab, inside the form editor.
	 *
	 * @since 2.13.0
	 *
	 * @param array $fields List of form fields with their data.
	 *
	 * @return array
	 */
	public function field_button( $fields ): array {

		$fields = (array) $fields;

		$fields[ $this->group ]['fields'][] = [
			'order'    => $this->order,
			'name'     => $this->name,
			'type'     => $this->type,
			'icon'     => $this->icon,
			'keywords' => $this->keywords,
		];

		return $fields;
	}

	/**
	 * Add a class to the button for the Map field button when the provider is not configured.
	 *
	 * @since 2.13.0
	 *
	 * @param array $attributes Button attributes.
	 * @param array $field      Field data.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function field_button_attributes( $attributes, $field ): array {

		$attributes = (array) $attributes;
		$field      = (array) $field;

		if ( ! $this->is_map_type( $field ) ) {
			return $attributes;
		}

		if ( empty( $this->is_disabled_field ) ) {
			return $attributes;
		}

		$attributes['class'][] = 'warning-modal';
		$attributes['class'][] = 'wpforms-add-fields-configure-geolocation-provider';

		return $attributes;
	}

	/**
	 * Enqueue frontend field js.
	 *
	 * @since 2.13.0
	 *
	 * @param array $forms Forms on the current page.
	 *
	 * @noinspection NullPointerExceptionInspection
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function frontend_js( $forms ): void {

		$forms = (array) $forms;

		if (
			! wpforms_has_field_type( 'map', $forms, true ) &&
			! wpforms()->obj( 'frontend' )->assets_global()
		) {
			return;
		}

		$min = wpforms_get_min_suffix();

		if ( $this->provider ) {
			$this->provider->enqueue_api_map_assets();
		}

		wp_enqueue_script(
			'wpforms-map-field',
			WPFORMS_GEOLOCATION_URL . "assets/js/wpforms-geolocation-map-field$min.js",
			[ 'jquery' ],
			WPFORMS_GEOLOCATION_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-map-field',
			'wpforms_geolocation_map_field',
			[
				'fields'    => $this->get_map_fields_settings( $forms ),
				'settings'  => [
					'access_token' => PlacesProviders\MapboxSearch::get_access_token(),
				],
				'api_error' => esc_html__( 'This page can\'t load Google Maps correctly.', 'wpforms-geolocation' ),
			]
		);
	}

	/**
	 * Get map fields settings.
	 *
	 * @since 2.13.0
	 *
	 * @param array $forms Forms on the current page.
	 */
	private function get_map_fields_settings( array $forms ): array {

		$field_settings = [];

		foreach ( $forms as $form ) {
			if ( empty( $form['id'] ) ) {
				continue;
			}

			$form_id = $form['id'];

			foreach ( $form['fields'] as $field ) {
				if ( ! isset( $field['id'] ) || wpforms_is_empty_string( $field['id'] ) ) {
					continue;
				}

				$field_id = $field['id'];

				if ( ! $this->is_map_type( $field ) ) {
					continue;
				}

				if ( empty( $field['choices'] ) ) {
					continue;
				}

				$map_field_settings = $this->get_map_field_settings( $field , $form );

				if ( ! empty( $map_field_settings ) ) {
					$field_settings[ $form_id ][ $field_id ] = $map_field_settings;
				}
			}
		}

		return $field_settings;
	}

	/**
	 * Get map field settings.
	 *
	 * @since 2.13.0
	 *
	 * @param array $field     Field settings.
	 * @param array $form_data Form data and settings.
	 */
	private function get_map_field_settings( array $field, array $form_data ): array {

		$field_id      = $field['id'];
		$markers       = [];
		$valid_choices = $this->get_valid_choices( $field['choices'] );

		if ( empty( $valid_choices ) ) {
			return [];
		}

		foreach ( $valid_choices as $choice ) {
			$choice = wp_parse_args(
				$choice,
				[
					'name'        => '',
					'description' => '',
					'icon'        => '',
					'icon_style'  => '',
					'icon_color'  => '#d63638',
					'marker_type' => 'icon',
					'image'       => '',
					'size'        => 'small',
				]
			);

			$marker = [
				'name'        => $choice['name'],
				'description' => $choice['description'],
				'latitude'    => $choice['latitude'],
				'longitude'   => $choice['longitude'],
				'markerType'  => $choice['marker_type'],
				'color'       => $choice['icon_color'],
				'size'        => $choice['size'],
			];

			if ( $choice['marker_type'] === 'icon' ) {
				$size = ! empty( self::ICON_MARKER_SIZES[ $choice['size'] ] ) ? (int) self::ICON_MARKER_SIZES[ $choice['size'] ] : 12;

				$marker['iconSVG'] = wpforms_get_icon_svg( $choice['icon'], $choice['icon_style'], $size );
			} else {
				$marker['imgUrl'] = $choice['image'];
			}

			$markers[] = $marker;
		}

		if ( empty( $markers ) ) {
			return [];
		}

		$settings = [
			'map'     => [
				'map_id'                => "wpforms-field-$field_id-map",
				'center'                => [
					'lat' => $markers[0]['latitude'],
					'lng' => $markers[0]['longitude'],
				],
				'hide_full_screen'      => ! empty( $field['hide_full_screen'] ),
				'hide_map_type'         => ! empty( $field['hide_map_type'] ),
				'hide_location_info'    => ! empty( $field['hide_location_info'] ),
				'hide_street_view'      => ! empty( $field['hide_street_view'] ),
				'hide_camera_control'   => ! empty( $field['hide_camera_control'] ),
				'hide_zoom'             => ! empty( $field['hide_zoom'] ),
				'disable_dragging'      => ! empty( $field['disable_dragging'] ),
				'disable_mouse_zooming' => ! empty( $field['disable_mouse_zooming'] ),
				'zoom_level'            => ! empty( $field['zoom_level'] ) ? absint( $field['zoom_level'] ) : 15,
			],
			'markers' => $markers,
			'bounds'  => $this->get_bounds_settings(),
		];

		/**
		 * Filter map field settings.
		 *
		 * @since 2.13.0
		 *
		 * @param array $settings  Map field settings.
		 * @param array $field     Map field settings.
		 * @param array $form_data Form data and settings.
		 */
		return (array) apply_filters( 'wpforms_geolocation_forms_field_get_map_field_settings', $settings, $field, $form_data );
	}

	/**
	 * List of parameters for map.fitBounds JS functions.
	 *
	 * @since 2.13.0
	 *
	 * @return array
	 */
	private function get_bounds_settings(): array {

		$is_rtl = is_rtl();

		return [
			'padding' => [
				'top'    => 100,
				'right'  => ! $is_rtl ? 175 : 25,
				'bottom' => 25,
				'left'   => $is_rtl ? 175 : 25,
			],
			'animate' => false,
		];
	}

	/**
	 * Enqueue frontend field js.
	 *
	 * @since 2.13.0
	 *
	 * @param array $forms Forms on the current page.
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection NullPointerExceptionInspection
	 */
	public function frontend_css( $forms ): void {

		$forms = (array) $forms;

		if (
			! wpforms_has_field_type( 'map', $forms, true ) &&
			! wpforms()->obj( 'frontend' )->assets_global()
		) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-map-field',
			WPFORMS_GEOLOCATION_URL . "assets/css/wpforms-geolocation-map-field$min.css",
			[],
			WPFORMS_GEOLOCATION_VERSION
		);
	}

	/**
	 * Sets a sequential label ID for valid markers in the field choices.
	 * Compatibility with S&R, and field population.
	 *
	 * @since        2.13.0
	 *
	 * @param array $field Field data.
	 *
	 * @return array
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function set_label_id( $field ): array {

		$field = (array) $field;

		if ( ! $this->is_map_type( $field ) ) {
			return $field;
		}

		$i = 0;

		foreach ( $field['choices'] as $key => $choice ) {
			if ( $this->is_valid_marker( $choice ) ) {
				$field['choices'][ $key ]['label'] = $i;

				++$i;
			}
		}

		return $field;
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 2.13.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function field_properties( $properties, $field, $form_data ): array {

		// Remove primary input, unset for attribute for label.
		unset( $properties['inputs']['primary'], $properties['label']['attr']['for'] );

		$valid_choices = $this->get_valid_choices( $field['choices'] );
		$size          = $field['size'] ?? 'medium';

		if ( empty( $valid_choices ) ) {
			// Return the already-built properties array (not an empty array) so downstream
			// consumers like Modern render and EntriesEdit still find the expected `inputs`
			// and `error.attr.for` keys and do not trigger PHP warnings.
			return $properties;
		}

		$form_id  = absint( $form_data['id'] );
		$field_id = wpforms_validate_field_id( $field['id'] );

		// Set input container (ul) properties.
		$properties['input_container'] = [
			'class' => [ 'wpforms-field-map-choices', 'wpforms-field-row', "wpforms-field-$size" ],
			'data'  => [],
			'attr'  => [],
			'id'    => "wpforms-$form_id-field_$field_id",
		];

		if ( $this->is_allowed_selection( $field ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-radio';
		}

		// Set input properties.
		foreach ( $valid_choices as $key => $choice ) {
			$properties['inputs'][ $key ] = [
				'container' => [
					'attr'  => [],
					'class' => [ "choice-$key", $key === 0 ? 'wpforms-selected' : '' ],
					'data'  => [],
					'id'    => '',
				],
				'label'     => [
					'attr'  => [
						'for' => "wpforms-$form_id-field_{$field_id}_$key",
					],
					'class' => [ 'wpforms-field-label-inline' ],
					'data'  => [],
					'id'    => '',
				],
				'attr'      => [
					'name'  => "wpforms[fields][$field_id]",
					'value' => $key,
				],
				'class'     => [],
				'data'      => [],
				'id'        => "wpforms-$form_id-field_{$field_id}_$key",
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => $key === 0,
			];
		}

		// Required class for the pagebreak field validation.
		if ( ! empty( $field['required'] ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-required';
		}

		return $properties;
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 2.13.0
	 *
	 * @param array $field      Field settings.
	 * @param array $deprecated Deprecated array.
	 * @param array $form_data  Form data and settings.
	 *
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 * @noinspection HtmlUnknownAttribute
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		if ( empty( $field['choices'] ) ) {
			return;
		}

		$valid_choices = $this->get_valid_choices( $field['choices'] );

		if ( empty( $valid_choices ) ) {
			return;
		}

		$field_id                 = $field['id'] ?? 0;
		$size                     = $field['size'] ?? 'medium';
		$primary                  = $field['properties']['inputs'][0];
		$allow_location_selection = $this->is_allowed_selection( $field );

		$this->print_map( $size, $field_id );

		if ( ! $allow_location_selection ) {
			printf(
				'<input type="hidden" %1$s>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] )
			);
		}

		$this->print_location_list( $field, $valid_choices );
	}

	/**
	 * Print location list.
	 *
	 * @since 2.13.0
	 *
	 * @param array $field         Field settings.
	 * @param array $valid_choices Valid choices.
	 *
	 * @noinspection HtmlUnknownAttribute
	 */
	protected function print_location_list( array $field, array $valid_choices ): void {

		if ( empty( $field['show_locations_list'] ) ) {
			return;
		}

		$container                = $field['properties']['input_container'] ?? [];
		$allow_location_selection = $this->is_allowed_selection( $field );

		printf(
			'<ul %1$s>',
			wpforms_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
		);

		foreach ( $valid_choices as $key => $choice ) {
			if ( empty( $field['properties']['inputs'][ $key ] ) ) {
				continue;
			}

			$label             = '';
			$choice_properties = $field['properties']['inputs'][ $key ];

			if ( ! wpforms_is_empty_string( $choice['name'] ) ) {
				$label .= sprintf( '<span class="wpforms-field-map-location-name">%1$s</span>', esc_html( $choice['name'] ) );
			}

			if ( ! wpforms_is_empty_string( $choice['address'] ) ) {
				$label .= sprintf( '<span class="wpforms-field-map-location-address">%1$s</span>', esc_html( $choice['address'] ) );
			}

			/**
			 * Filter the label for a location list item in a WPForms field.
			 *
			 * @since 2.13.0
			 *
			 * @param string $label  The label to display.
			 * @param array  $choice The choice data for the location.
			 * @param array  $field  The field data.
			 */
			$label = apply_filters( 'wpforms_geolocation_forms_field_location_list_label', $label, $choice, $field );

			printf(
				'<li %1$s>',
				wpforms_html_attributes( $choice_properties['container']['id'], $choice_properties['container']['class'], $choice_properties['container']['data'], $choice_properties['container']['attr'] )
			);

			if ( $allow_location_selection ) {
				printf(
					'<input type="radio" %1$s %2$s %3$s>',
					wpforms_html_attributes( $choice_properties['id'], $choice_properties['class'], $choice_properties['data'], $choice_properties['attr'] ),
					esc_attr( $choice_properties['required'] ),
					checked( '1', $choice_properties['default'], false )
				);
			}

			printf(
				'<label %s>%s</label>',
				wpforms_html_attributes( $choice_properties['label']['id'], $choice_properties['label']['class'], $choice_properties['label']['data'], $choice_properties['label']['attr'] ),
				wp_kses_post( $label )
			);

			echo '</li>';
		}
		echo '</ul>';
	}

	/**
	 * Get rid of a field on the frontend if there are no valid choices.
	 *
	 * @since 2.13.0
	 *
	 * @param array $field     Current field.
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 * @noinspection PhpMissingReturnTypeInspection
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 */
	public function field_data( $field, $form_data ) {

		if ( ! $this->is_map_type( $field ) ) {
			return parent::field_data( $field, $form_data );
		}

		if ( empty( $field['choices'] ) ) {
			return [];
		}

		$valid_choices = $this->get_valid_choices( $field['choices'] );

		if ( empty( $valid_choices ) ) {
			return [];
		}

		return parent::field_data( $field, $form_data );
	}

	/**
	 * Format and sanitize field.
	 *
	 * @since 2.13.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param mixed $field_submit Field value that was submitted.
	 * @param array $form_data    Form data and settings.
	 *
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		if ( wpforms_is_empty_string( $field_submit ) ) {
			return;
		}

		$field_settings = $form_data['fields'][ $field_id ];

		if ( empty( $field_settings['choices'] ) ) {
			return;
		}

		$valid_choices = $this->get_valid_choices( $field_settings['choices'] );

		if ( empty( $valid_choices[ $field_submit ] ) ) {
			return;
		}

		$submitted_choice = $valid_choices[ $field_submit ];

		if ( ! isset( $submitted_choice['latitude'], $submitted_choice['longitude'] ) ) {
			return;
		}

		if ( wpforms_is_empty_string( $submitted_choice['latitude'] ) || wpforms_is_empty_string( $submitted_choice['longitude'] ) ) {
			return;
		}

		$submitted_choice = wp_parse_args(
			$submitted_choice,
			[
				'name'        => '',
				'address'     => '',
				'description' => '',
			]
		);

		$location    = [
			'latitude'    => $submitted_choice['latitude'],
			'longitude'   => $submitted_choice['longitude'],
			'name'        => $submitted_choice['name'],
			'address'     => $submitted_choice['address'],
			'description' => $submitted_choice['description'],
		];
		$value_parts = [];

		if ( ! wpforms_is_empty_string( $submitted_choice['name'] ) ) {
			$value_parts[] = sprintf( '<strong>%1$s</strong>', $submitted_choice['name'] );
		}

		if ( ! wpforms_is_empty_string( $submitted_choice['address'] ) ) {
			$value_parts[] = $submitted_choice['address'];
		}

		$value_parts[] = $this->get_map_url( $location, $field_settings );

		wpforms()->obj( 'process' )->fields[ $field_id ] = [
			'name'      => sanitize_text_field( $field_settings['label'] ?? '' ),
			'value'     => implode( "\n", $value_parts ),
			'value_raw' => $field_submit,
			'location'  => $location,
			'id'        => wpforms_validate_field_id( $field_id ),
			'type'      => $this->type,
		];
	}

	/**
	 * Get rid of invalid markers for the choice list.
	 *
	 * @since 2.13.0
	 *
	 * @param array $choices Choices array.
	 *
	 * @return array
	 */
	private function get_valid_choices( array $choices ): array {

		foreach ( $choices as $key => $choice ) {
			if ( ! $this->is_valid_marker( $choice ) ) {
				unset( $choices[ $key ] );
			}
		}

		return array_values( $choices );
	}

	/**
	 * Export entry field data.
	 *
	 * @since 2.13.0
	 *
	 * @param array|mixed $field Field data.
	 *
	 * @return array
	 */
	public function export_entry_field_data( $field ): array {

		$field = (array) $field;

		if ( ! $this->is_map_type( $field ) ) {
			return $field;
		}

		if ( ! isset( $field['value'] ) || wpforms_is_empty_string( $field['value'] ) ) {
			return $field;
		}

		$field['value'] = wp_strip_all_tags( $field['value'] );

		return $field;
	}

	/**
	 * Register Entries Edit object.
	 *
	 * @since 2.13.0
	 */
	public function register_edit_field_object(): EntriesEdit {

		return new EntriesEdit();
	}

	/**
	 * Define if a field is displayed based on the Show In Entry field option.
	 *
	 * @since 2.13.0
	 *
	 * @param bool  $is_displayable Determine if the field should be displayed or not.
	 * @param array $field          Field data.
	 * @param array $form_data      Form data and settings.
	 *
	 * @return bool
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function is_displayed( $is_displayable, $field, $form_data ): bool {

		$is_displayable = (bool) $is_displayable;

		if ( ! $this->is_map_type( $field ) ) {
			return $is_displayable;
		}

		if ( ! isset( $field['id'] ) ) {
			return $is_displayable;
		}

		$field_settings = $this->get_field_from_form_data( $field['id'], $form_data );

		return $field_settings && ! empty( $field_settings['show_in_entry'] );
	}

	/**
	 * Determine if it's possible to edit the field.
	 * Field is editable when the field has more than two valid locations,
	 * and both Show Locations List and Allow Location Selection field options are enabled.
	 *
	 * @since 2.13.0
	 *
	 * @param bool   $is_editable True if is editable.
	 * @param string $type        Field type.
	 * @param array  $field       Field data.
	 *
	 * @return bool
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function is_editable( $is_editable, $type, $field ): bool {

		$is_editable = (bool) $is_editable;

		if ( ! $this->is_map_type( $field ) ) {
			return $is_editable;
		}

		return $this->is_allowed_selection( $field );
	}

	/**
	 * Determine if the field is allowed to be selected.
	 *
	 * @since 2.13.0
	 *
	 * @param array $field Field data.
	 *
	 * @return bool
	 */
	private function is_allowed_selection( array $field ): bool {

		if ( empty( $field['show_locations_list'] ) ) {
			return false;
		}

		if ( empty( $field['choices'] ) ) {
			return false;
		}

		if ( empty( $field['allow_location_selection'] ) ) {
			return false;
		}

		$valid_choices = $this->get_valid_choices( $field['choices'] );

		return count( $valid_choices ) > 1;
	}

	/**
	 * Replace a link with an HTML link tag.
	 *
	 * @since 2.13.0
	 *
	 * @param string $field_value Field value.
	 * @param array  $field       Field data.
	 * @param array  $form_data   Processed form settings/data, prepared to be used later.
	 * @param string $context     Context usage.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function replace_link( $field_value, $field, $form_data, $context ): string {

		$field_value = (string) $field_value;
		$context     = (string) $context;

		if ( ! $this->is_context_support_html( $context ) ) {
			return $field_value;
		}

		if ( ! $this->is_map_type( $field ) ) {
			return $field_value;
		}

		if ( empty( $field['location'] ) ) {
			return $field_value;
		}

		if ( ! isset( $field['id'] ) ) {
			return $field_value;
		}

		$field_settings = $this->get_field_from_form_data( $field['id'], $form_data );

		if ( ! $field_settings ) {
			return $field_value;
		}

		$value_parts = [];
		$location    = $field['location'];

		if ( ! wpforms_is_empty_string( $location['name'] ) ) {
			$value_parts[] = sprintf( '<strong>%1$s</strong>', $location['name'] );
		}

		if ( ! wpforms_is_empty_string( $location['address'] ) ) {
			$value_parts[] = $location['address'];
		}

		$value_parts[] = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			$this->get_map_url( $location, $field_settings ),
			esc_html__( 'View on Map', 'wpforms-geolocation' )
		);

		return implode( "\n", $value_parts );
	}

	/**
	 * Retrieve field data from form settings using its ID.
	 *
	 * @since 2.13.0
	 *
	 * @param int   $field_id  ID of the requested field.
	 * @param array $form_data Processed form settings/data, structured for retrieval.
	 *
	 * @return array|null
	 * @noinspection PhpMissingParamTypeInspection
	 */
	private function get_field_from_form_data( $field_id, $form_data ): ?array { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded

		if ( ! empty( $form_data['fields'][ $field_id ] ) ) {
			return $form_data['fields'][ $field_id ];
		}

		foreach ( $form_data['fields'] as $field ) {
			if ( empty( $field['type'] ) || ! Helpers::is_layout_based_field( $field['type'] ) ) {
				continue;
			}

			if ( empty( $field['columns'] ) || ! is_array( $field['columns'] ) ) {
				continue;
			}

			foreach ( $field['columns'] as $column ) {
				if ( empty( $column['fields'] ) || ! is_array( $column['fields'] ) ) {
					continue;
				}

				foreach ( $column['fields'] as $layout_field ) {
					if ( is_array( $layout_field ) && isset( $layout_field['id'] ) && (int) $layout_field['id'] === (int) $field_id ) {
						return $layout_field;
					}
				}
			}
		}

		return null;
	}

	/**
	 * Determine if we should replace a map link with an HTML link in the context.
	 *
	 * @since 2.13.0
	 *
	 * @param string $context Context.
	 *
	 * @return bool
	 */
	private function is_context_support_html( string $context ): bool {

		if ( $context === 'entry-preview' ) {
			return true;
		}

		if ( $context !== 'entry-single' ) {
			return false;
		}

		return wpforms_is_admin_page( 'entries', 'details' ) || wpforms_is_admin_page( 'entries', 'edit' );
	}

	/**
	 * Append a map thumbnail after value when the Show Thumbnail In Entry field option is enabled.
	 *
	 * @since 2.13.0
	 *
	 * @param string $field_value Field value.
	 * @param array  $field       Field data.
	 * @param array  $form_data   Processed form settings/data, prepared to be used later.
	 * @param string $context     Context usage.
	 *
	 * @return string
	 *
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function append_entry_thumbnail( $field_value, $field, $form_data, $context ): string {

		$field_value = (string) $field_value;

		if ( ! wpforms_is_admin_page( 'entries', 'details' ) ) {
			return $field_value;
		}

		if ( $context !== 'entry-single' ) {
			return $field_value;
		}

		if ( ! $this->is_map_type( $field ) ) {
			return $field_value;
		}

		if ( ! isset( $field['id'] ) ) {
			return $field_value;
		}

		$field_settings = $this->get_field_from_form_data( $field['id'], $form_data );

		if ( ! $field_settings ) {
			return $field_value;
		}

		if ( empty( $field_settings['show_thumbnail_in_entry'] ) ) {
			return $field_value;
		}

		if ( empty( $field['location'] ) ) {
			return $field_value;
		}

		$map_url = $this->get_embed_map_url( $field['location'], $field_settings );

		if ( ! $map_url ) {
			return $field_value;
		}

		$thumbnail = wpforms_render(
			WPFORMS_GEOLOCATION_PATH . 'templates/metabox/map-thumbnail',
			[
				'map_url' => $map_url,
			],
			true
		);

		return $field_value . "\n" . trim( $thumbnail );
	}

	/**
	 * Get embed map URL for an embed iframe.
	 *
	 * @since 2.13.0
	 *
	 * @param array $location       Submitted location data.
	 * @param array $field_settings Field settings.
	 *
	 * @return string
	 */
	private function get_embed_map_url( array $location, array $field_settings ): string {

		/**
		 * Allow modifying the embed map URL.
		 *
		 * @since 2.13.0
		 *
		 * @param string $map_url        Embed map URL.
		 * @param array  $location       Submitted location data.
		 * @param array  $field_settings Field settings.
		 */
		return (string) apply_filters( 'wpforms_geolocation_forms_field_get_embed_map_url', $this->build_embed_map_url( $location, $field_settings ), $location, $field_settings );
	}

	/**
	 * Get map URL.
	 *
	 * @since 2.13.0
	 *
	 * @param array $location       Submitted location data.
	 * @param array $field_settings Field settings.
	 *
	 * @return string
	 */
	private function get_map_url( array $location, array $field_settings ): string {

		$map_url = $this->build_embed_map_url( $location, $field_settings );
		$map_url = remove_query_arg( 'output', $map_url );

		/**
		 * Allow modifying the embed map URL.
		 *
		 * @since 2.13.0
		 *
		 * @param string $map_url        Embed map URL.
		 * @param array  $location       Submitted location data.
		 * @param array  $field_settings Field settings.
		 */
		return (string) apply_filters( 'wpforms_geolocation_forms_field_get_map_url', $map_url, $location, $field_settings );
	}

	/**
	 * Build embed map URL.
	 *
	 * @since 2.13.0
	 *
	 * @param array $location       Submitted location data.
	 * @param array $field_settings Field settings.
	 *
	 * @return string
	 */
	private function build_embed_map_url( array $location, array $field_settings ): string {

		if ( ! isset( $location['latitude'], $location['longitude'] ) || wpforms_is_empty_string( $location['latitude'] ) || wpforms_is_empty_string( $location['longitude'] ) ) {
			return '';
		}

		$latitude  = $location['latitude'];
		$longitude = $location['longitude'];

		if ( $this->get_active_provider_slug() === 'mapbox-search' ) {
			$offset = 0.002;

			$min_longitude = $longitude - $offset;
			$max_longitude = $longitude + $offset;
			$min_latitude  = $latitude - $offset;
			$max_latitude  = $latitude + $offset;

			$bbox = "$min_longitude,$min_latitude,$max_longitude,$max_latitude";

			return add_query_arg(
				[
					'bbox'   => $bbox,
					'layer'  => 'mapnik',
					'marker' => "$latitude,$longitude",
				],
				'https://www.openstreetmap.org/export/embed.html'
			);
		}

		$zoom = $field_settings['zoom_level'] ?? 15;

		/**
		 * Allow changing the default zoom level for the map thumbnail.
		 *
		 * @since 2.13.0
		 *
		 * @param int    $zoom    Default zoom level.
		 * @param string $context Context usage.
		 */
		$zoom = (int) apply_filters( 'wpforms_geolocation_map_zoom', $zoom, 'entry-map-field' ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

		return add_query_arg(
			[
				'q'      => "$latitude,$longitude",
				'll'     => "$latitude,$longitude",
				'z'      => $zoom,
				'output' => 'embed',
			],
			'https://maps.google.com/maps'
		);
	}

	/**
	 * Get rid of HTML tags for Google Sheets value.
	 *
	 * @since 2.13.0
	 *
	 * @param string $value     Field value.
	 * @param int    $field_id  Field ID.
	 * @param array  $form_data Processed form settings/data, prepared to be used later.
	 * @param array  $fields    Form fields data.
	 *
	 * @return string
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function google_sheets_value( $value, $field_id, $form_data, $fields ): string {

		$value = (string) $value;

		if ( empty( $fields[ $field_id ] ) ) {
			return $value;
		}

		if ( ! $this->is_map_type( $fields[ $field_id ] ) ) {
			return $value;
		}

		return wp_strip_all_tags( $value );
	}

	/**
	 * Get rid of HTML tags for Google Sheets value for the `{field_id="1"}` smart tag.
	 *
	 * @since 2.13.0
	 *
	 * @param string   $value            Field value.
	 * @param array    $form_data        Form data.
	 * @param array    $fields           List of fields.
	 * @param string   $entry_id         Entry ID.
	 * @param SmartTag $smart_tag_object Smart tag.
	 * @param string   $context          Context.
	 *
	 * @return string
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function google_sheets_smart_tags_value( $value, $form_data, $fields, $entry_id, $smart_tag_object, $context ): string {

		$value = (string) $value;

		if ( $context !== 'google-sheets-custom-value' ) {
			return $value;
		}

		$attributes = $smart_tag_object->get_attributes();

		if ( ! isset( $attributes['field_id'] ) || $attributes['field_id'] === '' ) {
			return $value;
		}

		$field_parts = explode( '|', $attributes['field_id'] );
		$field_id    = $field_parts[0];

		if ( ! isset( $fields[ $field_id ] ) || $fields[ $field_id ] === '' ) {
			return $value;
		}

		if ( ! $this->is_map_type( $fields[ $field_id ] ) ) {
			return $value;
		}

		return wp_strip_all_tags( $value );
	}

	/**
	 * Determine if the field is a Map field.
	 *
	 * @since 2.13.0
	 *
	 * @param array $field Field data.
	 *
	 * @return bool
	 */
	private function is_map_type( array $field ): bool {

		return ! empty( $field['type'] ) && $field['type'] === $this->type;
	}

	/**
	 * Save Find Nearby Location and Search Radius to user meta.
	 *
	 * @since 2.13.0
	 *
	 * @param array $post_data Post data.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 * @noinspection PhpMissingParamTypeInspection
	 */
	public function save_user_settings( $post_data, $form_data ): array {

		$post_data = (array) $post_data;

		if ( empty( $form_data['fields'] ) ) {
			return $post_data;
		}

		foreach ( $form_data['fields'] as $field ) {
			if ( ! $this->is_map_type( $field ) ) {
				continue;
			}

			$current_user_id = get_current_user_id();
			$search_radius   = ! empty( $field['search_radius'] ) ? absint( $field['search_radius'] ) : 0;
			$search_radius   = $search_radius > 0 ? $search_radius : self::DEFAULT_SEARCH_RADIUS;

			update_user_meta( $current_user_id, self::NEARBY_LOCATIONS_KEY, ! empty( $field['find_nearby_locations'] ) );
			update_user_meta( $current_user_id, self::NEARBY_LOCATIONS_RADIUS_KEY, $search_radius );

			// All map fields are synced in the builder, so we only need the first one.
			break;
		}

		return $post_data;
	}
}
