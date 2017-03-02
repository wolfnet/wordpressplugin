/**
 * This jQuery plugin can be applied to a Quick Search form with appropriate fields.
 *
 * @title         jquery.wolfnetQuickSearch.js
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

 /* Make sure the 'trim' function is available in the String object. Fix for older versions of IE. */
if(typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $
 * variable.
 */
if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		$.fn.wolfnetQuickSearch = function ( options ) {

			/* Define the default options for the plugin. */
			var defaultOptions = {
			        defaultSearchType : 'opentxt',
			        searchTypes : {
			            opentxt : {
			                hint : options.hintText,
			                name : 'open_text'
			                },
			            mlsnum  : {
			                hint : 'MLS Listing #',
			                name : 'property_id'
			                }
			            }
			    }

			/* If the options passed to the plugin contain 'searchTypes' merge them with the default
			 * search types so that we do not lose them. */
			if ( options && 'searchTypes' in options ) {
				options.searchTypes = $.extend( defaultOptions.searchTypes, options.searchTypes );
			}

			/* Merge the options passed into the plugin instance with the default options. */
			var option = $.extend( defaultOptions, options );

			/* This function establishes the hint text in the search text field based on the search
			 * type that has been selected/clicked. */
			var performTypeChange = function ( $searchTypeLink, $input )
			{
				var searchTypeAttr = 'wolfnet:search_type';
				var searchType     = $searchTypeLink.attr( searchTypeAttr );
				$input.get(0).changeSearchType( searchType );

			}

			/* This function is a callback for the onFocus event for the search text field. */
			var performFocus = function ( $input )
			{
				var hint = $input.prop( 'hint' );

				/* If the input field is currently populated with a hint replace it with empty string. */
				if ( $input.val() == hint ) {

					$input.val( '' );

				}

				/* Make sure the field is not styled as hint text. */
				$input.removeClass( 'hintText' );

			}

			/* This function is a callback for the onBlur event for the search text field. */
			var performBlur = function ( $input )
			{
				var hint = $input.prop( 'hint' );

				/* If the input field is empty we still want to show the user the hint. */
				if ( $input.val().trim() == '' || $input.val() == hint ) {

					$input.val( hint );
					$input.addClass( 'hintText' ); /* Style the field as as hint text. */

				}

			}

			/* For each of the elements to which the plugin is being applied do the following. */
			return this.each( function () {

				var  quickSearch       = this;
				var $quickSearch       = $( this );
				var $quickSearchForm   = $quickSearch.find( '.wolfnet_quickSearch_form:first' );
				var $searchInput       = $quickSearch.find( '.wolfnet_quickSearch_searchText:first' );
				var $searchTypeLinks   = $quickSearch.find( 'ul.wolfnet_searchType li a' );
				var $defaultSearchLink = $searchTypeLinks.first();

				/* Establish the new properties of the search text field for managing the hint text.
				 * this is done this way so that the 'changeSearchType' function is exposed as part
				 * of the elements DOM object and can then be manipulated outside of the plugin. */
				$searchInput.get(0).hint = '';
				$searchInput.get(0).searchTypes = option.searchTypes;
				$searchInput.get(0).defaultSearchType = option.defaultSearchType;
				$searchInput.get(0).changeSearchType = function ( searchType )
				{

					/* Make sure the searchType is defined and has the correct properties. */
					if ( !( searchType in this.searchTypes )
						|| (
							!( 'hint' in this.searchTypes[searchType] )
							|| !( 'name' in this.searchTypes[searchType] )
						)
					) {
						searchType = this.defaultSearchType;
					}

					/* Update the hint text. */
					this.hint = this.searchTypes[searchType].hint;
					this.value = this.hint;

					/* Update the text field name so that string is passed to the correct parameter in
					 * the search solution. */
					this.name = this.searchTypes[searchType].name;

					$searchTypeLinks.filter( function () {
						return $( this ).attr( 'wolfnet:search_type' );
					} ).each( function () {
						var $this = $( this );
						$this.removeClass( 'wolfnet_active' );
						if ( $this.attr( 'wolfnet:search_type' ) == searchType ) {
							$this.addClass( 'wolfnet_active' );
						}
					} );

				};

				/* Apply the follwing logic to the click event of any 'search type links' within the
				 * current quick search instance. This will cause the form to update base on which
				 * search type is being used. */
				$searchTypeLinks.click( function () {

					performTypeChange( $( this ), $searchInput );

				} );

				/* Apply the follwing logic to the focus event of search text input within the
				 * current quick search instance. */
				$searchInput.focus( function () {

					performFocus( $( this ) );

				} );

				/* Apply the follwing logic to the blur event of search text input within the
				 * current quick search instance. */
				$searchInput.blur( function () {

					performBlur( $( this ) );

				} );

				/* Apply the following logic to the submit event of the search form. */
				$quickSearchForm.submit( function () {

					performFocus( $searchInput );

				} );

				/* Imediately perform the change search type logic to make sure the defaults are set
				 * for the form. */
				performTypeChange( $defaultSearchLink, $searchInput );

				/* Imediately perform the blur logic to make sure the defaults are set for the form. */
				performBlur( $searchInput );

				/* Determine the size of the search container and update the container class based
				 * on the size. This allows for adaptive styling based on the area into which the
				 * quicksearch is placed. */
				var onResize = function ()
				{
					var containerWidth = $quickSearch.width();
					if ( containerWidth > 230 ) {
						$quickSearch.removeClass( 'wolfnet_wNarrow' );
						$quickSearch.addClass( 'wolfnet_wWide' );
					}
					else {
						$quickSearch.removeClass( 'wolfnet_wWide' );
						$quickSearch.addClass( 'wolfnet_wNarrow' );
					}

				}

				$( window ).resize( onResize );

				$( window ).trigger( 'resize' );

			} ); /* END: for each loop of elements the plugin has been applied to. */

		}; /* END: function $.fn.wolfnetQuickSearch */

		$.fn.toggleQuickSearchFields = function(baseElement, state) {
			var fieldNames = ['open_text', 'min_price', 'max_price', 'min_bedrooms', 'min_bathrooms', 'search'];
			for(var element in fieldNames) {
				$('#' + baseElement + ' [name=' + fieldNames[element] + ']').prop('disabled', state);
			}
		}

		$.fn.rebuildQuickSearchOptions = function(baseElement, keyId) {
			$.ajax( {
	            url: wolfnet_ajax.ajaxurl,
	            data: { action:'wolfnet_base_url', keyid:keyId },
	            dataType: 'json',
	            type: 'GET',
	            async: false,
	            cache: false,
	            timeout: 2500,
	            statusCode: {
	                404: function () {
	                    commFailure();
	                }
	            },
	            success: function ( data ) {
	                $('#' + baseElement).attr('action', data);
	            }
	        } );

			$.ajax( {
	            url: wolfnet_ajax.ajaxurl,
	            data: { action:'wolfnet_price_range', keyid:keyId },
	            dataType: 'json',
	            type: 'GET',
	            cache: false,
	            timeout: 2500,
	            statusCode: {
	                404: function () {
	                    commFailure();
	                }
	            },
	            success: function ( data ) {
	                var minOptions = buildPriceDropdownOptions(data['min_price']['options']);
	                var maxOptions = buildPriceDropdownOptions(data['max_price']['options']);
	                $('#' + baseElement + ' [name=min_price], #' + baseElement + ' [name=max_price]').html('');
	                $('#' + baseElement + ' [name=max_price]').append($('<option />').attr('value', '').html('Max. Price'));
	                $('#' + baseElement + ' [name=min_price]').append($('<option />').attr('value', '').html('Min. Price'));
	                $(minOptions).each(function() {
	                    $('#' + baseElement + ' [name=min_price]').append(this);
	                });
	                $(maxOptions).each(function() {
	                    $('#' + baseElement + ' [name=max_price]').append(this);
	                });
	            }
	        } );
		}

		$.fn.routeQuickSearch = function(form) {
			var data = {};
			var formData = $(form).serializeArray();
			for(var i = 0; i < formData.length; i++) {
				data[formData[i]['name']] = formData[i]['value'];
			}

			disabledFormFields(form);

			$.ajax( {
	            url: wolfnet_ajax.ajaxurl,
	            data: { action:'wolfnet_route_quicksearch', formData:data },
	            dataType: 'json',
	            type: 'GET',
	            async: true,
	            tryCount: 0,
	            retryLimit: 3,
	            cache: false,
	            statusCode: {
	                404: function () {
	                    commFailure();
	                }
	            },
	            error: function(jqXHR, textStatus, errorThrown) {
	            	if(textStatus == 'timeout') {
	            		this.tryCount++;
	            		if(this.tryCount < this.retryLimit) {
	            			$.ajax(this);
	            			return;
	            		}
	            	}
	            },
	            success: function ( data ) {
	            	window.location.href = data;
	            	return false;
	            }
	        } );
        }

        var _createLoaderImage = function(root)
		{
			var loaderClass = 'wolfnet_loaderImage';
			var overlayClass = 'wolfnet_loaderOverlay';
			var loaderImage = $(root).find('div.' + loaderClass + ':first');

			/* If the current element doesn't already have a loader add it. */
			if (loaderImage.length == 0) {
				loaderImage = $('<div/>');
				loaderImage.append($('<img src="' + wolfnet_ajax.loaderimg + '" />'));
				loaderImage.addClass(loaderClass);

				var overlay = $('<div/>')
					.addClass(overlayClass);

				loaderImage.insertAfter('#' + $(root).attr('id') + ' .wolfnet_quickSearchFormButton');
				overlay.appendTo(root);
			}
		}

		var disabledFormFields = function(root)
		{
			_createLoaderImage(root);
			$(root).find(':input').prop('disabled', true);
		}

		var buildPriceDropdownOptions = function(data) {
            var options = [];
            $(data).each(function() {
                options.push(
                    $('<option />').attr('value', this.value).html(this.label)
                );
            });
            return options;
        }

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
