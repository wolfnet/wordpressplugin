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

	( function ( $ ) {

		$moreInfoItems.hide();

		$moreInfoItems.before( '<span class="wolfnet_moreInfoIcon"/>' );

		$( '.wolfnet_moreInfoIcon' ).tooltip( {
			showURL     : false,
			width       : 200,
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