/* global wpforms_builder, WPForms, WPFormsBuilder, WPFormsUtils, WPFormsGeolocationMapField, wpforms_geolocation_map_field, mapboxsearch, google, mapboxgl  */

/**
 * @param google.maps
 * @param google.maps.places
 * @param google.maps.places.Autocomplete
 * @param google.maps.event.clearInstanceListeners
 * @param mapboxsearch
 * @param mapboxsearch.config
 * @param wpforms_geolocation_map_field.settings.access_token
 * @param wpforms_geolocation_map_field.allow_location_selection_disabled
 * @param wpforms_geolocation_map_field.incompatible_with_disabled_dragging
 * @param wpforms_geolocation_map_field.zoom_disabled
 * @param wpforms_geolocation_map_field.provider_not_configured.desription
 * @param wpforms_geolocation_map_field.provider_not_configured.title
 * @param wpforms_geolocation_map_field.thumbnail_in_entry_disabled
 * @param wpforms_geolocation_map_field.detection_denied
 * @param wpforms_geolocation_map_field.detection_not_supported
 * @param wpforms_geolocation_map_field.unable_retrieve_location
 * @param wpforms_geolocation_map_field.default_search_radius
 * @param wpforms_builder.heads_up
 * @param wpforms_builder.option_disabled
 */

