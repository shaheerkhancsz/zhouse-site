/* global WPFormsUtils, google, mapboxgl, wpforms_geolocation_map_field */

/**
 * @param google.maps
 * @param google.maps.LatLng
 * @param google.maps.marker
 * @param google.maps.marker.AdvancedMarkerElement
 * @param google.maps.LatLngBounds
 *
 * @param mapboxgl.accessToken
 * @param mapboxgl.Map
 * @param mapboxgl.NavigationControl
 * @param mapboxgl.FullscreenControl
 * @param mapboxgl.Marker
 * @param mapboxgl.LngLatBounds
 *
 * @param wpforms_geolocation_map_field.settings.access_token
 * @param wpforms_geolocation_map_field.fields
 * @param wpforms_geolocation_map_field.api_error
 */

/**
 * Handle the Places API authentication failure.
 *
 * `gm_authFailure` is the fixed callback name Google calls when it rejects the API key,
 * guarded because `wpforms-geolocation-google-api.js` defines the same callback.
 * See: https://developers.google.com/maps/documentation/javascript/events#auth-errors.
 *
 * @since 2.15.0
 */
if ( typeof window.gm_authFailure !== 'function' ) {
	// eslint-disable-next-line camelcase
	window.gm_authFailure = () => document.dispatchEvent( new Event( 'wpformsGeolocationApiError' ) );
}

