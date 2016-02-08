/**
 * This jQuery plugin can be applied to any number of containers which hold a listing thumbnail.
 *
 * @title         jquery.wolfnetThumbnailScroller.js
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
 * The following code relies on jQuery, so if jQuery has been initialized, encapsulate the following
 * code inside an immediately-invoked function expression (IIFE) to avoid naming conflicts with the
 * $ variable.
 */
if (typeof jQuery !== 'undefined') {

    (function ($, document, window, undefined) {

        var pluginName = 'wolfnetThumbnailScroller';
        var dataKey = 'wnt.' + pluginName;

        var defaultOptions = {
            containerClass: 'wnt-thumbnail-scroller',
            controlBtnClass: 'wnt-nav-btn',
            hideClass: 'wolfnet_hidden',
            hidableCtrlClass: 'wnt-hidable-controls',
            loaderClass: 'wnt-thumbnails-loader',
            loadingClass: 'wnt-thumbnails-loading',
            photoType: 'thumb_url',
            hideControls: true,
            withCount: false,
            controlsClass: 'wnt-controls',
            iconClass: 'wnt-icon',
            countClass: 'wnt-count',
            countIndexClass: 'wnt-count-index',
            countTotalClass: 'wnt-count-total',
            nextBtnClass: 'wnt-next',
            prevBtnClass: 'wnt-prev',
            nextIconClass: 'wnt-icon-triangle-right',
            prevIconClass: 'wnt-icon-triangle-left',
            photoSelector: '.primary-photo',
            extraButtonClass: '',
            photoUnavailable: ''
        };

        var methods = {

            public: {

                init: function(options) {
                    var opts = $.extend({}, defaultOptions, options);

                    this.each(function(){
                        var $this = $(this);
                        if ($this.data(dataKey) === undefined) {
                            methods.private.options($this, opts);
                            methods.private.setupDomElements($this);
                            methods.private.setupEventHandlers($this);
                        }
                    });

                    return this;

                },

                next: function() {
                    return methods.public.changeImage.call(this, 'next');
                },

                previous: function() {
                    return methods.public.changeImage.call(this, 'previous');
                },

                changeImage: function (direction) {
                    if (direction === undefined) {
                        $.error('The direction argument to changeImage was expected but not received.');
                    }

                    if (direction !== 'next' && direction !== 'previous') {
                        $.error('The only directions supported by changeImage are "next" and "previous".');
                    }

                    return this.each(function () {
                        var $this = $(this);
                        var options = methods.private.options($this);
                        var index = methods.private.state($this, 'index') || 0;
                        var photos = methods.private.state($this, 'photoMetadata') || null;
                        var $container = methods.private.state($this, '$container');
                        var containerHeight = $container.height();

                        if (photos !== null && photos.length > 1 && !methods.private.state($this, 'transitioning')) {

                            methods.private.state($this, 'transitioning', true);

                            var $currentPhoto = $container.find('img:visible:first');
                            var newIndex = 0;
                            switch (direction) {
                                case 'next':
                                    newIndex = (index+1 > photos.length-1) ? 0 : index+1;
                                    break;
                                case 'previous':
                                    newIndex = (index-1 < 0) ? photos.length-1 : index-1;
                                    break;
                            }
                            var photoUrl = photos[newIndex][options.photoType];
                            var $newPhoto = $container.find('img[src="' + photoUrl + '"]');

                            // If no photo was found we need to attempt to load it.
                            if ($newPhoto.length === 0) {
                                $newPhoto = $('<img>').attr('src', photoUrl)
                                .attr('alt', 'property image')
                                .css({'max-height': containerHeight + 'px'})
                                .hide()
                                .on('error', function(){
                                    $(this).attr('src', options.photoUnavailable);
                                })
                                .on('load', function(){
                                    methods.private.transitionPhotos($this, $currentPhoto, $(this), newIndex);
                                });

                                if (direction == 'next' && newIndex === 0) {
                                    $.error('Something when wrong. The 0 index photo should already be here.');
                                } else if (direction == 'previous' || index === 0) {
                                    $newPhoto.insertAfter($currentPhoto);
                                } else {
                                    $newPhoto.insertBefore($currentPhoto);
                                }

                            } else {
                                methods.private.transitionPhotos($this, $currentPhoto, $newPhoto, newIndex);
                            }

                        } else {
                            if (!methods.private.state($this, 'photoMetadataLoaded')) {
                                methods.private.loadPhotoMetadata($this, function(){
                                    methods.public[direction].call($this);
                                });
                            }
                        }

                    });

                },

                showControls: function() {
                    return this.each(function(){
                        var $this = $(this);
                        var options = methods.private.options($this);
                        var $controls = methods.private.controls($this);

                        $controls.removeClass(options.hideClass);

                    });

                },

                hideControls: function() {
                    return this.each(function(){
                        var $this = $(this);
                        var options = methods.private.options($this);
                        var $controls = methods.private.controls($this);

                        $controls.addClass(options.hideClass);

                    });

                }

            },

            private: {

                state: function($thumbnails, data, data2) {
                    if ($thumbnails.length > 1) {
                        $.error('The state method can only operate on a single element.');
                    }

                    // We are dealing with a specific value.
                    if (data !== undefined && typeof data === 'string') {
                        var currentData = $thumbnails.data(dataKey) || {};

                        if (data2 !== undefined) {
                            currentData[data] = data2;
                            currentData = $thumbnails.data(dataKey, currentData);
                        }

                        return currentData[data];

                    } else {
                        if (data !== undefined) {
                            $thumbnails.data(dataKey, data);
                        }

                        return $thumbnails.data(dataKey) || {};

                    }

                },

                /**
                 * This method retrieves and set plugin options data on each element that is part of the
                 * matching selection.
                 * @param  {Object} data Optional data to be set.
                 * @return {Object}      Data retrieved.
                 */
                options: function($thumbnails, data) {

                    if ($thumbnails.length > 1) {
                        $.error('The options method can only operate on a single element.');
                    }

                    var currentData = methods.private.state($thumbnails, 'options') || {};

                    if (data !== undefined) {
                        currentData = methods.private.state($thumbnails, 'options', data);
                    }

                    return currentData;

                },

                setupDomElements: function($thumbnails, options) {
                    methods.private.wrapImageElement($thumbnails);
                    methods.private.buildControls($thumbnails);

                },

                setupEventHandlers: function($thumbnails, options) {

                    $thumbnails.each(function(){
                        var $this = $(this);
                        var options = methods.private.options($this);

                        if (wolfnet.hasFeature('touch')) {
                            $this.addClass('has-swipe').wolfnetSwipe({
                                direction: 'horizontal'
                            })
                            .on('wntSwipeLeft', function(){
                                $(this)[pluginName]('next');
                            })
                            .on('wntSwipeRight', function(){
                                $(this)[pluginName]('previous');
                            });
                        }

                        // We only need these hover events if we are showing/hiding the controls.
                        if (options.hideControls) {
                            $this.hover(function(){
                                methods.private.state($this, 'mouseover', true);
                                // If the photos aren't ready to be downloaded there is no point in showing
                                // the controls.
                                if (!methods.private.state($this, 'photoMetadataLoaded')) {
                                    methods.private.loadPhotoMetadata($this, function(){
                                        // If the mouse is still over the image when the data finishes loading show the controls.
                                        if (methods.private.state($this, 'mouseover') && methods.private.state($this, 'photoMetadata').length > 1) {
                                            methods.public.showControls.call($this);
                                        }
                                    });
                                } else if (methods.private.state($this, 'photoMetadata').length > 1) {
                                    methods.public.showControls.call($this);
                                }
                            }, function(){
                                methods.private.state($this, 'mouseover', false);

                                if (options.hideControls) {
                                    methods.public.hideControls.call($this);
                                }

                            });
                        }

                        methods.private.controls($this).click(function(event){
                            methods.private.eventHandler.controlClick.call(this, event, $this);
                        });

                    });

                },

                wrapImageElement: function($thumbnails) {

                    $thumbnails.each(function () {
                        var $this = $(this);
                        var options = methods.private.options($this);
                        var $primaryPhoto = $this.find(options.photoSelector).first();

                        var $container = $('<span>')
                            .addClass(options.containerClass)
                            .addClass(options.hideControls ? options.hidableCtrlClass : '')
                            .insertBefore($primaryPhoto)
                            .append($primaryPhoto);

                        methods.private.state($this, '$container', $container);

                        $('<span>').addClass(options.loaderClass).appendTo($container);

                    });

                },

                buildControls: function($thumbnails) {
                    $thumbnails.each(function(){
                        var $this = $(this);
                        var state = methods.private.state($this);
                        var options = methods.private.options($this);
                        var $container = state.$container;
                        var $navNext = methods.private.buildNavBtn($this, 'next');
                        var $navPrev = methods.private.buildNavBtn($this, 'prev');
                        var $countCtrl = (options.withCount ? methods.private.buildCountControl($this) : '');

                        $container.append([ $navNext, $navPrev ]);

                        if (options.hideControls) {
                            methods.public.hideControls.call($this);
                        }

                    });

                },

                buildNavBtn: function($thumbnails, type) {

                    if ($thumbnails.length > 1) {
                        $.error('The buildNavBtn method can only operate on a single element.');
                    }

                    var options = methods.private.options($thumbnails);

                    var $btnIcon = $('<span>').addClass(options.iconClass);
                    var btnClass = '';

                    switch (type) {
                        case 'next':
                            $btnIcon.addClass(options.nextIconClass);
                            btnClass = options.nextBtnClass;
                            break;
                        case 'prev':
                            $btnIcon.addClass(options.prevIconClass);
                            btnClass = options.prevBtnClass;
                            break;
                    }

                    var $btn = $('<button>')
                        .addClass(options.controlBtnClass)
                        .addClass(options.extraButtonClass)
                        .addClass(btnClass)
                        .append($btnIcon);

                    return $btn;

                },

                buildCountControl: function ($thumbnails) {

                    if ($thumbnails.length > 1) {
                        $.error('The buildCountControl method can only operate on a single element.');
                    }

                    var options = methods.private.options($thumbnails);

                    var $countCtrl = $('<span>').addClass(options.countClass).append([
                        $('<span>').addClass(options.countIndexClass),
                        $('<span>').text(' of '),
                        $('<span>').addClass(options.countTotalClass),
                    ]);

                    return $countCtrl;

                },

                controls: function($thumbnails) {

                    if ($thumbnails.length > 1) {
                        $.error('The controls method can only operate on a single element.');
                    }

                    var state = methods.private.state($thumbnails);
                    var options = methods.private.options($thumbnails);

                    return state.$container.find('.' + options.controlBtnClass);

                },

                loadPhotoMetadata: function($thumbnails, complete) {

                    if ($thumbnails.length > 1) {
                        $.error('The loadPhotoMetadata method can only operate on a single element.');
                    }

                    complete = complete || function(){};

                    var state = methods.private.state($thumbnails);
                    var options = methods.private.options($thumbnails);

                    // If there is already an attempt to load the photos in progress we don't want to
                    // start another request.
                    if (!state.loadingPhotos) {
                        methods.private.state($thumbnails, 'loadingPhotos', true);

                        var photoUrl = $thumbnails.find(options.photoSelector).first().data('photo-url');

                        $.ajax({
                            url: photoUrl,
                            dataType: 'json',
                            type: 'get',
                            context: $thumbnails,
                            timeout: 2500,
                            statusCode: {
                                404: function () {
                                    commFailure();
                                }
                            },
                            beforeSend: function(){
                                state.$container.addClass(options.loadingClass);
                            }
                        })
                        .always(function(){
                            state.$container.removeClass(options.loadingClass);
                            methods.private.state(this, 'loadingPhotos', false);
                        })
                        .done(function(data){
                            methods.private.state(this, 'photoMetadataLoaded', true);
                            methods.private.state(this, 'photoMetadata', data);

                            complete();
                        });

                    }

                },

                transitionPhotos: function($thumbnails, $fromPhoto, $toPhoto, newIndex) {
                    $fromPhoto.fadeOut('fast', function(){
                        $toPhoto.fadeIn('fast', function(){
                            methods.private.state($thumbnails, 'index', newIndex);
                            methods.private.state($thumbnails, 'transitioning', false);
                        });
                    });

                },

                eventHandler: {

                    controlClick: function(event, $thumbnails) {
                        event.preventDefault();

                        var $btn = $(this);
                        var options = methods.private.options($thumbnails);

                        if ($btn.is('.' + options.nextBtnClass)) {
                            methods.public.next.call($thumbnails);
                        }

                        if ($btn.is('.' + options.prevBtnClass)) {
                            methods.public.previous.call($thumbnails);
                        }

                        return false; // We don't want any other click event being triggered.

                    }

                }

            }

        };

        // Register the plugin with jQuery.
        $.fn[pluginName] = function(method) {
            if (methods.public[method]) {
                return methods.public[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.public.init.apply(this, arguments);
            } else {
                $.error('Method ' + method + ' does not exist on jQuery.' + pluginName);
            }

        };

    })(jQuery, document, window); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
