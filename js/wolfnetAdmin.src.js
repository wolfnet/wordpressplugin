/**
 * This script is a general container for JavaScript code sepcific to the WordPress admin interface.
 *
 * @title         wolfnetAdmin.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 *
 */

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $
 * variable.
 */
if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		$( document ).ready( function () {

			/* simple browser detection */
			if ( navigator.appName == 'Microsoft Internet Explorer' ) {
				$( 'html' ).addClass( 'ie' );
			}

		} );

		$.fn.wolfnetFeaturedListingsControls = function ( options )
		{

			var option = $.extend( {}, options );

			var animationSpeed = 'fast';
			var easing         = 'swing';

			var showAutoFields = function ( $autoFields )
			{
				$autoFields.disabled = false;
				$autoFields.show();
				$autoFields.find( 'fieldset:first' ).slideDown( animationSpeed, easing );
			}

			var hideAutoFields = function ( $autoFields )
			{
				$autoFields.val( '' );
				$autoFields.disabled = true;
				$autoFields.find( 'fieldset:first' ).slideUp( animationSpeed, easing, function () {
					$autoFields.hide();
				} );
			}

			return this.each( function () {

				var $widgetCtrls = $( this );

				var $playMode   = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayField:first' );
				var $autoFields = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayOptions:first' ).hide();

				$playMode.change( function () {

					/* Automatic */
					if ( $( this ).val() == 'true' ) {
						showAutoFields( $autoFields );
					}
					/* Manual */
					else {
						hideAutoFields( $autoFields );
					}

				} );

				$playMode.trigger( 'change' );

			} );

		}


		$.fn.wolfnetListingGridControls = function ( options )
		{

			var option = $.extend( {}, options );

			var animationSpeed = 'fast';
			var easing         = 'swing';

			var showAdvancedOptions = function ( $fields )
			{
				$fields.filter( '.basic-option' ).hide();
				$fields.filter( '.advanced-option' ).show();
			}
			var showBasicOptions = function ( $fields )
			{
				$fields.filter( '.advanced-option' ).hide();
				$fields.filter( '.basic-option' ).show();
			}

			var eventHandler = function ()
			{
				var $mode   = $( this );
				var $fields = this.$fields;

				if ( $mode.is( ':checked' ) ) {

					switch ( $mode.val() ) {

						case 'basic':
							showBasicOptions( $fields );
							break;

						case 'advanced':
							showAdvancedOptions( $fields );
							break;

					}

				}

			}

			var savedSearchEventHandler = function ()
			{
				if ( !this.beenWarned ) {
					alert(
						'The Saved Search that was previously used for this widget no longer exists. ' +
						'The widget will contiue to function using the same search criteria unless you ' +
						'change the saved search value to something other than ** DELETED **.'
					);
					this.beenWarned = true;
				}
			}

			return this.each( function () {

				var $form   = $( this );
				var $fields = $form.find( 'tr.basic-option,tr.advanced-option' );
				var $mode   = $form.find( '.modeField input' );

				$fields.hide();

				$mode.each( function () {
					this.$fields = $fields;
				} );

				$mode.click( eventHandler );
				$mode.bind( 'ready', eventHandler );

				$( document ).ready( function () {
					$mode.trigger( 'ready' );
				} );

				var $savedSearch = $form.find( '.savedSearchField select:first' );

				if ( $savedSearch.val() == 'deleted' ) {

					$savedSearch.each( function () {
						this.beenWarned = false;
					} );
					$savedSearch.click( savedSearchEventHandler );
					$savedSearch.focus( savedSearchEventHandler );

				}

			} );

		}

		$.fn.wolfnetValidateProductKey = function ( clientOptions )
		{

			var options = {
				validClass      : 'valid',
				invalidClass    : 'invalid',
				wrapperClass    : 'wolfnetProductKeyValidationWrapper',
				validEvent      : 'validProductKey',
				invalidEvent    : 'invalidProductKey',
				validationEvent : 'validateProductKey',
				apiUri          : 'http://services.mlsfinder.com/v1/validateKey/'
			};
			$.extend( options, clientOptions );

			/* Validate that the key has the appropriate prefix. */
			var validatePrefix = function ( key )
			{
				if ( key.substring( 0, 3 ).toLowerCase() == 'wp_' ) {
					return true;
				}
				else {
					return false;
				}

			}

			/* Validate that the key is of an appropriate length. */
			var validateLength = function ( key )
			{
				if ( key.length == 35 ) {
					return true;
				}
				else {
					return false;
				}

			}

			/* Send the key to the API and validate that the key is active in mlsfinder.com */
			var validateViaApi = function ( input )
			{
				var $this    = $( input );
				var key      = $this.val();

				$.ajax( {
					url: options.apiUri + key + '.jsonp',
					dataType: 'jsonp',
					type: 'GET',
					cache: false,
					timeout: 2500,
					statusCode: {
						404: function () {
							console.log( '404' );
							commFailure();
						}
					},
					success: function ( data ) {
						/* If no errors are returned the key is valid. */
						if ( !data.error.status ) {
							$this.trigger( options.validEvent );
						}
						/* If the validation failed because the key is disabled notify the user */
						else if ( data.error.status === true && data.error.message == 'Product Key is Disabled' ) {
							$this.trigger( options.invalidEvent );
							alert( 'The product key you have entered is currently disabled. Please contact customer services for more information.' );
						}
						else {
							$this.trigger( options.invalidEvent );
						}
					},
					error: function () {
						/* If the Ajax request failed notify the user that validation of the key was not possible. */
						$this.trigger( options.invalidEvent );
						alert( 'Your product key appears to be formated correctly but we are unable to validate it against our servers at this time.' );
					}
				} );
			}

			/* This callback function is called whenever the validation event is trigger and takes
			 * any neccessary action to notify the user if the key is valid or not. */
			var onValidateEvent = function ()
			{
				var $this    = $( this );
				var $wrapper = $this.parent();
				var key      = $this.val();

				if ( key != '' ) {
					if ( validatePrefix( key ) === true && validateLength( key ) === true ) {
						$this.trigger( options.validEvent );
						validateViaApi( this );
					}
					else {
						$this.trigger( options.invalidEvent );
					}
				}
				else {
					$wrapper.removeClass( options.validClass );
					$wrapper.removeClass( options.invalidClass );
				}
			}

			var onValidEvent = function ()
			{
				var $this    = $( this );
				var $wrapper = $this.parent();
				$wrapper.addClass( options.validClass );
				$wrapper.removeClass( options.invalidClass );
			}

			var onInvalidEvent = function ()
			{
				var $this    = $( this );
				var $wrapper = $this.parent();
				$wrapper.addClass( options.invalidClass );
				$wrapper.removeClass( options.validClass );
			}

			return this.each( function () {

				var $this = $( this );

				/* Ensure the plugin is only applied to input elements. */
				if ( this.nodeName.toLowerCase() != 'input' ) {
					throw "wolfnetValidateProductKey jQuery plugin can only be applied to an input element!"
				}
				else {

					/* Create an element to wrap the input field with. ( this will make styling easier ) */
					var $wrapper = $( '<span/>' );
					$wrapper.addClass( options.wrapperClass );

					/* Add the wrapper element to the DOM immediately after the input field. Then
					 * move the input field inside of the wrapper. */
					$this.after( $wrapper );
					$this.appendTo( $wrapper );

					/* Bind the some custom events to callback */
					$this.bind( options.validationEvent, onValidateEvent );
					$this.bind( options.validEvent, onValidEvent );
					$this.bind( options.invalidEvent, onInvalidEvent );

					/* Trigger the validation even every time a key is pressed in input field. */
					$this.keyup( function () {
						$this.trigger( options.validationEvent );
					} );

					/* Trigger the validation event when the document is ready. */
					$( document ).ready( function () {
						$this.trigger( options.validationEvent );
					} );

				}

			} );

		}

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
