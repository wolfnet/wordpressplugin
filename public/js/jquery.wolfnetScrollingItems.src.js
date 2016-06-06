/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which
 * will then scroll based on the options that are passes in.
 *
 * @title         jquery.wolfnetScrollingItems.js
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


        /**
         * Retrieve the "state" data for the supplied target element.
         *
         * @param  DOMElement  target  The element for which to retrieve state data.
         *
         * @return Object              The state data that was retrieved.
         */
        var getData = function(target)
        {
            return $(target).data(pluginName) || {};
        };


        /**
         * This function is responsible for clearing whitespace which otherwise causes spacing issues.
         *
         * @param  DOMElement  target  The parent element within which whitespace should be removed.
         *
         * @return null
         */
        var removeWhitespaceBetweenTags = function(target)
        {
            var data = getData(target);

            data.$itemContainer.contents().filter(function(){
                return (this.nodeType == 3 && !/\S/.test(this.nodeValue));
            }).remove();

        };


        /**
         * Retrieves all child elements which match a specific class.
         *
         * @param  DOMElement  target  The element to look within for items.
         *
         * @return jQuery              A jQuery collection of items.
         */
        var getItems = function(target)
        {
            var data = getData(target);

            return data.$itemContainer.children().filter('.' + data.option.itemClass);

        };


        /**
         * Establishes the next frame event for animation. This method will use the most efficient
         * process available in the browser to process animation frames.
         *
         * @param  DOMElement  target  The target element to animate.
         *
         * @return null
         */
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


        /**
         * The callback which is executed for every animation frame. This method also determines
         * whether or not the animation should actually be performed.
         *
         * @param  DOMElement  target  The element to be animated.
         *
         * @return null
         */
        var executeFrame = function(target)
        {
            var data = getData(target);
            var enoughItems = hasEnoughItems(target);

            if (shouldAnimate(target)) {

                // Trigger the animation if there are enough items to scroll
				if (enoughItems) {
					cloneItems(target);
                	animate(target);
                }

                data.timeoutFlag = false;

                data.nextFrameSet = false;

                // continue animating
                setNextFrame(target);

            } else {
                data.nextFrameSet = false;

            }

        };


        /**
         * If there are not enough items within the container for a smooth animation, recursively
         * clone the items and append to the parent element.
         *
         * @param  DOMElement  target  The element within which to ensure there are enough items.
         *
         * @return null
         */
        var cloneItems = function(target)
        {
            var $target = $(target);
            var data = getData(target);
            var containerWidth = data.$itemContainer.innerWidth();
            var $items = getItems(target);

            if (
				(containerWidth) &&
				($items.length) &&
				(data.itemWidth) &&
				containerWidth >= (($items.length * data.itemWidth) / 2)
			) {
               	$items.clone().appendTo(data.$itemContainer);
               	cloneItems(target, containerWidth);
            }

        };


        /**
         * Return true if the sum width of the items included in the scroller are greater than
         * the container's width; return false if the sum width of the items is smaller.
         *
         * @param  DOMElement  target  The element within which to ensure there are enough items.
         *
         * @return null
         */
        var hasEnoughItems = function(target)
        {
            var $target = $(target);
            var data = getData(target);

            var numberOfItems = getItems(target).length;
            var itemWidth = data.itemWidth;
            var containerWidth = data.$itemContainer.innerWidth();

            // Do not scroll if width of all items in scroller is less than container
            if ((numberOfItems) && (itemWidth) && (containerWidth) &&
				(numberOfItems * itemWidth) < containerWidth) {
				return false;
            }

            // Do not scroll a single item
            if ((numberOfItems) && numberOfItems == 1) {
				return false;
            }

            return true;

        };


        /**
         * Perform the actual animation. This function advances or retreats the "scroll" position of
         * the element which holds all of the items to be scrolled. This function uses "state" data
         * to determine which direction and how far to move the scroll position. This function also
         * facilitates the "infinite scrolling" functionality which makes it looks as if the items
         * are scrolling by in an infinite loop. This is done by moving elements from one end of the
         * container to the other while scrolling.
         *
         * @param  DOMElement  target  The element which contains items to animate.
         *
         * @return null
         */
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


        /**
         * Determines whether or not animation should be performed.
         *
         * @param  DOMElement  target  The element to be animated.
         *
         * @return boolean
         */
        var shouldAnimate = function(target)
        {
            var data = getData(target);

            return (data.animating || false);

        };


        /**
         * Translate string speed to integer.
         *
         * @param  string  speed  The speed string to be translated ('slow'/'medium'/'fast')
         *
         * @return int
         */
        var translateSpeed = function(speed)
        {
            switch (speed) {
                case 'slow':
                    return 1;
                case 'medium':
                    return 2;
                case 'fast':
                    return 3;
                default:
                    // Default to slowest in case of unspecified speed or deprecated integer value
                    return 1;
            }
        };


        /**
         * This method builds control elements which can be used to control the animation of the
         * component. This method also modifies the parent container to make room for the controls.
         *
         * @param  DOMElement  target  The element to add controls to.
         *
         * @return null
         */
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


        /**
         * Creates a control element for a specified direction and returns it. The new element is
         * not yet attached to the DOM.
         *
         * @param  DOMElement  target     The element the button will control.
         * @param  String      direction  The direction the button should control.
         *
         * @return jQuery                 A jQuery representation of the new control element
         */
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

            /**
             * Initializes a plugin instance.
             *
             * @param  Object  options  A collection of options for the new plugin instance.
             *
             * @return jQuery           The collection of elements the plugin was applied to.
             */
            init: function(options)
            {
                return this.each(function(){
                    var target = this;
                    var $target = $(this);
                    options.speed = translateSpeed(options.speed);

                    $target.data(pluginName, {option:$.extend({}, defaultOptions, options)});

                    var data = getData(target);

                    data.$itemContainer = $target;
                    data.direction = data.option.direction;

                    if (!$target.hasClass(data.option.componentClass)) {
                        $target.addClass(data.option.componentClass);
                    }

                    removeWhitespaceBetweenTags(target);
                    data.itemWidth = getItems(target).first().outerWidth(true);

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

					var enoughItems = hasEnoughItems(target);

					$(window).resize(function(){
						if ((data.resizing || false) === false) {
							data.resizing = true;
							data.resizing = false;
						}
					});

                });

            },

            /**
             * Starts animation if it isn't already running.
             *
             * @return jQuery  The collection of elements the plugin was applied to.
             */
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

            /**
             * Stops animation if it is already running.
             *
             * @return jQuery  The collection of elements the plugin was applied to.
             */
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
