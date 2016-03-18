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
if (typeof jQuery !== 'undefined') {

    (function ($, jQuery, window, document, undefined) {

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

        };


        var updateColumnWidths = function(target, size)
        {
            size = size || '';

            var $target = $(target);
            var data = $target.data(pluginName);
            var $items = getGridItems(target);

            // Resize items
            $items.removeClass('wolfnet_listing_sm wolfnet_listing_xs');
            switch (size) {
                case 'sm':
                    $items.addClass('wolfnet_listing_sm');
                    break;
                case 'xs':
                    $items.addClass('wolfnet_listing_xs');
                    break;
            }
            $items.trigger('wntResizeItem');

            // Remove first/last column identifiers
            $items.removeClass('wolfnet_colFirst wolfnet_colLast');

            // Find the row breaks, and count the columns
            var $lastItem = null,
                columns = 0,
                rows = 0
                rowItems = 0;

            for (var i=0, l=$items.length; i<l; i++) {

                var $item = $($items[i]);

                if ($lastItem) {
                    if ($lastItem.offset().top != $item.offset().top) {
                        $lastItem.addClass('wolfnet_colLast');
                        $item.addClass('wolfnet_colFirst');
                        rows++;
                    }
                } else {
                    $item.addClass('wolfnet_colFirst');
                    rows++;
                }

                // Count the items in the row
                rowItems++;

                // In the first row, count the columns
                if (rows === 1) {
                    columns = rowItems;
                }

                // Note the last item
                if (i == (l - 1)) {
                    $item.addClass('wolfnet_colLast');
                }

                $lastItem = $item;

            }

            if (columns === 1) {
                // Try resizing to get more columns
                switch (size) {
                    case 'full':
                        // Done
                        data.$container.trigger('columns-updated.' + pluginName);
                        break;
                    case 'xs':
                        // Go back to full-size, 1-column
                        updateColumnWidths(target, 'full');
                        break;
                    case 'sm':
                        // Try the next size down
                        updateColumnWidths(target, 'xs');
                        break;
                    default:
                        // Try the next size down
                        updateColumnWidths(target, 'sm');
                        break;
                }
            } else {
                // Done
                data.$container.trigger('columns-updated.' + pluginName);
            }

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
            $images.each(function () {
                if ($(this).prop('complete') === true) {
                    loadedImgs++;
                }
            });

            if (loadedImgs >= imageCount) {
                // If all of the images are loaded, trigger the event
                $target.trigger('allImagesLoaded.' + pluginName);
            } else {
                // Otherwise, run this function again after a brief delay
                setTimeout(function () { monitorImages(target); }, 100);
            }

        };


        /* Methods available to the plugin. */
        var methods = {

            /* Initialize the plugin for all elements that have been selected. */
            init: function(options)
            {
                var plugin = this;

                // Capture the instances' options in each element's local data storage.
                this.data(pluginName, {
                    option: $.extend({}, defaultOptions, options || {})
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

                    $target
                        // Update start
                        .on('wolfnet.updating', function () {
                            var data = $target.data(pluginName);
                            data.imagesLoading = true;
                            data.isUpdating = true;
                            $target.addClass('wnt-in-transition');
                        })
                        // Whenever the parent container gets new data, update the listing columns
                        .on('wolfnet.updated', function (event) {
                            var data = $target.data(pluginName);
                        })
                        // Transitions
                        .on('refresh-end.' + pluginName, function () {
                            var data = $target.data(pluginName);
                            data.isUpdating = false;
                            if (!data.imagesLoading) {
                                $target.removeClass('wnt-in-transition');
                            }
                        });

                    $(window).on('columns-updated.' + pluginName, function () {
                        resizing = false;
                    });

                    // Once all images have been loaded update row heights to prevent stagger.
                    $target.on('allImagesLoaded.' + pluginName, function () {
                        var data = $target.data(pluginName);
                        data.imagesLoading = false;
                        methods.refresh.call($target);
                    });

                    data.imagesLoading = true;

                    updateColumnWidths(target);
                    monitorImages(target);

                }); /* END: for each loop of elements the plugin has been applied to. */

            },

            refresh: function(deep)
            {
                deep = deep || false;

                return this.each(function () {
                    var target = this;

                    $(target).trigger('refresh-start.' + pluginName);

                    preparePluginData(target);
                    updateColumnWidths(target);

                    if (deep) {
                        monitorImages(target);
                    }

                    $(target).trigger('refresh-end.' + pluginName);

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