const WPFormsGeolocationMapField = window.WPFormsGeolocationMapField || ( function( document, window, $ ) {
	const app = {
		/**
		 * CSS selectors.
		 *
		 * @since 2.13.0
		 */
		selectors: {
			form: '.wpforms-form',
			mapField: '.wpforms-field-map',
			mapWrapper: '.wpforms-geolocation-map',
			gmpMap: 'gmp-map',
			gmpMarker: 'gmp-advanced-marker',
			mapboxMarker: '.mapboxgl-marker',
			locationListInput: '.wpforms-field-map-choices input[type="radio"]',
			apiError: '.wpforms-geolocation-map-api-error',
		},
		/**
		 * CSS classes.
		 *
		 * @since 2.13.0
		 */
		classes: {
			mapFieldInitialized: 'wpforms-field-map-initialized',
			highlightMarker: 'wpforms-map-field-marker-highlight',
			apiError: 'wpforms-geolocation-map-api-error',
			hasApiError: 'wpforms-geolocation-map-has-api-error',
		},
		/**
		 * Whether the Places API rejected the credentials and maps cannot be rendered.
		 *
		 * @since 2.15.0
		 *
		 * @type {boolean}
		 */
		apiFailed: false,
		/**
		 * Run engine.
		 *
		 * @since 2.13.0
		 */
		init() {
			$( window ).on( 'load', function() {
				// in the case of jQuery 3.+ we need to wait for a ` ready ` event first.
				if ( typeof $.ready.then === 'function' ) {
					$.ready.then( app.load );
				} else {
					app.load();
				}
			} );

			app.bindEvents();
		},
		/**
		 * Bind events.
		 *
		 * @since 2.13.0
		 */
		bindEvents() {
			$( document )
				.on( 'wpformsProcessConditionalsField', app.initializeMapFields )
				.on( 'wpformsRepeaterFieldCloneCreated', app.initializeMapFields )
				.on( 'wpformsPageChange', app.maybeResize )
				.on( 'change', app.selectors.locationListInput, app.changeLocationList );

			window.addEventListener( 'elementor/popup/show', app.initializeMapFields );
			document.addEventListener( 'wpformsGeolocationApiError', app.handleApiError );
		},
		/**
		 * Window loaded.
		 *
		 * @since 2.13.0
		 */
		load() {
			if ( typeof wpforms_geolocation_map_field?.fields === 'undefined' ) {
				return;
			}

			app.initializeMapFields();
		},
		/**
		 * Initialize map fields.
		 *
		 * @since 2.13.0
		 */
		initializeMapFields() {
			$( `${ app.selectors.mapField }:not(.${ app.classes.mapFieldInitialized })` ).each( function() {
				const $field = $( this );

				const isElementorPopup = $field.get( 0 ).closest( '.elementor-location-popup' );

				if ( isElementorPopup && ! isElementorPopup.offsetParent ) {
					return;
				}

				const fieldSettings = app.getFieldSettings( $field ),
					$mapWrapper = $( app.selectors.mapWrapper, $field );

				if ( ! fieldSettings.map || ! fieldSettings.markers ) {
					$mapWrapper.remove();
					return;
				}

				const mapElement = $mapWrapper.get( 0 );

				app.renderMap( mapElement, fieldSettings.map ).then( function() {
					fieldSettings.markers.forEach( function( markerSettings ) {
						app.appendMarker( mapElement, markerSettings );
					} );

					app.fitBounds( mapElement, fieldSettings.bounds );
					$field.addClass( app.classes.mapFieldInitialized );

					document.dispatchEvent( new CustomEvent( 'elementor/lazyload/observe' ) );
				} ).catch( function() {} );
			} );
		},
		/**
		 * Get field settings.
		 *
		 * @since 2.13.0
		 *
		 * @param {jQuery} $field Field element.
		 *
		 * @return {Object} List of settings for a map and markers.
		 */
		getFieldSettings( $field ) {
			const fieldId = String( $field.data( 'field-id' ) ).split( '_' )[ 0 ],
				formId = $field.closest( app.selectors.form ).data( 'formid' );

			return wpforms_geolocation_map_field.fields[ formId ]?.[ fieldId ] || {};
		},
		/**
		 * Render map.
		 *
		 * @since 2.13.0
		 *
		 * @param {HTMLElement} mapElement Map element.
		 * @param {Object}      settings   Map settings.
		 *
		 * @return {Promise<void>} Promise resolved when the map is rendered.
		 */
		async renderMap( mapElement, settings ) {
			if ( app.apiFailed ) {
				app.renderApiError( mapElement );

				throw new Error( 'WPForms Geolocation: the Places API rejected the key.' );
			}

			return app.providers.getCurrentProvider()?.renderMap( mapElement, settings );
		},
		/**
		 * Display the API error message in every Map field on the page.
		 *
		 * @since 2.15.0
		 */
		handleApiError() {
			app.apiFailed = true;

			document
				.querySelectorAll( `${ app.selectors.mapField } ${ app.selectors.mapWrapper }` )
				.forEach( ( mapWrapper ) => app.renderApiError( mapWrapper ) );
		},
		/**
		 * Render the API error message inside a map wrapper.
		 *
		 * @since 2.15.0
		 *
		 * @param {HTMLElement} mapWrapper Map wrapper element.
		 */
		renderApiError( mapWrapper ) {
			if ( ! mapWrapper || mapWrapper.querySelector( app.selectors.apiError ) ) {
				return;
			}

			mapWrapper.classList.add( app.classes.hasApiError );

			const message = document.createElement( 'div' );

			message.className = app.classes.apiError;
			message.textContent = wpforms_geolocation_map_field?.api_error || '';

			mapWrapper.appendChild( message );
		},
		/**
		 * Append marker to a map.
		 *
		 * @since 2.13.0
		 *
		 * @param {HTMLElement} mapElement Map element.
		 * @param {Object}      settings   Marker settings.
		 */
		appendMarker( mapElement, settings ) {
			app.providers.getCurrentProvider()?.appendMarker( mapElement, settings );
		},
		/**
		 * Get marker template.
		 *
		 * @since 2.13.0
		 *
		 * @param {Object} settings Marker settings.
		 *
		 * @return {string} HTML template for a marker.
		 */
		getMarkerTemplate( settings ) {
			const style =
				( ! settings.latitude && ! settings.longitude ) ||
				( settings.markerType === 'image' && ! settings.imgUrl ) ? 'display:none' : '',
				iconColor = WPFormsUtils.cssColorsUtils.getContrastColor( settings.color ) === '#ffffff' ? 'light' : 'dark';

			let template = `<div class="wpforms-map-field-marker-wrapper"><div class="wpforms-map-field-marker" style="${ app.templateHelpers.escapeAttribute( style ) }">`;

			template += `<div class="wpforms-map-field-marker-pin wpforms-map-field-marker-pin-type-${ app.templateHelpers.escapeAttribute( settings.markerType ) } wpforms-map-field-marker-pin-size-${ app.templateHelpers.escapeAttribute( settings.size ) }">`;
			template += `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 38" fill="none" class="wpforms-map-field-marker-pin-icon-svg">
				<g filter="url(#a)"><path fill="#fff" d="M16 2C8.82 2 3 7.784 3 14.918 3 22.053 8.141 28.23 16 34c7.859-5.77 13-11.947 13-19.082C29 7.784 23.18 2 16 2Z"></path></g>
				<path class="wpforms-map-field-marker-pin-icon-background" fill="${ app.templateHelpers.escapeAttribute( settings.color ) }" d="M16 25c5.523 0 10-4.477 10-10S21.523 5 16 5 6 9.477 6 15s4.477 10 10 10Z"></path>
				<defs><filter id="a" width="32" height="38" x="0" y="0" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feColorMatrix in="SourceAlpha" result="hardAlpha" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dy="1"></feOffset><feGaussianBlur stdDeviation="1.5"></feGaussianBlur><feComposite in2="hardAlpha" operator="out"></feComposite><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.3 0"></feColorMatrix><feBlend in2="BackgroundImageFix" result="effect1_dropShadow_247_5513"></feBlend><feBlend in="SourceGraphic" in2="effect1_dropShadow_247_5513" result="shape"></feBlend></filter></defs>`;
			template += '</svg>';

			if ( settings.iconSVG ) {
				template += `<div class="wpforms-map-field-marker-pin-icon wpforms-map-field-marker-pin-icon-${ app.templateHelpers.escapeAttribute( iconColor ) }">`;
				template += settings.iconSVG;
				template += '</div>';
			} else {
				template += `<div class="wpforms-map-field-marker-pin-icon wpforms-map-field-marker-pin-icon-${ app.templateHelpers.escapeAttribute( iconColor ) } ${ app.templateHelpers.escapeAttribute( settings.icon ) }"></div>`;
			}

			if ( typeof settings.imgUrl !== 'undefined' ) {
				template += `<img src="${ app.templateHelpers.escapeAttribute( settings.imgUrl ) }" alt="" class="wpforms-map-field-marker-pin-image"/>`;
			}
			template += '</div>';

			template += '<div class="wpforms-map-field-marker-content">';
			template += `<div class="wpforms-map-field-marker-content-name">${ app.templateHelpers.escapeHtml( settings.name ) }</div>`;
			template += `<div class="wpforms-map-field-marker-content-description">${ app.templateHelpers.escapeHtml( settings.description ) }</div>`;
			template += '</div>';
			template += '</div>';
			template += '</div>';

			return template;
		},
		/**
		 * Make added markers visible all together on the map.
		 *
		 * @since 2.13.0
		 *
		 * @param {HTMLElement} mapElement Map element.
		 * @param {Object}      settings   Bounds settings.
		 */
		fitBounds( mapElement, settings ) {
			if ( ! mapElement ) {
				return;
			}

			app.providers.getCurrentProvider()?.fitBounds( mapElement, settings );
		},
		/**
		 * Resize map when page changes.
		 *
		 * @since 2.13.0
		 *
		 * @param {Event}  e        Event.
		 * @param {number} nextPage Next page number.
		 */
		maybeResize( e, nextPage ) {
			const provider = app.providers.getCurrentProvider();

			if ( provider !== app.providers.MapboxPlaces ) {
				return;
			}

			const $page = $( `.wpforms-page-${ nextPage }` ),
				$mapField = $( app.selectors.mapField, $page );

			if ( ! $mapField.length ) {
				return;
			}

			window.dispatchEvent( new Event( 'resize' ) );
		},
		/**
		 * Handle map choice change.
		 *
		 * @since 2.13.0
		 */
		changeLocationList() {
			const $input = $( this ),
				$field = $input.closest( app.selectors.mapField ),
				locationIndex = $input.val();

			const $markers = $( `${ app.selectors.gmpMarker }, ${ app.selectors.mapboxMarker }`, $field ),
				$activeMarker = $markers.eq( locationIndex );

			if ( ! $activeMarker.length ) {
				return;
			}

			$markers.removeClass( app.classes.highlightMarker );
			$activeMarker.addClass( app.classes.highlightMarker );
		},
		/**
		 * Providers.
		 *
		 * @since 2.13.0
		 */
		providers: {
			/**
			 * Get current provider instance.
			 *
			 * @since 2.13.0
			 *
			 * @return {object|undefined} Provider instance.
			 */
			getCurrentProvider() {
				if ( typeof google !== 'undefined' ) {
					return app.providers.GooglePlaces;
				}

				if ( typeof mapboxgl !== 'undefined' ) {
					return app.providers.MapboxPlaces;
				}
			},
			/**
			 * Google Places Provider.
			 *
			 * @since 2.13.0
			 */
			GooglePlaces: {
				/**
				 * Render map.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Map element.
				 * @param {Object}      settings   Field settings.
				 *
				 * @return {Promise<void>}
				 */
				async renderMap( mapElement, settings ) {
					// Prevent reinitialization if the map is already initialized.
					if ( mapElement.querySelector( app.selectors.gmpMap ) ) {
						return Promise.reject();
					}

					return Promise.all( [
						google.maps.importLibrary( 'maps' ),
						google.maps.importLibrary( 'marker' ),
					] ).then( async function() {
						const gmpMap = document.createElement( app.selectors.gmpMap );

						mapElement.appendChild( gmpMap );

						settings.center.lat = parseFloat( settings.center.lat.toString() );
						settings.center.lng = parseFloat( settings.center.lng.toString() );

						await new Promise( ( resolve ) => {
							const checkInnerMap = () => {
								if ( gmpMap.innerMap ) {
									resolve();

									return;
								}

								requestAnimationFrame( checkInnerMap );
							};
							checkInnerMap();
						} );

						gmpMap.innerMap.setOptions( {
							mapId: settings.map_id,
							center: new google.maps.LatLng( settings.center ),
							zoom: parseInt( settings.zoom_level, 10 ),
							fullscreenControl: ! settings.hide_full_screen,
							mapTypeControl: ! settings.hide_map_type,
							clickableIcons: ! settings.hide_location_info && ! settings.disable_dragging,
							streetViewControl: ! settings.hide_street_view,
							cameraControl: ! settings.hide_camera_control,
							zoomControl: ! settings.hide_zoom,
							gestureHandling: settings.disable_dragging ? 'none' : 'auto',
							scrollwheel: ! settings.disable_mouse_zooming,
						} );
					} );
				},
				/**
				 * Append marker to a map.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Map element.
				 * @param {Object}      settings   Marker settings.
				 */
				appendMarker( mapElement, settings ) {
					const gmpMap = mapElement.querySelector( app.selectors.gmpMap );

					if ( ! gmpMap ) {
						return;
					}

					const LatLng = new google.maps.LatLng( settings.latitude, settings.longitude ),
						markerContent = document.createElement( 'div' );

					markerContent.innerHTML = app.getMarkerTemplate( settings );

					gmpMap.append(
						new google.maps.marker.AdvancedMarkerElement( {
							position: LatLng,
							content: markerContent,
						} )
					);
				},
				/**
				 * Center map and bound them to display all markers at the same time.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Map element.
				 * @param {Object}      settings   Bounds settings.
				 */
				fitBounds( mapElement, settings ) {
					const gmpMap = mapElement.querySelector( app.selectors.gmpMap ),
						map = gmpMap?.innerMap;

					if ( ! gmpMap || ! map ) {
						return;
					}

					const markers = mapElement.querySelectorAll( app.selectors.gmpMarker ),
						bounds = new google.maps.LatLngBounds();

					const validMarkers = Array.from( markers ).filter( ( marker ) => {
						const position = marker.position || {},
							notEmptyPositions = Object.values( position ).reduce( ( a, b ) => a || b, 0 );

						return notEmptyPositions !== 0;
					} );

					if ( ! validMarkers.length ) {
						return;
					}

					if ( validMarkers.length === 1 ) {
						map.setCenter( validMarkers[ 0 ].position );

						return;
					}

					validMarkers.forEach( ( marker ) => bounds.extend( marker.position ) );

					map.fitBounds( bounds, settings?.padding );
				},
			},
			/**
			 * Mapbox Places Provider.
			 *
			 * @since 2.13.0
			 */
			MapboxPlaces: {
				/**
				 * Render map.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Map element.
				 * @param {Object}      settings   Field settings.
				 *
				 * @return {Promise<void>} Promise rejected when the map is already initialized.
				 */
				renderMap( mapElement, settings ) {
					// Prevent reinitialization if the map is already initialized.
					if ( mapElement.innerMap ) {
						return Promise.reject();
					}

					mapboxgl.accessToken = wpforms_geolocation_map_field.settings.access_token;

					const map = new mapboxgl.Map( {
						container: mapElement,
						style: 'mapbox://styles/mapbox/streets-v12',
						center: [ parseFloat( settings.center.lng ), parseFloat( settings.center.lat ) ],
						zoom: parseInt( settings.zoom_level, 10 ),
						interactive: ! settings.disable_dragging,
						scrollZoom: ! settings.disable_mouse_zooming,
					} );

					if ( ! settings.hide_full_screen ) {
						map.addControl( new mapboxgl.FullscreenControl() );
					}

					if ( ! settings.hide_zoom ) {
						map.addControl( new mapboxgl.NavigationControl() );
					}

					mapElement.innerMap = map;

					return Promise.resolve();
				},
				/**
				 * Append marker to a map.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Map element.
				 * @param {Object}      settings   Marker settings.
				 */
				appendMarker( mapElement, settings ) {
					if ( ! mapElement.innerMap ) {
						return;
					}

					const customContent = document.createElement( 'div' );

					customContent.innerHTML = app.getMarkerTemplate( settings );

					settings.longitude = parseFloat( settings.longitude ) || 0;
					settings.latitude = parseFloat( settings.latitude ) || 0;

					const marker = new mapboxgl.Marker( {
						draggable: false,
						element: customContent,
					} )
						.setLngLat( [ settings.longitude, settings.latitude ] )
						.addTo( mapElement.innerMap );

					const markerWrapper = customContent.closest( app.selectors.mapboxMarker );

					markerWrapper.markerInstance = marker;

					mapElement.innerMap.setCenter( [ settings.longitude, settings.latitude ] );
				},
				/**
				 * Center map and bound them to display all markers at the same time.
				 *
				 * @since 2.13.0
				 *
				 * @param {HTMLElement} mapElement Map element.
				 * @param {Object}      settings   Bounds settings.
				 */
				fitBounds( mapElement, settings ) {
					if ( ! mapElement.innerMap ) {
						return;
					}

					const bounds = new mapboxgl.LngLatBounds(),
						markers = mapElement.querySelectorAll( app.selectors.mapboxMarker ),
						validMarkers = Array.from( markers ).filter( ( marker ) => {
							if ( typeof marker.markerInstance === 'undefined' ) {
								return false;
							}

							const LngLat = marker.markerInstance.getLngLat();

							return LngLat.lng !== 0 && LngLat.lat !== 0;
						} );

					if ( ! validMarkers.length ) {
						return;
					}

					if ( validMarkers.length === 1 ) {
						mapElement.innerMap.setCenter( validMarkers[ 0 ].markerInstance.getLngLat() );

						return;
					}

					validMarkers.forEach( ( marker ) => bounds.extend( marker.markerInstance.getLngLat() ) );

					mapElement.innerMap.fitBounds( bounds, settings );
				},
			},
		},
		/**
		 * Template helpers.
		 *
		 * @since 2.13.0
		 */
		templateHelpers: {
			/**
			 * Escape HTML.
			 *
			 * @since 2.13.0
			 *
			 * @param {string} unsafe Unescaped HTML.
			 *
			 * @return {string} Escaped HTML.
			 */
			escapeHtml( unsafe ) {
				const div = document.createElement( 'div' );
				div.textContent = unsafe;
				return div.innerHTML;
			},
			/**
			 * Escape an HTML attribute.
			 *
			 * @since 2.13.0
			 *
			 * @param {string} unsafe Unescaped HTML attribute value.
			 *
			 * @return {string} Escaped HTML attribute value.
			 */
			escapeAttribute( unsafe ) {
				if ( typeof unsafe !== 'string' ) {
					unsafe = String( unsafe );
				}
				return unsafe
					.replace( /&/g, '&amp;' )
					.replace( /"/g, '&quot;' )
					.replace( /'/g, '&#x27;' )
					.replace( /</g, '&lt;' )
					.replace( />/g, '&gt;' );
			},
		},
	};

	return app;
}( document, window, jQuery ) );

WPFormsGeolocationMapField.init();
