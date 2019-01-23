/**
 * This script is a general container for JavaScript code sepcific to the WordPress admin interface.
 *
 * @title         wolfnetAdmin.js
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

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the $
 * variable.
 */

if ( typeof jQuery != 'undefined' ) {

	( function ( $ ) {

		$( document ).ready( function () {

			/* simple browser detection */
			if ( navigator.appName == 'Microsoft Internet Explorer' ) {
				$( 'html' ).addClass( 'ie' );
			}

			/* This is a HACK to remove some unwanted controls on the Saved Search custom post type. */
			var $formWrap = $( '.post-php .icon32-posts-wolfnet_search' ).parent();
			if ( $formWrap.length != 0 ) {
				$formWrap.find( '.add-new-h2' ).remove();
				$formWrap.find( '#submitpost #minor-publishing' ).remove();
			}

			/* The following code is responsible for displaying full version images of the
			 * "thumbnail" images on the support page. */
			( function () {

				var $supportPage = $( '#wolfnet_support_page' );
				var $thumbnailModal = $supportPage.find( '#thumbnail_modal' );

				if ( $thumbnailModal.length == 0 ) {
					$thumbnailModal = $( '<div />' );
					$thumbnailModal.css( 'text-align', 'center' );
					$supportPage.append( $thumbnailModal );
					$thumbnailModal.dialog( { autoOpen: false, modal: true, width: 550, height: 450 } );
				}

				$supportPage.find( 'a img.wolfnet_thumbnail' ).parent().click( function ( event ) {
					var $img = $( '<img />' );
					var maxHeight = $thumbnailModal.height() - 50;
					var maxWidth  = $thumbnailModal.width()  - 35;
					$img.attr( 'src', $( this ).attr( 'href' ) );
					$img.attr( 'align', 'center' );
					$img.css( { 'max-height': maxHeight, 'max-width': maxWidth, 'margin': '0' } );
					$img.click( function () {
						window.open( this.src );
					} );
					$thumbnailModal.html( $img );
					$thumbnailModal.dialog( 'open' );
					event.preventDefault();
					return false;
				} );

				var updateModalHeight = function () {
					var windowHeight = $( window ).height();
					var windowWidth  = $( window ).width();
					$thumbnailModal.dialog( { height: ( windowHeight * .8 ), width: ( windowWidth * .8 ) } );
				}
				updateModalHeight();

				$( window ).resize( function () {
					updateModalHeight();
				} );

				var $supportContent = $( '#wolfnet_support_content' );
				var $supportNav     = $supportContent.find( 'ol:first' );

				/* Remove Table of Contents Header */
				$supportContent.find( 'h3:contains(Table of Contents)' ).remove();

				$supportNav.find( 'li > ol,li > ul' ).remove();

				$supportContent.tabs();

			})();

		} );

		$.fn.wolfnetFeaturedListingsControls = function ( options )
		{

			var option = $.extend( {}, options );

			var animationSpeed = 'fast';
			var easing         = 'swing';

			var showAutoFields = function ( $autoFields )
			{
				$autoFields.disabled = false;
				$autoFields.show();
				$autoFields.find( 'fieldset:first' ).slideDown( animationSpeed, easing );
			}

			var hideAutoFields = function ( $autoFields )
			{
				$autoFields.val( '' );
				$autoFields.disabled = true;
				$autoFields.find( 'fieldset:first' ).slideUp( animationSpeed, easing, function () {
					$autoFields.hide();
				} );
			}

			return this.each( function () {

				var $widgetCtrls = $( this );

				var $playMode   = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayField:first' );
				var $autoFields = $widgetCtrls.find( '.wolfnet_featuredListingsOptions_autoPlayOptions:first' ).hide();

				$playMode.change( function () {

					/* Automatic */
					if ( $( this ).val() === 'true' ) {
						showAutoFields( $autoFields );
					}
					/* Manual */
					else {
						hideAutoFields( $autoFields );
					}

				} );

				$playMode.trigger( 'change' );

			} );

		}


		$.fn.wolfnetListingGridControls = function ( options )
		{

			var option = $.extend( {}, options );

			var animationSpeed = 'fast';
			var easing         = 'swing';

			var showAdvancedOptions = function ( $fields )
			{
				$fields.filter( '.basic-option' ).hide();
				$fields.filter( '.advanced-option' ).show();
			}
			var showBasicOptions = function ( $fields )
			{
				$fields.filter( '.advanced-option' ).hide();
				$fields.filter( '.basic-option' ).show();
			}

			var eventHandler = function ()
			{
				var $mode   = $( this );
				var $fields = this.$fields;

				if ( $mode.is( ':checked' ) ) {

					switch ( $mode.val() ) {

						case 'basic':
							showBasicOptions( $fields );
							break;

						case 'advanced':
							showAdvancedOptions( $fields );
							break;

					}

				}

			}

			var savedSearchEventHandler = function ()
			{
				if ( !this.beenWarned ) {
					alert(
						'The Saved Search that was previously used for this widget no longer exists. ' +
						'The widget will continue to function using the same search criteria unless you ' +
						'change the saved search value to something other than ** DELETED **.'
					);
					this.beenWarned = true;
				}
			}

			return this.each( function () {

				var $form   = $( this );
				var $fields = $form.find( 'tr.basic-option,tr.advanced-option' );
				var $mode   = $form.find( '.modeField input' );
				var $key    = $form.find( '.keyid' );

				$fields.hide();

				$mode.each( function () {
					this.$fields = $fields;
				} );

				$key.change( function () {
					$(this).wolfnetUpdateShortcodeControls($form);
				} );

				$mode.click( eventHandler );
				$mode.bind( 'ready', eventHandler );

				$( document ).ready( function () {
					$mode.trigger( 'ready' );
				} );

				var $savedSearch = $form.find( '.savedSearchField select:first' );

				if ( $savedSearch.val() == 'deleted' ) {

					$savedSearch.each( function () {
						this.beenWarned = false;
					} );
					$savedSearch.click( savedSearchEventHandler );
					$savedSearch.focus( savedSearchEventHandler );

				}

			} );

		}


        $.fn.wolfnetValidateProductKey = function (clientOptions) {

            var options = {
                validClass      : 'valid success',
                invalidClass    : 'invalid error',
                wrapperClass    : 'wolfnetProductKeyValidationWrapper',
                validEvent      : 'validProductKey',
                invalidEvent    : 'invalidProductKey',
                validationEvent : 'validateProductKey',
                iconClass       : 'wnt-icon',
                validIconClass  : 'wnt-icon-checkmark',
                invalidIconClass: 'wnt-icon wnt-icon-close',
                validIndicatorClass: 'wnt-indicator-valid',
                invalidIndicatorClass: 'wnt-indicator-invalid'
            };

            $.extend(options, clientOptions);

            var clearValidation = function ($input) {
                var $wrapper = $input.parent();

                $wrapper.removeClass(options.validClass);
                $wrapper.removeClass(options.invalidClass);

            };

            /* Validate that the key is of an appropriate length. */
            var validateLength = function (key) {
                key = $.trim(key);

                // Account for old keys with "wp_" and new keys without that prefix.
                if (key.length == 35 || key.length == 32) {
                    return true;
                } else {
                    return false;
                }

            };

            /* Send the key to the API and validate that the key is active in mlsfinder.com */
            var validateViaApi = function ($input) {
                var key = $input.val();

                $.ajax({
                    url: wolfnet_ajax.ajaxurl,
                    data: { action: 'wolfnet_validate_key', key: key },
                    dataType: 'json',
                    type: 'GET',
                    cache: false,
                    timeout: 2500,
                    success: function (data) {
                        if (data === 'true') {
                            $input.trigger(options.validEvent);
                        } else {
                            $input.trigger(options.invalidEvent);
                        }
                    },
                    error: function () {
                        /* If the Ajax request failed notify the user that validation of the key was not possible. */
                        $input.trigger(options.invalidEvent);
                        alert('Your product key appears to be formated correctly but we are ' +
                            'unable to validate it against our servers at this time.');
                    }
                });

            };

            /* This callback function is called whenever the validation event is trigger and takes
             * any necessary action to notify the user if the key is valid or not. */
            var onValidateEvent = function (event) {
                var $input = $(this);
                var key = $input.val();

                // Only perform validation when the user has entered something.
                if ($.trim(key) !== '') {

                    // First perform client side validation.
                    var valid = validateLength(key);

                    // If the client side validation passed move on to server side validation.
                    if (valid) {
                        validateViaApi($input);
                    }

                    // Trigger the appropriate validation event.
                    if (valid) {
                        $input.trigger(options.validEvent);
                    } else {
                        $input.trigger(options.invalidEvent);
                    }

                } else {
                    clearValidation($input);

                }

            };

            var onValidEvent = function (event) {
                var $input = $(this);
                var $wrapper = $input.parent();
                var $row = $wrapper.closest('tr');
                var $marketContainer = $row.find('.wolfnet_keyMarket');
                var $marketLabel = $row.find('.wolfnet_keyLabel');
                var key = $input.val();

                // Updating the appearance to indicate the input is valid
                $wrapper.addClass(options.validClass);
                $wrapper.removeClass(options.invalidClass);

                if ($.trim($marketContainer.html()) === '' || $.trim($marketLabel.val()) === '') {

                    // Update market name
                    $.ajax({
                        url: wolfnet_ajax.ajaxurl,
                        data: {action: 'wolfnet_market_name', productkey: key},
                        dataType: 'json',
                        type: 'GET',
                        cache: false,
                        timeout: 2500,
                        success: function (data) {

                            if ($.trim($marketContainer.html()) === '') {
                                $marketContainer.html(data);
                            }

                            if ($.trim($marketLabel.val()) === '') {
                                $marketLabel.val(data);
                            }

                            $wrapper.closest('tr').find('.wolfnet_keyMarket_value').val(data);

                        },
                        error: function () {
                            $input.trigger(options.invalidEvent);
                        }
                    });

                }

            };

            var onInvalidEvent = function (event) {
                var $input = $(this);
                var $wrapper = $input.parent();

                $wrapper.addClass(options.invalidClass);
                $wrapper.removeClass(options.validClass);

            };

            return this.each(function () {

                var $input = $(this);

                /* Ensure the plugin is only applied to input elements. */
                if (this.nodeName.toLowerCase() != 'input') {
                    $.error('wolfnetValidateProductKey jQuery plugin can only be applied to an input element!');
                } else {

                    /* Create an element to wrap the input field with. ( this will make styling easier ) */
                    var $wrapper = $('<span>')
                        .addClass(options.wrapperClass)
                        .append([
                            $('<span>')
                                .addClass(options.validIndicatorClass)
                                .addClass(options.validIconClass)
                                .addClass(options.iconClass),
                            $('<span>')
                                .addClass(options.invalidIndicatorClass)
                                .addClass(options.invalidIconClass)
                                .addClass(options.iconClass)
                        ]);

                    /* Add the wrapper element to the DOM immediately after the input field. Then
                     * move the input field inside of the wrapper. */
                    $input.after($wrapper).prependTo($wrapper);

                    /* Bind the some custom events to callback */
                    $input.bind(options.validationEvent, onValidateEvent);
                    $input.bind(options.validEvent, onValidEvent);
                    $input.bind(options.invalidEvent, onInvalidEvent);

                    /* Trigger the validation even every time a key is pressed in input field. */
                    $input.keyup(function () {
                        $input.trigger(options.validationEvent);
                    });

                    /* Trigger the validation event when the document is ready. */
                    $(document).ready(function () {
                        $input.trigger(options.validationEvent);
                    });

                }

            });

        };


		$.fn.wolfnetDeleteKeyRow = function ($button) {
			var key = $button.attr('data-wnt-key');
			$('.row' + key).remove();
		}


		$.fn.wolfnetInsertKeyRow = function ()
		{
			var $keyTable      = $('#wolfnet_keys'),
				$keyCount      = $('#wolfnet_keyCount'),
				$lastRow       = $keyTable.find('tr').last(),
				$row           = $lastRow.clone(),
				$keyField      = $row.find('.wolfnet_productKey');
			var nextIteration  = parseInt($keyCount.val()) + 1;

			$row.attr('class', 'row' + nextIteration);

			$row.find('.wolfnetProductKeyValidationWrapper').removeClass('valid invalid success error');

			$keyField.attr({
				'id':     'wolfnet_productKey_' + nextIteration,
				'name':   'wolfnet_productKey_' + nextIteration
			}).val('');

			$row.find('.wolfnet_keyMarket_value').attr({
				'id':     'wolfnet_keyMarket_' + nextIteration,
				'name':   'wolfnet_keyMarket_' + nextIteration
			}).val('');

			$row.find('.wolfnet_keyLabel').attr({
				'id':     'wolfnet_keyLabel_' + nextIteration,
				'name':   'wolfnet_keyLabel_' + nextIteration
			}).val('');

			$row.find('.wolfnet_deleteKey').attr('data-wnt-key', nextIteration);

			$keyCount.val(nextIteration);

			$('#wolfnet_productKey_' + nextIteration).wolfnetValidateProductKey( {
				rootUri: '<?php echo site_url(); ?>?pagename=wolfnet-admin-validate-key'
			});

			$lastRow.after($row);

			$keyTable.trigger('wnt_addKeyField', [ $keyField ]);

		}

        $.fn.wolfnetUpdateShortcodeControls = function (container)
	    {

	        var keyid = $(container).find('.keyid').val();
            var loaderClass = 'wolfnet_loaderImage';

            var createLoaderImage = function (root)
            {
                var loaderImage = $(root).find('div.' + loaderClass + ':first');

                /* If the current element doesn't already have a loader add it. */
                if (loaderImage.length == 0) {
                    loaderImage = $('<div/>');
                    loaderImage.append($('<img src="' + wolfnet_ajax.loaderimg + '" />'));
                    loaderImage.addClass(loaderClass);
                    loaderImage.appendTo('#' + $(root).attr('id') + ' .wolfnet_prices');
                } else {
                    loaderImage.show();
                }
            };

	        $.ajax({
	            url: wolfnet_ajax.ajaxurl,
	            data: { action: 'wolfnet_price_range', keyid: keyid },
	            dataType: 'json',
	            type: 'GET',
	            cache: false,
	            timeout: 10000,
	            statusCode: {
	                404: function () {
	                    commFailure();
	                }
	            },
                beforeSend: function () {
                    createLoaderImage($(container));
                    $(container).find('.pricerange').each(function () {
                        $(this).prop('disabled', true);
                    });
                }
	        })
            .error(function (data) {
                console.log(data);
            })
            .success(function (data) {
                $(container).find('.pricerange').html('');

                $(container).find('.minprice').append($('<option />').attr('value', '').html('Min. Price'));
                $(data.min_price.options).each(function () {
                    $(container).find('.minprice').append(
                        $('<option />').attr('value', this.value).html(this.label)
                    );
                });

                $(container).find('.maxprice').append($('<option />').attr('value', '').html('Max. Price'));
                $(data.max_price.options).each(function () {
                    $(container).find('.maxprice').append(
                        $('<option />').attr('value', this.value).html(this.label)
                    );
                });
            })
            .always(function () {
                $(container).find('.pricerange').each(function () {
                        $(this).prop('disabled', false);
                    });
                $('.' + loaderClass).hide();
            });

	        $.ajax( {
	            url: wolfnet_ajax.ajaxurl,
	            data: { action: 'wolfnet_saved_searches', keyid: keyid },
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
	                var options = buildSavedSearchDropdownOptions(data);
	                $(container).find('.savedsearch').html('');
	                $(container).find('.savedsearch').append($('<option />').html('-- Saved Search --'));
	                $(options).each(function () {
	                    $(container).find('.savedsearch').append(this);
	                });
	            }
	        } );

	        $.ajax( {
	            url: wolfnet_ajax.ajaxurl,
	            data: { action: 'wolfnet_map_enabled', keyid: keyid },
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
	                if (data === true) {
	                    $(container).find('.mapDisabled').css('display', 'none');
	                    $(container).find('.maptype').removeAttr('disabled');
	                } else {
	                    $(container).find('.mapDisabled').css('display', 'block');
	                    $(container).find('.maptype').attr('disabled', 'true');
	                }
	            }
	        } );

	        var buildSavedSearchDropdownOptions = function (data)
	        {
	            var options = [];
	            $(data).each(function () {
	                options.push(
	                    $('<option />').attr('value', this.ID).html(this.post_title)
	                );
	            });
	            return options;
	        };

	    }

	} )( jQuery ); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
