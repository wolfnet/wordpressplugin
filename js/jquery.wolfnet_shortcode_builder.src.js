/**
 * This jQuery script defines the functionality of the WolfNet Shortcode Builder tinyMCE button.
 *
 * @title         jquery.wolfnet_shortcode_builder.src.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 *
 */

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $
 * variable.
 */

var wolfnetShortcodeBuilder = {};

if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {


			wolfnetShortcodeBuilder.shortcode   = '';
			wolfnetShortcodeBuilder.initialized = false;


			/* This method initializes the builder if it has not already been initialized. */
			wolfnetShortcodeBuilder.init = function ()
			{
				if ( !wolfnetShortcodeBuilder.initialized ) {

					wolfnetShortcodeBuilder.initialized = true;

					wolfnetShortcodeBuilder._loadWindow();
					wolfnetShortcodeBuilder._loadMenuPage();

					$( wolfnetShortcodeBuilder ).bind( 'insertShortcodeEvent', function () {
						this.insertShortcode();
						this.close();
					} );

				}

			}


			wolfnetShortcodeBuilder._loadWindow = function ()
			{

				var windowId = 'wolfnetShortcodeBuilderWindow';
				var $window  = $( '#' + windowId );

				/* If the window element doesn't exist create it and add it to the page. */
				if ( $window.length == 0 ) {
					$window = $( '<div id="' + windowId + '" title="Wolfnet Shortcode Builder"></div>' );
					$window.css( 'display', 'none' );
					$( 'body:first' ).append( $window );
				}

				$window.dialog( {
					autoOpen: false,
					height: 300,
					width: 350,
					modal: true
				} );

				/* Store a reference to the window in memory. */
				wolfnetShortcodeBuilder.window = $window;

			}


			wolfnetShortcodeBuilder._loadMenuPage = function ()
			{
				var $window    = wolfnetShortcodeBuilder.window;
				var menuPageId = 'wolfnetShortcodeBuilder_menuPage';
				var $menuPage  = $window.find( '#' + menuPageId );

				if ( $menuPage.length == 0 ) {

					var pageContainer = '<div id="' + menuPageId + '"/>';
					var menuItems = [
						{ id: 'featuredListings', buttonLabel: 'Add Featured Listings' },
						{ id: 'listingGrid',      buttonLabel: 'Add Listing Grid' },
						{ id: 'propertyList',     buttonLabel: 'Add Property List' },
						{ id: 'quickSearch',      buttonLabel: 'Add QuickSearch' }
					];

					$menuPage = $( pageContainer );

					for ( var i in menuItems ) {

						var button = $( '<button/>' );
						button.html( menuItems[i].buttonLabel );
						button.addClass( 'pageButton' );
						button[0].wolfnetPageId = menuItems[i].id;
						button.click( wolfnetShortcodeBuilder._loadPage );

						$menuPage.append( button );

					}
					$window.append( $menuPage );
				}

				/* Store a reference to the page inside the window object. */
				$window.menuPage = $menuPage;

			}


			wolfnetShortcodeBuilder._loadPage = function ()
			{
				var $window = wolfnetShortcodeBuilder.window;

				switch ( this.wolfnetPageId ) {

					case 'featuredListings' :
						wolfnetShortcodeBuilder.shortcode = '[wnt_featured]';
						break;

					case 'listingGrid' :
						wolfnetShortcodeBuilder.shortcode = '[wnt_grid]';
						break;

					case 'propertyList' :
						wolfnetShortcodeBuilder.shortcode = '[wnt_list]';
						break;

					case 'quickSearch' :
						wolfnetShortcodeBuilder.shortcode = '[wnt_search]';
						break;

				}

				$( wolfnetShortcodeBuilder ).trigger( 'insertShortcodeEvent' );

			}


			/* This method inserts the shortcode stored in memory into the editor (tinyMCE) */
			wolfnetShortcodeBuilder.insertShortcode = function ()
			{
				var editor  = wolfnetShortcodeBuilder.tinymce;
				editor.execCommand( 'mceInsertContent', false, wolfnetShortcodeBuilder.shortcode );
			}


			/* This method opens the builder. */
			wolfnetShortcodeBuilder.open = function ()
			{
				wolfnetShortcodeBuilder.window.dialog( 'open' );
			}


			/* This method closes the builder. */
			wolfnetShortcodeBuilder.close = function ()
			{
				wolfnetShortcodeBuilder.window.dialog( 'close' );
			}


			/* This method is a callback for the tinyMCE button click event */
			wolfnetShortcodeBuilder.buttonClick = function ()
			{
				wolfnetShortcodeBuilder.tinymce = this;
				wolfnetShortcodeBuilder.init();
				wolfnetShortcodeBuilder.open();
			}

	} )( jQuery );

}