/**
 * This script is a general container for JavaScript used by the plugin.
 *
 * @package       com.wolfnet.wordpress.abstract
 * @title         AbstractDAO.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 *
 */
var wolfnet = function ()
{
}

wolfnet.initMoreInfo = function (  $moreInfoItems )
{

	var iconClass = 'wolfnet_moreInfoIcon';

	( function ( $ ) {

		$moreInfoItems.hide();

		$moreInfoItems.each( function () {

			var $item     = $( this );
			var $icon     = $item.siblings( 'span.' + iconClass );

			if ( $icon.length == 0 ) {

				$icon = $( '<span />' );
				$icon.addClass( iconClass );
				$item.before( $icon );

			}

		} );

		if ( typeof $.fn.tooltip != 'undefined' ) {

			$( '.' + iconClass ).tooltip( {
				showURL     : false,
				//width       : 200,
				bodyHandler : function () {
					return $( this ).siblings( '.wolfnet_moreInfo' ).html();
				}
			} );

		}

	} )( jQuery );

}

if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		var isPlaceholderSupported = function ()
		{
			var test = document.createElement( 'input' );
			return ( 'placeholder' in test );
		}

		$( document ).ready( function () {

			wolfnet.initMoreInfo( $( '.wolfnet_moreInfo' ) );

			( function () {

				if ( !isPlaceholderSupported() ) {

					var $placeHolderInputs = $( 'input[placeholder]');

					var inputBlur = function ()
					{
						var $this = $( this );
						var placeholder = $this.attr( 'placeholder' );
						if ( $this.val().trim() == '' || $this.val().trim() == placeholder ) {
							$this.val( placeholder );
							$this.addClass( 'input-placeholder' );
						}
					}

					var inputFocus = function ()
					{
						var $this = $( this );
						var placeholder = $this.attr( 'placeholder' );
						if ( $this.val().trim() == placeholder ) {
							$this.val('');
						}
						$this.removeClass( 'input-placeholder' );

					}

					$placeHolderInputs.blur( inputBlur );
					$placeHolderInputs.change( inputBlur );
					$placeHolderInputs.focus( inputFocus );
					$placeHolderInputs.submit( inputFocus );
					$placeHolderInputs.trigger( 'blur' );

				}

			} )();

		} );

	} )( jQuery );

}
