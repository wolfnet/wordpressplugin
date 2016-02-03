/**
 *
 * @title         wolfnetToolbar.src.js
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
 */
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

    var escapeHtml = function(text)
    {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function (m) { return map[m]; });

    };

    var renderPropertyList = function(data)
    {

        data = ($.isArray(data.responseData.data.listing)) ? data.responseData.data.listing : [];

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

            var $price = $('<span>')
                .addClass('wolfnet_price')
                .attr('itemprop', 'price')
                .html(data[i].listing_price.toString())
                .appendTo($link);

            var $location = $('<span>')
                .addClass('wolfnet_full_address')
                .attr('itemprop', 'street-address')
                .html(fullAddress)
                .appendTo($link);

        }

        $container.find('.wolfnet_listings').replaceWith($listings);

    };

    var getBedBathHTML = function(listing)
    {
        var bedBathHTML = '';

        var totalBeds = 0;
        if (listing.total_bedrooms !== '') {
            totalBeds += parseInt(listing.total_bedrooms);
        }

        var totalBaths = 0;
        if (listing.total_baths !== '') {
            totalBaths += parseInt(listing.total_baths);
        }

        if (totalBeds + totalBaths > 0) {
            bedBathHTML = (
                '<span class="wolfnet_bed_bath" title="' + escapeHtml(listing.bedsbaths_full) + '">' +
                (
                    totalBeds > 0 ? (
                        '<span class="wolfnet_beds">' +
                            totalBeds + ' ' + '<span class="wolfnet_label">Bedrooms</span>' +
                        '</span> ' +
                        (totalBaths > 0 ? '<span class="wolfnet_info_separator"></span> ' : '')
                    ) : ''
                ) +
                (
                    totalBaths > 0 ? (
                        '<span class="wolfnet_baths">' +
                            totalBaths + ' ' + '<span class="wolfnet_label">Bathrooms</span>' +
                        '</span> '
                    ) : ''
                ) +
                '</span> '
            );
        }

        return bedBathHTML;

    }

    var getBrandingHTML = function(listing)
    {
        var brandingHTML = '';

        if (listing.hasOwnProperty('branding')) {
            brandingHTML = (
                getBrandingLogoHTML(listing.branding) +
                '<span class="wolfnet_brandingMessage">' +
                    getBrandingMessageHTML(listing.branding) +
                '</span> '
            );
        }

        return brandingHTML;

    };

    var getBrandingLogoHTML = function(branding)
    {
        var brandingLogoHTML = '';

        if ($.trim(branding.logo || '') !== '') {
            var isIdx = (branding.type || '') === 'idx';
            brandingLogoHTML = (
                '<span class="wolfnet_brokerLogo' + (isIdx ? ' wolfnet_idxLogo' : '') + '">' +
                    '<img src="' + branding.logo + '" />' +
                '</span> '
            );
        }

        return brandingLogoHTML;

    }

    var getBrandingMessageHTML = function(branding)
    {
        var brandingMessageHTML = '';
        var brandingFields = [
            { name: 'courtesy_text',   className: 'wolfnet_brandingCourtesyText' },
            { name: 'agent_name',      className: 'wolfnet_brandingAgent wolfnet_brandingAgentName' },
            { name: 'agent_phone',     className: 'wolfnet_brandingAgent wolfnet_brandingAgentPhone' },
            { name: 'office_name',     className: 'wolfnet_brandingOffice wolfnet_brandingOfficeName' },
            { name: 'office_phone',    className: 'wolfnet_brandingOffice wolfnet_brandingOfficePhone' },
            { name: 'toll_free_phone', className: 'wolfnet_brandingTollFreePhone' }
        ];

        for (var i=0, l=brandingFields.length; i<l; i++) {
            if (
                branding.hasOwnProperty(brandingFields[i].name)
                && ($.trim(branding[brandingFields[i].name]) !== '')
            ) {
                brandingMessageHTML += (
                    '<span class="' + brandingFields[i].className + '">' +
                        branding[brandingFields[i].name] +
                    '</span> '
                );
            }
        }

        return brandingMessageHTML;

    };

    var renderListingGrid = function(data)
    {

        data = ($.isArray(data.responseData.data.listing)) ? data.responseData.data.listing : [];

        var $container = this;
        var $listings = $('<div>').addClass('wolfnet_listings');

        for (var i=0, l=data.length; i<l; i++) {
            var brokerLogo    = data[i].branding.logo  || null;
            var brandingType  = data[i].branding.type || '';
            var cityState     = data[i].city + ', ' + data[i].state;
            var fullAddress   = data[i].display_address + ', ' + cityState;

            var $listing = $('<div>')
                .attr({
                    'id': 'wolfnet_listing_' + data[i].property_id,
                    'class': 'wolfnet_listing',
                    'itemscope': 'itemscope'
                })
                .html(
                   '<a href="' + data[i].property_url + '" rel="follow">' +
                        '<div class="wolfnet_listingMain">' +
                            '<div class="wolfnet_listingHead">' +
                                '<div class="wolfnet_listingImage">' +
                                    '<img src="' + data[i].thumbnail_url + '"' +
                                    ' alt="Property for sale at ' + data[i].address + '" />' +
                                '</div> ' +
                                '<div class="wolfnet_listingInfo"' +
                                ' title="' + escapeHtml(data[i].listing_price.toString()) + '">' +
                                    '<span class="wolfnet_price" itemprop="price">' +
                                        data[i].listing_price.toString() +
                                    '</span> ' +
                                    getBedBathHTML(data[i]) +
                                '</div>' +
                            '</div>' +
                        '</div> ' +
                        '<div class="wolfnet_locationInfo" title="' + escapeHtml(data[i].address) + '">' +
                            '<div class="wolfnet_address">' +
                                data[i].display_address +
                            '</div> ' +
                            '<div class="wolfnet_location" itemprop="locality">' +
                                data[i].location +
                            '</div> ' +
                            '<div class="wolfnet_full_address" itemprop="street-address" style="display: none;">' +
                                data[i].address +
                            '</div> ' +
                        '</div> ' +
                        '<div class="wolfnet_branding">' +
                            getBrandingHTML(data[i]) +
                        '</div>' +
                    '</a>'
                )
                .appendTo($listings);

        }

        $container.find('.wolfnet_listings').replaceWith($listings);

    };

    //replicating building of html dom in wolfnet.php, function: getHouseoverHtml
    var getHouseoverHtml = function(listing)
    {
        var concatHouseover = '';

        concatHouseover += '<div class="wolfnet_wntHouseOverWrapper">';
        concatHouseover += '    <a rel="follow" href="' + listing.property_url + '">';
        concatHouseover += '        <div data-property-id="' + listing.property_id + '" class="wntHOItem">';
        concatHouseover += '            <table class="wolfnet_wntHOTable"><tbody><tr>';
        concatHouseover += '                <td class="wntHOImgCol">';
        concatHouseover += '                    <div class="wolfnet_wntHOImg wolfnet_listingImage">';
        concatHouseover += '                        <img src="' + listing.thumbnail_url + '" />';
        concatHouseover += '                    </div>';
        if (listing.branding.logo !== '') {
            concatHouseover += '<div class="wolfnet_wntHOBroker wolfnet_brokerLogo' + (listing.branding.type === 'idx' ? ' wolfnet_idxLogo' : '') + '">';
            concatHouseover += '    <img src="' + listing.branding.logo + '" />';
            concatHouseover += '</span>';
        }
        concatHouseover += '                </td>';
        concatHouseover += '                <td>';
        concatHouseover += '                    <div class="wolfnet_wntHOContentContainer">';
        concatHouseover += '                        <div class="wolfnet_listingInfo">';
        concatHouseover += '                            <div class="wolfnet_price">' + listing.listing_price.toString() + '</div>';
        if (listing.total_bedrooms || listing.total_baths) {
            concatHouseover += '<span class="wolfnet_bed_bath">';
            if (listing.total_bedrooms) {
                concatHouseover += '<span class="wolfnet_beds">' + listing.total_bedrooms + '<span class="wolfnet_label">Bedrooms</span></span>';
                if (listing.total_baths) {
                    concatHouseover += '<span class="wolfnet_info_separator"></span>';
                }
            }
            if (listing.total_baths) {
                concatHouseover += '<span class="wolfnet_baths">' + listing.total_baths + '<span class="wolfnet_label">Bathrooms</span></span>';
            }
            concatHouseover += '</span>';
        }
        concatHouseover += '                        </div>';
        concatHouseover += '                        <div class="wolfnet_locationInfo">';
        concatHouseover += '                            <div class="wolfnet_address">' + listing.display_address + '</div>';
        concatHouseover += '                            <div class="wolfnet_location">' + listing.city + ', ' + listing.state + '</div>';
        concatHouseover += '                        </div>';
        concatHouseover += '                        <div class="wolfnet_branding" style="text-align: left; padding-top: 20px;">';
        concatHouseover += '                            <span class="wolfnet_brandingMessage">';
        concatHouseover += '                                <span class="wolfnet_brandingCourtesyText">' + listing.branding.courtesy_text + '</span>';
        concatHouseover += '                            </span>';
        concatHouseover += '                        </div>';
        concatHouseover += '                    </div>';
        concatHouseover += '                </td>';
        concatHouseover += '            </tr></tbody></table>';
        concatHouseover += '        </div>';
        concatHouseover += '    </a>';
        concatHouseover += '</div>';

        return concatHouseover;

    };

    var populateMap = function(data)
    {
        data = ($.isArray(data.responseData.data.listing)) ? data.responseData.data.listing : [];

        var $container = this;
        var componentMap = $container.find('.wolfnet_wntMainMap').data('map');
        var houseIcon = wolfnet_ajax.houseoverIcon;

        componentMap.removeAllShapes();

        for (var i=0, l=data.length; i<l; i++) {
            houseoverHtml = getHouseoverHtml(data[i]);
            var houseoverIcon = componentMap.mapIcon(houseIcon,20,20);
            var houseover = componentMap.poi(data[i].geo.lat, data[i].geo.lng, houseoverIcon, houseoverHtml, data[i].property_id, data[i].property_url);
            componentMap.addPoi(houseover);
        }

        componentMap.bestFit();

    };

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
            $container.wolfnetListingGrid('refresh');
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
        if ($itemsDropDown.length === 0 && $container.is('.wolfnet_withPagination')) {
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
        if ($sortDropDown.length === 0 && $container.is('.wolfnet_withSortOptions')) {
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
            $container.wolfnetListingGrid("refresh", true);
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

                    // alert(JSON.stringify(state));
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
