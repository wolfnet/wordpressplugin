/**
 * This script is a general container for JavaScript used by the plugin.
 *
 * @title         wolfnet.src.js
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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


wolfnet.hideMap = function (mapId,hideMapId,showMapId)
{

	( function ( $ ) {
		$("#" + mapId).hide();
		$("#" + hideMapId).hide();
		$("#" + showMapId).show();
	} )( jQuery );
}


wolfnet.showMap = function (mapId,hideMapId,showMapId)
{

	( function ( $ ) {
		$("#" + mapId).show();
		$("#" + hideMapId).show();
		$("#" + showMapId).hide();
	} )( jQuery );
}


wolfnet.hideListings = function (collapseId,hideId,showId)
{

	( function ( $ ) {
		$("#" + collapseId).hide();
		$("#" + showId).show();
		$("#" + hideId).hide();
	} )( jQuery );
}


wolfnet.showListings = function (collapseId,hideId,showId,instanceId)
{

	( function ( $ ) {
		$("#" + collapseId).show();
		$("#" + hideId).show();
		$("#" + showId).hide();

		if (instanceId.indexOf("listingGrid") != -1) {
			$('#' + instanceId).wolfnetListingGrid('refresh');
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

				$(".wolfnet_error a").click( function() {
					$(this).siblings("div").toggle();
				});

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
