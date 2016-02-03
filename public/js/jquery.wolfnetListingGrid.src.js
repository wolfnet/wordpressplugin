/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which
 * will then be displayed in a grid format.
 *
 * @title         jquery.wolfnetListingGrid.js
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
if (typeof jQuery != 'undefined') {

    (function($, jQuery, window, document, undefined){

        var pluginName = 'wolfnetListingGrid';

        var defaultOptions = {
            containerClass: null,
            itemClass: 'item',
            appendClearfix: true,
            clearfixClass: 'clearfix',
            minColumnGap: 5,
            minRowGap: 20,
            gridAlign: 'center'
        };

        var getGridItems = function(target)
        {
            var $target = $(target);
            var data = $target.data(pluginName);
            var itemSelector = '.' + data.option.itemClass;
            var clearFixSelector = '.' + data.option.clearfixClass;

            return data.$container.find(itemSelector).not(clearFixSelector);

        };

        var preparePluginData = function(target)
        {
            var $target = $(target);
            var data = $target.data(pluginName);

            if (data.option.containerClass === null) {
                data.$container = $target;
            } else {
                data.$container = $target.find('.' + data.option.containerClass);
            }

            var $items = getGridItems(target);
            // Capture the original item width for later comparison, removing any applied max-width
            data.itemWidth = $items.first().css('max-width', '').innerWidth();
            if (data.hasOwnProperty('itemPadding') && !isNaN(data.itemPadding)) {
                data.itemWidth -= data.itemPadding;
            }
            // Remove any existing margins
            $items.css('margin', 0);

        };

        var prepareDomElements = function(target)
        {
            var $target = $(target);
            var data = $target.data(pluginName);
            var $items = getGridItems(target);

            if (data.option.appendClearfix) {
                $('<div>').addClass(data.option.clearfixClass).insertAfter($items.last());
            }

        };

        var updateColumnWidths = function(target)
        {
            var $target = $(target);
            var data = $target.data(pluginName);
            var $items = getGridItems(target);
            var targetWidth = data.$container.innerWidth();
            var minColumnWidth = data.itemWidth;
            var columnWidth = minColumnWidth;
            var columns = Math.max(Math.floor(targetWidth / (columnWidth + data.option.minColumnGap)), 1);
            var gridAlign = data.option.gridAlign;

            if (columns > $items.length) {

                // If number of columns exceeds number of items, limit columns to number of items
                columns = $items.length;

            } else if (columns === 1) {

                // If only 1 column can fit, try reducing column width to fit 2 columns
                var reducedColumnWidth = (targetWidth - data.option.minColumnGap) / 2;
                if (reducedColumnWidth >= columnWidth * 0.75) {
                    columns = 2;
                    columnWidth = reducedColumnWidth;
                }

            }

            var remainingPixels = targetWidth - (columnWidth * columns);
            var margin = remainingPixels / (columns + 1);

            //console.log('targetWidth: ' + targetWidth, 'columnWidth: ' + columnWidth, 'columns: ' + columns, 'remainingPixels: ' + remainingPixels, 'margin: ' + margin, 'rounded margin: ' + Math.floor(margin), '1/2 margin: ' + Math.floor(margin/2));

            var margin       = columns === 1 ? Math.floor(margin) : 0;
            var leftPadding  = columns === 1 ? 0 : Math.floor(margin / 2);
            var rightPadding = columns === 1 ? 0 : Math.floor(margin / 2);

            data.itemPadding = leftPadding + rightPadding;

            if (gridAlign === 'center') {
                $items.css('margin-left', margin);
            } else {
                $items.css('margin-right': 15);
            }

            $items.css({
                'padding-left': leftPadding,
                'max-width': columnWidth + leftPadding + rightPadding
            });
            $items.find('.wolfnet_listingMain').css({
                'padding-right': rightPadding
            });

            for (var i=0, l=$items.length; i<l; i++) {

                var $item = $($items[i]);
                $item.removeClass('wolfnet_colFirst wolfnet_colLast');

                if ((i - 1) % columns === 0) {
                    $item.addClass('wolfnet_colLast');
                    if ((i + 1) < l) {
                        var $nextItem = $($items[i + 1]);
                        $nextItem.addClass('wolfnet_colFirst');
                    }
                }

                if (i === 0) {
                    $item.addClass('wolfnet_colFirst');
                }

                if (i == (l - 1)) {
                    $item.addClass('wolfnet_colLast');
                }

            }

            data.$container.trigger('columns-updated.' + pluginName);

        };

        /**
         * This function loops over all images in the container and triggers an event on the target
         * when all images have completed loading.
         *
         * @param  DOMElement  target  The plugin target element.
         *
         * @return null
         */
        var monitorImages = function(target)
        {
            var $target = $(target);
            var data = $target.data(pluginName);
            var $items = getGridItems(target);
            var $images = $items.find('img');
            var imageCount = $images.length;
            var loadedImgs = 0;

            // Loop over each image and increment for each image that is completely loaded
            $images.each(function(){
                if ($(this).prop('complete') === true) {
                    loadedImgs++;
                }

            });

            // If all of the images are loaded trigger the event
            if (loadedImgs >= imageCount) {
                $target.trigger('allImagesLoaded.' + pluginName);

            // Otherwise run this function again after a brief delay
            } else {
                setTimeout(function(){monitorImages(target);}, 100);

            }

        };

        var updateRowHeight = function(target)
        {
            var $target = $(target);
            var data = $target.data(pluginName);
            var $items = getGridItems(target);
            var maxItemHeight = 0;
            $items.height('auto');

            // Loop over each item to determine what the height of the tallest one is.
            $items.each(function(){
                var itemHeight = this.scrollHeight;
                maxItemHeight = (maxItemHeight < itemHeight) ? itemHeight : maxItemHeight;
            });

            // Set all items to the same height as the tallest.
            $items.height(maxItemHeight);
            $items.css('marginBottom', data.option.minRowGap);

            $target.trigger('rows-updated.' + pluginName);

        };

        /* Methods available to the plugin. */
        var methods = {

            /* Initialize the plugin for all elements that have been selected. */
            init: function(options)
            {
                var plugin = this;

                // Capture the instances' options in each element's local data storage.
                this.data(pluginName, {
                    option: $.extend(defaultOptions, options || {})
                });

                // Initialized the plugin for each element that was selected.
                return this.each(function () {

                    var target = this;
                    var $target = $(target);
                    var data = $target.data(pluginName);
                    var resizing = false;

                    preparePluginData(target);

                    var targetWidth = data.$container.innerWidth();

                    // Whenever the parent container changes size update the column for even spacing
                    $(window).on('resize', function (event) {

                        // To help with performance only resize if the previous resize has completed
                        if (!resizing) {
                            resizing = true;
                            methods.refresh.call(plugin, false);
                        }

                    });

                    // Whenever the parent container gets new data, update the listing columns
                    $target.on('wolfnet.updated', function (event) {
                        methods.refresh.call(plugin, false);
                    });

                    $(window).on('columns-updated.' + pluginName, function () {
                        resizing = false;
                    });

                    // Once all images have been loaded update row heights to prevent stagger.
                    $target.on('allImagesLoaded.' + pluginName, function () {
                        methods.refresh.call($target);
                    });

                    prepareDomElements(target);
                    updateColumnWidths(target);
                    monitorImages(target);

                }); /* END: for each loop of elements the plugin has been applied to. */

            },

            refresh: function(deep)
            {
                deep = deep || false;

                return this.each(function () {
                    var target = this;

                    preparePluginData(target);
                    prepareDomElements(target);
                    updateColumnWidths(target);

                    if (deep) {
                        monitorImages(target);
                    } else {
                        updateRowHeight(target);
                    }

                });

            },

            /* This method provides a safe way for the plugin to be removed from elements on the page. */
            destroy: function()
            {
                var data = this.data(pluginName);
                data[pluginName].remove();
                this.removeData(pluginName);

            }

        };

        // Register the plugin with jQuery
        $.fn[pluginName] = function (method) {

            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || ! method) {
                return methods.init.apply(this, arguments);
            } else {
                $.error('Method ' + method + ' does not exist on jQuery.' + pluginName);
            }

        };

    })(jQuery, jQuery, window, document); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
