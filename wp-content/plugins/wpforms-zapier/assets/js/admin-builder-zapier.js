/**
 * WPForms Zapier function.
 *
 * @since 1.5.0
 */
const WPFormsZapier = window.WPFormsZapier || ( function( document, window, $ ) {
	/**
	 * Whether zapier script is loaded.
	 *
	 * @since 1.5.0
	 *
	 * @type {boolean}
	 */
	let isWidgetLoaded = false;

	/**
	 * Public functions and properties.
	 *
	 * @since 1.5.0
	 *
	 * @type {Object}
	 */
	const app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.5.0
		 */
		init() {
			app.events();
		},

		/**
		 * Initialized once the DOM and Providers are fully loaded.
		 *
		 * @since 1.5.0
		 * @deprecated 1.7.0 Due to changes in WPForms v1.9.3.
		 */
		ready() {
			// eslint-disable-next-line no-console
			console.warn( 'WARNING! Function "WPFormsZapier.ready()" has been deprecated, please use the "WPFormsZapier.init()" function instead!' );

			app.events();
		},

		/**
		 * Process various events as a response to UI interactions.
		 *
		 * @since 1.5.0
		 */
		events() {
			$( document )
				.on( 'wpformsPanelSectionSwitch', function( e, section ) {
					if ( section !== 'zapier' || isWidgetLoaded ) {
						return;
					}

					app.loadZapierWidget();
				} )
				.on( 'change', '#wpforms-panel-field-settings-form_abandonment', app.handleIncludeAbandonedEntries );
		},

		/**
		 * Show the "Include Abandoned Entries" checkbox only when Abandoned Entries are enabled.
		 * The logic is described in #15932.
		 *
		 * @since 1.9.0
		 */
		handleIncludeAbandonedEntries() {
			// Check if Abandoned Entries are enabled.
			const abandonedEnabled = $( '#wpforms-panel-field-settings-form_abandonment' ).prop( 'checked' );

			// Get the wrapper for the "Include Abandoned Entries" checkbox.
			const $includeAbandonedEntriesWrap = $( '#wpforms-panel-field-zapier-include_abandoned_entries-wrap' ),
				$includeAbandonedEntriesToggle = $( '#wpforms-panel-field-zapier-include_abandoned_entries' );

			// Show/hide wrapper instead of setting the checkbox state.
			$includeAbandonedEntriesWrap.toggleClass( 'wpforms-hidden', ! abandonedEnabled );

			// Enable/disable the "Include Abandoned Entries" checkbox.
			$includeAbandonedEntriesToggle.prop( 'checked', abandonedEnabled );
		},

		/**
		 * Load Zapier widget.
		 *
		 * @since 1.5.0
		 */
		loadZapierWidget() {
			if ( isWidgetLoaded ) {
				return;
			}

			const $script = $( '#wpforms-zapier-builder-embed-js' );
			const $style = $( '#wpforms-zapier-builder-embed-css' );

			$script.attr( 'src', $script.data( 'src' ) );
			$style.attr( 'href', $style.data( 'href' ) );

			isWidgetLoaded = true;
		},
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPFormsZapier.init();
