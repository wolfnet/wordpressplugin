/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which
 * will then scroll based on the options that are passes in.
 *
 * @title         jquery.wolfnetScrollingItems.js
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
 * @option  boolean  autoplay   Should the child elements start scrolling right away.
 * @option  int      speed      How fast should the child elements scroll.
 * @option  int      direction  The direction the elements will scroll when autoplay is true.
 *
 */

/**
 * The following code relies on jQuery so if jQuery has been initialized encapsulate the following
 * code inside an immediately invoked function expression (IIFE) to avoid naming conflicts with the
 * $ variable.
 */
if (typeof jQuery != 'undefined') {

    (function($, window){

        var pluginName = 'wolfnetScrollingItems';

        var defaultOptions = {
            autoPlay: false,
            direction: 'left',
            speed: 2,
            showControls: true,
            componentClass: 'scroller',
            withControlsClass: 'with-controls',
            controlClass: 'control',
            controlLeftClass: 'left',
            controlRightClass: 'right',
            itemClass: 'item'
        };

        var getData = function(target)
        {
            return $(target).data(pluginName) || {};
        };

        var ensureAllItemsAreEqualSize = function(target)
        {

        };

        /**
         * This function is responsible for clearing whitespace which otherwise causes spacing issues.
         */
        var removeWhitespaceBetweenTags = function(target)
        {
            var data = getData(target);

            data.$itemContainer.contents().filter(function(){
                return (this.nodeType == 3 && !/\S/.test(this.nodeValue));
            }).remove();

        };

        var ensureThereAreEnoughItems = function(target)
        {
            var $target = $(target);
            var data = getData(target);
            var containerWidth = data.$itemContainer.innerWidth();
            var $items = getItems(target);

            if (containerWidth >= (($items.length * data.itemWidth) / 2)) {
                $items.clone().appendTo(data.$itemContainer);
                ensureThereAreEnoughItems(target);
            }

        };

        var getItems = function(target)
        {
            var data = getData(target);

            return data.$itemContainer.children().filter('.' + data.option.itemClass);

        };

        var setNextFrame = function(target)
        {
            var data = getData(target);

            if (!(data.nextFrameSet || false)) {

                // If we can use the requestAnimationFrame event we should for performance
                if (window.requestAnimationFrame) {
                    window.requestAnimationFrame(function(){executeFrame(target);});

                } else {
                    if ((data.timeoutFlag || false) === false) {
                        data.timeoutFlag = true;

                        setTimeout(function(){
                            executeFrame(target);
                        }, 17); // targeted for 60fps

                    }

                }

                data.nextFrameSet = true;

            }

        };

        var executeFrame = function(target)
        {
            var data = getData(target);

            if (canAnimate(target) && shouldAnimate(target)) {
                // Trigger the animation
                animate(target);

                data.timeoutFlag = false;

                data.nextFrameSet = false;

                // continue animating
                setNextFrame(target);

            } else {
                data.nextFrameSet = false;

            }

        };

        var animate = function(target)
        {
            var $target = $(target);
            var data = getData(target);
            var pixelsPerFrame = data.speed || data.option.speed;
            var scroll = data.$itemContainer.scrollLeft();
            var containerWidth = data.$itemContainer.innerWidth();
            var maxScroll = data.$itemContainer[0].scrollWidth - containerWidth;
            var nextScroll;

            if (data.direction === 'right') {

                nextScroll = scroll - pixelsPerFrame;

                if (scroll <= 0) {
                    nextScroll = nextScroll + data.itemWidth;
                    getItems(target).last().prependTo(data.$itemContainer);
                }

            } else {

                nextScroll = scroll + pixelsPerFrame;

                if (scroll >= maxScroll) {
                    nextScroll = nextScroll - data.itemWidth;
                    getItems(target).first().appendTo(data.$itemContainer);
                }

            }

            data.$itemContainer.scrollLeft(nextScroll);

        };

        var canAnimate = function(target)
        {
            var data = getData(target);

            // TODO: Check if there are enough items to animate
            return true;

        };

        var shouldAnimate = function(target)
        {
            var data = getData(target);

            return (data.animating || false);

        };

        var buildControls = function(target)
        {
            var $target = $(target);
            var data = getData(target);
            var $items = getItems(target);

            $target.addClass(data.option.withControlsClass);

            // Wrap the contents to make button placement easier
            data.$itemContainer = $('<div>').append($items).appendTo($target);

            createButton(target, 'left').prependTo($target);
            createButton(target, 'right').prependTo($target);

        };

        var createButton = function(target, direction)
        {
            var $target = $(target);
            var data = getData(target);

            return $('<button>')
                .addClass(data.option.controlClass)
                .addClass(direction === 'right' ? data.option.controlRightClass : data.option.controlLeftClass)
                .hover(function(){
                    data.direction = direction;

                    if (data.animating) {
                        data.wasAnimatingBeforeDir = true;
                    } else {
                        data.wasAnimatingBeforeDir = false;
                        methods.play.call($target);
                    }

                },function(){
                    data.direction = data.option.direction;

                    if (data.wasAnimatingBeforeDir) {
                        data.wasAnimatingBeforeDir = false;
                    } else {
                        methods.pause.call($target);
                    }

                }).mousedown(function(){
                    data.speed = data.option.speed * 3;

                }).mouseup(function(){
                    data.speed = data.option.speed;

                });

        };

        var methods = {

            init: function(options)
            {

                return this.each(function(){
                    var target = this;
                    var $target = $(this);

                    $target.data(pluginName, {option:$.extend({}, defaultOptions, options)});

                    var data = getData(target);

                    data.$itemContainer = $target;
                    data.direction = data.option.direction;

                    if (!$target.hasClass(data.option.componentClass)) {
                        $target.addClass(data.option.componentClass);
                    }

                    data.option.speed = Math.round(data.option.speed / 4);
                    data.option.speed = (data.option.speed < 1) ? 1 : (data.option.speed > 5) ? 5 : data.option.speed;

                    removeWhitespaceBetweenTags(target);
                    ensureAllItemsAreEqualSize(target);
                    data.itemWidth = getItems(target).first().outerWidth(true);
                    ensureThereAreEnoughItems(target);

                    if (data.option.showControls) {
                        buildControls(target);
                    }

                    data.$itemContainer.css({
                        overflowX: 'hidden'
                    });

                    if (data.option.autoPlay) {
                        methods.play.call($target);
                    }

                    $target.hover(function(event){
                        if (data.animating && !$(event.target).hasClass(data.option.controlClass)) {
                            data.wasAnimating = true;
                            methods.pause.call($target);
                        }
                    }, function(){
                        if (data.wasAnimating) {
                            data.wasAnimating = false;
                            methods.play.call($target);
                        }
                    });

                    $(window).resize(function(){
                        if ((data.resizing || false) === false) {
                            data.resizing = true;
                            ensureThereAreEnoughItems(target);
                            data.resizing = false;
                        }
                    });

                });

            },

            play: function()
            {

                return this.each(function(){
                    var data = getData(this);

                    if (!data.animating) {
                        data.animating = true;
                        setNextFrame(this);
                    }

                });

            },

            pause: function()
            {

                return this.each(function(){
                    var data = getData(this);

                    data.animating = false;
                    data.timeoutFlag = false;

                });

            }

        };

        $.fn[pluginName] = function(method) {

            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
            } else if (typeof method === 'object' || ! method) {
                return methods.init.apply( this, arguments);
            } else {
                $.error('Method ' + method + ' does not exist on jQuery.' + pluginName);
            }

        };

    })(jQuery, window); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
