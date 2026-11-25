/* global wpformsEntryAutomation, Choices */
/**
 * Main JS file for the Tools > Entry Automation admin page.
 *
 * @since 1.0.0
 */
const WPFormsEntryAutomationDashboard = window.WPFormsEntryAutomationDashboard || ( function( document, window, $ ) {
	/**
	 * Public functions and properties.
	 *
	 * @since 1.0.0
	 *
	 * @type {Object}
	 */
	const app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init() {
			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready() {
			app.bindEvents();
		},

		/**
		 * Bind events.
		 *
		 * @since 1.0.0
		 */
		bindEvents() {
			$( document )
				.on( 'click', '.wpforms-entry-automation-task-add', app.displaySelectFormModal )
				.on( 'change', '.wpforms-entry-automation-select', app.handleSelectTask )
				.on( 'change', '.wpforms-entry-automation-select-all', app.selectAllFormTasks );
		},

		/**
		 * Display select form modal.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event The event object.
		 */
		displaySelectFormModal( event ) {
			event.preventDefault();

			$.alert( {
				title: wpformsEntryAutomation.i18n.modal_title,
				content: wpformsEntryAutomation.modal_content,
				icon: 'fa fa-question-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpformsEntryAutomation.i18n.confirm,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action() {
							const selectedFormId = parseInt( $( '#wpforms-entry-automation-select-form' ).val(), 10 ) || 0;

							if ( selectedFormId ) {
								// Redirect to the form builder page.
								window.location.href = wpformsEntryAutomation.admin_url + `admin.php?page=wpforms-builder&view=settings&form_id=${ selectedFormId }&section=entry_automation`;
							}
						},
					},
					cancel: {
						text: wpformsEntryAutomation.i18n.cancel,
						btnClass: 'btn-cancel',
						keys: [ 'esc' ],
					},
				},
				onOpenBefore() {
					$( 'body' ).addClass( 'wpforms-entry-automation-modal-loaded' );
					app.initChoicesJS();
					this.buttons.confirm.disable();
				},
			} );
		},

		/**
		 * Select all form tasks.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event The event object.
		 */
		selectAllFormTasks( event ) {
			const formId = parseInt( $( this ).val(), 10 ) || 0;

			const checkboxes = $( `.wpforms-entry-automation-task-form-${ formId }` ).find( 'input[type="checkbox"]' );

			checkboxes.each( function() {
				$( this ).prop( 'checked', $( event.target ).is( ':checked' ) );
			} );
		},

		/**
		 * Handle select task.
		 *
		 * @since 1.0.0
		 *
		 * @param {Object} event The event object.
		 */
		handleSelectTask( event ) {
			const $checkbox = $( event.target );
			const formId = parseInt( $checkbox.data( 'form' ), 10 ) || 0;

			if ( ! $checkbox.is( ':checked' ) ) {
				$( `.wpforms-entry-automation-select-form-${ formId }` ).prop( 'checked', false );
			}

			if ( $checkbox.is( ':checked' ) ) {
				const allChecked = $( `.wpforms-entry-automation-task-form-${ formId } input[type="checkbox"]` ).length ===
					$( `.wpforms-entry-automation-task-form-${ formId } input[type="checkbox"]:checked` ).length;

				$( `.wpforms-entry-automation-select-form-${ formId }` ).prop( 'checked', allChecked );
			}
		},

		/**
		 * Initialize ChoicesJS.
		 *
		 * @since 1.0.0
		 */
		initChoicesJS() {
			const $select = $( '#wpforms-entry-automation-select-form' );

			const args = {
				searchEnabled: true,
				renderChoiceLimit: -1,
				searchResultLimit: 10,
			};

			const choicesJS = new Choices( $select[ 0 ], args );

			choicesJS.containerOuter.element.addEventListener( 'showDropdown', () => {
				$( '.jconfirm' ).addClass( 'wpforms-entry-automation-select-form-open' );
			} );

			choicesJS.containerOuter.element.addEventListener( 'hideDropdown', () => {
				$( '.jconfirm' ).removeClass( 'wpforms-entry-automation-select-form-open' );
			} );

			choicesJS.containerOuter.element.addEventListener( 'choice', () => {
				const $container = $( choicesJS.containerOuter.element ).closest( '.jconfirm-box' );
				$container.find( '.jconfirm-buttons .btn-confirm' ).attr( 'disabled', false );
			} );
		},
	};

	return app;
}( document, window, jQuery ) );

WPFormsEntryAutomationDashboard.init();
