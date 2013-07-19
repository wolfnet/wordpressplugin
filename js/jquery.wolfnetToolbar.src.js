
/**
 * Plugin for pagination tools and results toolbar.
 * Results toolbar contains:
 *      -Sort dropdown
 *      -Results count display (i.e. "Results 1-XX of XXXX")
 *      -Show XX per page dropdown
 *
 * Pagination tools contains Previous/Next (only if enabled via admin)
 */

if ( typeof jQuery != 'undefined' ) {

    (function ($) {

        var pluginName = 'wolfnetToolbar';

        var defaultOptions = {
            sort        : '',
            max_results : 250,
            criteria    : {}
            };
        var options        = $.extend( defaultOptions, options );
        var datakey        = 'wolfnet.toolbarData';


        var priceFormatter = function ( number )
        {
            var number = number.toString();
            var dollars = number.split('.')[0];
            var dollars = dollars
                .split('')
                .reverse()
                .join('')
                .replace(/(\d{3}(?!$))/g, '$1,')
                .split('')
                .reverse()
                .join('');

            return '$' + dollars;

        } // end method priceFormatter


        var getLastPageNum = function ( numrows, rowcount )
        {
            return Math.ceil( Number(rowcount) / Number(numrows) );
        } // end method getLastPageNum


        var renderPaginationControls = function ()
        {

            return $('<span>')
                .addClass('wolfnet_page_items_select')
                .append($('<select>')
                    .change(function (event){
                        $(this).trigger('wolfnet.itemsPerPage', [$(this).val(), this]);
                        event.preventDefault();
                        return false;
                    })
                    .append(loadPageOptions.call(this))
                )
                .append('per page');

        }


        var loadPageOptions = function ()
        {
            var $container = $(this);
            // This method could easily be refactored to pull the data in statically rather than with Ajax.

            // Wait until the toolbars have been added to the DOM.
            $container.bind("wolfnet.toolbarsRendered", function () {

                var $select = $container.find('.wolfnet_page_items_select select');
                var state   = $container.data('state');

                $.ajax({
                    url      : wolfnet_ajax.ajaxurl,
                    data     : { action:'wolfnet_listings_per_page' },
                    dataType : 'json'
                })
                .done(function (data){

                    // Clear out any existing options.
                    $select.children().remove();

                    // If the 'default' value is not in the data set we need to add it.
                    if ( $.inArray( state.numrows, data ) == -1 ) {
                        var newData = [];
                        var defaultUsed = false;
                        for ( var i=0; i<data.length; i++ ) {
                            if ( !defaultUsed && data[i] > state.numrows ) {
                                newData[newData.length] = state.numrows;
                                defaultUsed = true;
                            }
                            newData[newData.length] = data[i];
                        }
                        data = newData;
                    }

                    // Add an option to the select element for each item in the array.
                    for ( var key=0; key<data.length; key++ ) {
                        var $option = $('<option>', {value:data[key],text:data[key]} );
                        if ( data[key] == state.numrows ) {
                            $option.attr( 'selected', 'selected' );
                        }
                        $select.append( $option );
                    }

                });

            });

            return '';

        }


        var renderSortControls = function ()
        {

            return $('<span>')
                .addClass('wolfnet_sortoptions')
                .append($('<select>')
                    .change(function ( event ){
                        $(this).trigger('wolfnet.sortChange', [$(this).val(), this] );
                        event.preventDefault();
                        return false;
                    })
                    .append(loadSortOptions.call(this))
                );

        }


        var loadSortOptions = function ()
        {
            var $container = $(this);
            // This method could easily be refactored to pull the data in statically rather than with Ajax.

            // Wait until the toolbars have been added to the DOM.
            $container.bind("wolfnet.toolbarsRendered", function () {

                var $select = $container.find('.wolfnet_sortoptions select');
                var state   = $container.data('state');

                $.ajax({
                    url      : wolfnet_ajax.ajaxurl,
                    data     : { action:'wolfnet_sort_options' },
                    dataType : 'json'
                })
                .done( function ( data ) {

                    // Clear out any existing options.
                    $select.children().remove();

                    for ( var key=0; key<data.length; key++ ) {
                        $select.append(
                            $('<option>', {value:data[key].value,text:data[key].label} )
                        );
                    }

                });

            });

            return '';

        }


        var loadDataEventHandler = function ( event, target )
        {
            var $container = $(this);
            var state      = $container.data('state');

            // If data is not already be refreshed attempt to do so.
            if ( !state.refreshing ) {
                var options = $container.data(datakey);
                var data    = $.extend(options.criteria, state);

                delete data.criteria;
                delete data.ownertypes;
                delete data.prices;

                for (var i in data) {
                    var isNameField  = (i.indexOf('_wpname') !== -1);
                    var isIdField    = (i.indexOf('_wpid') !== -1);
                    var isCheckField = (i.indexOf('_wpc') !== -1);
                    var isSelecField = (i.indexOf('_wps') !== -1);

                    if (isNameField || isIdField || isCheckField || isSelecField) {
                        delete data[i];
                    }
                }

                data.ownertype = options.ownertype;
                data.action = 'wolfnet_get_listings';

                // Make Ajax call to retrieve data.
                $.ajax({
                    url      : wolfnet_ajax.ajaxurl,
                    dataType : 'json',
                    data     : data,
                    beforeSend : function () {
                        state.refreshing = true;
                        $container.find('.wolfnet_listings').addClass('wolfnet_refreshing');
                    }
                })
                .done(function ( data ) {
                    // Notify the container that the data has been loaded and pass the data to any handlers.
                    $container.trigger( 'wolfnet.dataLoaded', [data,target] );
                })
                .always(function () {
                    state.refreshing = false;
                    $container.find('.wolfnet_listings').removeClass('wolfnet_refreshing');
                });

            }

        }


        var prevPageEventHandler = function ( event, target )
        {
            var state    = $(this).data('state');
            var newStart = Number(state.startrow) - Number(state.numrows);

            if ( newStart < 1) {
                newStart = state.max_results - state.numrows + 1;
            }

            if ( newStart < 1 ) {
                newStart = state.startrow;
            }

            // if there is a prev page update state data
            if ( !state.refreshing && newStart >= 1 ) {
                state.startrow = newStart;
                $(this).data( 'state', state );
                // trigger a data refresh.
                $(this).trigger('wolfnet.refreshData', target);
            }

        }


        var nextPageEventHandler = function ( event, target )
        {
            var state    = $(this).data('state');
            var newStart = Number(state.startrow) + Number(state.numrows);

            if (newStart >= state.max_results) {
                newStart = 1;
            }

            // if there is a next page update state data
            if ( !state.refreshing && newStart <= state.max_results ) {
                state.startrow = newStart;
                $(this).data( 'state', state );
                // trigger a data refresh.
                $(this).trigger('wolfnet.refreshData', [target] );
            }

        }


        var itemsPerPageEventHandler = function ( event, value, target )
        {
            var state = $(this).data('state');

            // if the value is acceptable update state data
            if ( !state.refreshing && value <= state.max_results ) {

                state.numrows  = Number(value);
                state.startrow = 1;

                $(this).data( 'state', state );

                // update all related input controls
                $(this).find('.wolfnet_page_items_select select').val(value);

                // trigger a data refresh.
                $(this).trigger('wolfnet.refreshData', [target]);

            }

        }


        var sortChangeEventHandler = function ( event, value, target )
        {
            var state = $(this).data('state');

            // if the value is acceptable update state data
            if ( !state.refreshing && value != state.sort ) {

                state.sort = value;
                $(this).data( 'state', state );

                // update all related input controls
                $(this).find('.wolfnet_sortoptions select').val(value);

                // trigger a data refresh.
                $(this).trigger('wolfnet.refreshData', [target]);

            }

        }


        var dataLoadedEventHandler = function ( event, data, target )
        {
            var $container = $(this);
            var state      = $container.data('state');
            var startrow   = state.startrow;
            var numrows    = state.numrows;
            var sortBy     = state.sort;

            // Clear pre-existing items
            $container.find('.wolfnet_listing').remove();
            $container.find('.wolfnet_clearfix').remove();

            // Render Listing Grid Items
            if ( $container.hasClass('wolfnet_listingGrid') ) {
                buildListingGrid.call( $container, data );
            }
            // Render Property List Items
            else if ( $container.hasClass('wolfnet_propertyList') ) {
                buildPropertyList.call( $container, data );
            }

            // Update results count display
            var rowcountDisplay = (Number(startrow) - 1) + Number(numrows);

            // Update page information
            $container.find('.wolfnet_page_start').text(startrow);
            $container.find('.wolfnet_page_end').text(rowcountDisplay);

            // clear show # select's options' selected attributes and update
            $container.find('.wolfnet_page_items_select select').val( numrows );

            // clear the sort option's selected attributes and update
            $container.find('.wolfnet_sortoptions select').val( sortBy );

            $(this).trigger('wolfnet.listingsRendered',[target]);

        }


        var buildListingGrid = function ( data )
        {
            var $container = $(this);
            var $listings  = $container.find('.wolfnet_listings:first').clone();
            var state      = $container.data('state');
            var numrows    = state.numrows;
            var sortBy     = state.sort;

            //START:  loop to rebuild listing grid dom (listingGrid uses listingSimple.php template)
            for (var i=0; i<data.length; i++) {

                var brokerLogo  = data[i].branding.brokerLogo  || null;
                var brokerName  = data[i].branding.content || null;
                var cityState   = data[i].city + ', ' + data[i].state;
                var fullAddress = data[i].display_address + ', ' + cityState;
                var hasBranding = ( brokerLogo == null && brokerName == null ) ? false : true ;

                var listingEntity = $('<div>')
                    .addClass('wolfnet_listing')
                    .addClass( (hasBranding) ? 'wolfnet_branded' : '' )
                    .attr('id','wolfnet_listing_'+data[i].property_id)
                    .appendTo($listings);

                var link = $('<a>')
                    .attr('href',data[i].property_url)
                    .appendTo(listingEntity);

                var listingImageSpan = $('<span>')
                    .addClass('wolfnet_listingImage')
                    .appendTo(link);

                var listingImgSrc = $('<img>')
                    .attr('src',data[i].thumbnail_url)
                    .appendTo(listingImageSpan);

                var price = $('<span>')
                    .addClass('wolfnet_price')
                    .attr('itemprop','price')
                    .html( priceFormatter(data[i].listing_price) )
                    .appendTo(link);

                var bedbath = $('<span>')
                    .addClass('wolfnet_bed_bath')
                    .attr('title',data[i].bedrooms+' Bedrooms & '+data[i].bathroom+' Bathrooms')
                    .html( data[i].bedrooms + 'bd/' + data[i].bathroom + 'ba' )
                    .appendTo(link);

                var location = $('<span>')
                    .attr('title',fullAddress)
                    .append(
                        $('<span>').addClass('wolfnet_location')
                            .attr('itemprop','locality')
                            .html(cityState)
                    )
                    .append(
                        $('<span>').addClass('wolfnet_address')
                        .html(data[i].display_address)
                    )
                    .append(
                        $('<span>').addClass('wolfnet_full_address')
                            .attr('itemprop','street_address')
                            .css('display','none')
                            .html(fullAddress)
                    )
                    .appendTo(link);

                if (hasBranding) {

                    var branding = $('<div>')
                        .addClass('wolfnet_branding')
                        .insertAfter(location);

                    if ( brokerLogo != null ) {

                        $('<span>').addClass('wolfnet_brokerLogo')
                            .append( $('<img>').attr('src',brokerLogo) )
                            .appendTo(branding);

                    }

                    if ( brokerName != null ) {

                        $('<span>').addClass('wolfnet_brandingMessage')
                            .html(brokerName)
                            .appendTo(branding);

                    }

                }

            }//END: loop to rebuild listing grid dom

            $container.find('.wolfnet_listings:first').replaceWith( $listings );

            $container.wolfnetListingGrid('reload');

        }


        var buildPropertyList = function ( data )
        {
            var $container = $(this);
            var $listings  = $container.find('.wolfnet_listings:first').clone();
            var state      = $container.data('state');
            var numrows    = state.numrows;
            var sortBy     = state.sort;

            //START:  rebuild property list dom (propertyList uses listingBrief.php)
            //loop listings in data object and build new listing entity to append to dom
            for ( var i=0; i<data.length; i++ ) {

                var cityState   = data[i].city + ', ' + data[i].state;
                var fullAddress = data[i].display_address + ', ' + cityState;

                var listingEntity = $('<div>')
                    .addClass('wolfnet_listing')
                    .attr('id','wolfnet_listing_' + data[i].property_id)
                    .appendTo($listings);

                var link = $('<a>')
                    .attr({'href':data[i].property_url,'title':fullAddress})
                    .appendTo(listingEntity);

                var location = $('<span>')
                    .addClass('wolfnet_full_address')
                    .html(fullAddress)
                    .appendTo(link);

                var price = $('<span>')
                    .addClass('wolfnet_price')
                    .attr('itemprop','price')
                    .html( priceFormatter(data[i].listing_price) )
                    .appendTo(link);

                var streetAddress = $('<span>')
                    .attr('itemprop','street-address')
                    .css('display','none')
                    .html(fullAddress)
                    .appendTo(link);

            }//END: rebuild property list DOM

            $container.find('.wolfnet_listings:first').replaceWith( $listings );

            $container.wolfnetPropertyList();

        }


        var listingsRenderedEventHandler = function ( event, target )
        {
            var $container = $(this);
            var state      = $container.data('state');

            if ( state.startrow - state.numrows < 1) {
                $container.find('a.wolfnet_page_nav_prev').addClass('wolfnet_disabled');
            }
            else {
                $container.find('a.wolfnet_page_nav_prev').removeClass('wolfnet_disabled');
            }

            if ( state.startrow + state.numrows > state.max_results ) {
                $container.find('a.wolfnet_page_nav_next').addClass('wolfnet_disabled');
            }
            else {
                $container.find('a.wolfnet_page_nav_next').removeClass('wolfnet_disabled');
            }

        }


        var methods = {

            init : function (options){

                return this.each(function () {

                    var $listingContainer = $(this);
                    var $toolbar          = $listingContainer.find('.wolfnet_toolbar');

                    $listingContainer.data(datakey, options);

                    var stateData = {
                        refreshing  : false,
                        sort        : options.sort,
                        ownertype   : options.ownertype,
                        max_results : options.max_results,
                        numrows     : Number($toolbar.data('numrows')) || options.numrows,
                        startrow    : Number($toolbar.data('startrow')) || options.startrow,
                        criteria    : options.criteria
                        };

                    $listingContainer.data('state', stateData);

                    // Bind events on the container.
                    $listingContainer.bind('wolfnet.nextPage', nextPageEventHandler);
                    $listingContainer.bind('wolfnet.prevPage', prevPageEventHandler);
                    $listingContainer.bind('wolfnet.itemsPerPage', itemsPerPageEventHandler);
                    $listingContainer.bind('wolfnet.sortChange', sortChangeEventHandler);
                    $listingContainer.bind('wolfnet.refreshData', loadDataEventHandler);
                    $listingContainer.bind('wolfnet.dataLoaded', dataLoadedEventHandler);
                    $listingContainer.bind('wolfnet.listingsRendered', listingsRenderedEventHandler);

                    // If the toolbar is to be used for pagination wire it up to make ajax requests.
                    if ($toolbar.is('.wolfnet_withPagination')) {

                        $toolbar.find('a.wolfnet_page_nav_prev').click(function(event){
                            $listingContainer.trigger('wolfnet.prevPage');
                            event.preventDefault();
                            return false;
                        });

                        $toolbar.find('a.wolfnet_page_nav_next').click(function(event){
                            $listingContainer.trigger('wolfnet.nextPage');
                            event.preventDefault();
                            return false;
                        });

                        $toolbar.find('.wolfnet_page_items').append(renderPaginationControls.call($listingContainer[0]));

                        $toolbar.filter('.wolfnet_toolbarBottom').find('a.wolfnet_page_nav').click(function(){
                            $('html,body').scrollTop( $(this).closest('.wolfnet_widget').offset().top - 100 );
                        });

                        $toolbar.filter('.wolfnet_toolbarBottom').find('.wolfnet_page_items_select select').change(function(){
                            $('html,body').scrollTop( $(this).closest('.wolfnet_widget').offset().top - 100 );
                        });

                    }

                    // If the toolbar is to be used for sorting add the sorting control.
                    if ($toolbar.is('.wolfnet_withSortOptions')) {
                        var $sortControls = renderSortControls.call($listingContainer);
                        var $pageInfo     = $toolbar.find('.wolfnet_page_info');

                        if ($pageInfo.length != 0) {
                            $pageInfo.append($sortControls);
                        }
                        else {
                            $toolbar.append($sortControls);
                        }

                        $toolbar.filter('.wolfnet_toolbarBottom').find('.wolfnet_sortoptions select').change(function(){
                            $('html,body').scrollTop( $(this).closest('.wolfnet_widget').offset().top - 100 );
                        });

                    }

                    $listingContainer.trigger('wolfnet.toolbarsRendered');

                });

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

    })(jQuery); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
