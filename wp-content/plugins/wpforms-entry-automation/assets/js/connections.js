/* global wpforms_builder, wpf, wpforms_builder_providers, wpformsEntryAutomationBuilderVars */

// noinspection ES6ConvertVarToLetConst
var WPForms = window.WPForms || {}; // eslint-disable-line no-var
WPForms.Admin = WPForms.Admin || {};
WPForms.Admin.Builder = WPForms.Admin.Builder || {};

/**
 * Handles admin functionalities for managing form connections in the WPForms builder interface.
 *
 * This module is responsible for initializing, managing, and processing
 * various connection-related capabilities in the WPForms builder interface.
 *
 * @since 1.0.0
 */
WPForms.Admin.Builder.Connections = WPForms.Admin.Builder.Connections || ( function( document, $ ) {
	let Cache;
	let Templates;

	return class Connections {
		/**
		 * Constructor for initializing the instance with specific settings and values.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $holder                    The holder element to be used.
		 * @param {Object} [options={}]               Options object for customization.
		 * @param {string} [options.panel='settings'] The panel type to be used.
		 * @param {string} [options.entitySlug='']    The entity slug identifier.
		 */
		constructor( $holder, { panel = 'settings', entitySlug = '' } = {} ) {
			this.$holder = $holder;

			// We need to show him "Required fields empty" popup only once.
			this.isRequiredEmpty = false;
			this.panel = panel;
			this.entitySlug = entitySlug;
			this.isReady = false;

			this.selectors = {
				requiredFields: 'input.wpforms-required, select.wpforms-required, textarea.wpforms-required',
				requiredSelectField: 'select.wpforms-required',
				connection: '.wpforms-builder-entity-connection',
				connectionId: '.wpforms-builder-entity-connection-id',
				connectionList: '.wpforms-builder-entity-connections',
				addConnection: '.wpforms-builder-entity-connection-add',
				deleteConnection: '.wpforms-builder-entity-connection-delete',
				cloneConnection: '.wpforms-builder-entity-connection-clone',
				titleInput: '.wpforms-builder-entity-connection-title-input',
				emptyState: '.wpforms-entity-empty-state',
				connectionNameModal: '#wpforms-builder-entity-connection-name',
				exportTo: '.wpforms-entry-automation-export-to',
			};
		}

		/**
		 * Initializes the settings panel by checking the query string for a specific view
		 * and binding the ready event if the condition is met. Also binds actions to switch panels.
		 *
		 * @since 1.0.0
		 */
		init() {
			// We are requesting/loading a Settings panel.
			if ( wpf.getQueryString( 'view' ) === this.panel ) {
				$( this.ready.bind( this ) );
			}

			this.bindPanelSwitchAction();
		}

		/**
		 * Binds an event listener to handle actions when the panel is switched.
		 * The method listens for the 'wpformsPanelSwitched' event and invokes the `ready` method if the switched panel matches the current panel.
		 *
		 * @since 1.0.0
		 */
		bindPanelSwitchAction() {
			// We have switched panel.
			$( document ).on( 'wpformsPanelSwitched', ( event, panel ) => {
				if ( panel === this.panel ) {
					this.ready();
				}
			} );
		}

		/**
		 * Initializes the ready state of the object.
		 * Sets up the necessary conditions and binds required events if not already in the ready state.
		 *
		 * @since 1.0.0
		 */
		ready() {
			if ( this.isReady ) {
				return;
			}

			Cache = WPForms.Admin.Builder.Providers.cache;
			Templates = WPForms.Admin.Builder.Templates;

			this.bindEvents();
			this.initSortableConnections();
		}

		/**
		 * Binds event handlers to various DOM elements and actions.
		 * This includes form save notifications and click events for add, delete, and clone buttons.
		 *
		 * @since 1.0.0
		 */
		bindEvents() {
			$( document )
				.on( 'wpformsSaved', this.onFormSaved.bind( this ) )
				.on( 'wpformsBeforeSave', this.onBeforeFormSave.bind( this ) );

			// Add click event handler for the "Add New Connection" button.
			this.$holder.on( 'click', this.selectors.addConnection, ( e ) => {
				e.preventDefault();
				this.onAddConnection();
			} );

			// Add click event handler for the "Delete" button.
			this.$holder.on( 'click', this.selectors.deleteConnection, ( e ) => {
				e.preventDefault();
				this.onDeleteConnection( $( e.target ).closest( this.selectors.connection ) );
			} );

			// Add click event handler for the "Clone" button.
			this.$holder.on( 'click', this.selectors.cloneConnection, ( e ) => {
				e.preventDefault();
				this.onCloneConnection( $( e.target ).closest( this.selectors.connection ) );
			} );

			// Remove error class in required fields if a value is supplied.
			this.$holder.on( 'change', this.selectors.requiredSelectField, ( e ) => {
				this.validateRequiredField( e.target );
			} );

			// Validate required fields on input change.
			this.$holder.on( 'input', this.selectors.requiredFields, ( e ) => {
				this.validateRequiredField( e.target );
			} );
		}

		/**
		 * Initializes the sortable functionality for connections.
		 *
		 * @since 1.0.0
		 */
		initSortableConnections() {
			$( this.selectors.connectionList ).sortable( {
				items: `${ this.selectors.connection }:not(:first-child)`,
				handle: '.wpforms-builder-entity-connection-block-icon',
				start: ( event, ui ) => {
					ui.item.addClass( 'wpforms-builder-entity-connection-dragging' );
				},
				stop: ( event, ui ) => {
					ui.item.removeClass( 'wpforms-builder-entity-connection-dragging' );
				},
				update: ( event, ui ) => {
					this.enableQueue( ui.item );
				},
			} );
		}

		/**
		 * Enables the queue for a specific connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $connection The jQuery object representing the connection to enable the queue for.
		 */
		enableQueue( $connection ) {
			// Enable queue for the connection.
			$connection.find( '.wpforms-entry-automation-schedule-queue' ).removeClass( 'wpforms-hidden' );
			$connection.find( '.wpforms-entry-automation-schedule-queue-settings' ).removeClass( 'wpforms-hidden' );

			const connectionId = $connection.data( 'connection_id' );

			$( `#wpforms-entry-automation-schedule-queue-${ connectionId }` ).prop( 'checked', true ).trigger( 'change' );
		}

		/**
		 * Retrieves all connection elements based on the defined selector.
		 *
		 * @since 1.0.0
		 *
		 * @return {jQuery} A jQuery object containing all elements matching the connection selector.
		 */
		getConnections() {
			return this.$holder.find( this.selectors.connection );
		}

		/**
		 * Handles the form save event, processes condition logic, and applies it to the connected elements.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event The event object triggered by the form save action.
		 * @param {Object} data  The data object containing form submission details.
		 */
		onFormSaved( event, data ) {
			if ( ! Object.hasOwn( data, this.entitySlug ) ) {
				return;
			}

			const entity = data[ this.entitySlug ];

			if ( ! Object.hasOwn( entity, 'connections' ) ) {
				return;
			}

			// Clear HTML.
			this.getConnections().remove();
			// Save sanitized connections.
			Cache.set( this.entitySlug, 'connections', entity.connections );
			Cache.set( this.entitySlug, 'dropbox_folders', entity.dropbox_folders );
			// Render.
			this.generateConnections( data[ this.entitySlug ] );
		}

		/**
		 * Handles the event triggered before saving the form.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event The event object triggered by the form save action.
		 */
		onBeforeFormSave( event ) {
			this.isRequiredEmpty = false;
			this.isDaysEmpty = false;
			this.ftpFailed = false;
			this.requiredThing = '';

			// By default - validate required fields.
			this.getConnections().each( ( _, connection ) => this.processConditionOnFormSave( connection ) );

			// Check if there are any required fields empty.
			if ( this.isRequiredEmpty || this.requiredThing || this.isDaysEmpty || this.ftpFailed ) {
				event.preventDefault();
			}

			this.notifyUser();
		}

		/**
		 * Notifies the user about empty required fields or FTP connection issues.
		 *
		 * @since 1.0.0
		 */
		notifyUser() {
			if ( this.isRequiredEmpty || this.requiredThing || this.isDaysEmpty ) {
				// Notify user.
				this.notifyUserAboutEmptyFields();
			}

			if ( this.ftpFailed ) {
				// Notify user about FTP connection requirement.
				this.notifyUserAboutFTPConnection();
			}
		}

		/**
		 * Validates if a required field is filled. Highlights the field with an error class if empty.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} field The field element to be validated.
		 */
		validateRequiredField( field ) {
			const $this = $( field );

			// If field is Choices.js element, validate it differently.
			if ( 'undefined' !== typeof $this.data( 'choicesjs' ) ) {
				this.validateStatusField( $this );

				return;
			}

			// Native hidden inputs (e.g. the Google Drive folder ID) are never `:visible`
			// regardless of context, so their required state is judged by class alone.
			const isNativelyHidden = $this.attr( 'type' ) === 'hidden';

			if ( ! $this.is( ':visible' ) && ! isNativelyHidden && ! $this.hasClass( 'wpforms-smart-tags-widget-original' ) ) {
				return;
			}

			const value = $this.val();

			if ( ! _.isEmpty( value ) ) {
				$this.removeClass( 'wpforms-error' );

				return;
			}

			$this.addClass( 'wpforms-error' );

			this.isRequiredEmpty = true;
		}

		/**
		 * Validates the status field for Choices.js elements.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $field The Choices.js field element to validate.
		 */
		validateStatusField( $field ) {
			const value = $field.data( 'choicesjs' ).getValue( true );

			const $element = $field.closest( '.choices__inner' );

			if ( ! _.isEmpty( value ) ) {
				$element.removeClass( 'wpforms-error' );

				return;
			}

			$element.addClass( 'wpforms-error' );

			this.isRequiredEmpty = true;
		}

		/**
		 * Gets the appropriate element to highlight for error display.
		 * For smart tags widgets, returns the widget container; otherwise returns the field itself.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0
		 *
		 * @param {Object} $field The field element to check for error highlighting.
		 *
		 * @return {Object} The element that should receive the error highlight styling.
		 */
		getElementForErrorHighlight( $field ) {
			// eslint-disable-next-line no-console
			console.warn( 'WARNING! Function "getElementForErrorHighlight" is deprecated.' );

			return $field;
		}

		/**
		 * Processes specific conditions when saving a form by handling required fields
		 * and updating the summary text.
		 *
		 * @param {HTMLElement} connection The connection object used for interacting with the system.
		 */
		processConditionOnFormSave( connection ) { // eslint-disable-line no-unused-vars
			this.processRequiredFields( connection );
			this.processEntryInformationChange( connection );
		}

		/**
		 * Processes changes in entry information for a given connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} connection The connection element to be processed.
		 */
		processEntryInformationChange( connection ) {
			const $connection = $( connection );
			const connectionData = this.getConnectionData( $connection );

			// Skip If File Exists parameter is not Add Entries to file.
			if ( connectionData.connection?.on_duplicate !== 'add' ) {
				return;
			}

			const connectionId = this.getConnectionId( $connection );
			const additionalField = Cache.get( this.entitySlug, `connection_${ connectionId }_additional_field` );
			const formField = Cache.get( this.entitySlug, `connection_${ connectionId }_form_field` );

			let isChanged = false;

			// Check if the additional field have changed.
			if ( JSON.stringify( connectionData.connection.additional_field ) !== JSON.stringify( additionalField ) ) {
				isChanged = true;
			}

			// Check if the form field have changed.
			if ( JSON.stringify( connectionData.connection.form_field ) !== JSON.stringify( formField ) ) {
				isChanged = true;
			}

			if ( ! isChanged ) {
				return;
			}

			$.alert( {
				title: wpforms_builder.heads_up,
				content: wpformsEntryAutomationBuilderVars.columnsChanged,
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
		}

		/**
		 * Processes and validates the required fields in the given connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} connection The DOM element or container within which the required fields are checked.
		 */
		processRequiredFields( connection ) {
			// Do actually require fields checking.
			$( connection ).find( this.selectors.requiredFields ).each(
				( _, field ) => this.validateRequiredField( field )
			);

			const connectionId = $( connection ).data( 'connection_id' );

			if ( ! this.isDaysChecked( connectionId, connection ) ) {
				this.requiredThing = 'days_required';
			}

			this.validateFtpConnection( connectionId, connection );
		}

		/**
		 * Validates the FTP connection for a given connection ID.
		 *
		 * @since 1.0.0
		 *
		 * @param {string}      connectionId The ID of the connection to validate.
		 * @param {HTMLElement} connection   The connection element to validate.
		 */
		validateFtpConnection( connectionId, connection ) {
			if (
				$( this.selectors.exportTo, $( connection ) ).val() === 'ftp' &&
				$( `.wpforms-entry-automation-ftp-connection-remove-${ connectionId }` ).hasClass( 'wpforms-hidden' ) &&
				! this.isRequiredEmpty
			) {
				this.ftpFailed = true;
			}
		}

		/**
		 * Checks if any days are selected for a given connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {string}      connectionId The ID of the connection to check.
		 * @param {HTMLElement} connection   The connection element to check for selected days.
		 *
		 * @return {boolean} Returns true if at least one day is checked, false otherwise.
		 */
		isDaysChecked( connectionId, connection ) {
			// If the days or the whole section with dates, frequency, days and time is hidden, return true to avoid validation.
			if ( $( connection ).find( '.wpforms-entry-automation-schedule-days, .wpforms-entry-automation-schedule-settings' ).hasClass( 'wpforms-hidden' ) ) {
				return true;
			}

			const days = $( `input[name^="settings[entry_automation][${ connectionId }][schedule][days]"]:checked` );

			return days.length > 0;
		}

		/**
		 * Notifies the user about empty required fields in a form by showing an alert message.
		 *
		 * @since 1.0.0
		 */
		notifyUserAboutEmptyFields() {
			let msg = wpforms_builder.provider_required_flds;

			if ( this.isDaysEmpty ) {
				msg = wpformsEntryAutomationBuilderVars.taskModal.days_required;
			}

			if ( this.requiredThing ) {
				msg = wpformsEntryAutomationBuilderVars.taskModal[ this.requiredThing ];
			}

			$.alert( {
				title: wpforms_builder.heads_up,
				content: msg.replace( '{provider}', '<strong>' + this.getEntityTitle() + '</strong>' ),
				icon: 'fa fa-exclamation-circle',
				type: 'red',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		}

		/**
		 * Notifies the user about the requirement of an FTP connection.
		 *
		 * @since 1.0.0
		 */
		notifyUserAboutFTPConnection() {
			$.alert( {
				title: wpformsEntryAutomationBuilderVars.ftp.required.title,
				content: wpformsEntryAutomationBuilderVars.ftp.required.content,
				icon: 'fa fa-exclamation-circle',
				type: 'red',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		}

		/**
		 * Retrieves and returns the text content of the entity title, after removing any buttons within the title area.
		 *
		 * @since 1.0.0
		 *
		 * @return {string} The trimmed text content of the entity title.
		 */
		getEntityTitle() {
			const $titleArea = this.$holder.find( '.wpforms-builder-entity-title' ).clone();
			$titleArea.find( 'button' ).remove();

			return $titleArea.text().trim();
		}

		/**
		 * Handles the addition of a new connection.
		 *
		 * @since 1.0.0
		 */
		onAddConnection() {
			this.addConnection().then( ( [ name ] ) => {
				this.connectionCreate( name );
				this.toggleEmptyState();
			} );
		}

		/**
		 * Handles the deletion of a connection and subsequent operations after deletion.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $connection The connection object to be deleted.
		 */
		onDeleteConnection( $connection ) {
			this.deleteConnection( $connection )
				.then( ( [ connectionId ] ) => this.connectionDeleted( connectionId ) )
				.then( () => this.toggleEmptyState() );
		}

		/**
		 * Handles the deletion of a connection by updating the cache and managing the UI state accordingly.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} connectionId The ID of the connection to be deleted.
		 */
		connectionDeleted( connectionId ) {
			Cache.deleteFrom( this.entitySlug, 'connections', connectionId );
		}

		/**
		 * Handle cloning of an existing connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $connection The connection element to be cloned.
		 */
		onCloneConnection( $connection ) {
			// Extract connection name.
			const name = $connection.find( this.selectors.titleInput ).val() || '';
			const currentValues = this.getCurrentValues( $connection );
			const connectionId = currentValues.id;

			delete currentValues.id;
			delete currentValues.name;

			currentValues.clonedBy = connectionId;

			// Call connectionCreate with the cloned name and values.
			this.connectionCreate( name + ' cloned', currentValues );
		}

		/**
		 * Caches the current values of the specified connection.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $connection The connection object to cache.
		 */
		cacheCurrentValues( $connection ) {
			const connectionId = this.getConnectionId( $connection );
			const connection = this.getCurrentValues( $connection );

			Cache.addTo( this.entitySlug, 'connections', connectionId, connection );
		}

		/**
		 * Extracts the current field values from a provided field container element
		 * and returns them in a structured JSON format.
		 * Processes nested field names to properly map their hierarchy in the resulting object.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $connection A jQuery object containing the form or field container to extract values from.
		 *
		 * @return {Object} An object representing the current connection values with nested keys structured accordingly.
		 */
		getCurrentValues( $connection ) {
			const connectionId = this.getConnectionId( $connection );
			// Extract all field values into JSON format using serializeArray.
			const currentValues = {};
			const formData = $( '#wpforms-builder-form' ).serializeArray();

			formData.forEach( ( { name: fieldName, value } ) => {
				if ( ! fieldName ||
					! fieldName.includes( `[${ this.entitySlug }][${ connectionId }]` )
				) {
					return;
				}

				this.handleNestedFieldNames( fieldName, value, currentValues );
			} );

			return currentValues;
		}

		/**
		 * Handle nested field names in form data.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} fieldName     The field name, potentially containing brackets for nesting.
		 * @param {string} value         The field value to be assigned.
		 * @param {Object} currentValues The object where values are collected.
		 */
		handleNestedFieldNames( fieldName, value, currentValues ) { // eslint-disable-line complexity
			// Handle regular field names (no brackets).
			if ( ! fieldName.includes( '[' ) ) {
				currentValues[ fieldName ] = value;
				return;
			}

			const parts = fieldName.match( /([^[\]]+)|\[([^[\]]*)\]/g ) || [];

			// Extract the root name and all keys.
			const keys = [];
			parts.forEach( ( part ) => {
				if ( part.startsWith( '[' ) ) {
					// This is a key inside brackets, remove the brackets.
					keys.push( part.slice( 1, -1 ) );
				} else {
					// This is the root name.
					keys.unshift( part );
				}
			} );

			// Check if this is the pattern: settings[entitySlug][id][field_name...].
			if ( keys[ 0 ] === 'settings' && keys.length >= 4 && keys[ 1 ] === this.entitySlug ) {
				// Skip the settings, entitySlug, and ID parts.
				const actualKeys = keys.slice( 3 );

				// Process the remaining keys.
				this.buildNestedStructure( actualKeys, value, currentValues );

				// Convert objects with numeric keys to arrays.
				this.convertObjectsToArrays( currentValues );
				return;
			}

			// Handle all other nested field patterns.
			this.buildNestedStructure( keys, value, currentValues );

			// Convert objects with numeric keys to arrays.
			this.convertObjectsToArrays( currentValues );
		}

		/**
		 * Recursively builds a nested object structure from an array of keys.
		 *
		 * @since 1.0.0
		 *
		 * @param {Array}  keys          Array of keys defining the path in the object.
		 * @param {*}      value         Value to set at the final path.
		 * @param {Object} currentValues Object where the nested structure is built.
		 */
		buildNestedStructure( keys, value, currentValues ) {
			if ( keys.length === 1 ) {
				// We've reached the final key, set the value.
				currentValues[ keys[ 0 ] ] = value;
				return;
			}

			const currentKey = keys[ 0 ];

			// If the current level doesn't exist, create it.
			if ( currentValues[ currentKey ] === undefined ) {
				currentValues[ currentKey ] = {};
			}

			// Recursively process the next level.
			this.buildNestedStructure( keys.slice( 1 ), value, currentValues[ currentKey ] );
		}

		/**
		 * Recursively converts objects with all numeric keys to arrays.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} obj The object to check and potentially convert.
		 *
		 * @return {Object|Array} The original object or a converted array.
		 */
		convertObjectsToArrays( obj ) {
			// Base case: if not an object or is null, return as is.
			if ( typeof obj !== 'object' || obj === null ) {
				return obj;
			}

			// Process each property recursively.
			Object.keys( obj ).forEach( ( key ) => {
				if ( typeof obj[ key ] === 'object' && obj[ key ] !== null ) {
					obj[ key ] = this.convertObjectsToArrays( obj[ key ] );
				}
			} );

			// Check if this object should be converted to an array.
			if ( this.shouldBeArray( obj ) ) {
				return Object.values( obj );
			}

			return obj;
		}

		/**
		 * Determines if an object should be converted to an array.
		 * An object should be an array if all its keys are numeric indices.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} obj The object to check.
		 *
		 * @return {boolean} True if the object should be an array, false otherwise.
		 */
		shouldBeArray( obj ) {
			const keys = Object.keys( obj );

			// If empty object or not an object, it's not a candidate for array conversion.
			if ( keys.length === 0 || typeof obj !== 'object' || obj === null ) {
				return false;
			}

			// Check if all keys are numeric.
			return keys.every( ( key ) => key === '' || /^\d+$/.test( key ) );
		}

		/**
		 * Creates a new connection with the specified name.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} name            The name of the connection to be created.
		 * @param {Object} [currentValues] Optional. Values to pre-populate in the connection.
		 */
		connectionCreate( name, currentValues = {} ) {} // eslint-disable-line no-unused-vars

		/**
		 * Get default connection object.
		 *
		 * @since 1.0.0
		 * @param {string} name            The name of the connection to be created.
		 * @param {Object} [currentValues] Optional. Values to pre-populate in the connection.
		 */
		getDefaultConnection( name, currentValues = {} ) {} // eslint-disable-line no-unused-vars

		/**
		 * Displays a confirmation dialog for adding a new connection.
		 * Prompts the user to enter a connection name and performs validation.
		 * The resolved output will contain the entered connection name.
		 *
		 * @since 1.0.0
		 *
		 * @return {Promise<string[]>} A promise that resolves with an array containing the connection name entered by the user.
		 */
		addConnection() {
			const defaultValue = this.getDefaultConnectionName();
			const self = this;

			return new Promise( ( resolve ) => {
				$.confirm( {
					title: false,
					content: wpformsEntryAutomationBuilderVars.taskModal.prompt_connection +
						'<input ' + ( defaultValue === '' ? ' autofocus=""' : '' ) + 'type="text" id="wpforms-builder-entity-connection-name" placeholder="' + wpforms_builder_providers.prompt_placeholder + '" value="' + defaultValue + '">' +
						'<p class="error">' + wpformsEntryAutomationBuilderVars.taskModal.error_name + '</p>',
					icon: 'fa fa-info-circle',
					type: 'blue',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action() {
								const name = this.$content.find( '#wpforms-builder-entity-connection-name' ).val().trim(),
									error = this.$content.find( '.error' );

								if ( name === '' ) {
									error.show();
									return false;
								}

								resolve( [ name ] );
							},
						},
						cancel: {
							text: wpforms_builder.cancel,
						},
					},
					onContentReady() {
						// Update autofocus to be at the end of string when the default value is set.
						const input = $( self.selectors.connectionNameModal )[ 0 ];

						if ( input && defaultValue ) {
							input.setSelectionRange( defaultValue.length, defaultValue.length );
							input.focus();
						}
					},
				} );
			} );
		}

		/**
		 * Deletes a specified connection with confirmation dialog.
		 *
		 * Displays a confirmation dialog before deleting the connection. Upon confirmation,
		 * executes the provided callback function and removes the connection element.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery}   $connection             The connection element to be deleted.
		 * @param {Function} [beforeDelete=() => {}] A callback function executed before the connection is removed.
		 *
		 * @return {Promise<void>} A promise that resolves when the connection has been successfully deleted.
		 */
		deleteConnection( $connection, beforeDelete = () => {} ) {
			let content = wpformsEntryAutomationBuilderVars.taskModal.confirm_connection;
			let type = 'orange';

			// Change the message if the connection is the first in the queue.
			if ( this.isQueueFirst( $connection ) ) {
				content = wpformsEntryAutomationBuilderVars.taskModal.queue_first;
				type = 'red';
			}

			return new Promise( ( resolve ) => {
				$.confirm( {
					title: false,
					content,
					icon: 'fa fa-exclamation-circle',
					type,
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action() {
								beforeDelete();

								const connectionId = $connection.data( 'connection_id' );

								$connection.fadeOut( 'fast', function() {
									$( this ).remove();
									resolve( [ connectionId ] );
								} );
							},
						},
						cancel: {
							text: wpforms_builder.cancel,
						},
					},
				} );
			} );
		}

		/**
		 * Check if the connection is the first in the queue.
		 *
		 * @since 1.0.0
		 *
		 * @param {jQuery} $connection The connection element to check.
		 *
		 * @return {boolean} True if the connection is a parent of another queue connection, false otherwise.
		 */
		isQueueFirst( $connection ) {
			const connectionId = this.getConnectionId( $connection );
			const $toggle = $( `#wpforms-entry-automation-schedule-queue-${ connectionId }` );

			const numberOfConnections = this.getConnections().length;

			return ! $toggle.is( ':checked' ) && numberOfConnections > 1;
		}

		/**
		 * Get the default name for a new connection.
		 *
		 * @since 1.0.0
		 *
		 * @return {string} Returns the default name for a new connection.
		 */
		getDefaultConnectionName() {
			const numberOfConnections = this.getConnections().length;
			const defaultName = `${ this.getEntityTitle() } ${ wpformsEntryAutomationBuilderVars.taskModal.task_label }`;

			return ( numberOfConnections < 1 ? defaultName : '' ).trim();
		}

		/**
		 * Generates and processes connections based on the provided response data.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} responseData The response data containing connections to be processed.
		 *
		 * @return {Object} The updated response data.
		 */
		generateConnections( responseData ) {
			if ( _.isEmpty( responseData.connections ) ) {
				return responseData;
			}

			for ( const connectionID in responseData.connections ) {
				this.connectionGenerate( {
					connection: responseData.connections[ connectionID ],
				} );

				this.$holder.closest( '.wpforms-panel' ).trigger( 'connectionGeneralSettingsRendered', [ this.entitySlug, connectionID ] );
			}

			this.toggleEmptyState();

			// Show the Entry Automation Dashboard notice.
			$( '#wpforms-entry-automation-dashboard-notice' ).removeClass( 'wpforms-hidden' );

			return responseData;
		}

		/**
		 * Generates a connection based on the provided connection data.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} connectionData The data required to generate the connection.
		 */
		connectionGenerate( connectionData ) { // eslint-disable-line no-unused-vars
			// Implement.
		}

		/**
		 * Retrieves the connection ID associated with the provided element.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $element The jQuery-wrapped DOM element from which the connection ID is to be retrieved.
		 *
		 * @return {string} The connection ID if found, otherwise undefined.
		 */
		getConnectionId( $element ) {
			const $connection = $element.closest( this.selectors.connection );

			return $connection.data( 'connection_id' );
		}

		/**
		 * Retrieves connection data for a given element.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} $element The element to retrieve the connection data for.
		 *
		 * @return {Object} An object containing the connection data.
		 */
		getConnectionData( $element ) {
			const connectionId = this.getConnectionId( $element );

			return {
				connection: Cache.getById( this.entitySlug, 'connections', connectionId ),
			};
		}

		/**
		 * Toggles the visibility of the empty state element based on the number of connections.
		 *
		 * @since 1.0.0
		 */
		toggleEmptyState() {
			const connections = this.getConnections();

			if ( connections.length > 0 ) {
				this.$holder.find( this.selectors.emptyState ).hide();

				return;
			}

			this.$holder.find( this.selectors.emptyState ).show();
		}

		/**
		 * Retrieves the error message element for the specified provider.
		 *
		 * @since 1.0.0
		 *
		 * @return {Object} The jQuery object containing the error message element for the provider.
		 */
		getEntityError() {
			return $( `#wpforms-${ this.entitySlug }-builder-provider-error` );
		}

		/**
		 * Displays an error message for the specified provider in the UI.
		 * This method checks if an error already exists for the provider and displays it.
		 * If no error exists, it dynamically creates and displays a new error template.
		 *
		 * @since 1.0.0
		 */
		showError() {
			const $error = this.getEntityError();

			if ( $error.length ) {
				$error.show();

				return;
			}

			const templateId = `wpforms-${ this.entitySlug }-builder-content-connection-default-error`;
			const $connections = this.$holder.find( '.wpforms-builder-entity-connections' );

			// Register and prepend template.
			Templates.add( [ templateId ] );
			$connections.prepend( Templates.get( templateId )() );

			// Show error.
			this.getEntityError().show();
		}
	};
}( document, jQuery ) );
