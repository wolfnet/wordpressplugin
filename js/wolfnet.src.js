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

if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		$( document ).ready( function () {

			var $moreInfoItems = $( '.wolfnet_moreInfo' );

			$moreInfoItems.hide();

			$moreInfoItems.before( '<span class="wolfnet_moreInfoIcon"/>' );

			$( '.wolfnet_moreInfoIcon' ).tooltip( {
				showURL     : false,
				bodyHandler : function () {
					return $( this ).siblings( '.wolfnet_moreInfo' ).html();
				}
			} );

		} );

	} )( jQuery );

}