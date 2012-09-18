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

		$( '.' + iconClass ).tooltip( {
			showURL     : false,
			//width       : 200,
			bodyHandler : function () {
				return $( this ).siblings( '.wolfnet_moreInfo' ).html();
			}
		} );

	} )( jQuery );

}

if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		$( document ).ready( function () {

			wolfnet.initMoreInfo( $( '.wolfnet_moreInfo' ) );

		} );

	} )( jQuery );

}