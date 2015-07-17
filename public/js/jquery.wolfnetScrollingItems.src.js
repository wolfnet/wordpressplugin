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

	(function($){

		$.widget("ui.wolfnetScrollingItems", $.thomaskahn.smoothDivScroll, {

			options : {
				autoPlay: false,
				direction: 'left',
				speed: 5,
				minMargin: 2,
				manualContinuousScrolling: true,
				mousewheelScrolling: true,
				scrollingHotSpotLeftClass: "scrollingHotSpotLeft",
				scrollingHotSpotRightClass: "scrollingHotSpotRight",
				scrollableAreaClass: "scrollableArea",
				scrollWrapperClass: "scrollWrapper",
				visibleHotSpotBackgrounds: ""
			},

			_create: function(){
				var widget = this;
				var option = this.options;
				var container = this.element;
				var $container = $(container);

				widget.resizeDelay = 0;

				option.autoScrollingMode = ( option.autoPlay ) ? "always" : "";
				option.autoScrollingDirection = ( option.direction == 'right' ) ? 'endlessloopleft' : 'endlessloopright';
				option.autoScrollingInterval = option.speed;

				widget._setup();

				widget._establishEvents();

				$.thomaskahn.smoothDivScroll.prototype._create.call(this);

				widget._recalculateItemMargins();

			},

			_setup: function(){
				var widget = this;
				var option = this.options;
				var container = this.element;
				var $container = $(container);
				var $items = $container.children();

				/* ****************************************************************************** */
				/* ADD STYLES SPECIFIC TO THE WOLFNET WIDGET ************************************ */
				/* ****************************************************************************** */

				$container.css({
					'position':'relative',
					'width':'100%',
					'height':'100px'
				});

				$container.children().css({
					'float':'left'
				});

				/* ****************************************************************************** */
				/* CALCULATE THE MAX ITEM HEIGHT AND WIDTH ************************************** */
				/* ****************************************************************************** */
				var maxItemHeight = 0;
				var maxItemWidth = 0;
				var maxItemMarginTop = 0;
				var maxItemMarginBottom = 0;

				$items.each(function(){

					var $this = $(this);

					var itemHeight = $this.height();
					var itemWidth = $this.width();

					maxItemHeight = (itemHeight > maxItemHeight) ? itemHeight : maxItemHeight;
					maxItemWidth = (itemWidth  > maxItemWidth) ? itemWidth  : maxItemWidth;

					var itemMarginTop = Number($this.css('margin-top').replace('px', ''));
					var itemMarginBottom = Number($this.css('margin-bottom').replace('px', ''));

					maxItemMarginTop = (itemMarginTop > maxItemMarginTop) ? itemMarginTop : maxItemMarginTop;
					maxItemMarginBottom = (itemMarginBottom > maxItemMarginBottom) ? itemMarginBottom : maxItemMarginBottom;

				});

				option.maxItemHeight = maxItemHeight;
				option.maxItemWidth = maxItemWidth;
				option.maxItemMarginTop = maxItemMarginTop;
				option.maxItemMarginBottom = maxItemMarginBottom;

				/* ****************************************************************************** */
				/* UPDATE ITEM HEIGHT AND WIDTH BASED ON MAXIMUMS ******************************* */
				/* ****************************************************************************** */
				$items.height(maxItemHeight);
				$items.width(maxItemWidth);

				$container.height(maxItemHeight);
				$container.width($container.width());

			},

			_establishEvents: function(){
				var widget = this;
				var option = this.options;
				var container = this.element;
				var $container = $(container);

				$container.mouseover(function(){
					if (option.autoPlay) {
						widget.stopAutoScrolling();
					}
					widget.showHotSpotBackgrounds();
				});

				$container.mouseleave(function(){
					widget.hideHotSpotBackgrounds();
					if (option.autoPlay) {
						widget.startAutoScrolling();
					}
				});

			},

			_recalculateItemMargins: function(){
				var widget = this;
				var option = this.options;
				var container = this.element;
				var $container = $(container);
				var $items = $container.find('.scrollableArea:first').children();

				if (option.autoPlay) {
					clearTimeout(widget.resizeDelay);
					widget.stopAutoScrolling();
				}

				/* ****************************************************************************** */
				/* CALCULATE IDEAL MARGINS TO FIT CONTAINER ************************************* */
				/* ****************************************************************************** */

				var numColumns = 1;
				var maxItemWidthWithMargins = 0;
				var maxItemHeightWithMargins = 0;

				var calculateIdealMargin = function(container, item, minMargin, modifier)
				{
					numColumns = Math.floor(container / item) + modifier;

                    var leftOverSpace = container - (item * numColumns);
					var marginPerItem = leftOverSpace / numColumns;

					/* Does work in <=IE8, but avoids single columns */
					var idealMargin   = marginPerItem / 2;

					/* Works in every browser but has single columns */
					//var idealMargin   = Math.ceil( marginPerItem / 2 );

					if (idealMargin == -1) {
						idealMargin = 0;
					}

					var itemsWithMargins = ((idealMargin * 2) + item) * numColumns;

					var validMargins = (idealMargin < minMargin || itemsWithMargins > container);

					if (validMargins && numColumns > 1) {
						idealMargin = calculateIdealMargin(container, item, minMargin, modifier - 1);
					}

					return idealMargin;

				};

				var idealMargin = calculateIdealMargin(
					$container.width(),
					option.maxItemWidth,
					option.minMargin,
					0
					);

				$items.css({
					'margin-top': option.maxItemMarginTop,
					'margin-right': idealMargin,
					'margin-bottom': option.maxItemMarginBottom,
					'margin-left': idealMargin
					});

				maxItemWidthWithMargins  = option.maxItemWidth + (idealMargin * 2);
				maxItemHeightWithMargins = option.maxItemHeight + option.maxItemMarginTop + option.maxItemMarginBottom;

				/* ****************************************************************************** */
				/* UPDATE CONTAINER HEIGHT AND WIDTH BASED ON MAXIMUMS ************************** */
				/* ****************************************************************************** */
				$container.height(maxItemHeightWithMargins);

				if (option.autoPlay) {
					widget.resizeDelay = setTimeout(function(){
						widget.startAutoScrolling();
					}, 500);
				}

			}

		});

	})(jQuery); /* END: jQuery IIFE */

} /* END: If jQuery Exists */
