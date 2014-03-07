/**
 * This jQuery script defines the functionality necessary to perform saving operations on a search
 * saver form such as the one in the Search Manager.
 *
 * @title         jquery.wolfnetSearchManager.src.js
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
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

/* Make sure the 'trim' function is available in the String object. Fix for older versions of IE. */
if(typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the
 * $ variable.
 */
if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		var pluginName = 'wolfnetSearchManager';

		var defaultOptions = {
			apiGetSuffix    : '-get',
			apiPostSuffix   : '-save',
			apiDeleteSuffix : '-delete',
			loaderClass     : 'wolfnet_loaderImage',
			refreshedEvent  : 'wolfnetDataRefreshed',
			savedEvent      : 'wolfnetSearchSaved',
			deletedEvent    : 'wolfnetSearchDelete',
			itemIdPrefix    : 'savedsearch_'
		};

		var methods = {

			init : function ( options )
			{

				return this.each( function () {

					var $this = $( this );

					var data = {
						option        : $.extend( defaultOptions, options ),
						savedSearches : [],
						loading       : false,
						saving        : false,
						deleting      : false
					}

					if ( typeof data.option.saveForm == 'jQuery' ) {
						data.saveForm = data.option.saveForm;
					}
					else {
						data.saveForm = $( data.option.saveForm );
					}

					$this.data( pluginName, data );

					$this[pluginName]( '_createLoaderImage' );
					$this[pluginName]( 'refresh' );

					var $saveButton       = data.saveForm.find( 'button:first' );
					var $descriptionField = data.saveForm.find( 'input:first' );

					/* When the save button is clicked perform the save action. */
					var saveHandler = function () {

						if ( $descriptionField.length != 0 ) {
							$descriptionField.trigger( 'submit' );
							var description = $descriptionField.val().trim();
						}
						else {
							var description = '';
						}

						if ( typeof WNTWP != 'undefined' ) {
							var criteria = WNTWP.returnSearchParams();
							// This gets set separately since the above function deals with search solutions params only.
							criteria.productkey = $('#productkey').val();
						}
						else {
							var criteria = {};
						}

						$this[pluginName]( 'save', description, criteria );

					}

					$saveButton.click( saveHandler );
					$descriptionField.keypress( function ( event ) {
						if ( event.keyCode == 13 ) {
							saveHandler();
						}
					} );

					$this.bind( data.option.refreshedEvent, function () {
						$( this )[pluginName]( '_buildTable' );
					} );

					$this.bind( data.option.savedEvent, function () {

						if ( $descriptionField.length != 0 ) {
							$descriptionField.val( '' );
							$descriptionField.trigger( 'blur' );
						}

						$( this ).data( pluginName, $.extend( data, { saving:false } ) );

					} );

					$this.bind( data.option.deletedEvent, function () {
						$this.data( pluginName, $.extend( data, { deleting:false } ) );
					} );

				} );

			},

			refresh : function ()
			{

				return this.each( function () {

					var $this = $( this );

					/* Call the method to perform Ajax request to get data. */
					$this[pluginName]( '_loadData' );

				} );

			},

			save : function ( description, criteria )
			{

				/* Make sure the required arguments are included and are of the correct type. */
				if ( typeof description != 'string' || typeof criteria != 'object' ) {
					$.error( 'To perform save on jQuery.' + pluginName + ' valid description and criteria arguments must be passed.');
					return this;
				}

				/* Validate the description argument. */
				if ( description.trim() == '' ) {
					alert( 'You must specify a description to save your search.' );
					return this;
				}

				/* Ensure that the search criteria are not in a URL encoded format. */
				for ( var i in criteria ) {
					criteria[i] = decodeURIComponent( criteria[i] );
				}

				return this.each( function () {

					var $this = $( this );
					var data  = $this.data( pluginName );

					if ( data.loading || data.saving || data.deleting ) {
						alert( 'Cannot save, please wait until the data has updated.' );
						return;
					}

					$.ajax( {
						url: wolfnet_ajax.ajaxurl,
						dataType: 'json',
						type: 'POST',
						data: {
                            action        : 'wolfnet_save_search',
							post_title    : description,
							custom_fields : criteria
						},
						beforeSend: function () {
							data.loaderImage.show();
							$this.data( pluginName, $.extend( data, { saving:true } ) );
						},
						success: function ( responseData ) {
							data.savedSearches = responseData;
							$this.data( pluginName, data );
							$this.trigger( data.option.savedEvent );
							$this.trigger( data.option.refreshedEvent );
						},
						complete: function () {
							data.loaderImage.hide();
						}
					} );

				} );

			},

			"delete" : function ( id )
			{

				return this.each( function () {

					var $this = $( this );
					var data  = $this.data( pluginName );

					if ( data.loading || data.saving || data.deleting ) {
						alert( 'Cannot delete, please wait until the data has updated.' );
						return;
					}

					$.ajax( {
						url: wolfnet_ajax.ajaxurl,
						dataType: 'json',
						type: 'GET',
						data: { action:'wolfnet_delete_search', id:id },
						beforeSend: function () {
							data.loaderImage.show();
							$this.data( pluginName, $.extend( data, { deleting:true } ) );
						},
						success: function ( responseData ) {
							data.savedSearches = responseData;
							$this.data( pluginName, data );
							$this.trigger( data.option.deletedEvent );
							$this.trigger( data.option.refreshedEvent );
						},
						complete: function () {
							data.loaderImage.hide();
						}
					} );

				} );

			},

			_createLoaderImage : function ()
			{

				return this.each( function () {

					var $this        = $( this );
					var data         = $this.data( pluginName );
					var loaderClass  = data.option.loaderClass;

					data.loaderImage = $this.find( 'div.' + loaderClass + ':first' );

					/* If the current element doesn't already have a loader add it. */
					if ( data.loaderImage.length == 0 ) {

						data.loaderImage = $( '<div/>' );
						data.loaderImage.append( $( '<img src="' + wolfnet_ajax.loaderimg + '" />' ) );
						data.loaderImage.addClass( loaderClass );
						data.loaderImage.hide();
						data.loaderImage.appendTo( $this );

					}

					$this.data( pluginName, data );

				} );

			},

			_loadData : function ()
			{

				return this.each( function () {

					var $this = $( this );
					var data  = $this.data( pluginName );

					$.ajax( {
						url: wolfnet_ajax.ajaxurl,
                        data: { action:'wolfnet_saved_searches', productkey: $('#productkey').val() },
						dataType: 'json',
						type: 'GET',
						beforeSend: function () {
							data.loaderImage.show();
							$this.data( pluginName, $.extend( data, { loading:true } ) );
						},
						success: function ( responseData ) {
							data.savedSearches = responseData;
							$this.data( pluginName, data );
							$this.trigger( data.option.refreshedEvent );
						},
						complete: function () {
							$this.data( pluginName, $.extend( data, { loading:false } ) );
							data.loaderImage.hide();
						}
					} );

				} );

			},

			_buildTable : function ()
			{

				return this.each( function () {

					var $this         = $( this );
					var data          = $this.data( pluginName );
					var savedSearches = data.savedSearches;

					var $row, $descCell, $cdateCell, $ctrlCell, $delBtn;

					var $tbody = $this.find( 'tbody:first' );

					$tbody.children().remove();

					for ( var i in savedSearches ) {

						var post_url = 'post.php?action=edit&post=' + savedSearches[i].ID;

						$row = $( '<tr/>' );
						$row.attr( 'id', data.option.itemIdPrefix + savedSearches[i].ID );
						$row.addClass( 'savedsearch' );
						$row.appendTo( $tbody );
						if ( i % 2 == 0 ) {
							$row.addClass( 'alternate' );
						}

						$descCell = $( '<td/>' );
						$descCell.html( savedSearches[i].post_title + ' (<a href="' + post_url + '">View Criteria</a>)' );
						$descCell.appendTo( $row );

						$cdateCell = $( '<td/>' );
						$cdateCell.html( savedSearches[i].post_date );
						$cdateCell.appendTo( $row );

						$ctrlCell = $( '<td/>' );
						$ctrlCell.appendTo( $row );

						$delBtn = $( '<button/>' );
						$delBtn.addClass( 'button-secondary' );
						$delBtn.attr( 'wnt:search', savedSearches[i].ID );
						$delBtn.html( 'Delete' );
						$delBtn.appendTo( $ctrlCell );
						$delBtn.click( function () {
							$this[pluginName]( 'delete', $( this ).attr( 'wnt:search' ) );
						} );

					}

				} );

			}

		};

		$.fn[pluginName] = function ( method )
		{

			if ( methods[method] ) {

				return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));

			}
			else if ( typeof method === 'object' || ! method ) {

				return methods.init.apply( this, arguments );

			}
			else {

				$.error( 'Method ' +  method + ' does not exist on jQuery.' + pluginName );

			}

		}

	} )( jQuery );

}
