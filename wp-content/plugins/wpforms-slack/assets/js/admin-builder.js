/* global WPForms, Choices, wpf, wpforms_builder, chrono, wpforms_slack_builder */
/**
 * WPForms Providers Builder Slack module.
 *
 * @since 1.0.0
 */
WPForms.Admin.Builder.Providers.Slack = WPForms.Admin.Builder.Providers.Slack || ( function( document, window, $ ) {
	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	const app = {

		/**
		 * CSS selectors.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		selectors: {
			providersPanel: '#wpforms-panel-providers',
			connection: '.wpforms-panel-content-section-slack .wpforms-builder-provider-connection',
			actionData: '.wpforms-builder-slack-provider-actions-data',
			connections: '.wpforms-panel-content-section-slack .wpforms-builder-provider-connection',
			accountField: '.js-wpforms-builder-slack-provider-connection-account',
			actionField: '.js-wpforms-builder-slack-provider-connection-action',
			channelsField: '.wpforms-builder-slack-provider-connection-channels',
			usersField: '.wpforms-builder-slack-provider-connection-users',
			timeField: '.wpforms-builder-slack-provider-connection-time',
			choiceJS: '.choicesjs-select',
		},

		/**
		 * jQuery elements.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		$elements: {
			$builder: $( '#wpforms-builder' ),
			$panel: $( '#slack-provider' ),
			$connections: $( '#slack-provider .wpforms-builder-provider-connections' ),
		},

		/**
		 * Current provider slug.
		 *
		 * @since 1.0.0
		 *
		 * @type {string}
		 */
		provider: 'slack',

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Providers object,
		 * that handles the parent all-providers functionality.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		Providers: {},

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Templates object,
		 * that handles all the template management.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		Templates: {},

		/**
		 * This is a shortcut to the WPForms.Admin.Builder.Providers.cache object,
		 * that handles all the cache management.
		 *
		 * @since 1.0.0
		 *
		 * @type {Object}
		 */
		Cache: {},

		/**
		 * This is a flag for ready state.
		 *
		 * @since 1.0.0
		 *
		 * @type {boolean}
		 */
		isReady: false,

		/**
		 * Start the engine.
		 *
		 * Run initialization on a providers panel only.
		 *
		 * @since 1.0.0
		 */
		init() {
			// We are requesting/loading a Providers panel.
			if ( wpf.getQueryString( 'view' ) === 'providers' ) {
				$( app.selectors.providersPanel ).on( 'WPForms.Admin.Builder.Providers.ready', app.ready );
			}

			// We have switched to a Providers panel.
			$( document ).on( 'wpformsPanelSwitched', function( event, panel ) {
				if ( panel === 'providers' ) {
					app.ready();
				}
			} );
		},

		/**
		 * Initialized once the DOM and Providers are fully loaded.
		 *
		 * @since 1.0.0
		 */
		ready() {
			if ( app.isReady ) {
				return;
			}

			// Done by reference, so we are not doubling memory usage.
			app.Providers = WPForms.Admin.Builder.Providers;
			app.Templates = WPForms.Admin.Builder.Templates;
			app.Cache = app.Providers.cache;

			// Register custom Underscore.js templates.
			app.Templates.add( [
				'wpforms-slack-builder-content-connection',
				'wpforms-slack-builder-content-connection-error',
				'wpforms-slack-builder-content-connection-select-field',
				'wpforms-slack-builder-content-connection-text-field',
				'wpforms-slack-builder-content-connection-textarea-field',
				'wpforms-slack-builder-content-connection-conditionals',
			] );

			// Events registration.
			app.bindUIActions();
			app.bindTriggers();
			app.processInitial();

			// Save a flag for ready state.
			app.isReady = true;
		},

		/**
		 * Process various events as a response to UI interactions.
		 *
		 * @since 1.0.0
		 */
		bindUIActions() {
			app.$elements.$panel
				.on( 'connectionCreate', app.connection.create )
				.on( 'connectionDelete', app.connection.delete )
				.on( 'change', app.selectors.accountField, app.ui.accountField.change )
				.on( 'change', app.selectors.actionField, app.ui.action.change );

			app.$elements.$builder.on( 'wpformsBeforeSave', app.ui.validation.validateTime );
			app.$elements.$builder.on( 'wpformsSaved', app.connection.refresh );
		},

		/**
		 * Fire certain events on certain actions, specific for related connections.
		 * These are not directly caused by user manipulations.
		 *
		 * @since 1.0.0
		 */
		bindTriggers() {
			app.$elements.$connections.on( 'connectionsDataLoaded', function( event, data ) {
				if ( _.isEmpty( data.connections ) ) {
					return;
				}

				for ( const connectionId in data.connections ) {
					app.connection.generate( {
						connection: data.connections[ connectionId ],
						conditional: data.conditionals[ connectionId ],
					} );
				}
			} );

			app.$elements.$connections.on( 'connectionGenerated', function( event, data ) {
				const $connection = app.connection.getById( data.connection.id );

				if ( _.has( data.connection, 'isNew' ) && data.connection.isNew ) {
					// Run replacing temporary connection ID if it's a new connection.
					app.connection.replaceIds( data.connection.id, $connection );
					return;
				}

				$( app.selectors.actionField, $connection ).trigger( 'change', [ $connection ] );
			} );
		},

		/**
		 * Compile template with data if any and display them on a page.
		 *
		 * @since 1.0.0
		 */
		processInitial() {
			app.$elements.$connections.prepend( app.tmpl.commonsHTML() );
			app.connection.dataLoad();
		},

		/**
		 * Connection property.
		 *
		 * @since 1.0.0
		 */
		connection: {

			/**
			 * Sometimes we might need to a get a connection DOM element by its ID.
			 *
			 * @since 1.0.0
			 *
			 * @param {string} connectionId Connection ID to search for a DOM element by.
			 *
			 * @return {jQuery} jQuery object for connection.
			 */
			getById( connectionId ) {
				return app.$elements.$connections.find( '.wpforms-builder-provider-connection[data-connection_id="' + connectionId + '"]' );
			},

			/**
			 * Sometimes in DOM we might have placeholders or temporary connection IDs.
			 * We need to replace them with actual values.
			 *
			 * @since 1.0.0
			 *
			 * @param {string} connectionId New connection ID to replace to.
			 * @param {Object} $connection  jQuery DOM connection element.
			 */
			replaceIds( connectionId, $connection ) {
				// Replace old temporary %connection_id% from PHP code with the new one.
				$connection.find( 'input, select, label' ).each( function() {
					const $this = $( this );

					if ( $this.attr( 'name' ) ) {
						$this.attr( 'name', $this.attr( 'name' ).replace( /%connection_id%/gi, connectionId ) );
					}

					if ( $this.attr( 'id' ) ) {
						$this.attr( 'id', $this.attr( 'id' ).replace( /%connection_id%/gi, connectionId ) );
					}

					if ( $this.attr( 'for' ) ) {
						$this.attr( 'for', $this.attr( 'for' ).replace( /%connection_id%/gi, connectionId ) );
					}

					if ( $this.attr( 'data-name' ) ) {
						$this.attr( 'data-name', $this.attr( 'data-name' ).replace( /%connection_id%/gi, connectionId ) );
					}
				} );
			},

			/**
			 * Create a connection using the user entered name.
			 *
			 * @since 1.0.0
			 *
			 * @param {Object} event Event object.
			 * @param {string} name  Connection name.
			 */
			create( event, name ) {
				const connectionId = new Date().getTime().toString( 16 ),
					connection = {
						id: connectionId,
						name,
						isNew: true,
					};

				app.Cache.addTo( app.provider, 'connections', connectionId, connection );

				app.connection.generate( {
					connection,
				} );
			},

			/**
			 * Connection is deleted - delete a cache as well.
			 *
			 * @since 1.0.0
			 *
			 * @param {Object} event       Event object.
			 * @param {Object} $connection jQuery DOM element for a connection.
			 */
			delete( event, $connection ) {
				const $eHolder = app.Providers.getProviderHolder( app.provider );

				if ( ! $connection.closest( $eHolder ).length ) {
					return;
				}

				const connectionId = $connection.data( 'connection_id' );

				if ( _.isString( connectionId ) ) {
					app.Cache.deleteFrom( app.provider, 'connections', connectionId );
				}
			},

			/**
			 * Get the template and data for a connection and process it.
			 *
			 * @since 1.0.0
			 *
			 * @param {Object} data Connection data.
			 *
			 * @return {void}
			 */
			generate( data ) {
				const accounts = app.Cache.get( app.provider, 'accounts' ),
					actions = app.Cache.get( app.provider, 'actions' );

				/*
				 * We may or may not receive accounts previously.
				 * If yes - render instantly, if no - request them via AJAX.
				 */
				if ( ! _.isEmpty( accounts ) && app.account.isAccountExists( data.connection.account_id, accounts ) ) {
					return app.connection.renderConnections( accounts, actions, data );
				}

				app.ui.accountForm.dataLoad( data );
			},

			/**
			 * Render connections.
			 *
			 * @since 1.0.0
			 *
			 * @param {Object} accounts List of accounts.
			 * @param {Object} actions  List of actions.
			 * @param {Object} data     Connection data.
			 */
			renderConnections( accounts, actions, data ) {
				if ( ! app.account.isAccountExists( data.connection.account_id, accounts ) ) {
					return;
				}

				const tmplConnection = app.Templates.get( `wpforms-${ app.provider }-builder-content-connection` ),
					tmplConditional = app.Templates.get( `wpforms-${ app.provider }-builder-content-connection-conditionals` ),
					conditional = _.has( data.connection, 'isNew' ) && data.connection.isNew ? tmplConditional() : data.conditional;

				app.$elements.$connections
					.prepend(
						tmplConnection( {
							accounts,
							actions,
							connection: data.connection,
							conditional,
							provider: app.provider,
						} ) );

				// When we are done adding a new connection with its accounts - trigger next steps.
				app.$elements.$connections.trigger( 'connectionGenerated', [ data ] );
			},

			/**
			 * Fire AJAX-request to retrieve the list of all saved connections.
			 *
			 * @since 1.0.0
			 */
			dataLoad() {
				app
					.Providers.ajax
					.request( app.provider, {
						data: {
							task: 'connections_get',
						},
					} )
					.done( function( response ) {
						if (
							! response.success ||
							! _.has( response.data, 'connections' )
						) {
							return;
						}

						[
							'connections',
							'conditionals',
							'actions',
							'actions_fields',
							'accounts',
							'users',
							'channels',
							'message',
						].forEach( ( dataType ) => {
							app.Cache.set( app.provider, dataType, jQuery.extend( {}, response.data[ dataType ] ) );
						} );

						app.$elements.$connections.trigger( 'connectionsDataLoaded', [ response.data ] );
					} );
			},

			/**
			 * Refresh connections data after saving progress.
			 *
			 * @since 1.0.0
			 *
			 * @param {Event}  e        Event.
			 * @param {Object} response Ajax response.
			 */
			refresh( e, response ) {
				if ( ! Object.hasOwn( response, app.provider ) ) {
					return;
				}

				const data = response[ app.provider ];
				[
					'connections',
					'accounts',
					'message',
				].forEach( ( dataType ) => {
					if ( ! _.isEmpty( data[ dataType ] ) ) {
						app.Cache.set( app.provider, dataType, jQuery.extend( {}, data[ dataType ] ) );
					}
				} );

				app.$elements.$connections.html( '' );

				for ( const connectionId in data.connections ) {
					app.connection.generate( {
						connection: data.connections[ connectionId ],
						conditional: data.conditionals[ connectionId ],
					} );
				}
			},
		},

		/**
		 * Account property.
		 *
		 * @since 1.0.0
		 */
		account: {

			/**
			 * Check if a provided account is listed inside an accounts list.
			 *
			 * @since 1.0.0
			 *
			 * @param {string} connectionAccID Connection account ID to check.
			 * @param {Object} accounts        Array of objects, usually received from API.
			 *
			 * @return {boolean} True if account exists.
			 */
			isAccountExists( connectionAccID, accounts ) {
				if ( _.isEmpty( accounts ) ) {
					return false;
				}

				// New connections that have not been saved don't have the account ID yet.
				if ( _.isEmpty( connectionAccID ) ) {
					return true;
				}

				return _.has( accounts, connectionAccID );
			},
		},

		/**
		 * All methods that modify the UI of a page.
		 *
		 * @since 1.0.0
		 */
		ui: {

			/**
			 * Account form methods.
			 *
			 * @since 1.0.0
			 */
			accountForm: {
				/**
				 * Retrieve a list of required new accounts data for generating connections.
				 *
				 * @since 1.0.0
				 *
				 * @param {Object} data Connection data.
				 */
				dataLoad( data ) {
					const actions = app.Cache.get( app.provider, 'actions' );

					app
						.Providers.ajax
						.request( app.provider, {
							data: {
								task: 'accounts_get',
							},
						} )
						.done( function( response ) {
							[
								'accounts',
								'users',
								'channels',
							].forEach( ( dataType ) => {
								if ( ! _.isEmpty( response.data[ dataType ] ) ) {
									app.Cache.set( app.provider, dataType, jQuery.extend( {}, response.data[ dataType ] ) );
								}
							} );

							app.connection.renderConnections( response.data.accounts, actions, data );
						} );
				},
			},

			/**
			 * Account field methods.
			 *
			 * @since 1.0.0
			 */
			accountField: {

				/**
				 * Callback-function on change event.
				 *
				 * @since 1.0.0
				 */
				change() {
					const $this = $( this ),
						$connection = $this.closest( app.selectors.connection ),
						$actionName = $( app.selectors.actionField, $connection );

					$( app.selectors.actionData, $connection ).html( '' );
					$actionName.prop( 'selectedIndex', 0 );

					// If account is empty.
					if ( _.isEmpty( $this.val() ) ) {
						// Block `Action` select box.
						$actionName.prop( 'disabled', true );

						return;
					}

					// Unblock `Action` select box.
					$actionName.prop( 'disabled', false );
					$this.removeClass( 'wpforms-error' );
				},
			},

			/**
			 * Action methods.
			 *
			 * @since 1.0.0
			 */
			action: {

				/**
				 * Callback-function on change event.
				 *
				 * @since 1.0.0
				 */
				change() {
					const $this = $( this ),
						$connection = $this.closest( app.selectors.connection ),
						$account = $( app.selectors.accountField, $connection ),
						$action = $( app.selectors.actionField, $connection );

					$this.removeClass( 'wpforms-error' );

					app.ui.action.render( {
						action: 'action',
						target: $this,
						/* eslint-disable camelcase */
						account_id: $account.val(),
						action_name: $action.val(),
						connection_id: $connection.data( 'connection_id' ),
						/* eslint-enable camelcase */
					} );
				},

				/**
				 * Render HTML.
				 *
				 * @since 1.0.0
				 *
				 * @param {Object} args Arguments.
				 */
				render( args ) {
					const fields = wpf.getFields(),
						requiredFields = app.tmpl.renderActionFields( args, fields ),
						$connection = app.connection.getById( args.connection_id ),
						$connectionData = $( app.selectors.actionData, $connection );

					$connectionData.html( requiredFields );

					app.$elements.$connections.trigger( 'connectionRendered', [ app.provider,
						args.connection_id ] );

					// Load ChoicesJS fields.
					app.ui.action.initChoicesJS( $connection );
				},

				/**
				 * Initialize Choices.js library.
				 *
				 * @since 1.0.0
				 *
				 * @param {Object} $connection jQuery connection selector.
				 */
				initChoicesJS( $connection ) {
					// Load if function exists.
					if ( typeof window.Choices !== 'function' ) {
						return;
					}

					const $choices = $( app.selectors.choiceJS, $connection );

					$choices.each( function( index, element ) {
						const $this = $( element );

						// Return if already initialized.
						if ( 'undefined' !== typeof $this.data( 'choicesjs' ) ) {
							return;
						}

						$this.data( 'choicesjs', new Choices( $this[ 0 ], {
							shouldSort: false,
							removeItemButton: true,
							fuseOptions:{
								threshold: 0.1,
								distance: 1000,
							},
							allowHTML: true,
							callbackOnCreateTemplates( template ) {
								return {
									choice: app.ui.action.renderChoiceTemplate.bind( this, template ),
									item: app.ui.action.renderItemTemplate.bind( this, template ),
								};
							},
							callbackOnInit() {
								wpf.initMultipleSelectWithSearch( this );
								wpf.showMoreButtonForChoices( this.containerOuter.element );
							},
						} ) );
					} );
				},

				/**
				 * Get icon HTML for a given icon class.
				 *
				 * @since 1.1.0
				 *
				 * @param {string} iconClass Icon class to render.
				 *
				 * @return {string} HTML string for the icon.
				 */
				getIconHTML( iconClass ) {
					return iconClass ? `<i class="fa fa-fw ${ iconClass }"></i>` : '';
				},

				/**
				 * Choices.js template for choice items.
				 *
				 * @since 1.1.0
				 *
				 * @param {Function} template The Choices.js template helper function.
				 * @param {Object}   classObj The CSS class map from Choices.js.
				 * @param {Object}   data     Data for the selected item.
				 *
				 * @return {string} Rendered HTML string for the selected item.
				 */
				renderChoiceTemplate( template, classObj, data ) {
					const iconHtml = app.ui.action.getIconHTML( data.customProperties?.icon );
					return template( `
						  <div class="${ classObj.classNames.item } ${ classObj.classNames.itemChoice } ${ data.disabled ? classObj.itemDisabled : classObj.itemSelectable }"
								data-select-text="${ this.config.itemSelectText }"
								data-choice
								data-id="${ data.id }"
								data-value="${ data.value }"
								${ data.groupId > 0 ? 'role="treeitem"' : 'role="option"' }>
								${ iconHtml }
								${ data.label }
						</div>
					` );
				},

				/**
				 * Choices.js template for item elements.
				 *
				 * @since 1.1.0
				 *
				 * @param {Function} template The Choices.js template helper function.
				 * @param {Object}   classObj The CSS class map from Choices.js.
				 * @param {Object}   data     Data for the selected item.
				 *
				 * @return {string} Rendered HTML string for the selected item.
				 */
				renderItemTemplate( template, classObj, data ) {
					const iconHtml = app.ui.action.getIconHTML( data.customProperties?.icon );
					return template( `
										<div class="${ classObj.classNames.item } ${ data.highlighted ? classObj.classNames.highlightedState : classObj.classNames.itemSelectable }"
											data-item
											data-id="${ data.id }"
											data-value="${ data.value }"
											${ data.active ? 'aria-selected="true"' : '' }
											${ data.disabled ? 'aria-disabled="true"' : '' }>
											${ iconHtml }
											${ data.label }
											<button type="button" class="${ classObj.classNames.button }" data-button aria-label="${ wpforms_slack_builder.remove_item }">${ wpforms_slack_builder.remove_item }</button>
									  </div>
									` );
				},
			},

			/**
			 * Input validation methods.
			 *
			 * @since 1.0.0
			 */
			validation: {

				/**
				 * Validate time input.
				 *
				 * @since 1.0.0
				 */
				validateTime() {
					// If another modal is open, exit early.
					if ( _.has( window, 'jconfirm' ) && ! _.isEmpty( window.jconfirm.instances ) ) {
						return;
					}

					$( app.selectors.connections ).each( ( index, connection ) => {
						const $timeField = $( connection ).find( app.selectors.timeField );

						$timeField.removeClass( 'wpforms-slack-error' );

						if ( $timeField.length === 0 || _.isEmpty( $timeField.val().trim() ) ) {
							return;
						}

						const timeValue = app.ui.validation.cleanTimeValue( $timeField.val().trim() );
						$timeField.val( timeValue );

						if ( app.ui.validation.isInvalidTime( timeValue ) ) {
							app.modal( wpforms_builder.slack.time_validation_error );
							$timeField.addClass( 'wpforms-slack-error' );
						}
					} );
				},

				/**
				 * Parse and validate time input.
				 *
				 * @since 1.0.0
				 *
				 * @param {string} timeValue Time input value.
				 *
				 * @return {boolean} True if time is invalid.
				 */
				isInvalidTime( timeValue ) {
					const currentTime = new Date();
					let parsedTime;

					// Check if the input is a Unix timestamp.
					if ( /^\d+$/.test( timeValue ) ) {
						// Convert the Unix timestamp (in seconds or milliseconds) to a JavaScript Date object.
						parsedTime = new Date( parseInt( timeValue, 10 ) * ( timeValue.length === 10 ? 1000 : 1 ) );
					} else {
						// Use Chrono to parse natural language and structured time inputs.
						parsedTime = chrono.parseDate( timeValue, { forwardDate: true } );
					}

					// Return true if parsing failed or time is in the past.
					return ! parsedTime || parsedTime.getTime() <= currentTime.getTime();
				},

				/**
				 * Clean the input value.
				 *
				 * @since 1.0.0
				 *
				 * @param {string} value Time value.
				 *
				 * @return {string} Cleared time string.
				 */
				cleanTimeValue( value ) {
					// Remove unwanted characters, keeping words, spaces, colons, commas, and periods, Replace multiple spaces with a single space, trim leading and trailing whitespace.
					return value
						.replace( /[^\w\s:,.]/g, '' )
						.replace( /\s+/g, ' ' )
						.trim();
				},
			},

			fields: {
				timeFieldValue( timeValue ) {
					let parsedTime;

					// Check if the input is a Unix timestamp.
					if ( /^\d+$/.test( timeValue ) ) {
						return timeValue;
					}

					// Use Chrono to parse natural language and structured time inputs.
					parsedTime = chrono.parse( timeValue );

					if ( chrono.parse( timeValue ).length === 1 ) {
						parsedTime = chrono.parse( timeValue )[ 0 ].text;
					}

					// Return true if parsing failed or time is in the past.
					return parsedTime ?? timeValue;
				},
			},
		},

		/**
		 * All methods for JavaScript templates.
		 *
		 * @since 1.0.0
		 */
		tmpl: {
			/**
			 * Compile and retrieve an HTML for common elements.
			 *
			 * @since 1.0.0
			 *
			 * @todo Since WPForms v1.9.5, this method will be redundant and can be deprecated once addon requirements are updated.
			 *
			 * @return {string} Compiled HTML.
			 */
			commonsHTML() {
				const tmplError = app.Templates.get( 'wpforms-' + app.provider + '-builder-content-connection-error' );

				return tmplError();
			},

			/**
			 * Compile and retrieve an HTML for "Custom Fields Table".
			 *
			 * @since 1.0.0
			 *
			 * @param {Object} args   Arguments
			 * @param {Object} fields Fields
			 *
			 * @return {string} Compiled HTML.
			 */
			renderActionFields( args, fields ) {
				const actionsFields = app.Cache.get( app.provider, 'actions_fields' );

				let fieldHTML = '';

				$.each( actionsFields[ args.target.val() ], function( key, field ) {
					if ( [ 'users', 'channels' ].includes( key ) ) {
						field.options = app.Cache.getById(
							app.provider,
							key,
							args.account_id
						);
					}

					const templateName = field.type === 'custom-fields'
						? 'wpforms-' + app.provider + '-builder-content-connection-' + field.type
						: 'wpforms-' + app.provider + '-builder-content-connection-' + field.type + '-field';
					const tmplField = app.Templates.get( templateName );

					fieldHTML += tmplField( {
						connection: app.Cache.getById( app.provider, 'connections', args.connection_id ),
						name: key,
						field,
						provider: {
							slug: app.provider,
							fields: actionsFields[ args.target.val() ],
						},
						options: fields,
					} );
				} );

				return fieldHTML;
			},
		},

		/**
		 * Modal.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} content Modal content.
		 */
		modal( content ) {
			// Checking required data.
			if ( ! content ) {
				return;
			}

			$.alert( {
				title: wpforms_builder.heads_up,
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

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPForms.Admin.Builder.Providers.Slack.init();
