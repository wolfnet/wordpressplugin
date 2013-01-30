if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		var properties = {
			pluginName      : 'wolfnetShortcodeBuilder',
			builderWindowId : 'wolfnetShortcodeBuilder',
			$builderWindow  : null,
			activeEditor    : null,
			windowTitle     : 'WolfNet Shortcode Builder',
			activePage      : null
		};


		var defaultOptions = {
			elmPrefix    : 'wolfnetShortcodeBuilder_',
			rootUri      : '',
			loaderUri    : '',
			loaderId     : 'loaderImage',
			menuId       : 'menuPage',
			pageSuffix   : '_page',
			menuItems    : {
				featuredListings : {
					buttonLabel : 'Add Featured Listings',
					shortcode   : 'wnt_featured',
					pageTitle   : 'Featured Listing Shortcode',
					uri         : '-options-featured'
				},
				listingGrid : {
					buttonLabel : 'Add Listing Grid',
					shortcode   : 'wnt_grid',
					pageTitle   : 'Listing Grid Shortcode',
					uri         : '-options-grid'
				},
				propertyList : {
					buttonLabel : 'Add Property List',
					shortcode   : 'wnt_list',
					pageTitle   : 'Property List Shortcode',
					uri         : '-options-list'
				},
				quickSearch : {
					buttonLabel : 'Add QuickSearch',
					shortcode   : 'wnt_search',
					pageTitle   : 'QuickSearch Shortcode',
					uri         : '-options-quicksearch'
				}
			}
		};

		var methods = {

			init : function ( options )
			{

				return this.each( function () {

					var $this = $( this );

					var data = {
						option : $.extend( defaultOptions, options )
					}

					if ( typeof data.option.saveForm == 'jQuery' ) {
						data.saveForm = data.option.saveForm;
					}
					else {
						data.saveForm = $( data.option.saveForm );
					}

					$this.data( properties.pluginName, data );

				} );

			},

			open : function ( editor )
			{

				properties.activeEditor = editor||null;

				methods._createDialogIfNotExists();

				if ( properties.$builderWindow != null ) {
					properties.$builderWindow.dialog( 'open' );
				}

			},

			close : function ()
			{

				if ( properties.$builderWindow != null ) {
					properties.$builderWindow.dialog( 'close' );
				}

			},

			openPage : function ( page )
			{
				var pageId      = page||defaultOptions.menuId;
				var $activePage = methods._getPage( properties.activePage )||$();

				if ( pageId != properties.activePage ) {

					$activePage.hide();

					properties.activePage = pageId;

					/* Update the window title. */
					if ( pageId in defaultOptions.menuItems && 'pageTitle' in defaultOptions.menuItems[pageId] ) {
						methods._setTitle( properties.windowTitle + ': ' + defaultOptions.menuItems[pageId].pageTitle );
					}
					else {
						methods._setTitle( properties.windowTitle );
					}

					methods._getPage( pageId ).show();

				}

			},

			closePage : function ()
			{

				if ( properties.activePage != defaultOptions.menuId ) {
					methods.openPage( defaultOptions.menuId );
				}

			},

			insertShortcode : function ()
			{

				if ( properties.activeEditor instanceof tinyMCE ) {
					properties.activeEditor.activeEditor.execCommand( 'mceInsertContent', false, methods._buildShortcodeString() );
				}

			},

			_createDialogIfNotExists : function ()
			{

				if ( properties.$builderWindow == null ) {
					properties.$builderWindow  = $( '#' + properties.builderWindowId );
				}

				/* If the window element doesn't exist create it and add it to the document body. */
				if ( !(properties.$builderWindow instanceof $) || properties.$builderWindow.length == 0 ) {

					properties.$builderWindow = $( '<div/>' );
					properties.$builderWindow.attr( 'id', properties.builderWindowId );
					properties.$builderWindow.hide();
					properties.$builderWindow.appendTo( $( 'body:first' ) );
					properties.$builderWindow.dialog( {
						autoOpen : false,
						height   : 450,
						width    : 475,
						modal    : true,
						title    : properties.windowTitle
					} );

					methods.openPage( defaultOptions.menuId );

				}

			},

			_defineEvents : function ()
			{

				return this.each( function () {

					var $this = $( this );

					$this.bind( 'insertShortcodeEvent', function ( event ) {
						$this[properties.pluginName]('insertShortcode');
						$this[properties.pluginName]('close');
					} );

				} );

			},

			_createMenuPage : function ()
			{
				var menuItems = defaultOptions.menuItems;
				var $menuPage = null;

				var $menuPage = $( '<div/>' )
					.attr( 'id', defaultOptions.elmPrefix + defaultOptions.menuId + defaultOptions.pageSuffix );

				for ( var pageId in menuItems ) {

					$( '<button/>' )
					.html( menuItems[pageId].buttonLabel )
					.addClass( 'pageButton' )
					.attr( 'wolfnet:page', pageId )
					.appendTo( $menuPage )
					.click( function () {
						methods.openPage( $( this ).attr( 'wolfnet:page' ) );
					} );

				}

				$menuPage.appendTo( properties.$builderWindow );

				properties.activePage = defaultOptions.menuId;

				return $menuPage;

			},

			_createLoaderImage : function ()
			{

			},

			_createPage : function ( page )
			{

				console.log( page );

				if ( page in defaultOptions.menuItems && 'uri' in defaultOptions.menuItems[page] && defaultOptions.menuItems[page].uri != '' ) {

					var pageUri = defaultOptions.rootUri + defaultOptions.menuItems[page].uri;

					var $page = $( '<div/>' )
						.attr( 'id', defaultOptions.elmPrefix + page + defaultOptions.pageSuffix )
						.attr( 'class', ( defaultOptions.elmPrefix + defaultOptions.pageSuffix ).replace( '__', '_' ) )
						.appendTo( properties.$builderWindow );

					$( '<button/>' )
						.html( 'Back' )
						.appendTo( $page )
						.click( function () {
							methods.closePage();
						} );

					$.ajax( {
						url      : pageUri,
						type     : 'GET',
						dataType : 'html',
						cache    : false,
						success  : function ( data ) {
							$page.append( data ).show();
							$( '<button/>' )
								.html( 'Insert Shortcode' )
								.appendTo( $page )
								.click( function () {
									methods._buildShortcode( page );
								} );
							wolfnet.initMoreInfo( $page.find( '.wolfnet_moreInfo' ) );
						}
					} );

					return $page;

				}

			},

			_getPage : function ( page )
			{

				var pageId = defaultOptions.elmPrefix + page + defaultOptions.pageSuffix;
				var $page  = $( '#' + pageId );

				if ( $page.length == 0 ) {
					if ( page == defaultOptions.menuId ) {
						$page = methods._createMenuPage();
					}
					else {
						$page = methods._createPage( page );
					}
				}

				return $page;

			},

			_buildShortcode : function ()
			{

			},

			_buildShortcodeString : function ()
			{

			},

			_setTitle : function ( title )
			{

				if ( properties.$builderWindow != null ) {
					properties.$builderWindow.dialog( { title:title } );
				}

			}

		};

		$.fn[properties.pluginName] = function ( method )
		{

			if ( methods[method] ) {

				return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));

			}
			else if ( typeof method === 'object' || ! method ) {

				return methods.init.apply( this, arguments );

			}
			else {

				$.error( 'Method ' +  method + ' does not exist in jQuery.' + pluginName );

			}

		}

	} )( jQuery );

}
