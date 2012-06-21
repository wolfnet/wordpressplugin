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
 * code inside an immediately executed function to avoid naming conflicts.
 */
if ( jQuery ) {
	
	( function ( $ ) {
		
		jQuery( document ).ready( function () {
			
			$( '.wolfnet_listingGridOptions' ).each( function () {
				
			} );
			
			$( '.wolfnet_featuredListingsOptions' ).each( function () {
				
				var $this = $( this );
				
				var $autoPlay = $this.parent().parent().find( 'select.wolfnet_featuredListingsOptions_autoPlayField:first' );
				
				$autoPlay.on( 'change', function ( event ) {
					
					$field = $( this );
					
					var $autoDir  = $this.parent().parent().find( 'select.wolfnet_featuredListingsOptions_autoDirField:first' );
					var $manDir   = $this.parent().parent().find( 'select.wolfnet_featuredListingsOptions_manDirField:first' );
					
					var $autoPlayOptions   = $this.parent().parent().find( 'fieldset.wolfnet_featuredListingsOptions_autoPlayOptions:first' );
					var $manualPlayOptions = $this.parent().parent().find( 'fieldset.wolfnet_featuredListingsOptions_manualPlayOptions:first' );
					
					if ( $field.val() == 'true' ) {
						$autoPlayOptions.show();
						$manualPlayOptions.hide();
						$autoDir.removeAttr( 'disabled' );;
						$manDir.attr( 'disabled', 'disabled' );;
					}
					else {
						$manualPlayOptions.show();
						$autoPlayOptions.hide();
						$manDir.removeAttr( 'disabled' );;
						$autoDir.attr( 'disabled', 'disabled' );;
					}
					
				} );
				
				$autoPlay.trigger( 'change' );
				
			} );
			
		} );
		
	} )( jQuery );
	
}