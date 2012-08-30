/**
 * This script is responsible for creating a button in the tinyMCE editor used in the post and page
 * editor. Only the button is created with this script the functionality of the button is managed in
 * js/jquery.wolfnet_shortcode_builder.src.js
 *
 * @package       js
 * @title         tinymce.wolfnet_shortcode_builder.src.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 *
 */

if ( typeof tinymce != 'undefined' && typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		var builderId = 'wolfnetShortcodeBuilderWindow';
		var $window  = $( '#' + builderId );

		/* If the window element doesn't exist create it and add it to the page. */
		if ( $window.length == 0 ) {
			$window = $( '<div/>' );
			$window.attr( 'id', builderId );
			$window.hide();
			$window.appendTo( $( 'body:first' ) );
		}

		tinymce.create( 'tinymce.plugins.wolfnetShortcodeBuilder', {

			init : function( editor, url )
			{
				var wolfnetPluginUrl = url.substring( 0, url.length - 2 );
				$window.wolfnetShortcodeBuilder( {
					tinymce   : editor,
					rootUri   : wordpressBaseUrl + '/index.php?pagename=wolfnet-admin-shortcodebuilder-optionform&formpage=',
					leaderUri : wolfnetPluginUrl + 'img/loader.gif'
				} );
				editor.addButton( 'wolfnetShortcodeBuilderButton', {
					title   : 'WolfNet Shortcode Builder',
					/* since the URL automatically include the js directory we need to strip it off
					 * to get to the img directory. */
					image   : wolfnetPluginUrl + 'img/wp_wolfnet_nav.png',
					onclick : function ()
					{
						$window.wolfnetShortcodeBuilder( 'open' );
					}
				} );
			}

		} );

		tinymce.PluginManager.add( 'wolfnetShortcodeBuilder', tinymce.plugins.wolfnetShortcodeBuilder );

	} )( jQuery );

}