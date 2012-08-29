/**
 * This jQuery script defines the functionality of the WolfNet Shortcode Builder tinyMCE button.
 *
 * @title         jquery.wolfnet_shortcode_builder.src.js
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 *
 */

 /* Make sure the 'trim' function is available in the String object. Fix for older versions of IE. */
if ( typeof String.prototype.trim !== 'function' ) {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the
 * $ variable.
 */
( function ( $ ) {

	$.widget( "ui.wolfnetShortcodeBuilder", $.ui.dialog, {

		options : {
			autoOpen     : false,
			height       : 450,
			width        : 475,
			modal        : true,
			defaultTitle : 'WolfNet Shortcode Builder',
			elmPrefix    : 'wolfnetShortcodeBuilder_',
			rootUri      : '',
			leaderUri    : '',
			loaderId     : 'loaderImage',
			menuId       : 'menuPage',
			pageSuffix   : '_page',
			menuItems    : {
				featuredListings : {
					buttonLabel : 'Add Featured Listings',
					shortcode   : 'wnt_featured',
					pageTitle   : 'Featured Listing Shortcode',
					uri         : 'featured-options'
				},
				listingGrid : {
					buttonLabel : 'Add Listing Grid',
					shortcode   : 'wnt_grid',
					pageTitle   : 'Listing Grid Shortcode',
					uri         : 'grid-options'
				},
				propertyList : {
					buttonLabel : 'Add Property List',
					shortcode   : 'wnt_list',
					pageTitle   : 'Property List Shortcode',
					uri         : 'list-options'
				},
				quickSearch : {
					buttonLabel : 'Add QuickSearch',
					shortcode   : 'wnt_search',
					pageTitle   : 'QuickSearch Shortcode',
					uri         : 'quicksearch-options'
				}
			}
		},

		shortcode : '',

		_create : function ()
		{
			var  widget    = this;
			var  option    = this.options;
			var  container = this.element;

			widget._createLoaderImage();
			widget._createMenuPage();
			widget._establishEvents();
			widget._activePage = option.menuId;

			option.title = option.defaultTitle;

			$.ui.dialog.prototype._create.call( this );
		},

		_establishEvents : function ()
		{
			var widget     = this;
			var $container = $( this.element );

			$container.bind( 'insertShortcodeEvent', function () {
				widget.insertShortcode();
				widget.close();
			} );

		},

		_createMenuPage : function ()
		{
			var widget     = this;
			var option     = this.options;
			var container  = this.element;
			var $container = $( container );
			var menuPageId = option.elmPrefix + option.menuId + option.pageSuffix;
			var $menuPage  = container.find( '#' + menuPageId );
			var menuItems  = option.menuItems;
			var $button    = null;

			if ( $menuPage.length == 0 ) {

				$menuPage = $( '<div/>' );
				$menuPage.attr( 'id', menuPageId );

				for ( var pageId in menuItems ) {

					$button = $( '<button/>' );
					$button.html( menuItems[pageId].buttonLabel );
					$button.addClass( 'pageButton' );
					$button.appendTo( $menuPage );
					$button[0].pageId = pageId;
					$button.click( function () {
						widget.openPage( this.pageId );
					} );

				}

				$menuPage.appendTo( $container );

			}

		},

		_createLoaderImage : function ()
		{
			var widget    = this;
			var option    = this.options;
			var container = this.element;
			var loaderId  = option.elmPrefix + option.loaderId ;
			var $loader   = $( '#' + loaderId );

			/* If the window element doesn't exist create it and add it to the page. */
			if ( $loader.length == 0 ) {
				$loader = $( '<img/>' );
				$loader.attr( 'id', loaderId );
				$loader.attr( 'src', option.leaderUri );
				$loader.hide();
				$loader.appendTo( container );
			}

			/* Store a reference to the loader image in memory. */
			widget.loaderImage = $loader;
		},

		_createPage : function ( page )
		{
			var widget        = this;
			var option        = this.options;
			var container     = this.element;
			var $container    = $( container );
			var $loaderImg    = widget.loaderImage;
			var $pageTitle    = null;
			var $backButton   = null;
			var $insertButton = null;

			if ( ( 'uri' in option.menuItems[page] ) && option.menuItems[page].uri != '') {

				var pageUri = option.rootUri + option.menuItems[page].uri;

				$page = $( '<div/>' );
				$page.attr( 'id', option.elmPrefix + page + option.pageSuffix );
				$page.attr( 'class', ( option.elmPrefix + option.pageSuffix ).replace( '__', '_' ) );
				$page.appendTo( $container );

				$backButton = $( '<button/>' );
				$backButton.html( 'Back' );
				$backButton.appendTo( $page );
				$backButton.click( function () {
					widget.closePage();
				} );

				$.ajax( {
					type: 'GET',
					dataType: 'html',
					url: pageUri,
					beforeSend: function () {
						$page.hide();
						$loaderImg.show();
					},
					success: function ( data ) {
						$page.append( data );
						wolfnet.initMoreInfo( $page.find( '.wolfnet_moreInfo' ) );
						$loaderImg.hide();
						$page.show();

						$insertButton = $( '<button/>' );
						$insertButton.html( 'Insert Shortcode' );
						$insertButton.appendTo( $page );
						$insertButton.click( function () {
							widget._buildShortcode( page );
							$container.trigger( 'insertShortcodeEvent' );
						} );

					}
				} );

			}
		},

		_getPage : function ( page )
		{
			var option = this.options;
			var pageId = option.elmPrefix + page + option.pageSuffix;
			return $( '#' + pageId );
		},

		openPage : function ( page )
		{
			var widget      = this;
			var option      = this.options;
			var container   = this.element;
			var $container = $( container );
			var $activePage = widget._getPage( widget._activePage );

			if ( page != widget._activePage ) {

				var $page = widget._getPage( page );
				widget._activePage = page;
				$activePage.hide();

				if ( page in option.menuItems && 'pageTitle' in option.menuItems[page] ) {
					widget._setOption( 'title', option.defaultTitle + ': ' + option.menuItems[page].pageTitle );
				}
				else {
					widget._setOption( 'title', option.defaultTitle );
				}

				if ( $page.length == 0 ) {
					widget._createPage( page );
					$page = widget._getPage( page );
				}
				else {
					$page.show();
				}

			}

		},

		closePage : function ()
		{
			var widget      = this;
			var option      = this.options;
			widget.openPage( option.menuId );
		},

		_buildShortcode : function ( page )
		{
			var widget = this;
			var option = widget.options;
			var $page  = widget._getPage( page );
			var attrs  = {};
			var string = '[' + option.menuItems[page].shortcode + ' /]';

			$page.find( 'input, select' ).each( function () {

				if ( this.name != '' ) {

					switch ( this.type ) {

						default:
							if ( this.value.trim() != '' ) {
								attrs[this.name] = this.value.trim();
							}
							break;

						//case 'checkbox':
						//	attrs[this.name] = this.value;
						//	break;
						//
						//case 'radio':
						//	attrs[this.name] = this.value;
						//	break;

					}

				}

			} );

			for ( var attr in attrs ) {
				string = string.replace( '/]', ' ' + attr + '="' + attrs[attr] + '" /]' );
			}

			widget.shortcode = string;

		},

		insertShortcode : function ()
		{
			this.options.tinymce.execCommand( 'mceInsertContent', false, this.shortcode );
		}

	} );

} )( jQuery );
