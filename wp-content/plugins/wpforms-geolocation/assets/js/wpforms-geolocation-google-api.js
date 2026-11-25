/* global wpforms_geolocation_settings, google, wpforms_geolocation_google_places, wpf */

/**
 * Handle Google Maps authentication failure.
 *
 * See: https://developers.google.com/maps/documentation/javascript/events#auth-errors.
 *
 * @since 2.12.0
 */
// eslint-disable-next-line camelcase,no-unused-vars
function gm_authFailure() {
	document.dispatchEvent( new Event( 'wpformsGeolocationApiError' ) );
}

/**
 * WPForms Geolocation Google Places API.
 *
 * @since 2.0.0
 */
const WPFormsGeolocationGooglePlacesAPI = window.WPFormsGeolocationGooglePlacesAPI || ( function( document, window ) {
	/**
	 * List of fields with the autocomplete feature.
	 *
	 * @type {Array}
	 */
	const fieldsPlaces = [];

	/**
	 * Geocoder from Geolocation API which help to detect current place by latitude and longitude.
	 *
	 * @type {Object}
	 */
	let geocoder;

	/**
	 * Whether a current location lookup is in flight.
	 *
	 * @since 2.14.0
	 *
	 * @type {boolean}
	 */
	let isDetectingLocation = false;

	// noinspection JSUnusedGlobalSymbols
	/**
	 * Plugin engine.
	 *
	 * @since 2.0.0
	 *
	 * @type {Object}
	 */
	const app = {

		/**
		 * Start the engine.
		 *
		 * @since 2.0.0
		 */
		init() {
			// The rejection handler is passed to then() instead of a chained catch(), so that only a failure
			// to load the libraries tears the fields down. An exception thrown below must not clear user data.
			app.loadLibraries().then( function() {
				app.bindEvents();
				app.getFields();

				if ( ! fieldsPlaces.length ) {
					return;
				}
				app.initGeocoder();
				app.initFieldPlaceMaps();
				app.detectGeolocation();
				app.bindFormEvents();
			}, app.destroyFieldPlaceMaps );
		},

		/**
		 * Bind events.
		 *
		 * @since 2.12.0
		 */
		bindEvents() {
			document.addEventListener( 'wpformsGeolocationApiError', function() {
				app.destroyFieldPlaceMaps();
			} );
		},

		/**
		 * Init maps on field places.
		 *
		 * @since 2.10.0
		 */
		initFieldPlaceMaps() {
			fieldsPlaces.forEach( function( currentFieldPlace ) {
				// Skip already initialized maps.
				if ( currentFieldPlace.map ) {
					return;
				}

				app.initMap( currentFieldPlace );
				app.initAutocomplete( currentFieldPlace );
			} );
		},

		/**
		 * Destroy maps on field places.
		 *
		 * @since 2.12.0
		 */
		destroyFieldPlaceMaps() {
			fieldsPlaces.forEach( function( currentFieldPlace ) {
				// Remove map.
				if ( currentFieldPlace.mapField ) {
					currentFieldPlace.mapField.remove();
				}
				currentFieldPlace.map = null;

				// Reset search field.
				currentFieldPlace.searchField.value = '';
				currentFieldPlace.searchField.setAttribute( 'placeholder', currentFieldPlace.searchField.getAttribute( 'data-placeholder' ) || '' );
				currentFieldPlace.searchField.setAttribute( 'style', '' );

				// Enable the search field.
				currentFieldPlace.searchField.removeAttribute( 'disabled' );
			} );

			// Add notice about API error.
			app.addNoticeBeforeApiKeyRow();
		},

		/**
		 * Add a notice before the API key settings row.
		 *
		 * It's supposed to be used on the Admin settings page when the wpf object is available.
		 *
		 * @since 2.12.0
		 */
		addNoticeBeforeApiKeyRow() {
			const apiKeyRow = document.getElementById( 'wpforms-setting-row-geolocation-google-places-api-key' );
			if ( ! apiKeyRow ) {
				return;
			}

			// Check if a notice already exists to avoid duplicates.
			const existingNotice = document.getElementById( 'wpforms-geolocation-api-notice' );
			if ( existingNotice ) {
				return;
			}

			if ( ! wpforms_geolocation_google_places.l10n?.apiError ) {
				return;
			}

			// Create the notice element.
			const notice = document.createElement( 'div' );
			notice.id = 'wpforms-geolocation-api-notice';
			notice.className = 'wpforms-geolocation-api-notice notice-error';

			const p = document.createElement( 'p' );
			p.innerHTML = wpf.sanitizeHTML( wpforms_geolocation_google_places.l10n.apiError );

			notice.appendChild( p );

			// Insert the notice before the API key row.
			apiKeyRow.parentNode.insertBefore( notice, apiKeyRow );
		},

		/**
		 * Bind form events.
		 *
		 * @since 2.3.0
		 */
		bindFormEvents() {
			const enableButton = [];

			document.querySelectorAll( '.wpforms-form' ).forEach( function( form ) {
				if ( ! form.querySelector( '[data-autocomplete]' ) ) {
					return;
				}

				const formID = form.getAttribute( 'data-formid' );

				// We should prevent a form submission on the Enter keyboard key.
				// Enable/disable a form button when the suggestions popup is close/open.
				const observer = new MutationObserver( function( MutationRecords ) {
					MutationRecords.forEach( function( MutationRecord ) {
						if ( ! MutationRecord.target.classList.contains( 'pac-container' ) || MutationRecord.type !== 'attributes' || MutationRecord.attributeName !== 'style' ) {
							return;
						}

						const formButton = form.querySelector( '.wpforms-submit' );

						if ( MutationRecord.target.style.display !== 'none' ) {
							formButton.disabled = true;
							delete enableButton[ formID ];

							return;
						}

						enableButton[ formID ] = setTimeout( function() {
							formButton.disabled = false;
						}, 300 );
					} );
				} );

				observer.observe( document.querySelector( 'body' ), { attributes: true, subtree: true } );
			} );

			// Core fires these events with jQuery's trigger(), which addEventListener() never receives;
			// jQuery instead calls matching on{type} properties along the event path, which ends at window.
			// Bind on window rather than document: other scripts (e.g. Form Locker) assign the same
			// document properties, and whichever script ran last would clobber the other handler.
			// Re-assigning when init() re-runs (Elementor popups) is idempotent by design.
			window.onwpformsProcessConditionalsField = app.processConditionalLogic;
			window.onwpformsRepeaterFieldCloneCreated = app.repeaterFieldCloneCreated;
		},

		/**
		 * Initialize autocomplete for added repeated field.
		 *
		 * @since 2.13.0
		 */
		repeaterFieldCloneCreated() {
			app.getFields();
			app.initFieldPlaceMaps();
			app.detectGeolocation();
		},

		/**
		 * Process conditional logic.
		 * Relaunch geolocation detection if the field is visible.
		 *
		 * @since 2.13.0
		 *
		 * @param {Event}   e       Event.
		 * @param {number}  formID  Form ID.
		 * @param {number}  fieldID Field ID.
		 * @param {boolean} pass    If the field condition is met.
		 * @param {string}  action  'show' or 'hide'.
		 */
		processConditionalLogic( e, formID, fieldID, pass, action ) {
			const el = document.getElementById( `wpforms-${ formID }-field_${ fieldID }` );

			if ( ! el || ! el.hasAttribute( 'data-autocomplete' ) ) {
				return;
			}

			const currentFieldPlace = app.findFieldPlaceBy( 'searchField', el );

			if (
				( pass && action === 'hide' ) ||
				( ! pass && action !== 'hide' )
			) {
				// The field is hidden and conditional logic resets its value.
				// Drop the detection flag so the current location is detected again once the field is shown.
				if ( currentFieldPlace && currentFieldPlace.currentGeolocationInited ) {
					delete currentFieldPlace.currentGeolocationInited;
				}
			} else if ( ! currentFieldPlace || ! currentFieldPlace.currentGeolocationInited ) {
				// The field became visible and has not been auto-filled yet.
				app.detectGeolocation();
			}
		},

		/**
		 * Show a debug message.
		 *
		 * @since 2.0.0
		 *
		 * @param {string|object} message Debug message.
		 */
		showDebugMessage( message ) {
			if ( ! window.location.hash || '#wpformsdebug' !== window.location.hash ) {
				return;
			}

			// eslint-disable-next-line no-console
			console.log( message );
		},

		/**
		 * Closest function.
		 *
		 * @param {Element} el       Element.
		 * @param {string}  selector Parent selector.
		 *
		 * @return {Element|undefined} Parent.
		 */
		closest( el, selector ) {
			const matchesSelector = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;

			while ( el ) {
				if ( matchesSelector.call( el, selector ) ) {
					break;
				}
				el = el.parentElement;
			}
			return el;
		},

		/**
		 * Get all fields for geolocation.
		 *
		 * @since 2.0.0
		 */
		getFields() {
			const forms = document.querySelectorAll( '.wpforms-form' );

			forms.forEach( function( form, formIndex ) {
				const fields = Array.prototype.slice.call(
					form.querySelectorAll( '.wpforms-field input[type="text"][data-autocomplete="1"]:not(.pac-target-input)' )
				);

				fields.forEach( function( el ) {
					const wrapper = app.closest( el, '.wpforms-field' ),
						mapField = el.hasAttribute( 'data-display-map' ) ? wrapper.querySelector( '.wpforms-geolocation-map' ) : null,
						type = wrapper.classList[ 1 ] ? wrapper.classList[ 1 ].replace( 'wpforms-field-', '' ) : 'text';

					const fieldID = el.getAttribute( 'id' );

					if ( app.placeAlreadyAdded( fieldID + '_' + formIndex ) ) {
						return;
					}

					el.setAttribute( 'data-placeholder', el.getAttribute( 'placeholder' ) || '' );

					const field = {
						fieldID: fieldID + '_' + formIndex,
						searchField: el,
						mapField,
						type,
						settings: app.getFieldSettings( el ),
					};

					if ( type === 'address' ) {
						field.additionalFields = app.getAdditionalFields( wrapper );
					}

					fieldsPlaces.push( field );
				} );
			} );
		},

		/**
		 * Get additional fields for an address type.
		 *
		 * @since 2.12.0
		 *
		 * @param {Element} wrapper Field wrapper.
		 *
		 * @return {Object} Additional fields.
		 */
		getAdditionalFields( wrapper ) {
			const country = wrapper.querySelector( '.wpforms-field-address-country' );

			return {
				locality: {
					el: wrapper.querySelector( '.wpforms-field-address-city' ),
					type: 'long_name',
				},
				postal_town: { // eslint-disable-line camelcase
					el: wrapper.querySelector( '.wpforms-field-address-city' ),
					type: 'long_name',
				},
				political: {
					el: wrapper.querySelector( '.wpforms-field-address-state' ),
					type: country ? 'long_name' : 'short_name',
				},
				administrative_area_level_1: { // eslint-disable-line camelcase
					el: wrapper.querySelector( '.wpforms-field-address-state' ),
					type: country ? 'long_name' : 'short_name',
				},
				postal_code: { // eslint-disable-line camelcase
					el: wrapper.querySelector( '.wpforms-field-address-postal' ),
					type: 'long_name',
				},
				country: {
					el: country,
					type: 'short_name',
				},
			};
		},

		/**
		 * Check if the place has already been added.
		 *
		 * @since 2.11.0
		 *
		 * @param {string} fieldID Field ID.
		 *
		 * @return {boolean} True if the place is already added, false otherwise.
		 */
		placeAlreadyAdded( fieldID ) {
			return fieldsPlaces.some( function( field ) {
				return field.fieldID === fieldID;
			} );
		},

		/**
		 * Get the field settings.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} el Element.
		 *
		 * @return {Object} The field settings.
		 */
		getFieldSettings( el ) {
			return {
				autocompleteSettings: app.getFieldAutocompleteSettings( el ),
				mapSettings: app.getFieldMapSettings( el ),
				markerSettings: app.getFieldMarkerSettings( el ),
			};
		},

		/**
		 * Get the autocomplete field settings.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} el Element.
		 *
		 * @return {Object} The field autocomplete settings.
		 */
		getFieldAutocompleteSettings( el ) {
			const fieldSettingsName = el.getAttribute( 'id' ).replaceAll( '-', '_' );

			return Object.assign(
				{
					types: [ 'geocode' ],
				},
				wpforms_geolocation_settings.autocompleteSettings.common ? wpforms_geolocation_settings.autocompleteSettings.common : {},
				wpforms_geolocation_settings.autocompleteSettings[ fieldSettingsName ] ? wpforms_geolocation_settings.autocompleteSettings[ fieldSettingsName ] : {},
			);
		},

		/**
		 * Get the map field settings.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} el Element.
		 *
		 * @return {Object} The field map settings.
		 */
		getFieldMapSettings( el ) {
			const fieldSettingsName = el.getAttribute( 'id' ).replaceAll( '-', '_' );

			return Object.assign(
				{

					// Backward compatibility with older versions.
					zoom: wpforms_geolocation_settings.zoom ? wpforms_geolocation_settings.zoom : 9,
					center: wpforms_geolocation_settings.default_location ? wpforms_geolocation_settings.default_location : {},
				},
				wpforms_geolocation_settings.mapSettings.common ? wpforms_geolocation_settings.mapSettings.common : {},
				wpforms_geolocation_settings.mapSettings[ fieldSettingsName ] ? wpforms_geolocation_settings.mapSettings[ fieldSettingsName ] : {},
			);
		},

		/**
		 * Get the marker field settings.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} el Element.
		 *
		 * @return {Object} The field marker settings.
		 */
		getFieldMarkerSettings( el ) {
			const fieldSettingsName = el.getAttribute( 'id' ).replaceAll( '-', '_' );

			return Object.assign(
				{
					draggable: true,
				},
				wpforms_geolocation_settings.markerSettings.common ? wpforms_geolocation_settings.markerSettings.common : {},
				wpforms_geolocation_settings.markerSettings[ fieldSettingsName ] ? wpforms_geolocation_settings.markerSettings[ fieldSettingsName ] : {},
			);
		},

		/**
		 * Init Google Maps.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 */
		initMap( currentFieldPlace ) {
			if ( ! currentFieldPlace.mapField ) {
				return;
			}

			currentFieldPlace.map = new google.maps.Map(
				currentFieldPlace.mapField, currentFieldPlace.settings.mapSettings
			);
			currentFieldPlace.marker = new google.maps.Marker(
				Object.assign(
					{
						map: currentFieldPlace.map,
						position: currentFieldPlace.settings.mapSettings.center ? currentFieldPlace.settings.mapSettings.center : {},
					},
					currentFieldPlace.settings.markerSettings
				)
			);

			currentFieldPlace.marker.addListener( 'dragend', app.markerDragend );
		},

		/**
		 * Loads the required Google Maps JavaScript API libraries asynchronously.
		 *
		 * @since 2.13.0
		 *
		 * @return {Promise} A promise that resolves when all libraries are successfully loaded.
		 */
		loadLibraries() {
			return Promise.all( [
				google.maps.importLibrary( 'maps' ),
				google.maps.importLibrary( 'marker' ),
				google.maps.importLibrary( 'places' ),
				google.maps.importLibrary( 'geocoding' ),
			] );
		},

		/**
		 * Init Google Geocoder.
		 *
		 * @since 2.0.0
		 */
		initGeocoder() {
			geocoder = new google.maps.Geocoder;
		},

		/**
		 * Action after marker was dragend.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} marker Google Marker.
		 */
		markerDragend( marker ) {
			const currentFieldPlace = app.findFieldPlaceBy( 'marker', this );

			if ( ! currentFieldPlace ) {
				return;
			}

			app.detectPlaceByCoordinates( marker.latLng, function( place ) {
				app.updateFields( currentFieldPlace, place );
				currentFieldPlace.map.setCenter( marker.latLng );
			} );
		},

		/**
		 * Detect Place by latitude and longitude.
		 *
		 * @since 2.0.0
		 * @deprecated 2.3.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 * @param {Object} latLng            Latitude and longitude.
		 */
		detectByCoordinates( currentFieldPlace, latLng ) {
			// eslint-disable-next-line no-console
			console.warn( 'The WPFormsGeolocationGooglePlacesAPI.detectByCoordinates() is deprecated since version 2.3.0! Use the WPFormsGeolocationGooglePlacesAPI.detectPlaceByCoordinates() instead.' );

			if ( ! geocoder ) {
				return;
			}

			geocoder.geocode( { location: latLng }, function( results, status ) {
				if ( status !== 'OK' ) {
					app.showDebugMessage( 'Geocode was wrong' );
					app.showDebugMessage( results );
					return;
				}
				if ( ! results[ 0 ] ) {
					return;
				}
				app.updateFields( currentFieldPlace, results[ 0 ] );
			} );
		},

		/**
		 * Detect a place by search.
		 *
		 * @since 2.11.0
		 *
		 * @param {Object}   search   Search object.
		 * @param {Function} callback Success callback.
		 */
		detectPlace: ( search, callback ) => {
			if ( ! geocoder ) {
				return;
			}

			geocoder.geocode( search, function( results, status ) {
				if ( status !== 'OK' ) {
					app.showDebugMessage( 'Geocode was wrong' );
					app.showDebugMessage( results );
					return;
				}
				if ( ! results[ 0 ] ) {
					return;
				}

				callback( results[ 0 ] );
			} );
		},

		/**
		 * Detect a place by latitude and longitude.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object}   latLng   Latitude and longitude.
		 * @param {Function} callback Success callback.
		 */
		detectPlaceByCoordinates( latLng, callback ) {
			this.detectPlace( { location: latLng }, callback );
		},

		/**
		 * Detect a place by address.
		 *
		 * @since 2.11.0
		 *
		 * @param {string}   address  Address.
		 * @param {Function} callback Success callback.
		 */
		detectPlaceByAddress( address, callback ) {
			this.detectPlace( { address }, callback );
		},

		/**
		 * Get address from current field place.
		 *
		 * @since 2.11.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 *
		 * @return {string} Address.
		 */
		getFieldPlaceAddress: ( currentFieldPlace ) => {
			// Single Line Text fields have no additional address sub-fields.
			if ( ! currentFieldPlace.additionalFields ) {
				return currentFieldPlace.searchField.value;
			}

			const fieldsValues = [ currentFieldPlace.searchField.value ];
			Object.values( currentFieldPlace.additionalFields ).forEach( function( field ) {
				if ( ! field.el ) {
					return;
				}

				if ( fieldsValues.includes( field.el.value ) ) {
					return;
				}

				fieldsValues.push( field.el.value );
			} );

			return fieldsValues.join( ' ' );
		},

		/**
		 * Update map.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 * @param {Object} latLng            Latitude and longitude.
		 */
		updateMap( currentFieldPlace, latLng ) {
			if ( ! currentFieldPlace.map ) {
				return;
			}

			currentFieldPlace.marker.setPosition( latLng );
			currentFieldPlace.map.setCenter( latLng );
		},

		/**
		 * Find the current group field with places API by Google marker.
		 *
		 * @since 2.0.0
		 * @deprecated 2.13.0
		 *
		 * @param {Object} marker Google marker.
		 *
		 * @return {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByMarker( marker ) {
			// eslint-disable-next-line no-console
			console.warn( "The WPFormsGeolocationGooglePlacesAPI.findFieldPlaceByMarker( marker ) is deprecated since version 2.13.0! Use the WPFormsGeolocationGooglePlacesAPI.findFieldPlaceBy( 'marker', marker ) instead." );

			return app.findFieldPlaceBy( 'marker', marker );
		},

		/**
		 * Find the current group field with places API by Google Autocomplete.
		 *
		 * @since 2.0.0
		 * @deprecated 2.13.0
		 *
		 * @param {Object} autocomplete Google Autocomplete.
		 *
		 * @return {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByAutocomplete( autocomplete ) {
			// eslint-disable-next-line no-console
			console.warn( "The WPFormsGeolocationGooglePlacesAPI.findFieldPlaceByMarker( autocomplete ) is deprecated since version 2.13.0! Use the WPFormsGeolocationGooglePlacesAPI.findFieldPlaceBy( 'autocomplete', autocomplete ) instead." );

			return app.findFieldPlaceBy( 'autocomplete', autocomplete );
		},

		/**
		 * Find the current group field by a field name and value.
		 *
		 * @since 2.13.0
		 *
		 * @param {string} name  Field name.
		 * @param {*}      value Value.
		 *
		 * @return {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceBy( name, value ) {
			let currentFieldPlace = null;

			fieldsPlaces.some( function( el ) {
				if (
					( Object.prototype.hasOwnProperty.call( el, name ) && el[ name ] === value ) ||
					( el.additionalFields && Object.prototype.hasOwnProperty.call( el.additionalFields, name ) && el.additionalFields[ name ] === value )
				) {
					currentFieldPlace = el;

					return true;
				}

				return false;
			} );

			return currentFieldPlace;
		},

		/**
		 * Find the current group field with places API by country field element.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} countryEl Country field element.
		 *
		 * @return {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByCountry( countryEl ) {
			let currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {
				if ( ! el.additionalFields || ! el.additionalFields.country || el.additionalFields.country.el !== countryEl ) {
					return;
				}

				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Find the current group field with places API by state field element.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} politicalEl State field element.
		 *
		 * @return {object|null} currentFieldPlace Current group field with places API.
		 */
		findFieldPlaceByPolitical( politicalEl ) {
			let currentFieldPlace = null;

			fieldsPlaces.forEach( function( el ) {
				if ( ! el.additionalFields || ! el.additionalFields.political || el.additionalFields.political.el !== politicalEl ) {
					return;
				}

				currentFieldPlace = el;
			} );

			return currentFieldPlace;
		},

		/**
		 * Init Google Autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 */
		initAutocomplete( currentFieldPlace ) {
			currentFieldPlace.autocomplete = new google.maps.places.Autocomplete(
				currentFieldPlace.searchField,
				currentFieldPlace.settings.autocompleteSettings
			);

			currentFieldPlace.autocomplete.addListener( 'place_changed', app.updateFieldPlace );

			if ( 'address' === currentFieldPlace.type ) {
				app.initAutocompleteAddress( currentFieldPlace );
			}

			// Retrieve the place by the pre-filled address, e.g. when the form is loaded
			// from a Save and Resume link. An Address field can be partially filled with
			// the search field (Address Line 1) empty — a city with no street — so the
			// combined address decides whether there is anything to geocode.
			const prefilledAddress = app.getFieldPlaceAddress( currentFieldPlace ).trim();

			if ( prefilledAddress ) {
				app.detectPlaceByAddress( prefilledAddress, function( place ) {
					app.updateMap( currentFieldPlace, place.geometry.location );
				} );
			}

			if ( currentFieldPlace.settings.autocompleteSettings.strict ) {
				currentFieldPlace.autocomplete.setComponentRestrictions( {
					country: currentFieldPlace.settings.autocompleteSettings.strict,
				} );

				app.showDebugMessage( 'The #' + currentFieldPlace.searchField.getAttribute( 'id' ) + ' autocomplete field restrict to the ' + currentFieldPlace.settings.autocompleteSettings.strict.join( ', ' ) + ' counties' );
			}
		},

		/**
		 * Check whether the field already holds a value, e.g. restored by Save and Resume or pre-filled by a default value.
		 *
		 * @since 2.15.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 *
		 * @return {boolean} True when any user-facing input of the field is not empty.
		 */
		hasPrefilledValue( currentFieldPlace ) {
			if ( currentFieldPlace.searchField.value ) {
				return true;
			}

			if ( ! currentFieldPlace.additionalFields ) {
				return false;
			}

			// The country sub-input always holds the scheme default, so it never counts as a pre-filled value.
			return Object.entries( currentFieldPlace.additionalFields ).some(
				( [ key, field ] ) => key !== 'country' && field.el && field.el.value
			);
		},

		/**
		 * Init address field autocomplete features.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 */
		initAutocompleteAddress( currentFieldPlace ) {
			app.disableBrowserAutocomplete( currentFieldPlace.searchField );

			if ( currentFieldPlace.additionalFields.country.el ) {
				// Init can re-run for the same element (repeater clones, Elementor popups), so rebind via a namespace to avoid duplicate handlers.
				jQuery( currentFieldPlace.additionalFields.country.el )
					.off( 'change.wpformsGeolocation' )
					.on( 'change.wpformsGeolocation', app.updateCountry );
			}

			if ( currentFieldPlace.additionalFields.political.el ) {
				currentFieldPlace.additionalFields.political.el.addEventListener( 'change', app.updateArea );
			}
		},

		/**
		 * Disable Chrome browser autocomplete.
		 *
		 * @since 2.0.0
		 *
		 * @param {Element} searchField Search field.
		 */
		disableBrowserAutocomplete( searchField ) {
			if ( navigator.userAgent.indexOf( 'Chrome' ) === -1 ) {
				return;
			}

			const observerHack = new MutationObserver( function() {
				observerHack.disconnect();
				searchField.setAttribute( 'autocomplete', 'chrome-off' );
			} );

			observerHack.observe( searchField, {
				attributes: true,
				attributeFilter: [ 'autocomplete' ],
			} );
		},

		/**
		 * Update field place when Google Autocomplete field fills.
		 *
		 * @since 2.0.0
		 */
		updateFieldPlace() {
			const currentFieldPlace = app.findFieldPlaceBy( 'autocomplete', this );

			if ( ! currentFieldPlace?.autocomplete ) {
				return;
			}

			const place = currentFieldPlace.autocomplete.getPlace();

			if ( ! place.geometry || ! place.geometry.location ) {
				return;
			}

			app.updateMap( currentFieldPlace, place.geometry.location );
			app.updateFields( currentFieldPlace, place );

			// The user picked this place, so current-location detection must not overwrite it later.
			currentFieldPlace.currentGeolocationInited = true;
		},

		/**
		 * Update fields at a specified place.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 * @param {Object} place             Current place.
		 */
		updateFields( currentFieldPlace, place ) {
			if ( ! Object.prototype.hasOwnProperty.call( place, 'formatted_address' ) ) {
				return;
			}

			if ( 'text' === currentFieldPlace.type ) {
				app.updateTextField( currentFieldPlace, place );
			} else if ( 'address' === currentFieldPlace.type ) {
				app.updateAddressFields( currentFieldPlace, place );
			}

			app.triggerEvent( currentFieldPlace.searchField, 'change' );

			app.showDebugMessage( 'Fields was updated' );
			app.showDebugMessage( currentFieldPlace );
			app.showDebugMessage( place );
		},

		/**
		 * Update text field at specified place.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 * @param {Object} place             Current place.
		 */
		updateTextField( currentFieldPlace, place ) {
			currentFieldPlace.searchField.value = place.formatted_address;
		},

		/**
		 * Trigger JS event.
		 *
		 * @since 2.0.0
		 *
		 * @param {Element} el        Element.
		 * @param {string}  eventName Event name.
		 */
		triggerEvent( el, eventName ) {
			const e = document.createEvent( 'HTMLEvents' );

			e.initEvent( eventName, true, true );
			el.dispatchEvent( e );
		},

		/**
		 * Update address fields at a specified place.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 * @param {Object} place             Current place.
		 */
		updateAddressFields( currentFieldPlace, place ) { // eslint-disable-line complexity
			let street = '';
			const streetNumberParts = [];

			app.clearAdditionalFields( currentFieldPlace );

			for ( const component of place.address_components ) {
				const addressType = component.types[ 0 ];

				if ( 'route' === addressType ) {
					street = component.short_name;
					continue;
				}

				if ( [ 'street_number', 'subpremise' ].includes( addressType ) && component.short_name ) {
					streetNumberParts[ addressType ] = component.short_name;
					continue;
				}

				if ( currentFieldPlace.additionalFields[ addressType ] ) {
					if ( currentFieldPlace.additionalFields[ addressType ].el ) {
						app.updateAddressField(
							currentFieldPlace.additionalFields[ addressType ].el,
							component[ currentFieldPlace.additionalFields[ addressType ].type ]
						);
					}
				}
			}

			currentFieldPlace.searchField.value = app.formatAddressField( place, app.getStreetNumber( streetNumberParts ), street );
		},

		/**
		 * Retrieves the street number formatted with an optional subpremise.
		 *
		 * @since 2.11.0
		 *
		 * @param {Object} streetNumberParts An object containing parts of the street number.
		 *
		 * @return {string} The formatted street number, optionally including the subpremise.
		 */
		getStreetNumber( streetNumberParts ) {
			if ( ! Object.keys( streetNumberParts ).length ) {
				return '';
			}

			return streetNumberParts?.street_number + ( streetNumberParts?.subpremise ? '/' + streetNumberParts.subpremise : '' );
		},

		/**
		 * Update the specified address field.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} field Field.
		 * @param {string}  value Value.
		 */
		updateAddressField( field, value ) {
			if ( app.isConversationalSelect( field ) ) {
				app.updateConversationalSelect( field, value );
			}

			field.value = value;
			this.triggerEvent( field, 'change' );
		},

		/**
		 * Is conversational form select.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} field Field.
		 *
		 * @return {boolean} Is the field is conversational select format?
		 */
		isConversationalSelect( field ) {
			if ( field.tagName !== 'SELECT' ) {
				return false;
			}

			return Boolean( field.closest( '.wpforms-conversational-select' ) );
		},

		/**
		 * Update conversational forms select.
		 *
		 * @since 2.3.0
		 *
		 * @param {Element} field Field.
		 * @param {string}  value Value.
		 */
		updateConversationalSelect( field, value ) {
			const select = field.closest( '.wpforms-conversational-select' ),
				selectedOption = field.querySelector( 'option[value="' + value + '"]' ),
				input = select.querySelector( '.wpforms-conversational-form-dropdown-input input' );

			field.value = value;

			if ( selectedOption && input ) {
				input.value = selectedOption.innerText;
			}
		},

		/**
		 * Clear additional fields.
		 *
		 * @since 2.1.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 */
		clearAdditionalFields( currentFieldPlace ) {
			Object.values( currentFieldPlace.additionalFields ).forEach( function( field ) {
				if ( ! field.el ) {
					return;
				}

				field.el.value = '';
			} );
		},

		/**
		 * Get a formatted address.
		 *
		 * @since 2.0.0
		 *
		 * @param {Object} place        Current place.
		 * @param {string} streetNumber Street number.
		 * @param {string} street       Street name.
		 *
		 * @return {string} Formatted address.
		 */
		formatAddressField( place, streetNumber, street ) {
			let address = 0 === place.formatted_address.indexOf( streetNumber )
				? streetNumber + ' ' + street // US format.
				: street + ', ' + streetNumber; // EU format.

			// Remove spaces and commas at the start or end of the string.
			address = address.replace( /,$|^,/g, '' ).trim();

			return address === 'undefined' ? '' : address;
		},

		/**
		 * Update country for address field.
		 * Conditional strict.
		 * Start work after CUSTOMER changes a country field.
		 *
		 * @since 2.0.0
		 */
		updateCountry() {
			const currentFieldPlace = app.findFieldPlaceByCountry( this );

			if ( ! currentFieldPlace?.autocomplete ) {
				return;
			}

			const countryCode = this.value.toString().toLocaleLowerCase();

			currentFieldPlace.autocomplete.setComponentRestrictions( {
				country: [ countryCode ],
			} );

			app.showDebugMessage( 'Autocomplete field restrict to the ' + countryCode + ' country' );
		},

		/**
		 * Update state for the address field.
		 * Conditional, not strict.
		 * Start work after CUSTOMER changes a state field.
		 *
		 * @since 2.0.0
		 */
		updateArea() {
			const currentFieldPlace = app.findFieldPlaceByPolitical( this );

			if ( ! currentFieldPlace?.autocomplete ) {
				return;
			}

			const stateCode = this.value.toString().toUpperCase();

			app.showDebugMessage( 'Autocomplete field try to find the ' + stateCode + ' state' );

			const latLng = app.findStateCoordinates( currentFieldPlace, stateCode );

			if ( ! latLng ) {
				app.showDebugMessage( 'Autocomplete field doesn\'t restrict to the ' + stateCode + ' state' );

				return;
			}

			currentFieldPlace.autocomplete.setBounds( new google.maps.LatLngBounds( latLng ) );

			app.showDebugMessage( 'Autocomplete field restrict to the ' + stateCode + ' state' );
		},

		/**
		 * Find the state coordinates.
		 *
		 * @since 2.3.0
		 *
		 * @param {Object} currentFieldPlace Current group field with places API.
		 * @param {string} stateCode         A state code.
		 *
		 * @return {object|undefined} Latitude and Longitude for the current state.
		 */
		findStateCoordinates( currentFieldPlace, stateCode ) {
			if ( ! currentFieldPlace.settings.autocompleteSettings.strict ) {
				return;
			}

			let latLng;

			currentFieldPlace.settings.autocompleteSettings.strict.forEach( function( countryCode ) {
				if ( wpforms_geolocation_settings.states[ countryCode ] && wpforms_geolocation_settings.states[ countryCode ][ stateCode ] ) {
					latLng = {
						lat: wpforms_geolocation_settings.states[ countryCode ][ stateCode ].lat,
						lng: wpforms_geolocation_settings.states[ countryCode ][ stateCode ].lng,
					};

					return false;
				}
			} );

			return latLng;
		},

		/**
		 * Detect customer geolocation.
		 *
		 * @since 2.0.0
		 */
		detectGeolocation() {
			if ( isDetectingLocation || ! wpforms_geolocation_settings.current_location || ! navigator.geolocation || ! fieldsPlaces ) {
				return;
			}

			// Collapse concurrent calls: revealing several fields at once must not spawn parallel lookups.
			isDetectingLocation = true;

			const geolocation = {};

			navigator.geolocation.getCurrentPosition( function( position ) {
				isDetectingLocation = false;

				geolocation.lat = position.coords.latitude;
				geolocation.lng = position.coords.longitude;

				app.detectPlaceByCoordinates( geolocation, function( place ) {
					fieldsPlaces.forEach( function( currentFieldPlace, i ) {
						// Skip when the field map location has already been initialized.
						if ( currentFieldPlace.currentGeolocationInited ) {
							return;
						}

						const container = currentFieldPlace.searchField.closest( '.wpforms-field' );

						if ( container.classList.contains( 'wpforms-conditional-hide' ) ) {
							return;
						}

						// A value already in the field belongs to the user — restored by Save and Resume,
						// re-applied by a conditional logic reset, or set as a default — so never overwrite it.
						if ( app.hasPrefilledValue( currentFieldPlace ) ) {
							fieldsPlaces[ i ].currentGeolocationInited = true;

							return;
						}

						app.updateMap( currentFieldPlace, geolocation );
						app.updateFields( currentFieldPlace, place );

						fieldsPlaces[ i ].currentGeolocationInited = true;
					} );
				} );
			}, () => {
				// Allow a retry after a denied or failed location request.
				isDetectingLocation = false;
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;
}( document, window ) );

// Load script if a Google geolocation library was included from another theme or plugin.
window.addEventListener( 'load', WPFormsGeolocationGooglePlacesAPI.init );

// Initialize after the Elementor popup is opened.
window.addEventListener( 'elementor/popup/show', WPFormsGeolocationGooglePlacesAPI.init );

// noinspection JSUnusedGlobalSymbols
/**
 * Use function callback for running throw Google Ads API.
 *
 * @since 2.0.0
 */
function WPFormsGeolocationInitGooglePlacesAPI() { // eslint-disable-line no-unused-vars
	window.removeEventListener( 'load', WPFormsGeolocationGooglePlacesAPI.init );
	WPFormsGeolocationGooglePlacesAPI.init();
}
