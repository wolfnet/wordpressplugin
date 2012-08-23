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

if ( typeof tinymce != 'undefined' ) {

	( function () {

		tinymce.create( 'tinymce.plugins.wolfnetShortcodeBuilder', {

			init : function( editor, url ) {
				editor.addButton( 'wolfnetShortcodeBuilderButton', {
					title   : 'WolfNet Shortcode Builder',
					/* since the URL automatically include the js directory we need to strip it off
					 * to get to the img directory. */
					image   : url.substring( 0, url.length - 2 ) + 'img/wp_wolfnet_nav.png',
					onclick : wolfnetShortcodeBuilder.buttonClick
				} );
			}

		} );

		tinymce.PluginManager.add( 'wolfnetShortcodeBuilder', tinymce.plugins.wolfnetShortcodeBuilder );

	} )();

}