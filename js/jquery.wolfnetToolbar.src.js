/**
 *
 * @title         wolfnetToolbar.src.js
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
 */

if ( typeof String.prototype.wolfnetPriceFormat !== 'function' ) {
    String.prototype.wolfnetPriceFormat = function() {
        var number = this.valueOf();
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
    }
}

(function($){

    var plugin = 'wolfnetToolbar';

    var stateKey = plugin + '.state';
    var optionsKey = plugin + '.options';

    var UPDATED = 'wolfnet.updated';

    var nextClass  = 'a.wolfnet_page_nav_next';
    var prevClass  = 'a.wolfnet_page_nav_prev';
    var itemsClass = 'wolfnet_page_items_select';
    var sortClass  = 'wolfnet_sortoptions';

    var defaultOptions = {
        sort             : 'price_desc',
        maxResults       : 250,
        criteria         : {},
        itemsPerPageData : [5,10,15,20,25,30,35,40,45,50],
        sortOptionsData  : [
            {value:'price_desc', label:'Descending by Price'},
            {value:'price', label:'Ascending by Price'}
            ]
        };

    var renderPropertyList = function(data)
    {
        var data = ($.isArray(data)) ? data : [];
        var $container = this;
        var $listings = $('<div>').addClass('wolfnet_listings');

        for (var i=0, l=data.length; i<l; i++) {
            var cityState   = data[i].city + ', ' + data[i].state;
            var fullAddress = data[i].display_address + ', ' + cityState;

            var $listing = $('<div>')
                .attr('id', 'wolfnet_listing_' + data[i].property_id)
                .addClass('wolfnet_listing')
                .appendTo($listings);

            var $link = $('<a>')
                .attr({'href':data[i].property_url, 'title':fullAddress})
                .appendTo($listing);

            var $location = $('<span>')
                .addClass('wolfnet_full_address')
                .html(fullAddress)
                .appendTo($link);

            var $price = $('<span>')
                .addClass('wolfnet_price')
                .attr('itemprop', 'price')
                .html(data[i].listing_price.toString().wolfnetPriceFormat())
                .appendTo($link);

            var $streetAddress = $('<span>')
                .attr('itemprop', 'street-address')
                .css('display', 'none')
                .html(fullAddress)
                .appendTo($link);

        }

        $container.find('.wolfnet_listings').replaceWith($listings);

    }

    var renderListingGrid = function(data)
    {
        var data = ($.isArray(data)) ? data : [];
        var $container = this;
        var $listings = $('<div>').addClass('wolfnet_listings');

        for (var i=0, l=data.length; i<l; i++) {
            var brokerLogo  = data[i].branding.brokerLogo  || null;
            var brokerName  = data[i].branding.content || null;
            var cityState   = data[i].city + ', ' + data[i].state;
            var fullAddress = data[i].display_address + ', ' + cityState;
            var hasBranding = (brokerLogo == null && brokerName == null) ? false : true ;

            var $listing = $('<div>')
                .addClass('wolfnet_listing')
                .addClass( (hasBranding) ? 'wolfnet_branded' : '' )
                .attr('id', 'wolfnet_listing_' + data[i].property_id)
                .appendTo($listings);

            var $link = $('<a>')
                .attr('href',data[i].property_url)
                .appendTo($listing);

            var $imageContainer = $('<span>')
                .addClass('wolfnet_listingImage')
                .appendTo($link);

            var $image = $('<img>')
                .attr('src',data[i].thumbnail_url)
                .appendTo($imageContainer);

            var $price = $('<span>')
                .addClass('wolfnet_price')
                .attr('itemprop', 'price')
                .html(data[i].listing_price.toString().wolfnetPriceFormat())
                .appendTo($link);

            var $bedBath = $('<span>')
                .addClass('wolfnet_bed_bath')
                .attr('title', data[i].bedrooms + ' Bedrooms & ' + data[i].bathroom + ' Bathrooms')
                .appendTo($link);

                if (data[i].bedrooms != '' && data[i].bedrooms != 'n/a') {
                    $bedBath.append(data[i].bedrooms + 'bd');
                }

                if (data[i].bathroom != '' && data[i].bathroom != 'n/a') {
                    if ($bedBath.text() != '') {
                        $bedBath.append('/');
                    }
                    $bedBath.append(data[i].bathroom + 'ba');
                }

            var $locationContainer = $('<span>')
                .attr('title', fullAddress)
                .appendTo($link);

            var $address = $('<span>')
                .addClass('wolfnet_address')
                .html(data[i].display_address)
                .appendTo($locationContainer);

            var $location = $('<span>')
                .addClass('wolfnet_location')
                .attr('itemprop', 'locality')
                .html(cityState)
                .appendTo($locationContainer);

            var $fullAddress = $('<span>')
                .addClass('wolfnet_full_address')
                .attr('itemprop', 'street_address')
                .css('display', 'none')
                .html(fullAddress)
                .appendTo($locationContainer);

            if (hasBranding) {

                var $brandingContainer = $('<div>')
                    .addClass('wolfnet_branding')
                    .insertAfter($locationContainer);

                if (brokerLogo != null) {

                    var $brokerLogo = $('<span>')
                        .addClass('wolfnet_brokerLogo')
                        .append($('<img>').attr('src',brokerLogo))
                        .appendTo($brandingContainer);

                }

                if (brokerName != null) {

                    var $brokerName = $('<span>')
                        .addClass('wolfnet_brandingMessage')
                        .html(brokerName)
                        .appendTo($brandingContainer);

                }

            }

        }

        $container.find('.wolfnet_listings').replaceWith($listings);

    }


    var getBedBath = function(bath,bed)
    {
        var bedBathString = '';

        if (bed != '' && bed != 'n/a') {
            bedBathString = bed + 'bd';
        }
        if (bath != '' && bath != 'n/a') {
            if (bedBathString != '') {
                bedBathString += '/';
            }
            bedBathString += bath + 'ba';
        }

        return bedBathString;
    }


    //replicating building of html dom in wolfnet.php, function: getHouseoverHtml
    var getHouseoverHtml = function(listing)
    {          
        var concatHouseover = '';
        var bed_bath = getBedBath(listing.bathroom,listing.bedrooms);

        concatHouseover += '<a style="display:block" rel="follow" href=" ' + listing.property_url + '">';
        concatHouseover += '<div class="wolfnet_wntHouseOverWrapper"><div data-property-id="' + listing.property_id;
        concatHouseover += '" class="wolfnet_wntHOItem"><table class="wolfnet_wntHOTable"><tbody><tr>';
        concatHouseover += '<td class="wntHOImgCol" valign="top" style="vertical-align:top;"><div class="wolfnet_wntHOImg">';
        concatHouseover += '<img src="' + listing.thumbnail_url + '""></div><div class="wolfnet_wntHOBroker" style="text-align: center">';
        concatHouseover += '<img class="wolfnet_wntHOBrokerLogo" src="' + listing.branding.brokerLogo + '" alt="Broker Reciprocity">';
        concatHouseover += '</div></td><td valign="top" style="vertical-align:top;"><div class="wolfnet_wntHOContentContainer">';
        concatHouseover += '<div style="text-align:left;font-weight:bold">' + listing.listing_price.toString().wolfnetPriceFormat() + '</div>';
        concatHouseover += '<div style="text-align:left;">' + listing.display_address + '</div><div style="text-align:left;">';
        concatHouseover += listing.city + ', ' + listing.state + '</div><div style="text-align:left;">' + bed_bath;
        concatHouseover += '</div><div style="text-align:left;padding-top:20px;">' + listing.branding.content + '</div>';
        concatHouseover += '</div></td></tr></tbody></table></div></div></a>';

        return concatHouseover;
    }


    var populateMap = function(data)
    {
        var data = ($.isArray(data)) ? data : [];
        var $container = this;
        var componentMap = $container.find('.wolfnet_wntMainMap').data('map');
        var houseIcon = wolfnet_ajax.houseoverIcon;

        componentMap.removeAllShapes();

        for (var i=0, l=data.length; i<l; i++) {
            houseoverHtml = getHouseoverHtml(data[i]);
            var houseoverIcon = componentMap.mapIcon(houseIcon,30,30);
            var houseover = componentMap.poi(data[i].lat, data[i].lng, houseoverIcon, houseoverHtml);
            componentMap.addPoi(houseover);
        }  

        componentMap.bestFit();        
    }


    // Take the data returned from an Ajax request and use it to render listings.
    var renderListings = function(data)
    {
        var $container = this;

        if ($container.is('.wolfnet_propertyList') || $container.is('.wolfnet_listingGrid')) {
            $container.find('.wolfnet_listings').children().remove();
        }

        if ($container.is('.wolfnet_propertyList')) {
            renderPropertyList.call($container, data);
        }
        else if ($container.is('.wolfnet_listingGrid')) {
            renderListingGrid.call($container, data);
        }

        if ($container.find('.wolfnet_wntMainMap').length > 0) {
            populateMap.call($container, data);
        }

        $container.trigger(UPDATED);

    };

    // Determine if page tools should be rendered and do so if they do.
    var renderItemsPerPageTool = function()
    {
        var $container = $(this);
        var $itemsDropDown = $container.find('span.' + itemsClass);
        var state = $container.data(stateKey);
        var options = $container.data(optionsKey);

        // If there is no dropdown and there should be, create one.
        if ($itemsDropDown.length == 0 && $container.is('.wolfnet_withPagination')) {
            var $select = $('<select>');

            // Register change event handler to trigger an update when the tool is changed.
            $select.change(function(){
                state.numrows = $(this).val();
                $container.data(stateKey, state);
                $container.find('span.' + itemsClass + ' select').val(state.numrows);
                methods.update.call($container);
            });

            for (var i=0,l=options.itemsPerPageData.length; i<l; i++) {
                var items = options.itemsPerPageData[i];

                $('<option>').attr('value', items).text(items).appendTo($select);

            }

            $itemsDropDown = $('<span>').addClass(itemsClass);
            $itemsDropDown.append($select).append('per page');
            $itemsDropDown.appendTo($container.find('.wolfnet_toolbar .wolfnet_page_items'));

        }

        $container.find('span.' + itemsClass + ' select').each(function(){
            $(this).val(Number(state.numrows));
        });

    };

    // Determine if sort tools should be rendered and do so if they do.
    var renderSortOptionsTool = function()
    {
        var $container = $(this);
        var $sortDropDown = $container.find('span.' + sortClass + ' select');
        var state = $container.data(stateKey);
        var options = $container.data(optionsKey);

        // If there is no dropdown and there should be, create one.
        if ($sortDropDown.length == 0 && $container.is('.wolfnet_withSortOptions')) {
            var $select = $('<select>');

            // Register change event handler to trigger an update when the tool is changed.
            $select.change(function(){
                state.sort = $(this).val();
                $container.data(stateKey, state);
                $container.find('span.' + sortClass + ' select').val(state.sort);
                methods.update.call($container);
            });

            for (var i=0,l=options.sortOptionsData.length; i<l; i++) {
                var sort = options.sortOptionsData[i];

                $('<option>').text(sort.label).attr('value', sort.value).appendTo($select);

            }

            $sortDropDown = $('<span>').addClass(sortClass);
            $sortDropDown.append($select);
            $sortDropDown.appendTo($container.find('.wolfnet_toolbar .wolfnet_page_info'));

        }

        $container.find('span.' + sortClass + ' select').each(function(){
            $(this).val(state.sort);
        });

    };

    var updateEvent = function(event)
    {
        var $container = $(this);
        var $nextBtn = $container.find(nextClass);
        var $prevBtn = $container.find(prevClass);
        var state = $container.data(stateKey);
        var options = $container.data(optionsKey);
        var maxPage = Math.ceil(options.maxResults / state.numrows);

        if (state.page <= 1) {
            $prevBtn.addClass('wolfnet_disabled');
        }
        else {
            $prevBtn.removeClass('wolfnet_disabled');
        }

        if (state.page >= maxPage) {
            $nextBtn.addClass('wolfnet_disabled');
        }
        else {
            $nextBtn.removeClass('wolfnet_disabled');
        }

        // Update results count display
        var rowcountDisplay = (Number(state.startrow) - 1) + Number(state.numrows);

        if (Number(options.maxResults) < rowcountDisplay) {
            rowcountDisplay = Number(options.maxResults);
        }

        // Update page information
        $container.find('.wolfnet_page_start').text(state.startrow);
        $container.find('.wolfnet_page_end').text(rowcountDisplay);

        $('html,body').scrollTop($container.closest('.wolfnet_widget').offset().top - 100);

        if ($container.is('.wolfnet_listingGrid') && $container.wolfnetListingGrid) {
            $container.wolfnetListingGrid("resizeWidth");
        }

        $container.removeClass('wolfnet_refreshing');

        return true;

    };

    var methods = {

        // Initialize the plugin.  All of this code runs once per page request.
        init : function(options)
        {
            return this.each(function() {
                var $container = $(this);
                var opts = $.extend(true, {}, defaultOptions, options);
                var state = $.extend(true, {}, opts.criteria, opts, {page:1});

                delete opts.criteria;
                delete state.criteria;
                delete state.prices;
                delete state.ownertypes;
                delete state.savedsearches;

                for (var i in state) {
                    var isNameField  = (i.indexOf('_wpname') !== -1);
                    var isIdField    = (i.indexOf('_wpid') !== -1);
                    var isCheckField = (i.indexOf('_wpc') !== -1);
                    var isSelecField = (i.indexOf('_wps') !== -1);

                    if (isNameField || isIdField || isCheckField || isSelecField) {
                        delete state[i];
                    }

                }

                state.maxresults = opts.maxResults;

                if ($.inArray(Number(state.numrows), opts.itemsPerPageData) === -1) {
                    opts.itemsPerPageData.push(Number(state.numrows));
                }
                opts.itemsPerPageData.sort(function(a,b){
                    return a - b;
                });

                $container.data(optionsKey, opts);
                $container.data(stateKey, state);

                renderItemsPerPageTool.call($container);
                renderSortOptionsTool.call($container);

                $container.click(function(event){
                    var $target = $(event.target);

                    if ($target.is(nextClass) || $target.parent().is(nextClass)) {
                        event.preventDefault();
                        methods.next.call($container);
                        return false;
                    }
                    else if ($target.is(prevClass) || $target.parent().is(prevClass)) {
                        event.preventDefault();
                        methods.prev.call($container);
                        return false;
                    }

                    return true;

                });

                $container.on(UPDATED, updateEvent);

            });

        },

        // Perform Ajax request using the state data then update the listings content
        update : function()
        {
            return this.each(function() {
                var $container = $(this);
                var state = $container.data(stateKey);

                var getData = function() {
                    var data = $.extend(state, {});
                    delete data.itemsPerPageData;
                    delete data.sortOptionsData;
                    return $.extend(state, {action:'wolfnet_get_listings'});
                };

                // perform ajax request
                $.ajax({
                    url : wolfnet_ajax.ajaxurl,
                    dataType : 'jsonp',
                    data : getData(),
                    beforeSend: function(xhr){
                        $container.addClass('wolfnet_refreshing');
                    }
                })
                // success: update listings
                .done(function(data, textStatus, xhr){
                    renderListings.call($container, data);
                })
                // failure: indicate failure to user
                .fail(function(xhr, textStatus){
                    $container.removeClass('wolfnet_refreshing');
                })
                .always(function(){
                });

            });

        },

        // Update state data with new page and then call the update method to retrieve new data.
        next : function()
        {
            return this.each(function() {
                var $container = $(this);
                var state = $container.data(stateKey);
                var newPage = state.page + 1;

                methods.page.call($container, newPage);

            });

        },

        // Update state data with new page and then call the update method to retrieve new data.
        prev : function()
        {
            return this.each(function() {
                var $container = $(this);
                var state = $container.data(stateKey);
                var newPage = state.page - 1;

                methods.page.call($container, newPage);

            });

        },

        // Update the state data to a specific page number.
        page : function(page)
        {
            return this.each(function() {
                var $container = $(this);
                var options = $container.data(optionsKey);
                var state = $container.data(stateKey);
                var maxPage = Math.ceil(options.maxResults / Number(state.numrows));
                var newPage = page;

                // Ensure that only valid pages can be requested.
                if (newPage < 1) {
                    newPage = maxPage;
                }
                else if (newPage > maxPage) {
                    newPage = 1;
                }
                var newStartRow = ((newPage - 1) * Number(state.numrows)) + 1;

                // update the current page.
                $container.data(stateKey, $.extend(state, {page:newPage, startrow:newStartRow}));

                methods.update.call($container);

            });

        }

    };

    $.fn[plugin] = function(method)
    {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        }
        else {
            $.error('Method ' +  method + ' does not exist on jQuery.' + pluginName);
        }

    };

})(jQuery);