const WPFormsGeolocationMapFieldBuilder = window.WPFormsGeolocationMapFieldBuilder || ( function( document, window, $ ) {
	const app = {
		/**
		 * Short country codes that use miles instead of kilometers.
		 *
		 * @since 2.13.0
		 */
		milesCountries: [ 'us', 'gb' ],
		/**
		 * Selectors.
		 *
		 * @since 2.13.0
		 *
		 * @type {Object}
		 */
		selectors: {
			builder: '#wpforms-builder',
			locationAddressField: '.wpforms-geolocation-map-field-location-address',
			locationMarkerTypeField: '.wpforms-geolocation-map-field-location-marker-type',
			locationLatitudeField: '.wpforms-geolocation-map-field-location-latitude',
			locationLongitudeField: '.wpforms-geolocation-map-field-location-longitude',
			locationNameField: '.wpforms-geolocation-map-field-location-name',
			locationDescriptionField: '.wpforms-geolocation-map-field-location-description',
			locationIconPreview: '.wpforms-icon-select i',
			locationIconField: '.wpforms-icon-select .source-icon',
			locationIconStyleField: '.wpforms-icon-select .source-icon-style',
			locationIconLabel: '.wpforms-icon-select span',
			locationIconColorFieldWrapper: '.wpforms-geolocation-map-field-location-icon-color',
			locationIconColorField: '.wpforms-geolocation-map-field-location-icon-color .wpforms-color-picker',
			locationIconColorFieldSwatches: '.wpforms-field-option-row-locations .minicolors-swatch',
			locationImageUrlField: '.wpforms-image-upload .source',
			locationSizeField: '.wpforms-geolocation-map-field-location-size',
			fieldOptions: '.wpforms-field-option-map',
			locationsFields: '.wpforms-field-option-row-locations',
			locationRows: '.choices-list > li',
			colorPicker: '.wpforms-color-picker',
			findNearbyLocations: '.wpforms-field-option-row-find_nearby_locations input',
			searchRadius: '.wpforms-field-option-row-search_radius select',
			searchRadiusRow: '.wpforms-field-option-row-search_radius',
			showLocationsListFieldOption: '.wpforms-field-option-row-show_locations_list input',
			allowLocationSelectionFieldOptionRow: '.wpforms-field-option-row-allow_location_selection',
			allowLocationSelectionFieldOption: '.wpforms-field-option-row-allow_location_selection input',
			disableDraggingFieldOption: '.wpforms-field-option-row-disable_dragging input',
			incompatibleWithDisabledDraggingFieldOptions: '.wpforms-field-option-row-disable_mouse_zooming, .wpforms-field-option-row-hide_location_info',
			zoomLevelFieldOptionRow: '.wpforms-field-option-row-zoom_level',
			zoomLevelFieldOption: '.wpforms-field-option-row-zoom_level select',
			showInEntryFieldOption: '.wpforms-field-option-row-show_in_entry input',
			showThumbnailInEntryFieldOption: '.wpforms-field-option-row-show_thumbnail_in_entry input',
			showThumbnailInEntryFieldOptionRow: '.wpforms-field-option-row-show_thumbnail_in_entry',
			fieldSizeFieldOptionRow: '.wpforms-field-option-map .wpforms-field-option-row-size select',
			mapboxElement: 'mapbox-address-autofill',
			mapFieldPreview: '.wpforms-field-map',
			mapFieldPreviewMapWrapper: '.wpforms-geolocation-map',
			mapFieldPreviewMap: 'gmp-map',
			mapFieldPreviewMarker: 'gmp-advanced-marker, .mapboxgl-marker',
			mapFieldPreviewMarkerName: '.wpforms-map-field-marker-content-name',
			mapFieldPreviewMarkerDescription: '.wpforms-map-field-marker-content-description',
			mapFieldPreviewMarkerPin: '.wpforms-map-field-marker-pin',
			mapFieldPreviewMarkerIconPin: '.wpforms-map-field-marker-pin-icon',
			mapFieldPreviewMarkerIconBackgroundPin: '.wpforms-map-field-marker-pin-icon-background',
			mapFieldPreviewMarkerImagePin: '.wpforms-map-field-marker-pin-image',
			mapFieldPreviewList: '.wpforms-field-map-choices',
		},
		/**
		 * Classes used in the builder.
		 *
		 * @since 2.13.0
		 */
		classes: {
			disabled: 'wpforms-disabled',
			hidden: 'wpforms-hidden',
			error: 'wpforms-error',
			googleAutocompleteInput: 'pac-target-input',
			notConfiguredProvider: 'wpforms-add-fields-configure-geolocation-provider',
			fieldMapPreview: 'wpforms-field-map',
		},
		/**
		 * Run engine.
		 *
		 * @since 2.13.0
		 */
		init() {
			$( app.ready );

			$( window ).on( 'load', function() {
				// in the case of jQuery 3.+ we need to wait for a ` ready ` event first.
				if ( typeof $.ready.then === 'function' ) {
					$.ready.then( app.load );
				} else {
					app.load();
				}
			} );
		},
		/**
		 * Document ready.
		 *
		 * @since 2.13.0
		 */
		ready() {
			app.bindEvents();
			app.loadDefaultState();
		},
		/**
		 * Load default builder state.
		 *
		 * @since 2.13.0
		 */
		loadDefaultState() {
			WPFormsBuilder.UndoRedo?.preventRecord( true );

			$( app.selectors.fieldOptions ).each( function() {
				app.fieldOptions.updateZoomAvailability( $( this ) );
			} );

			$( app.selectors.showInEntryFieldOption ).trigger( 'change' );
			app.fieldOptions.triggerLocationList();
			app.fieldOptions.triggerDragging();

			WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
		},
		/**
		 * Load providers logic.
		 *
		 * @since 2.13.0
		 */
		load() {
			app.preview.init();
			app.fieldOptions.locations.init();
		},
		/**
		 * Bind events.
		 *
		 * @since 2.13.0
		 */
		bindEvents() { // eslint-disable-line max-lines-per-function
			$( app.selectors.builder )
				// Locations related events.
				.on( 'input', app.selectors.locationAddressField, app.fieldOptions.locations.autocomplete.clearCoordinates )
				.on( 'input', app.selectors.locationAddressField, app.fieldOptions.locations.autocomplete.resetAddressValidation )
				.on( 'focusout', app.selectors.locationAddressField, app.fieldOptions.locations.autocomplete.validateAddress )
				.on( 'change', app.selectors.locationMarkerTypeField, app.fieldOptions.locations.changeMarkerType )
				.on( 'wpformsFieldChoiceAdd', app.fieldOptions.locations.afterChoiceAdded )
				.on( 'wpformsFieldChoiceBeforeCloning', app.fieldOptions.locations.beforeCloning )
				.on( 'input', app.selectors.locationSizeField, app.fieldOptions.locations.updateMarkerSize )
				// Field related events.
				.on( 'wpformsBeforeFieldDuplicate', app.general.beforeDuplicated )
				.on( 'wpformsBeforeFieldAdd', app.general.beforeFieldAdd )
				.on( 'wpformsFieldAdd', app.general.afterFieldAdded )
				.on( 'wpformsBuilderReady', app.general.builderReady )
				.on( 'wpformsUndoRedoRun', app.general.onUndoRedoRun )
				.on( 'wpformsMapFieldUpdateCoordinates', app.general.onUpdateCoordinates )
				// Field Options related events.
				.on( 'input', app.selectors.locationLatitudeField, app.fieldOptions.allowLocationSelection )
				.on( 'input', app.selectors.locationLongitudeField, app.fieldOptions.allowLocationSelection )
				.on( 'wpformsFieldChoiceDelete', app.fieldOptions.triggerLocationList )
				.on( 'change', app.selectors.findNearbyLocations, app.fieldOptions.findNearbyLocations )
				.on( 'change', app.selectors.searchRadius, app.fieldOptions.searchRadius )
				.on( 'wpformsFieldOptionTabToggle', app.fieldOptions.openFieldOptions )
				.on( 'change', app.selectors.showLocationsListFieldOption, app.fieldOptions.allowLocationSelection )
				.on( 'input', app.selectors.locationLatitudeField, app.fieldOptions.changeZoomAvailability )
				.on( 'input', app.selectors.locationLongitudeField, app.fieldOptions.changeZoomAvailability )
				.on( 'change', app.selectors.disableDraggingFieldOption, app.fieldOptions.disableDragging )
				.on( 'change', app.selectors.showInEntryFieldOption, app.fieldOptions.toggleThumbnailInEntry )
				.on( 'mousedown', app.selectors.locationIconColorFieldSwatches, app.fieldOptions.fixSwatches )
				// Compatibility-related events.
				.on( 'click', app.selectors.incompatibleWithDisabledDraggingFieldOptions, app.compatibility.incompatibleWithDisabledDraggingPopup )
				.on( 'click', app.selectors.allowLocationSelectionFieldOptionRow, app.compatibility.allowLocationSelectionDisabledPopup )
				.on( 'click', app.selectors.zoomLevelFieldOptionRow, app.compatibility.zoomDisabledPopup )
				.on( 'click', app.selectors.showThumbnailInEntryFieldOptionRow, app.compatibility.thumbnailInEntryDisabledPopup )
				.on( 'wpformsBeforeFieldAddOnClick', app.compatibility.providerNotConfiguredPopup )
				// Preview related events.
				.on( 'change', '.wpforms-field-map-settings', app.preview.updateMapSettings )
				.on( 'input', app.selectors.locationNameField, app.preview.updateMarkerName )
				.on( 'input', app.selectors.locationDescriptionField, app.preview.updateMarkerDescription )
				.on( 'input', app.selectors.locationIconField, app.preview.updateMakerIcon )
				.on( 'input', app.selectors.locationIconColorField, app.preview.updateMarkerIconColor )
				.on( 'input', app.selectors.locationMarkerTypeField, app.preview.updateMarkerType )
				.on( 'input', app.selectors.locationImageUrlField, app.preview.updateMarkerImage )
				.on( 'input', app.selectors.locationSizeField, app.preview.updateMarkerSize )
				.on( 'wpformsFieldChoiceDelete', app.preview.deleteMarker )
				.on( 'input', app.selectors.locationLatitudeField, app.preview.updateMarkerCoordinates )
				.on( 'input', app.selectors.locationLongitudeField, app.preview.updateMarkerCoordinates )
				.on( 'wpformsFieldDragStart', app.preview.handleDragStart )
				.on( 'wpformsFieldMove', app.preview.handleDragMoved )
				.on( 'change', app.selectors.allowLocationSelectionFieldOption, app.preview.listLocation.updateFieldType )
				.on( 'input', app.selectors.locationNameField, app.preview.listLocation.updateName )
				.on( 'input', app.selectors.locationLatitudeField, app.preview.listLocation.updateAddress )
				.on( 'wpformsFieldAdd', app.preview.listLocation.fieldAdd )
				.on( 'wpformsFieldChoiceAdd', app.preview.listLocation.addItem )
				.on( 'wpformsFieldChoiceDelete', app.preview.listLocation.removeItem )
				.on( 'wpformsFieldChoiceMove', app.preview.listLocation.changeOrder )
				.on( 'change', app.selectors.fieldSizeFieldOptionRow, app.preview.resize )
				// AI Form Generator related events.
				.on( 'wpformsAIFormGeneratorFieldPreviewDisplayed', app.preview.initAIGeneratorPreview )
				.on( 'wpformsAIFormGeneratorPreviewClear', app.preview.destroyAIGeneratorPreview );

			document.addEventListener( 'wpformsGeolocationApiError', app.fieldOptions.locations.onApiError );
		},
		/**
		 * Compatibility related logic.
		 *
		 * @since 2.13.0
		 */
		compatibility: {
			/**
			 * Don't allow adding a new map field and show a popup when the provider isn't configured well.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e      Event.
			 * @param {string} type   Field type.
			 * @param {jQuery} $field Add field button element.
			 */
			providerNotConfiguredPopup( e, type, $field ) {
				if ( type !== 'map' ) {
					return;
				}

				if ( ! $field.hasClass( app.classes.notConfiguredProvider ) ) {
					return;
				}

				e.preventDefault();

				app.showAlert(
					wpforms_geolocation_map_field.provider_not_configured.title,
					wpforms_geolocation_map_field.provider_not_configured.description
				);
			},
			/**
			 * Show a popup when an option isn't available withe enabled the Disabled Dragging option.
			 *
			 * @since 2.13.0
			 */
			incompatibleWithDisabledDraggingPopup() {
				if ( ! $( this ).hasClass( app.classes.disabled ) ) {
					return;
				}

				app.showAlert(
					wpforms_builder.option_disabled,
					wpforms_geolocation_map_field.incompatible_with_disabled_dragging
				);
			},
			/**
			 * Show a popup when the Allow Location Selection option is disabled.
			 *
			 * @since 2.13.0
			 */
			allowLocationSelectionDisabledPopup() {
				if ( ! $( this ).hasClass( app.classes.disabled ) ) {
					return;
				}

				app.showAlert(
					wpforms_builder.option_disabled,
					wpforms_geolocation_map_field.allow_location_selection_disabled
				);
			},
			/**
			 * Show a popup when the Zoom Level option is disabled.
			 *
			 * @since 2.13.0
			 */
			zoomDisabledPopup() {
				if ( ! $( `.${ app.classes.disabled }`, $( this ) ).length ) {
					return;
				}

				app.showAlert(
					wpforms_builder.option_disabled,
					wpforms_geolocation_map_field.zoom_disabled
				);
			},
			/**
			 * Show a popup when the Show Thumbnail in Entry option is disabled.
			 *
			 * @since 2.13.0
			 */
			thumbnailInEntryDisabledPopup() {
				if ( ! $( this ).hasClass( app.classes.disabled ) ) {
					return;
				}

				app.showAlert(
					wpforms_builder.option_disabled,
					wpforms_geolocation_map_field.thumbnail_in_entry_disabled
				);
			},
		},
		/**
		 * General logic related to map field.
		 *
		 * @since 2.13.0
		 */
		general: {
			/**
			 * Flag to track if the field being added is a new field.
			 * Set to false during field duplication or undo/redo operations.
			 *
			 * @since 2.13.0
			 */
			isNewField: true,
			/**
			 * Destroy JS fields instances before field cloning.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e       Event object.
			 * @param {number} fieldId Field ID.
			 */
			beforeDuplicated( e, fieldId ) {
				const $fieldOptions = app.fieldOptions.getById( fieldId );

				app.fieldOptions.locations.destroyAllForField( $fieldOptions );
				app.general.isNewField = false;
			},
			/**
			 * Handles logic before a new field is added in the builder interface.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e       Event triggered by the field add action.
			 * @param {string} fieldId Identifier of the field to be added.
			 * @param {string} type    Type of the field to be added.
			 *
			 * @return {void}
			 */
			beforeFieldAdd( e, fieldId, type ) {
				if ( type !== 'map' ) {
					return;
				}

				if ( wpforms_builder.icon_choices.is_installed ) {
					return;
				}

				WPFormsBuilder.iconChoices.openInstallPromptModal();

				e.preventDefault();
			},
			/**
			 * Initialize location autocomplete after a new map field was added.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e       Event.
			 * @param {number} fieldId Field ID.
			 * @param {string} type    Field type.
			 */
			afterFieldAdded( e, fieldId, type ) {
				if ( type !== 'map' ) {
					return;
				}

				WPFormsBuilder.UndoRedo?.preventRecord( true );

				const $fieldOptions = app.fieldOptions.getById( fieldId ),
					$list = $( app.selectors.locationsFields, $fieldOptions ),
					$rows = $( app.selectors.locationRows, $list ),
					$existedFieldOptions = $( app.selectors.fieldOptions ).not( $fieldOptions );

				// Sync the Find Nearby Locations and Search Radius field options for the newly added field.
				if ( $existedFieldOptions.length ) {
					const $existedToggle = $( app.selectors.findNearbyLocations, $existedFieldOptions ),
						isChecked = $existedToggle.is( ':checked' ),
						appliedRadius = $( app.selectors.searchRadius, $existedFieldOptions ).val(),
						$toggle = $( app.selectors.findNearbyLocations, $fieldOptions ),
						$searchRadius = $( app.selectors.searchRadius, $fieldOptions ),
						$searchRadiusRow = $( app.selectors.searchRadiusRow, $fieldOptions );

					$searchRadiusRow.toggleClass( 'wpforms-hidden', ! isChecked );
					$searchRadius.val( appliedRadius );
					$toggle.prop( 'checked', isChecked );
				}

				app.fieldOptions.locations.init();
				app.fieldOptions.triggerLocationList();
				app.fieldOptions.triggerDragging();

				// Set the default icon only when the field is added fresh, not when duplicated or during undo/redo.
				if ( app.general.isNewField ) {
					app.fieldOptions.locations.setDefaultIcon( $rows );
				}

				// Reset the flag.
				app.general.isNewField = true;

				WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );

				// We need a tick to properly render a map after field duplication.
				setTimeout( app.preview.init, 0 );
			},
			/**
			 * Checks if the builder is ready by verifying required field options' presence
			 * and icon choices installation status.
			 *
			 * @since 2.13.0
			 */
			builderReady() {
				if ( ! $( app.selectors.fieldOptions ).length ) {
					return;
				}

				if ( ! wpforms_builder.icon_choices.is_installed ) {
					WPFormsBuilder.iconChoices.openInstallPromptModal( true );
				}
			},
			/**
			 * Handle wpformsUndoRedoRun event.
			 * Re-initialize map field previews and field options on undo/redo.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e           Event object.
			 * @param {string} commandType Command type (undo or redo).
			 * @param {Object} command     Command object.
			 */
			onUndoRedoRun( e, commandType, command ) {
				const commandId = command?.constructor?.id?.toString();

				if ( ! commandId?.length ) {
					return;
				}

				// Field operations.
				if (
					commandId.includes( 'ActionField' ) ||
					commandId.includes( 'ActionMultiField' )
				) {
					$( app.selectors.fieldOptions ).each( function() {
						app.fieldOptions.locations.destroyAllForField( $( this ) );
					} );

					// Wait a tick until a map preview is destroyed.
					setTimeout( app.load, 0 );

					return;
				}

				// Location operations.
				if (
					( commandId === 'ActionItemsAddRemoveCommand' && command?.args?.fieldType === 'map' ) ||
					( commandId === 'ChoicesListReorderCommand' && command?.args?.fieldType === 'location' )
				) {
					const $fieldOptions = app.fieldOptions.getById( command?.args?.fieldId );

					app.general.isNewField = false;

					app.fieldOptions.locations.destroyAllForField( $fieldOptions );
					app.general.afterFieldAdded( e, command.args?.fieldId, 'map' );
					WPFormsBuilder.loadColorPickers( $fieldOptions );

					return;
				}

				app.general.undoRedoLocationAddress( commandType, command );
			},

			/**
			 * Process location address field undo/redo.
			 *
			 * @since 2.13.0
			 *
			 * @param {string} commandType Command type (undo or redo).
			 * @param {Object} command     Command object.
			 */
			undoRedoLocationAddress( commandType, command ) {
				// Update location coordinates.
				if (
					command?.constructor?.id?.toString() === 'InputChangeCommand' &&
					command.$input.is( app.selectors.locationAddressField ) &&
					command.args?.oldCoordinates && command.args?.newCoordinates
				) {
					const $input = WPForms.Admin.Builder.UndoRedoHelpers?.getElement( command.$input );

					if ( ! $input ) {
						return;
					}

					const fieldId = $input.closest( '.choices-list' ).data( 'field-id' ),
						direction = commandType === 'undo' ? 'oldCoordinates' : 'newCoordinates',
						latitude = Number( command.args[ direction ].latitude ),
						longitude = Number( command.args[ direction ].longitude );

					WPFormsBuilder.UndoRedo.preventRecord( true );

					app.fieldOptions.locations.autocomplete.updateCoordinates( $input, latitude, longitude );
					app.preview.listLocation.refreshPreview( fieldId );

					WPFormsBuilder.UndoRedo.preventRecord( 'continue' );
				}
			},

			/**
			 * Handle wpformsMapFieldUpdateCoordinates event.
			 * Store address and coordinates within Undo/Redo command.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e              Event object.
			 *
			 * @param {jQuery} $addressField  Location address field.
			 * @param {Object} newCoordinates New coordinates.
			 * @param {Object} oldCoordinates Old coordinates.
			 */
			onUpdateCoordinates( e, $addressField, newCoordinates, oldCoordinates ) {
				if ( WPFormsBuilder.UndoRedo?.isRecordPrevented() ) {
					return;
				}

				const command = WPFormsBuilder.UndoRedo?.getCurrentCommand( 'undo' );

				if ( command?.args?.newCoordinates || ! command?.$input?.is( app.selectors.locationAddressField ) ) {
					return;
				}

				command.newValue = $addressField.val();

				command.args = command.args ?? {};
				command.args.newCoordinates = newCoordinates;
				command.args.oldCoordinates = oldCoordinates;
			},
		},
		/**
		 * Field options related logic.
		 *
		 * @since 2.13.0
		 */
		fieldOptions: {
			/**
			 * Get field options by field ID.
			 *
			 * @since 2.13.0
			 *
			 * @param {number} fieldId Field ID.
			 *
			 * @return {jQuery} Field preview element.
			 */
			getById( fieldId ) {
				return $( `#wpforms-field-option-${ fieldId }` );
			},
			/**
			 * Trigger the Show List of Locations fields change event.
			 *
			 * @since 2.13.0
			 */
			triggerLocationList() {
				WPFormsBuilder.UndoRedo?.preventRecord( true );
				$( app.selectors.showLocationsListFieldOption ).trigger( 'change' );
				WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
			},
			/**
			 * Trigger the Disable Dragging fields change event.
			 *
			 * @since 2.13.0
			 */
			triggerDragging() {
				WPFormsBuilder.UndoRedo?.preventRecord( true );
				$( app.selectors.disableDraggingFieldOption ).trigger( 'change' );
				WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
			},
			/**
			 * Set autocomplete restriction by country to the address input fields when we have the option is checked.
			 * or remove any restriction when the option isn't checked.
			 *
			 * @since 2.13.0
			 */
			findNearbyLocations() { // eslint-disable-line max-lines-per-function
				const provider = app.providers.getCurrentProvider();

				if ( ! provider ) {
					return;
				}

				const $field = $( this ),
					isChecked = $field.is( ':checked' ),
					$toggles = $( app.selectors.findNearbyLocations ),
					$searchRadiusRow = $( app.selectors.searchRadiusRow );

				// Sync all toggles.
				WPFormsBuilder.UndoRedo?.preventRecord( true );
				$searchRadiusRow.toggleClass( 'wpforms-hidden', ! isChecked );
				$toggles.prop( 'checked', isChecked );
				WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );

				if ( ! isChecked ) {
					provider.removeAutocompleteRestriction();

					return;
				}

				if ( ! navigator.geolocation ) {
					app.showAlert(
						wpforms_builder.heads_up,
						wpforms_geolocation_map_field.detection_not_supported
					);

					WPFormsBuilder.UndoRedo?.preventRecord( true );
					$field.prop( 'checked', false ).trigger( 'change' );
					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );

					return;
				}

				if ( app.providers.customerLocation ) {
					provider.applyAutocompleteRestriction();
					app.fieldOptions.updateSearchRadiusOptions();

					return;
				}

				navigator.geolocation.getCurrentPosition(
					function( position ) {
						provider
							.detectCustomerLocation( position.coords.latitude, position.coords.longitude )
							.then( function() {
								provider.applyAutocompleteRestriction();
								app.fieldOptions.updateSearchRadiusOptions();
							} )
							.catch( function() {
								app.showAlert(
									wpforms_builder.heads_up,
									wpforms_geolocation_map_field.unable_retrieve_location
								);

								WPFormsBuilder.UndoRedo?.preventRecord( true );
								$field.prop( 'checked', false ).trigger( 'change' );
								WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
							} );
					},
					function( error ) {
						const message = error.code === error.PERMISSION_DENIED
							? wpforms_geolocation_map_field.detection_denied
							: wpforms_geolocation_map_field.unable_retrieve_location;

						app.showAlert(
							wpforms_builder.heads_up,
							message
						);

						WPFormsBuilder.UndoRedo?.preventRecord( true );
						$field.prop( 'checked', false ).trigger( 'change' );
						WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
					}
				);
			},
			/**
			 * Open field options.
			 *
			 * @since 2.13.0
			 * @param {Event}  e  Event.
			 * @param {number} id Field ID.
			 */
			openFieldOptions( e, id ) {
				const $fieldOptions = app.fieldOptions.getById( id );

				if ( ! $fieldOptions.hasClass( 'wpforms-field-option-map' ) ) {
					return;
				}

				if ( ! wpforms_builder.icon_choices.is_installed ) {
					WPFormsBuilder.iconChoices.openInstallPromptModal( true );
				}

				const $nearbyLocation = $( app.selectors.findNearbyLocations, $fieldOptions );

				if ( ! $nearbyLocation.is( ':checked' ) ) {
					return;
				}

				WPFormsBuilder.UndoRedo?.preventRecord( true );
				$nearbyLocation.trigger( 'change' );
				WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
			},
			/**
			 * Determine if the Allow Location Selection fields should be available for usage.
			 *
			 * @since 2.13.0
			 */
			allowLocationSelection() {
				const $fieldOptions = $( this ).closest( app.selectors.fieldOptions ),
					$locations = $( app.selectors.locationsFields, $fieldOptions ),
					fieldId = $fieldOptions.data( 'field-id' ),
					$preview = app.preview.getById( fieldId ),
					$list = $( app.selectors.mapFieldPreviewList, $preview );

				if ( ! $locations.length ) {
					$list.addClass( app.classes.hidden );

					return;
				}

				const showLocationList = $( app.selectors.showLocationsListFieldOption, $fieldOptions ).is( ':checked' ),
					$field = $( app.selectors.allowLocationSelectionFieldOption, $fieldOptions ),
					$fieldRow = $( app.selectors.allowLocationSelectionFieldOptionRow, $fieldOptions );

				if ( ! showLocationList ) {
					WPFormsBuilder.UndoRedo?.preventRecord( true );
					$field.prop( 'checked', false ).trigger( 'change' );
					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );

					$fieldRow.addClass( app.classes.disabled );
					$list.addClass( app.classes.hidden );

					return;
				}

				let completedLocations = 0;

				$( app.selectors.locationRows, $locations ).each( function() {
					const $row = $( this ),
						$latitude = $( app.selectors.locationLatitudeField, $row ),
						$longitude = $( app.selectors.locationLongitudeField, $row );

					if ( $latitude.val() && $longitude.val() ) {
						completedLocations++;
					}

					if ( completedLocations >= 2 ) {
						return false;
					}
				} );

				const showLocations = completedLocations >= 1,
					isAllowed = completedLocations >= 2;

				$list.toggleClass( app.classes.hidden, ! showLocations );
				$fieldRow.toggleClass( app.classes.disabled, ! isAllowed );

				if ( ! isAllowed ) {
					WPFormsBuilder.UndoRedo?.preventRecord( true );
					$field.prop( 'checked', false ).trigger( 'change' );
					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
				}
			},
			/**
			 * Disable/enable the Zoom Level field option.
			 *
			 * @since 2.13.0
			 */
			changeZoomAvailability() {
				const $fieldOptions = $( this ).closest( app.selectors.fieldOptions );

				app.fieldOptions.updateZoomAvailability( $fieldOptions );
			},
			/**
			 * Update the availability of the Zoom Level field option.
			 *
			 * @since 2.13.0
			 *
			 * @param {jQuery} $fieldOptions Field options element.
			 */
			updateZoomAvailability( $fieldOptions ) {
				const $zoom = $( app.selectors.zoomLevelFieldOption, $fieldOptions ),
					$locations = $( app.selectors.locationsFields, $fieldOptions );

				let completedLocations = 0;

				$( app.selectors.locationRows, $locations ).each( function() {
					const $row = $( this ),
						$latitude = $( app.selectors.locationLatitudeField, $row ),
						$longitude = $( app.selectors.locationLongitudeField, $row );

					if ( $latitude.val() && $longitude.val() ) {
						completedLocations++;
					}

					if ( completedLocations >= 2 ) {
						return false;
					}
				} );

				$zoom.toggleClass( app.classes.disabled, completedLocations >= 2 );
			},
			/**
			 * Enable or disable the Disable Mouse Zooming and Hide Location Info field options based on whether Disable Dragging is enabled or disabled.
			 *
			 * @since 2.13.0
			 */
			disableDragging() {
				const $field = $( this ),
					$fieldOptions = $field.closest( app.selectors.fieldOptions ),
					isChecked = $field.is( ':checked' ),
					$dependedFieldRows = $( app.selectors.incompatibleWithDisabledDraggingFieldOptions, $fieldOptions );

				$dependedFieldRows.toggleClass( app.classes.disabled, isChecked );
			},
			/**
			 * Enable or disable the Show Thumbnail in Entry field option based on whether Show in Entry is enabled or disabled.
			 *
			 * @since 2.13.0
			 */
			toggleThumbnailInEntry() {
				const $field = $( this ),
					$fieldOptions = $field.closest( app.selectors.fieldOptions ),
					isChecked = $field.is( ':checked' ),
					$thumbnailInEntryFieldOption = $( app.selectors.showThumbnailInEntryFieldOption, $fieldOptions ),
					$thumbnailInEntryFieldOptionRow = $( app.selectors.showThumbnailInEntryFieldOptionRow, $fieldOptions );

				$thumbnailInEntryFieldOptionRow.toggleClass( app.classes.disabled, ! isChecked );

				if ( ! isChecked ) {
					WPFormsBuilder.UndoRedo?.preventRecord( true );
					$thumbnailInEntryFieldOption.prop( 'checked', false ).trigger( 'change' );
					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
				}
			},
			/**
			 * Update Search Radius field options based on detected country.
			 *
			 * @since 2.13.0
			 */
			updateSearchRadiusOptions() {
				if ( ! app.providers.customerLocation ) {
					return;
				}

				const isMilesCountry = app.providers.isMileCountry();

				if ( ! isMilesCountry ) {
					return;
				}

				const $searchRadiusSelect = $( app.selectors.searchRadius );

				if ( ! $searchRadiusSelect.length ) {
					return;
				}

				const optionsData = $searchRadiusSelect.data( 'miles-options' );

				if ( ! optionsData ) {
					return;
				}

				const customerValue = $searchRadiusSelect.val();

				$searchRadiusSelect.empty();

				Object.entries( $searchRadiusSelect.data( 'miles-options' ) ).forEach( ( [ value, label ] ) => {
					$searchRadiusSelect.append( $( '<option>', { value, text: label } ) );
				} );

				if ( optionsData[ customerValue ] ) {
					$searchRadiusSelect.val( customerValue );

					return;
				}

				$searchRadiusSelect.val( Object.keys( optionsData )[ 0 ] );
			},
			/**
			 * Synchronize Search Radius value across all map fields.
			 *
			 * @since 2.13.0
			 */
			searchRadius() {
				const $changed = $( this ),
					newValue = $changed.val();

				$( app.selectors.searchRadius ).not( $changed ).each( function() {
					WPFormsBuilder.UndoRedo?.preventRecord( true );
					$( this ).val( newValue );
					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
				} );

				const provider = app.providers.getCurrentProvider();

				if ( ! provider ) {
					return;
				}

				provider.applyAutocompleteRestriction();
			},
			/**
			 * Fix minicolor swatches field update.
			 *
			 * @since 2.13.0
			 */
			fixSwatches() {
				const $swatch = $( this ),
					$wrapper = $swatch.closest( app.selectors.locationIconColorFieldWrapper ),
					$input = $( app.selectors.colorPicker, $wrapper );

				// Focus the input. It fixes the preview highlight sticking.
				setTimeout( () => {
					$input.trigger( 'focus' );
				}, 0 );
			},
			/**
			 * Map field Locations field related logic.
			 *
			 * @since 2.13.0
			 */
			locations: {
				/**
				 * Initialize locations.
				 */
				init() {
					app.providers.getCurrentProvider()?.initAutocomplete();
				},
				/**
				 * Destroy all JS elements for a location row.
				 *
				 * @param {jQuery} $row Location row.
				 */
				destroy( $row ) {
					const addressInput = $( app.selectors.locationAddressField, $row ).get( 0 );

					app.providers.getCurrentProvider()?.destroyAutocomplete( addressInput );
				},
				/**
				 * Destroy autocompleting for all locations in the field.
				 *
				 * @param {jQuery} $fieldOptions Field options.
				 */
				destroyAllForField( $fieldOptions ) {
					const $preview = app.preview.getById( $fieldOptions.data( 'field-id' ) );

					if ( $preview.hasClass( app.classes.fieldMapPreview ) ) {
						app.preview.destroy( $preview );
					}

					const $list = $fieldOptions.find( app.selectors.locationsFields );

					app.fieldOptions.locations.destroyAllInChoiceList( $list );
				},
				/**
				 * Destroy autocompleting for all locations in the choices list.
				 *
				 * @param {jQuery} $choicesList Choices list.
				 */
				destroyAllInChoiceList( $choicesList ) {
					$choicesList.find( app.selectors.locationRows ).each( function() {
						app.fieldOptions.locations.destroy( $( this ) );
					} );
				},
				/**
				 * Restore the address inputs after the Google Places API rejected the key.
				 *
				 * The autocomplete widget disables the input it is bound to and replaces its placeholder
				 * with an error message, so the initial state has to be put back manually.
				 *
				 * @since 2.15.0
				 */
				onApiError() {
					const provider = app.providers.getCurrentProvider();

					if ( provider !== app.providers.GooglePlaces ) {
						return;
					}

					document.querySelectorAll( app.selectors.locationAddressField ).forEach( ( input ) => {
						provider.destroyAutocomplete( input );

						input.placeholder = input.dataset.placeholder || input.placeholder;
						input.disabled = false;
						input.removeAttribute( 'style' );
					} );
				},
				/**
				 * Address autocomplete field.
				 *
				 * @since 2.13.0
				 */
				autocomplete: {
					/**
					 * Clear coordinates based on Address field input.
					 *
					 * @since 2.13.0
					 */
					clearCoordinates() {
						app.fieldOptions.locations.autocomplete.updateCoordinates( $( this ), '', '' );
					},
					/**
					 * Remove validation error from Address field input.
					 *
					 * @since 2.13.0
					 */
					resetAddressValidation() {
						$( this ).removeClass( app.classes.error );
					},
					/**
					 * Validate Address field input inform customer you have to select a field below.
					 *
					 * @since 2.13.0
					 */
					validateAddress() {
						const $input = $( this );

						if ( ! $input.val() ) {
							$input.removeClass( app.classes.error );

							return;
						}

						const $row = $input.closest( 'li' ),
							$latitude = $( app.selectors.locationLatitudeField, $row ),
							$longitude = $( app.selectors.locationLongitudeField, $row );

						$input.toggleClass( app.classes.error, ! $latitude.val() || ! $longitude.val() );
					},
					/**
					 * Set coordinates for Address field.
					 *
					 * @since 2.13.0
					 *
					 * @param {jQuery}        $addressField Address field element.
					 * @param {string|number} latitude      Latitude value.
					 * @param {string|number} longitude     Longitude value.
					 */
					updateCoordinates( $addressField, latitude, longitude ) {
						const $row = $addressField.closest( 'li' ),
							$latitude = $( app.selectors.locationLatitudeField, $row ),
							$longitude = $( app.selectors.locationLongitudeField, $row ),
							previousLatitude = $latitude.val(),
							previousLongitude = $longitude.val();

						latitude = latitude ? latitude.toFixed( 6 ) : '';
						longitude = longitude ? longitude.toFixed( 6 ) : '';

						if ( previousLatitude?.length ) {
							$latitude.data( 'previous', previousLatitude );
						}

						if ( previousLongitude?.length ) {
							$longitude.data( 'previous', previousLongitude );
						}

						WPFormsBuilder.UndoRedo?.preventRecord( true );

						$latitude.val( latitude ).trigger( 'input' );
						$longitude.val( longitude ).trigger( 'input' );

						if ( latitude !== previousLatitude ) {
							$addressField.trigger( 'focusout' );
						}

						WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );

						if ( ! latitude?.length || ! longitude?.length ) {
							return;
						}

						WPFormsBuilder.triggerBuilderEvent( 'wpformsMapFieldUpdateCoordinates', [
							$addressField,
							// New coordinates.
							{
								latitude,
								longitude,
							},
							// Old coordinates.
							{
								latitude: $latitude.data( 'previous' ),
								longitude: $longitude.data( 'previous' ),
							},
						] );
					},
				},
				/**
				 * Show/hide fields depends on the selected marker type.
				 *
				 * @since 2.13.0
				 */
				changeMarkerType() {
					const $this = $( this ),
						type = $this.val(),
						$row = $this.closest( 'li' );

					$row
						.removeClass( function( index, className ) {
							return ( className.match( /\bwpforms-geolocation-map-field-location-\S+/g ) || [] ).join( ' ' );
						} )
						.addClass( `wpforms-geolocation-map-field-location-${ type }` );
				},
				/**
				 * Destroy JS fields instances before cloning a location field.
				 *
				 * @since 2.13.0
				 *
				 * @param {Event}  e       Event object.
				 * @param {number} fieldId Field ID.
				 * @param {jQuery} $row    Cloned row element.
				 */
				beforeCloning( e, fieldId, $row ) {
					const $list = $row.closest( app.selectors.locationsFields );

					if ( ! $list.length ) {
						return;
					}

					app.fieldOptions.locations.destroy( $row );
				},
				/**
				 * Initialize JS fields instances after cloning a location field.
				 *
				 * @since 2.13.0
				 *
				 * @param {Event}  e       Event object.
				 * @param {number} fieldId Field ID.
				 * @param {jQuery} $list   Choices list element.
				 */
				afterChoiceAdded( e, fieldId, $list ) {
					if ( ! $list.closest( app.selectors.locationsFields ).length ) {
						return;
					}

					// Identifies the single newly-added row; data-next-id is already incremented before the event fires.
					const addedChoiceId = $list.attr( 'data-next-id' ) - 1,
						$newRow = $( `> li[data-key="${ addedChoiceId }"]`, $list );

					$( app.selectors.locationMarkerTypeField, $newRow ).each( function() {
						const $select = $( this );

						if ( ! $select.val() ) {
							$select.val( 'icon' ).trigger( 'change' );
						}
					} );

					$( app.selectors.locationSizeField, $newRow ).each( function() {
						const $select = $( this );

						if ( ! $select.val() ) {
							$select.val( 'small' );
						}
					} );

					$newRow
						.find( app.selectors.locationAddressField )
						.attr( 'id', `fields-${ fieldId }-choices-${ addedChoiceId }-address` );

					app.fieldOptions.locations.init();
					WPFormsBuilder.loadColorPickers( $newRow );

					WPFormsBuilder.UndoRedo?.preventRecord( true );
					// Fire on all rows to flush any pending geocoding, not just the new row.
					$( app.selectors.locationAddressField, $list ).trigger( 'focusout' );
					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );

					app.fieldOptions.locations.setDefaultIcon( $newRow );
				},
				/**
				 * Update marker image preview based on an image size option.
				 *
				 * @since 2.13.0
				 */
				updateMarkerSize() {
					const $field = $( this ),
						val = $field.val(),
						$row = $field.closest( 'li' );

					$row
						.removeClass( function( index, className ) {
							return ( className.match( /\bwpforms-geolocation-map-field-location-size-\S+/g ) || [] ).join( ' ' );
						} )
						.addClass( `wpforms-geolocation-map-field-location-size-${ val }` );
				},
				/**
				 * Set the default icon for a location row.
				 *
				 * @since 2.13.0
				 *
				 * @param {jQuery} $rows Location rows.
				 */
				setDefaultIcon( $rows ) {
					const icon = 'star',
						iconStyle = 'solid',
						$iconSource = $( app.selectors.locationIconField, $rows ),
						$iconSourceStyle = $( app.selectors.locationIconStyleField, $rows ),
						$iconPreview = $( app.selectors.locationIconPreview, $rows ),
						$iconLabel = $( app.selectors.locationIconLabel, $rows );

					WPFormsBuilder.UndoRedo?.preventRecord( true );

					$iconSourceStyle.val( iconStyle ).trigger( 'input' );
					$iconSource.val( icon ).trigger( 'input' );
					$iconPreview.removeClass().addClass( 'ic-fa-preview ic-fa-solid ic-fa-star' );
					$iconLabel.text( icon );

					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
				},
			},
		},
		/**
		 * Field preview related logic.
		 *
		 * @since 2.13.0
		 */
		preview: {
			/**
			 * Get field preview by field ID.
			 *
			 * @since 2.13.0
			 *
			 * @param {number} fieldId Field ID.
			 *
			 * @return {jQuery} Field preview element.
			 */
			getById( fieldId ) {
				return $( `#wpforms-field-${ fieldId }` );
			},
			/**
			 * Initialize preview.
			 *
			 * @since 2.13.0
			 */
			init() {
				$( app.selectors.mapFieldPreview ).each( function() {
					app.preview.initMap( $( this ) );
				} );
			},
			/**
			 * Destroy preview instances.
			 *
			 * @since 2.13.0
			 *
			 * @param {jQuery} $fieldPreview Field preview element.
			 */
			destroy( $fieldPreview ) {
				const mapElement = $( app.selectors.mapFieldPreviewMapWrapper, $fieldPreview ).get( 0 );

				app.providers.getCurrentProvider()?.destroyMap( mapElement );
			},

			/**
			 * Initialize Map Field preview, loading a map with markers.
			 *
			 * @since 2.13.0
			 *
			 * @param {jQuery} $fieldPreview Field preview element.
			 */
			initMap( $fieldPreview ) {
				if ( $( app.selectors.mapFieldPreviewMap, $fieldPreview ).length ) {
					return;
				}

				const map = $( app.selectors.mapFieldPreviewMapWrapper, $fieldPreview ).get( 0 ),
					fieldId = $fieldPreview.data( 'field-id' ),
					$fieldOptions = app.fieldOptions.getById( fieldId );

				const settings = {
					/* eslint-disable camelcase */
					zoom_level: parseInt( $( app.selectors.zoomLevelFieldOption, $fieldOptions ).val(), 10 ),
					hide_full_screen: $( '.wpforms-field-option-row-hide_full_screen input', $fieldOptions ).is( ':checked' ),
					hide_map_type: $( '.wpforms-field-option-row-hide_map_type input', $fieldOptions ).is( ':checked' ),
					hide_street_view: $( '.wpforms-field-option-row-hide_street_view input', $fieldOptions ).is( ':checked' ),
					hide_camera_control: $( '.wpforms-field-option-row-hide_camera_control input', $fieldOptions ).is( ':checked' ),
					hide_zoom: $( '.wpforms-field-option-row-hide_zoom input', $fieldOptions ).is( ':checked' ),
					/* eslint-enable camelcase */
				};

				app.preview.renderPreviewMap( map, settings, () => app.preview.initMapMarkers( $fieldPreview ) );
			},
			/**
			 * Render a builder preview map with the shared preview settings.
			 *
			 * @since 2.14.0
			 *
			 * @param {HTMLElement} map             Map element.
			 * @param {Object}      settings        Caller-specific map settings.
			 * @param {Function}    appendMarkersFn Callback that appends markers once the map is rendered.
			 */
			renderPreviewMap( map, settings, appendMarkersFn ) {
				WPFormsGeolocationMapField.renderMap( map, {
					/* eslint-disable camelcase */
					map_id: map.getAttribute( 'id' ),
					center: { lat: 40.7831, lng: -73.9712 },
					hide_location_info: true,
					disable_dragging: true,
					disable_mouse_zooming: true,
					...settings,
					/* eslint-enable camelcase */
				} ).then( appendMarkersFn ).catch( () => {} );
			},
			/**
			 * Initialize the Map field preview inside the AI Form Generator panel.
			 *
			 * The generator preview has no field options panel, so the map and
			 * marker settings come from the AI response field data passed with
			 * the event instead of the options DOM.
			 *
			 * @since 2.14.0
			 *
			 * @param {Event}  event         Event object.
			 * @param {Object} fieldSettings AI response field settings.
			 */
			initAIGeneratorPreview( event, fieldSettings ) {
				if ( fieldSettings?.type !== 'map' ) {
					return;
				}

				const map = $( app.selectors.mapFieldPreviewMapWrapper, event.target ).get( 0 );

				if ( ! map ) {
					return;
				}

				const settings = {
					/* eslint-disable camelcase */
					zoom_level: parseInt( fieldSettings.zoom_level, 10 ) || 15,
					hide_full_screen: app.preview.isHiddenControl( fieldSettings.hide_full_screen ),
					hide_map_type: app.preview.isHiddenControl( fieldSettings.hide_map_type ),
					hide_street_view: app.preview.isHiddenControl( fieldSettings.hide_street_view ),
					hide_camera_control: app.preview.isHiddenControl( fieldSettings.hide_camera_control ),
					hide_zoom: app.preview.isHiddenControl( fieldSettings.hide_zoom ),
					/* eslint-enable camelcase */
				};

				app.preview.renderPreviewMap( map, settings, () => app.preview.appendAIGeneratorMarkers( map, fieldSettings ) );
			},
			/**
			 * Whether an AI response control flag hides a map control.
			 *
			 * Values may come as booleans or '1'/'0' strings; a missing value keeps the control hidden.
			 *
			 * @since 2.14.0
			 *
			 * @param {boolean|string|number|undefined} value AI response control flag.
			 *
			 * @return {boolean} True when the control must be hidden.
			 */
			isHiddenControl( value ) {
				return ! [ false, 0, '0' ].includes( value );
			},
			/**
			 * Destroy map instances inside the AI Form Generator preview before it is cleared.
			 *
			 * @since 2.14.0
			 *
			 * @param {Event} event Event object.
			 */
			destroyAIGeneratorPreview( event ) {
				$( app.selectors.mapFieldPreviewMapWrapper, event.target ).get().forEach(
					( mapElement ) => app.providers.getCurrentProvider()?.destroyMap( mapElement )
				);
			},
			/**
			 * Append AI response location markers to the generator preview map.
			 *
			 * @since 2.14.0
			 *
			 * @param {HTMLElement} map           Map element.
			 * @param {Object}      fieldSettings AI response field settings.
			 */
			appendAIGeneratorMarkers( map, fieldSettings ) {
				const settingsList = Object.values( fieldSettings.choices || {} )
					.filter( ( choice ) => {
						if ( Number.isNaN( parseFloat( choice.latitude ) ) || Number.isNaN( parseFloat( choice.longitude ) ) ) {
							return false;
						}

						// Mirror the PHP marker validation: an image marker without an image is invisible.
						return choice.marker_type !== 'image' || Boolean( choice.image );
					} )
					.map( ( choice ) => app.preview.getAIGeneratorMarkerSettings( choice ) );

				app.preview.appendMarkers( map, settingsList );
			},
			/**
			 * Build marker settings from an AI response location choice.
			 *
			 * Defaults mirror the location row defaults in the Map field PHP class.
			 *
			 * @since 2.14.0
			 *
			 * @param {Object} choice AI response location choice.
			 *
			 * @return {Object} Marker settings.
			 */
			getAIGeneratorMarkerSettings( choice ) {
				const iconStyle = choice.icon_style || wpforms_builder.icon_choices.default_icon_style || 'regular',
					icon = choice.icon || wpforms_builder.icon_choices.default_icon || 'face-smile',
					// Non-hex colors crash the contrast-color util, so accept 6/8-digit hex only.
					isValidColor = /^#(?:[0-9a-f]{6}|[0-9a-f]{8})$/i.test( String( choice.icon_color ) );

				return {
					name: choice.name || '',
					description: choice.description || '',
					icon: `ic-fa-preview ic-fa-${ iconStyle } ic-fa-${ icon }`,
					color: isValidColor ? choice.icon_color : '#d63638',
					markerType: choice.marker_type || 'icon',
					imgUrl: choice.image,
					size: choice.size || 'small',
					latitude: choice.latitude,
					longitude: choice.longitude,
				};
			},
			/**
			 * Initialize Map Field markers on preview.
			 *
			 * @since 2.13.0
			 *
			 * @param {jQuery} $fieldPreview Field preview element.
			 */
			initMapMarkers( $fieldPreview ) {
				const map = $( app.selectors.mapFieldPreviewMapWrapper, $fieldPreview ).get( 0 ),
					fieldId = $fieldPreview.data( 'field-id' ),
					$fieldOptions = app.fieldOptions.getById( fieldId ),
					$locations = $( app.selectors.locationsFields, $fieldOptions ),
					settingsList = $( app.selectors.locationRows, $locations ).map( function() {
						return app.preview.getRowMarkerSettings( $( this ) );
					} ).get();

				app.preview.appendMarkers( map, settingsList );
			},
			/**
			 * Append markers to a map and fit its bounds.
			 *
			 * @since 2.14.0
			 *
			 * @param {HTMLElement} map          Map element.
			 * @param {Array}       settingsList Marker settings objects, each optionally with a `key` property.
			 */
			appendMarkers( map, settingsList ) {
				settingsList.forEach( ( settings ) => app.preview.appendSingleMarker( map, settings ) );

				WPFormsGeolocationMapField.fitBounds( map, wpforms_geolocation_map_field.settings.bounds );
			},
			/**
			 * Append a single marker to a map, stamping the location row key when provided.
			 *
			 * @since 2.14.0
			 *
			 * @param {HTMLElement} map      Map element.
			 * @param {Object}      settings Marker settings, optionally with a `key` property.
			 */
			appendSingleMarker( map, settings ) {
				const { key, ...markerSettings } = settings;

				WPFormsGeolocationMapField.appendMarker( map, markerSettings );

				if ( key === undefined ) {
					return;
				}

				const markers = map.querySelectorAll( app.selectors.mapFieldPreviewMarker ),
					addedMarker = markers[ markers.length - 1 ];

				if ( addedMarker ) {
					addedMarker.setAttribute( 'data-key', key );
				}
			},
			/**
			 * Build marker settings from a location options row.
			 *
			 * @since 2.14.0
			 *
			 * @param {jQuery} $row Location row element.
			 *
			 * @return {Object} Marker settings with the row key.
			 */
			getRowMarkerSettings( $row ) {
				return {
					key: $row.data( 'key' ),
					name: $( app.selectors.locationNameField, $row ).val(),
					description: $( app.selectors.locationDescriptionField, $row ).val(),
					icon: $( app.selectors.locationIconPreview, $row ).attr( 'class' ),
					color: $( app.selectors.colorPicker, $row ).val(),
					markerType: $( app.selectors.locationMarkerTypeField, $row ).val(),
					imgUrl: $( app.selectors.locationImageUrlField, $row ).val(),
					size: $( app.selectors.locationSizeField, $row ).val(),
					latitude: $( app.selectors.locationLatitudeField, $row ).val(),
					longitude: $( app.selectors.locationLongitudeField, $row ).val(),
				};
			},
			/**
			 * Append a marker to the map for a location field preview.
			 *
			 * @since 2.13.0
			 *
			 * @param {jQuery} $row          Location row element.
			 * @param {jQuery} $fieldPreview Field preview element.
			 */
			appendMarker( $row, $fieldPreview ) {
				const map = $( app.selectors.mapFieldPreviewMapWrapper, $fieldPreview ).get( 0 );

				app.preview.appendSingleMarker( map, app.preview.getRowMarkerSettings( $row ) );
			},
			/**
			 * Update map settings based on field options.
			 *
			 * @since 2.13.0
			 */
			updateMapSettings() {
				const $option = $( this ),
					optionName = $option.data( 'map-control' ),
					isZoomLevel = $option.is( 'select' ),
					value = isZoomLevel
						? parseInt( $option.val(), 10 )
						: ! $option.is( ':checked' ),
					fieldId = $option.closest( app.selectors.fieldOptions ).data( 'field-id' ),
					$preview = app.preview.getById( fieldId ),
					mapElement = $( app.selectors.mapFieldPreviewMapWrapper, $preview ).get( 0 );

				app.providers.getCurrentProvider()?.updateMapSettings( mapElement, optionName, value );

				WPFormsGeolocationMapField.fitBounds( mapElement, wpforms_geolocation_map_field.settings.bounds );
			},
			/**
			 * Update the marker name based on the location name.
			 *
			 * @since 2.13.0
			 */
			updateMarkerName() {
				const $field = $( this ),
					val = $field.val(),
					$marker = app.preview.getMarkerByLocationField( $field ),
					$markerName = $( app.selectors.mapFieldPreviewMarkerName, $marker );

				$markerName.text( val );
			},
			/**
			 * Update the marker description based on the location name.
			 *
			 * @since 2.13.0
			 */
			updateMarkerDescription() {
				const $field = $( this ),
					val = $field.val(),
					$marker = app.preview.getMarkerByLocationField( $field ),
					$markerDescription = $( app.selectors.mapFieldPreviewMarkerDescription, $marker );

				$markerDescription.text( val );
			},
			/**
			 * Update the marker icon based on the location icon.
			 *
			 * @since 2.13.0
			 */
			updateMakerIcon() {
				const $field = $( this ),
					$row = $field.closest( 'li' ),
					$iconPreview = $( app.selectors.locationIconPreview, $row ),
					$marker = app.preview.getMarkerByLocationField( $field ),
					$markerIcon = $( app.selectors.mapFieldPreviewMarkerIconPin, $marker ),
					$markerIconColor = $( app.selectors.locationIconColorField, $row );

				WPFormsBuilder.UndoRedo?.preventRecord( true );

				$markerIcon.removeClass();

				$markerIcon.addClass( 'wpforms-map-field-marker-pin-icon' );
				$markerIcon.addClass( $iconPreview.attr( 'class' ) );
				$markerIconColor.trigger( 'input' );

				WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
			},
			/**
			 * Update the marker icon color based on the location marker icon color.
			 *
			 * @since 2.13.0
			 */
			updateMarkerIconColor() {
				const $field = $( this ),
					val = $field.val(),
					$marker = app.preview.getMarkerByLocationField( $field ),
					$markerIcon = $( app.selectors.mapFieldPreviewMarkerIconPin, $marker ),
					$markerIconBackground = $( app.selectors.mapFieldPreviewMarkerIconBackgroundPin, $marker ),
					iconColor = WPFormsUtils.cssColorsUtils.getContrastColor( val ) === '#ffffff' ? 'light' : 'dark';

				$markerIconBackground.attr( 'fill', val );
				$markerIcon.removeClass( 'wpforms-map-field-marker-pin-icon-light wpforms-map-field-marker-pin-icon-dark' );
				$markerIcon.addClass( `wpforms-map-field-marker-pin-icon-${ iconColor }` );
			},
			/**
			 * Switch the marker type based on the location marker type e.g., image or icon.
			 *
			 * @since 2.13.0
			 */
			updateMarkerType() {
				const $field = $( this ),
					val = $field.val(),
					$marker = app.preview.getMarkerByLocationField( $field ),
					$pin = $( app.selectors.mapFieldPreviewMarkerPin, $marker );

				$pin
					.removeClass( function( index, className ) {
						return ( className.match( /\bwpforms-map-field-marker-pin-type-\S+/g ) || [] ).join( ' ' );
					} )
					.addClass( `wpforms-map-field-marker-pin-type-${ val }` );
			},
			/**
			 * Update the marker image based on the location image URL.
			 *
			 * @since 2.13.0
			 */
			updateMarkerImage() {
				const $field = $( this ),
					val = $field.val().trim(),
					$marker = app.preview.getMarkerByLocationField( $field );

				if ( val === '' ) {
					$marker.hide();

					return;
				}

				const $pinImage = $( app.selectors.mapFieldPreviewMarkerImagePin, $marker );

				$pinImage.attr( 'src', val );
				$marker.show();
			},
			/**
			 * Update the marker image size based on the location image size.
			 *
			 * @since 2.13.0
			 */
			updateMarkerSize() {
				const $field = $( this ),
					val = $field.val(),
					$marker = app.preview.getMarkerByLocationField( $field ),
					$pin = $( app.selectors.mapFieldPreviewMarkerPin, $marker ),
					map = $marker.closest( app.selectors.mapFieldPreviewMapWrapper ).get( 0 );

				$pin
					.removeClass( function( index, className ) {
						return ( className.match( /\bwpforms-map-field-marker-pin-size-\S+/g ) || [] ).join( ' ' );
					} )
					.addClass( `wpforms-map-field-marker-pin-size-${ val }` );

				WPFormsGeolocationMapField.fitBounds( map, wpforms_geolocation_map_field.settings.bounds );
			},
			/**
			 * Update marker coordinates on a map after the longitude or latitude location field were updated.
			 *
			 * @since 2.13.0
			 */
			updateMarkerCoordinates() {
				const $row = $( this ).closest( 'li' ),
					latitude = $( app.selectors.locationLatitudeField, $row ).val(),
					longitude = $( app.selectors.locationLongitudeField, $row ).val(),
					$marker = app.preview.getMarkerByLocationField( $row );

				if ( ! $marker.length && ! latitude && ! longitude ) {
					return;
				}

				const fieldId = $row.closest( app.selectors.locationsFields ).data( 'field-id' ),
					$fieldPreview = app.preview.getById( fieldId ),
					map = $( app.selectors.mapFieldPreviewMapWrapper, $fieldPreview ).get( 0 );

				if ( ! latitude || ! longitude ) {
					$marker?.remove();
					WPFormsGeolocationMapField.fitBounds( map, wpforms_geolocation_map_field.settings.bounds );

					return;
				}

				if ( ! $marker.length ) {
					app.preview.appendMarker( $row, $fieldPreview );
					WPFormsGeolocationMapField.fitBounds( map, wpforms_geolocation_map_field.settings.bounds );

					return;
				}

				$marker.position = { lat: latitude, lng: longitude };
				WPFormsGeolocationMapField.fitBounds( map, wpforms_geolocation_map_field.settings.bounds );
			},
			/**
			 * Delete a marker preview after the location row was deleted.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e       Event.
			 * @param {number} fieldId Field ID.
			 */
			deleteMarker( e, fieldId ) {
				const $fieldOptions = app.fieldOptions.getById( fieldId ),
					$locations = $( app.selectors.locationsFields, $fieldOptions );

				if ( ! $locations.length ) {
					return;
				}

				const markerIds = [];

				$( app.selectors.locationRows, $locations ).each( function() {
					markerIds.push( $( this ).data( 'key' ) );
				} );

				const $preview = app.preview.getById( fieldId ),
					$markers = $preview.find( app.selectors.mapFieldPreviewMarker );

				$markers.each( function() {
					if ( ! markerIds.includes( $( this ).data( 'key' ) ) ) {
						$( this ).remove();
					}
				} );
			},
			/**
			 * Destroy a field preview before the field was moved.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e       Event.
			 * @param {number} fieldId Field ID.
			 */
			handleDragStart( e, fieldId ) {
				const $preview = app.preview.getById( fieldId );

				if ( ! $preview.hasClass( app.classes.fieldMapPreview ) ) {
					return;
				}

				app.preview.destroy( $preview );
			},
			/**
			 * Reinitialize a field preview after the field was moved.
			 *
			 * @since 2.13.0
			 *
			 * @param {Event}  e  Event.
			 * @param {Object} ui UI-sortable object.
			 */
			handleDragMoved( e, ui ) {
				const fieldId = ui.item.data( 'field-id' ),
					$preview = app.preview.getById( fieldId );

				if ( ! $preview.hasClass( app.classes.fieldMapPreview ) ) {
					return;
				}

				app.preview.init();
			},
			/**
			 * Get marker by any location field.
			 *
			 * @since 2.13.0
			 *
			 * @param {jQuery} $field Field element.
			 *
			 * @return {jQuery} Marker element.
			 */
			getMarkerByLocationField( $field ) {
				const $row = $field.closest( 'li' ),
					key = $row.data( 'key' ),
					fieldId = $row.closest( app.selectors.locationsFields ).data( 'field-id' ),
					$preview = app.preview.getById( fieldId ),
					$markers = $( app.selectors.mapFieldPreviewMarker, $preview );

				return $markers.filter( `[data-key="${ key }"]` );
			},
			/**
			 * List Location preview related logic.
			 *
			 * @since 2.13.0
			 */
			listLocation: {
				/**
				 * Change field type from hidden to radio depending on the Allow Location Selection option.
				 *
				 * @since 2.13.0
				 */
				updateFieldType() {
					const $field = $( this ),
						isAllowed = $field.is( ':checked' ),
						fieldType = isAllowed ? 'radio' : 'hidden',
						$fieldOptions = $field.closest( app.selectors.fieldOptions ),
						fieldId = $fieldOptions.data( 'field-id' ),
						$preview = app.preview.getById( fieldId ),
						$list = $( app.selectors.mapFieldPreviewList, $preview ),
						$inputs = $( 'input', $list );

					$inputs.attr( 'type', fieldType );
				},
				/**
				 * Update location name.
				 *
				 * @since 2.13.0
				 */
				updateName() {
					const $field = $( this ),
						val = $field.val(),
						$row = $field.closest( 'li' ),
						index = $row.index(),
						fieldId = $row.closest( app.selectors.locationsFields ).data( 'field-id' ),
						$preview = app.preview.getById( fieldId ),
						$list = $( app.selectors.mapFieldPreviewList, $preview ),
						$name = $( '.wpforms-field-map-location-name:eq(' + index + ')', $list );

					$name.text( val );
				},
				/**
				 * Update location address.
				 *
				 * @since 2.13.0
				 */
				updateAddress() {
					const $field = $( this ),
						$row = $field.closest( 'li' ),
						val = $( '.wpforms-geolocation-map-field-location-address', $row ).val(),
						index = $row.index(),
						fieldId = $row.closest( app.selectors.locationsFields ).data( 'field-id' ),
						$preview = app.preview.getById( fieldId ),
						$list = $( app.selectors.mapFieldPreviewList, $preview ),
						$address = $( '.wpforms-field-map-location-address:eq(' + index + ')', $list );

					$address.text( val );
				},
				/**
				 * Refresh preview when field changes occur.
				 *
				 * @since 2.13.0
				 *
				 * @param {Event}  e       Event.
				 * @param {number} fieldId Field ID.
				 */
				fieldAdd( e, fieldId ) {
					app.preview.listLocation.refreshPreviewIfMapField( fieldId );
				},
				/**
				 * Add a new item to the preview when a new location is added.
				 *
				 * @since 2.13.0
				 *
				 * @param {Event}  e       Event.
				 * @param {number} fieldId Field ID.
				 */
				addItem( e, fieldId ) {
					app.preview.listLocation.refreshPreviewIfMapField( fieldId );
				},
				/**
				 * Remove a new item to preview when a location is removed.
				 *
				 * @since 2.13.0
				 *
				 * @param {Event}  e       Event.
				 * @param {number} fieldId Field ID.
				 */
				removeItem( e, fieldId ) {
					app.preview.listLocation.refreshPreviewIfMapField( fieldId );
				},
				/**
				 * Refresh preview if the field is a map field.
				 *
				 * @since 2.13.0
				 *
				 * @param {number} fieldId Field ID.
				 */
				refreshPreviewIfMapField( fieldId ) {
					const $preview = app.preview.getById( fieldId );

					if ( $preview.data( 'field-type' ) !== 'map' ) {
						return;
					}

					app.preview.listLocation.refreshPreview( fieldId );
				},
				/**
				 * Change items order when the location order is changed.
				 *
				 * @since 2.13.0
				 *
				 * @param {Event}  e  Event.
				 * @param {Object} ui jQuery UI object.
				 */
				changeOrder( e, ui ) {
					const $fieldOptions = $( ui.item ).closest( app.selectors.fieldOptions );

					if ( ! $fieldOptions.length ) {
						return;
					}

					app.preview.listLocation.refreshPreview( $fieldOptions.data( 'field-id' ) );
				},
				/**
				 * Refresh preview for a specific field.
				 *
				 * @since 2.13.0
				 *
				 * @param {number} fieldId Field ID.
				 */
				refreshPreview( fieldId ) {
					const $fieldOptions = app.fieldOptions.getById( fieldId ),
						$preview = app.preview.getById( fieldId ),
						$list = $( app.selectors.mapFieldPreviewList, $preview ),
						$names = $( app.selectors.locationNameField, $fieldOptions ),
						itemHTML = '<li><input type="hidden"><label><span class="wpforms-field-map-location-name"></span><span class="wpforms-field-map-location-address"></span></label></li>';

					$list.empty().append( itemHTML.repeat( $names.length ) );

					WPFormsBuilder.UndoRedo?.preventRecord( true );

					$( app.selectors.locationNameField, $fieldOptions ).trigger( 'input' );
					$( app.selectors.locationLatitudeField, $fieldOptions ).trigger( 'input' );
					$( app.selectors.allowLocationSelectionFieldOption, $fieldOptions ).trigger( 'change' );

					WPFormsBuilder.UndoRedo?.preventRecord( 'continue' );
				},
			},
			/**
			 * Resize initialized map.
			 *
			 * @since 2.13.0
			 */
			resize() {
				window.dispatchEvent( new Event( 'resize' ) );
			},
		},
		/**
		 * Providers related logic.
		 *
		 * @since 2.13.0
		 */
		providers: {
			/**
			 * Customer location.
			 *
			 * @since 2.13.0
			 *
			 * @return {object|null} Customer data required for limiting an autocomplete result.
			 */
			customerLocation: null,
			/**
			 * Get search radius value and unit from field options.
			 *
			 * @since 2.13.0
			 *
			 * @return {Object} Object with value and unit properties.
			 */
			getSearchRadius() {
				const $radiusField = $( app.selectors.searchRadius );
				let value = $radiusField.length ? parseInt( $radiusField.val(), 10 ) : +wpforms_geolocation_map_field.default_search_radius;

				// Validate parsed value and set to default if invalid.
				if ( isNaN( value ) || value <= 0 ) {
					value = +wpforms_geolocation_map_field.default_search_radius;
				}

				const isMilesCountry = app.providers.isMileCountry();

				return {
					value,
					unit: isMilesCountry ? 'mi' : 'km',
				};
			},
			/**
			 * Determines whether the current customer's country uses the mile system for distances.
			 *
			 * @since 2.13.0
			 *
			 * @return {boolean} True if the customer's country uses the mile system, false otherwise.
			 */
			isMileCountry() {
				return app.providers.customerLocation && app.milesCountries.includes( app.providers.customerLocation.countryCode.toLowerCase() );
			},
			/**
			 * Convert distance to latitude degrees offset.
			 *
			 * @since 2.13.0
			 *
			 * @param {number} distance Distance value.
			 * @param {string} unit     Unit of measurement (`km` or `mi`).
			 *
			 * @return {number} Latitude offset in degrees.
			 */
			distanceToLatitudeDegrees( distance, unit = 'km' ) {
				const km = unit === 'mi' ? distance * 1.60934 : distance;

				return km / 111.32;
			},
			/**
			 * Convert distance to longitude degrees offset at a given latitude.
			 *
			 * @since 2.13.0
			 *
			 * @param {number} distance Distance value.
			 * @param {number} lat      Latitude where the conversion is calculated.
			 * @param {string} unit     Unit of measurement (`km` or `mi`).
			 *
			 * @return {number} Longitude offset in degrees.
			 */
			distanceToLongitudeDegrees( distance, lat, unit = 'km' ) {
				// 1 degree of longitude = cos(latitude) * 111.32 km.
				// At equator (lat=0): cos(0) = 1, so 1° = 111.32 km.
				// At poles (lat=90): cos(90) = 0, so 1° = 0 km.
				const km = unit === 'mi' ? distance * 1.60934 : distance;
				const latRad = lat * Math.PI / 180;
				const cosLat = Math.cos( latRad );

				// Prevent division by zero at poles.
				if ( Math.abs( cosLat ) < 0.0001 ) {
					return 0;
				}

				return km / ( cosLat * 111.32 );
			},
			/**
			 * Get current provider.
			 *
			 * @since 2.13.0
			 *
			 * @return {object|undefined} Provider object or undefined if provider is not loaded.
			 */
			getCurrentProvider() {
				if ( app.providers.GooglePlaces.isLoaded() ) {
					return app.providers.GooglePlaces;
				}

				if ( app.providers.MapboxPlaces.isLoaded() ) {
					return app.providers.MapboxPlaces;
				}
			},
			/**
			 * Google Places related logic.
			 *
			 * @since 2.13.0
			 */
			GooglePlaces: {
				/**
				 * Determine if the provider is loaded.
				 *
				 * @since 2.13.0
				 *
				 * @return {boolean} True if provider is loaded, false otherwise.
				 */
				isLoaded() {
					return Boolean( typeof google !== 'undefined' && google.maps && google.maps.importLibrary );
				},
				/**
				 * Loads necessary Google Maps libraries concurrently.
				 *
				 * @since 2.13.0
				 *
				 * @return {Promise} A promise that resolves when all libraries are successfully loaded.
				 */
				loadLibraries() {
					return Promise.all( [
						google.maps.importLibrary( 'places' ),
						google.maps.importLibrary( 'geocoding' ),
					] );
				},
				/**
				 * Initialize autocomplete fields.
				 *
				 * @since 2.13.0
				 */
				async initAutocomplete() {
					if ( WPFormsGeolocationMapField.apiFailed ) {
						return;
					}

					if ( ! google.maps.places || ! google.maps.places.Autocomplete ) {
						await app.providers.GooglePlaces.loadLibraries();
					}

					document.querySelectorAll( `${ app.selectors.locationAddressField }:not(.${ app.classes.googleAutocompleteInput })` ).forEach( ( input ) => {
						const autocompleteOptions = {
							types: [ 'geocode' ],
						};

						const $nearbyLocation = $( app.selectors.findNearbyLocations );

						if ( app.providers.customerLocation && $nearbyLocation.is( ':checked' ) ) {
							Object.assign( autocompleteOptions, app.providers.GooglePlaces.getAutocompleteRestrictionOptions() );
						}

						// The widget overwrites the placeholder with an error message when the API rejects the key.
						input.dataset.placeholder = input.placeholder;

						const autocomplete = new google.maps.places.Autocomplete( input, autocompleteOptions );

						input.setAttribute( 'data-1p-ignore', true );

						input.autocompleteInstance = autocomplete;

						autocomplete.addListener( 'place_changed', function() {
							const place = autocomplete.getPlace();

							if ( ! place.geometry || ! place.geometry.location ) {
								return;
							}

							const coordinates = place.geometry.location;

							if ( ! coordinates.lat || ! coordinates.lng ) {
								return;
							}

							app.fieldOptions.locations.autocomplete.updateCoordinates( $( input ), coordinates.lat(), coordinates.lng() );
						} );
					} );
				},
				/**
				 * Destroy an autocomplete instance for an input.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} input Field element.
				 */
				destroyAutocomplete( input ) {
					google.maps.event.clearInstanceListeners( input );
					input.classList.remove( app.classes.googleAutocompleteInput );
				},
				/**
				 * Update Google Maps settings based on field options.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement}    mapElement Map HTML element.
				 * @param {string}         optionName Option name, see the `data-map-control` attribute.
				 * @param {number|boolean} value      Option value.
				 */
				updateMapSettings( mapElement, optionName, value ) {
					const map = mapElement.querySelector( app.selectors.mapFieldPreviewMap )?.innerMap;

					if ( ! map ) {
						return;
					}

					map.setOptions( { [ optionName ]: value } );
				},
				/**
				 * Destroy a map instance for a map preview.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Field element.
				 */
				destroyMap( mapElement ) {
					const gmap = mapElement.querySelector( app.selectors.mapFieldPreviewMap ),
						mapInstance = gmap?.innerMap;

					if ( mapInstance ) {
						google.maps.event.clearInstanceListeners( mapInstance );
					}

					gmap?.remove();
				},
				/**
				 * Detect customer location by coordinates.
				 *
				 * @since 2.13.0
				 *
				 * @param {number} latitude  Latitude coordinate.
				 * @param {number} longitude Longitude coordinate.
				 *
				 * @return {Promise} Promise if we could detect customer location and set a country code.
				 */
				detectCustomerLocation( latitude, longitude ) {
					return new Promise( ( resolve, reject ) => {
						const geocoder = new google.maps.Geocoder,
							coordinates = { lat: latitude, lng: longitude };

						geocoder.geocode( { location: coordinates }, ( results, status ) => {
							if ( status !== 'OK' || ! results || ! results.length ) {
								reject();
								return;
							}

							const countryComponent = results[ 0 ]?.address_components.find(
								( component ) => component.types.includes( 'country' )
							);

							if ( ! countryComponent ) {
								reject();

								return;
							}

							app.providers.customerLocation = {
								countryCode: countryComponent.short_name.toString().toLocaleLowerCase(),
								coordinates,
							};

							resolve();
						} );
					} );
				},
				/**
				 * Apply autocomplete restriction by country to the address input fields.
				 *
				 * @since 2.13.0
				 */
				applyAutocompleteRestriction() {
					if ( ! app.providers.customerLocation ) {
						return;
					}

					$( `.${ app.classes.googleAutocompleteInput }` ).each( function() {
						const input = $( this ).get( 0 );

						if ( ! input.autocompleteInstance ) {
							return;
						}

						input.autocompleteInstance.setOptions( app.providers.GooglePlaces.getAutocompleteRestrictionOptions() );
					} );
				},
				/**
				 * Prepare a list of restriction options for the autocomplete field.
				 * Uses Legacy API: bounds (with precise lat/lng calculations), strictBounds, and country restriction.
				 *
				 * @since 2.13.0
				 *
				 * @return {Object} List of options.
				 */
				getAutocompleteRestrictionOptions() {
					const radius = app.providers.getSearchRadius(),
						lat = app.providers.customerLocation.coordinates.lat,
						latOffset = app.providers.distanceToLatitudeDegrees( radius.value, radius.unit ),
						lngOffset = app.providers.distanceToLongitudeDegrees( radius.value, lat, radius.unit );

					return {
						componentRestrictions: {
							country: [ app.providers.customerLocation.countryCode ],
						},
						bounds: {
							north: app.providers.customerLocation.coordinates.lat + latOffset,
							south: app.providers.customerLocation.coordinates.lat - latOffset,
							east:  app.providers.customerLocation.coordinates.lng + lngOffset,
							west:  app.providers.customerLocation.coordinates.lng - lngOffset,
						},
						strictBounds: true,
					};
				},
				/**
				 * Remove any autocomplete restriction to the address input fields.
				 *
				 * @since 2.13.0
				 */
				removeAutocompleteRestriction() {
					$( `.${ app.classes.googleAutocompleteInput }` ).each( function() {
						const input = $( this ).get( 0 );

						if ( ! input.autocompleteInstance ) {
							return;
						}

						input.autocompleteInstance.setComponentRestrictions( {
							country: [],
						} );

						input.autocompleteInstance.setOptions( {
							bounds: null,
							strictBounds: false,
						} );
					} );
				},
			},
			/**
			 * Mapbox Places related logic.
			 *
			 * @since 2.13.0
			 */
			MapboxPlaces: {
				/**
				 * Determine if the provider is loaded.
				 *
				 * @since 2.13.0
				 *
				 * @return {boolean} True if provider is loaded, false otherwise.
				 */
				isLoaded() {
					return typeof mapboxsearch !== 'undefined';
				},
				/**
				 * Initialize autocomplete fields.
				 *
				 * @since 2.13.0
				 */
				initAutocomplete() {
					mapboxsearch.config.accessToken = wpforms_geolocation_map_field.settings.access_token;

					const addressElements = document.querySelectorAll( app.selectors.locationAddressField );

					addressElements.forEach( ( addressEl ) => {
						const autofill = document.createElement( app.selectors.mapboxElement );

						autofill.append( addressEl.cloneNode( true ) );
						addressEl.replaceWith( autofill );

						const input = autofill.querySelector( 'input' );

						input.setAttribute( 'name', input.getAttribute( 'name' ).replace( ' address-search', '' ) );

						input.setAttribute( 'data-1p-ignore', true );

						autofill.accessToken = wpforms_geolocation_map_field.settings.access_token;
						autofill.options = {
							language: 'en',
						};
						autofill.theme = {
							cssText: `
										.Results {
											z-index: 100100;
										}
									`,
						};

						const $nearbyLocation = $( app.selectors.findNearbyLocations );

						if ( app.providers.customerLocation && $nearbyLocation.is( ':checked' ) ) {
							Object.assign( autofill.options, app.providers.MapboxPlaces.getAutocompleteRestrictionOptions() );
						}

						autofill.addEventListener( 'retrieve', function( e ) {
							if ( ! e.detail || ! Object.prototype.hasOwnProperty.call( e.detail, 'features' ) || ! e.detail.features.length ) {
								return;
							}

							const feature = e.detail.features.shift();

							if ( ! Object.prototype.hasOwnProperty.call( feature, 'geometry' ) || ! Object.prototype.hasOwnProperty.call( feature.geometry, 'coordinates' ) || feature.geometry.coordinates.length !== 2 ) {
								return;
							}

							if ( ! Object.prototype.hasOwnProperty.call( feature, 'properties' ) || ! Object.prototype.hasOwnProperty.call( feature.properties, 'place_name' ) ) {
								return;
							}

							const row = autofill.closest( 'li' ),
								$input = $( app.selectors.locationAddressField, $( row ) );

							$input.val( feature.properties.place_name );

							app.fieldOptions.locations.autocomplete.updateCoordinates( $input, feature.geometry.coordinates[ 1 ], feature.geometry.coordinates[ 0 ] );
						} );

						autofill.addEventListener( 'keydown', function( e ) { // Prevent form submission by pressing Enter.
							if ( e.keyCode === 13 && e.target.ariaExpanded ) {
								e.stopPropagation();
							}
						} );
					} );
				},
				/**
				 * Destroy an autocomplete instance for an input.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} input Field element.
				 */
				destroyAutocomplete( input ) {
					const autofill = input.closest( app.selectors.mapboxElement );

					if ( ! autofill ) {
						return;
					}

					const clonedInput = input.cloneNode( true );
					autofill.after( clonedInput );
					autofill.remove();

					[
						'role',
						'aria-autocomplete',
						'aria-controls',
						'data-lpignore',
						'autocomplete',
						'aria-expanded',
						'data-mapbox-success',
					].forEach( ( attr ) => clonedInput.removeAttribute( attr ) );
				},
				/**
				 * Update Mapbox StreetMap settings based on field options.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement}    mapElement Map HTML element.
				 * @param {string}         optionName Option name, see the `data-map-control` attribute.
				 * @param {number|boolean} value      Option value.
				 */
				updateMapSettings( mapElement, optionName, value ) {
					if ( ! app.providers.MapboxPlaces.isLoaded() ) {
						return;
					}

					const map = mapElement.innerMap;

					if ( optionName === 'zoom' ) {
						map.setZoom( value );

						return;
					}

					const controlMap = {
							fullscreenControl: 'FullscreenControl',
							zoomControl: 'NavigationControl',
						},
						currentControl = controlMap[ optionName ];

					if ( value ) { // Add control.
						map.addControl( new mapboxgl[ currentControl ]() );

						return;
					}

					map._controls.forEach( ( control ) => {
						if ( control.constructor.name === currentControl ) {
							map.removeControl( control );
						}
					} );
				},
				/**
				 * Destroy a map instance for a map preview.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Field element.
				 */
				destroyMap( mapElement ) {
					mapElement.classList.remove( 'mapboxgl-map' );

					while ( mapElement.firstChild ) {
						mapElement.removeChild( mapElement.firstChild );
					}

					mapElement.innerMap?.remove();
					mapElement.innerMap = null;
				},
				/**
				 * Detect customer location by coordinates.
				 *
				 * @since 2.13.0
				 *
				 * @param {number} latitude  Latitude coordinate.
				 * @param {number} longitude Longitude coordinate.
				 *
				 * @return {Promise} Promise if we could detect customer location and set a country code.
				 */
				detectCustomerLocation( latitude, longitude ) {
					return new Promise( ( resolve, reject ) => {
						const url = new URL( `https://api.mapbox.com/geocoding/v5/mapbox.places/${ longitude },${ latitude }.json` ),
							xhr = new XMLHttpRequest;

						url.searchParams.set( 'access_token', wpforms_geolocation_map_field.settings.access_token );
						url.searchParams.set( 'limit', '1' );
						url.searchParams.set( 'type', 'address' );

						xhr.onreadystatechange = function() {
							if ( xhr.readyState === 4 && xhr.status === 200 ) {
								const data = JSON.parse( xhr.responseText );

								if ( ! data.features || ! data.features[ 0 ] || ! data.features[ 0 ].context ) {
									reject();

									return;
								}

								data.features[ 0 ].context.forEach( function( property ) {
									if ( ! property?.id.startsWith( 'country' ) || ! property.short_code ) {
										return;
									}

									app.providers.customerLocation = {
										countryCode: property.short_code.split( '-' ).pop().toString().toUpperCase(),
										coordinates: {
											lat: latitude,
											lng: longitude,
										},
									};

									resolve();
								} );

								reject();
							}
						};

						xhr.open( 'GET', url.toString() );
						xhr.send();
					} );
				},
				/**
				 * Apply autocomplete restriction by country to the address input fields.
				 *
				 * @since 2.13.0
				 */
				applyAutocompleteRestriction() {
					if ( ! app.providers.customerLocation ) {
						return;
					}

					$( `${ app.selectors.mapboxElement }` ).each( function() {
						const autofill = $( this ).get( 0 );

						Object.assign( autofill.options, app.providers.MapboxPlaces.getAutocompleteRestrictionOptions() );
					} );
				},
				/**
				 * Prepare a list of restriction options for autocomplete field.
				 *
				 * @since 2.13.0
				 *
				 * @return {Object} List of options.
				 */
				getAutocompleteRestrictionOptions() {
					const radius = app.providers.getSearchRadius(),
						lat = app.providers.customerLocation.coordinates.lat,
						latOffset = app.providers.distanceToLatitudeDegrees( radius.value, radius.unit ),
						lngOffset = app.providers.distanceToLongitudeDegrees( radius.value, lat, radius.unit );

					return {
						country: app.providers.customerLocation.countryCode,
						bbox: [
							app.providers.customerLocation.coordinates.lng - lngOffset,
							app.providers.customerLocation.coordinates.lat - latOffset,
							app.providers.customerLocation.coordinates.lng + lngOffset,
							app.providers.customerLocation.coordinates.lat + latOffset,
						].join( ',' ),
					};
				},
				/**
				 * Remove any autocomplete restriction to the address input fields.
				 *
				 * @since 2.13.0
				 */
				removeAutocompleteRestriction() {
					$( `${ app.selectors.mapboxElement }` ).each( function() {
						const autofill = $( this ).get( 0 );

						autofill.options.country = undefined;
						autofill.options.bbox = undefined;
					} );
				},
			},
		},
		/**
		 * Show an alert popup.
		 *
		 * @since 2.13.0
		 *
		 * @param {string} title   Title.
		 * @param {string} content Content message.
		 */
		showAlert( title, content ) {
			$.alert( {
				title,
				content,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},
	};

	return app;
}( document, window, jQuery ) );

WPFormsGeolocationMapFieldBuilder.init();
