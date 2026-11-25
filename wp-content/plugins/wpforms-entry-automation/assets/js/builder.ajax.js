/* global wpforms_builder */

// noinspection ES6ConvertVarToLetConst
var WPForms = window.WPForms || {}; // eslint-disable-line no-var
WPForms.Admin = WPForms.Admin || {};
WPForms.Admin.Builder = WPForms.Admin.Builder || {};

WPForms.Admin.Builder.Ajax = WPForms.Admin.Builder.Ajax || ( function( document, window, $ ) {
	/**
	 * Form builder object.
	 *
	 * @since 1.0.0
	 *
	 * @type {jQuery|HTMLElement|*}
	 */
	const $form = $( '#wpforms-builder-form' );

	/**
	 * Merge a custom AJAX data object with defaults.
	 *
	 * @since 1.0.0
	 *
	 * @param {string} entity Current entity slug.
	 * @param {Object} custom Ajax data object with custom settings.
	 *
	 * @return {Object} Ajax data.
	 */
	const mergeData = ( entity, custom ) => {
		const data = {
			id: $form.data( 'id' ),
			// eslint-disable-next-line camelcase
			revision_id: $form.data( 'revision' ),
			nonce: wpforms_builder.nonce,
			action: `wpforms_builder_ajax_${ entity }`,
		};

		$.extend( data, custom );

		return data;
	};

	return {
		/**
		 * Sends an AJAX request to interact with a server using the specified entity and custom parameters.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} entity       The entity object containing data related to the operation.
		 * @param {Object} custom       An object containing custom AJAX parameters such as `data` and other configurations.
		 * @param {Object} [options={}] An optional object containing additional configurations. Example keys include `lock` for managing lock state.
		 *
		 * @return {Promise} A jQuery jqXHR object representing the asynchronous request.
		 */
		request( entity, custom, options = {} ) {
			const params = {
				url: wpforms_builder.ajax_url,
				type: 'post',
				dataType: 'json',
				beforeSend() {
					options?.lock?.val?.( 1 );
					if ( options?.spinner ) {
						options?.spinner.removeClass( 'wpforms-hidden' );
					}
				},
			};

			custom.data = mergeData( entity, custom.data || {} );
			$.extend( params, custom );

			return new Promise( ( resolve, reject ) => {
				$.ajax( params )
					.done( resolve )
					.fail( function( jqXHR, textStatus, errorThrown ) { // eslint-disable-line no-unused-vars
						// Right now we are logging into the browser console. In the future, that might be better.
						console.error( 'entity:', entity ); // eslint-disable-line no-console
						console.error( jqXHR ); // eslint-disable-line no-console
						console.error( textStatus ); // eslint-disable-line no-console

						options?.lock?.val?.( 1 );
						reject( jqXHR );
					} )
					.always( function( dataOrjqXHR, textStatus, jqXHROrerrorThrown ) { // eslint-disable-line no-unused-vars
						if ( options?.spinner ) {
							options?.spinner.addClass( 'wpforms-hidden' );
						}

						if ( 'success' !== textStatus ) {
							return;
						}

						options?.lock?.val?.( 0 );
					} );
			} );
		},
	};
}( document, window, jQuery ) );
