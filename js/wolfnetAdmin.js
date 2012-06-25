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
if ( jQuery ) {
	
	( function ( $ ) {
		
		/* Make sure there is always a scrollbar on the window so there is no jumping of content 
		 * when sections are expanded and collapsed. */
		$( 'html' ).css( 'overflow-y', 'scroll' );
		
		$.fn.wolfnetFeaturedListingsControls = function ( options ) {
			
			var option = $.extend( {}, options );
			
			var animationSpeed = 'fast';
			var easing         = 'swing';
			
			return this.each( function () {
				
				var  widgetCtrls = this;
				var $widgetCtrls = $( this );
				
				var $playMode   = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayField:first' );
				var $autoFields = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayOptions:first' ).hide();
				var $manFields  = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_manualPlayOptions:first' ).hide();
				
				var showAutoFields = function () {
					if ( $manFields.is( ':visible' ) ) {
						$manFields.val( '' );
						$manFields.disabled = true;
						$manFields.find( 'fieldset:first' ).slideUp( animationSpeed, easing, function () {
							$manFields.hide();
							showAutoFields();
						} );
					}
					else {
						$autoFields.disabled = false;
						$autoFields.show();
						$autoFields.find( 'fieldset:first' ).slideDown( animationSpeed, easing );
					}
				}
				
				var showManFields = function () {
					if ( $autoFields.is( ':visible' ) ) {
						$autoFields.val( '' );
						$autoFields.disabled = true;
						$autoFields.find( 'fieldset:first' ).slideUp( animationSpeed, easing, function () {
							$autoFields.hide();
							showManFields();
						} );
					}
					else {
						$manFields.disabled = false;
						$manFields.show();
						$manFields.find( 'fieldset:first' ).slideDown( animationSpeed, easing );
					}
				}
				
				$playMode.change( function () {
					
					/* Automatic */
					if ( $( this ).val() == 'true' ) {
						showAutoFields();
					}
					/* Manual */
					else {
						showManFields();
					}
					
				} );
				
				$playMode.trigger( 'change' );
				
				var $pause = $widgetCtrls.find( 'input.wolfnet_featuredListingsOptions_pauseField:first' );
				
				var clickPauseOption = function ()
				{
					
					var $pauseLen      = $widgetCtrls.find( 'input.wolfnet_featuredListingsOptions_pauseLenField:first' );
					var $pauseLenLabel = $widgetCtrls.find( 'label[for="' + $pauseLen.attr( 'id' ) + '"]' );
					var $count         = $widgetCtrls.find( 'input.wolfnet_featuredListingsOptions_scrollCountField:first' );
					var $countLabel    = $widgetCtrls.find( 'label[for="' + $count.attr( 'id' ) + '"]' );
					
					if ( $pause.is( ':checked' ) ) {
						
						$pauseLen.prop( 'disabled', false );
						$count.prop( 'disabled', false );
						$pauseLenLabel.removeClass( 'disabled' );
						$countLabel.removeClass( 'disabled' );
						
					}
					else {
						
						$pauseLen.prop( 'disabled', true );
						$count.prop( 'disabled', true );
						$pauseLenLabel.addClass( 'disabled' );
						$countLabel.addClass( 'disabled' );
						
					}
					
				}
				
				$pause.click( clickPauseOption );
				
				clickPauseOption( $pause );
				
				var $dirField = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_dirField:first' );
				var $dirAuto  = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoDirField:first' );
				var $dirMan   = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_manDirField:first' );
				
				$dirAuto.change( function () {
					if ( $playMode.val() == 'true' ) {
						$dirField.val( $dirAuto.val() );
					}
				} );
				
				$dirMan.change( function () {
					if ( $playMode.val() != 'true' ) {
						$dirField.val( $dirMan.val() );
					}
				} );
				
				$dirAuto.trigger( 'change' );
				$dirMan.trigger( 'change' );
				
			} );
			
		}
		
	} )( jQuery ); /* END: jQuery IIFE */
	
} /* END: If jQuery Exists */