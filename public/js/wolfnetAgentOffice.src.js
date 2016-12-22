jQuery(function ($) {

	var $aoWidget = $('.wolfnet_widget.wolfnet_ao'),
		$aoHeader = $aoWidget.find('.wolfnet_agentOfficeHeader'),
		$aoItems  = $aoWidget.find('.wolfnet_aoItem'),
		$aoImages = $aoItems.find('.wolfnet_aoImage');


	var officeItemSections = [
		{ selector: '.wolfnet_aoContact', maxHeight: 0, origMaxHeight: 0 },
		{ selector: '.wolfnet_aoLinks',   maxHeight: 0, origMaxHeight: 0 }
	];

	var agentItemSections = [
		{ name: 'contact',  selector: '.wolfnet_aoContact',  maxHeight: 0,  origMaxHeight: 0 },
		{ name: 'links',    selector: '.wolfnet_aoLinks',    maxHeight: 0,  origMaxHeight: 0 },
		{ name: 'info',     selector: '.wolfnet_aoInfo .wolfnet_aoActions',  maxHeight: 0,  origMaxHeight: 0 },
		{ name: 'body',     selector: '.wolfnet_aoBody',     maxHeight: 0,  origMaxHeight: 0, alwaysResize: true },
		{ name: 'footer',   selector: '.wolfnet_aoFooter',   maxHeight: 0,  origMaxHeight: 0 },
		{ name: 'item',     selector: '.wolfnet_aoItem',     maxHeight: 0,  origMaxHeight: 0 }
	];


	var breakpointClassPrefix = 'wnt-ao-';

	var breakpoints = { xl: 1140, lg: 988, md: 735, sm: 560 };


	var updateBreakpointClass = function () {
		var widgetWidth = $aoWidget.width();
		$aoWidget.removeClass([
			breakpointClassPrefix + 'lg',
			breakpointClassPrefix + 'md',
			breakpointClassPrefix + 'sm',
			breakpointClassPrefix + 'xs'
		].join(' '));

		if (widgetWidth >= breakpoints.xl) {
			$aoWidget.addClass(breakpointClassPrefix + 'xl');
		} else if (widgetWidth >= breakpoints.lg) {
			$aoWidget.addClass(breakpointClassPrefix + 'lg');
		} else if (widgetWidth >= breakpoints.md) {
			$aoWidget.addClass(breakpointClassPrefix + 'md');
		} else if (widgetWidth >= breakpoints.sm) {
			$aoWidget.addClass(breakpointClassPrefix + 'sm');
		} else {
			$aoWidget.addClass(breakpointClassPrefix + 'xs');
		}

	};


	// Resize item boxes to height of tallest one.

	var resizeAOItems = function () {
		var sectionsSelector = '',
			colCount = 0,
			colsCounted = false,
			itemSections = ($aoWidget.is('.wolfnet_aoAgentsList') ? agentItemSections : officeItemSections);

		// Reset the max heights & set default options
		for (var i=0, l=itemSections.length; i<l; i++) {
			itemSections[i].maxHeight = 0;
			if (!itemSections[i].hasOwnProperty('alwaysResize')) {
				itemSections[i]['alwaysResize'] = false;
			}
		}

		for (var i=0, l=$aoItems.length; i<l; i++) {
			var $aoItem = $($aoItems[i]);

			// Update the max heights
			getItemSectionsMaxHeights($aoItem, itemSections);

			// Count the columns
			if (!colsCounted) {
				var $prevItem = $aoItem.prev();
				if ($prevItem.length > 0) {
					if ($aoItem.position().top != $prevItem.position().top) {
						colsCounted = true;
					} else {
						colCount++;
					}
				} else {
					colCount++;
				}
			}

		}

		// Set the new heights
		for (var i=0, l=itemSections.length; i<l; i++) {
			var $itemSection = $aoItems.find(itemSections[i].selector);
			if (($itemSection.length === 0) && $aoItems.is(itemSections[i].selector)) {
				$itemSection = $aoItems;
			}
			if (itemSections[i].alwaysResize || (colCount > 1)) {
				$itemSection.height(Math.max(itemSections[i].maxHeight, itemSections[i].origMaxHeight));
			} else {
				$itemSection.css('height', '');
			}
		}

		// Reposition the agent/office nav
		if (($aoItems.length > 0) && ($(window).width() >= 600)) {
			var itemWidth = $aoItems.outerWidth(true) + 4; // Add 4 to acct for inline space
			var itemMargin = itemWidth - $aoItems.outerWidth() - 4; // Remove 1 margin width
			var rowWidth = (itemWidth * colCount) - itemMargin;
			$aoHeader.width(rowWidth).css('padding-right', itemMargin);
		} else {
			$aoHeader.css({
				'width': 'inherit',
				'padding-right': 'inherit'
			});
		}

		if ($aoWidget.is('.wolfnet_aoAgentsList')) {
			for (var i=0, l=itemSections.length; i<l; i++) {
				if (itemSections[i].hasOwnProperty('name') && (itemSections[i].name === 'body')) {
					$aoImages.height(itemSections[i].maxHeight);
					break;
				}
			}
		}

	};


	var getItemSectionsMaxHeights = function ($aoItem, itemSections) {
		var $itemSection, sectionHeight;
		// Get original max height
		for (var i=0, l=itemSections.length; i<l; i++) {
			$itemSection = $aoItem.find(itemSections[i].selector);
			if (($itemSection.length === 0) && $aoItem.is(itemSections[i].selector)) {
				$itemSection = $aoItem;
			}
			sectionHeight = $itemSection.height();
			if (sectionHeight > itemSections[i].origMaxHeight) {
				itemSections[i].origMaxHeight = sectionHeight;
			}
		}
		// Get new max height
		for (var i=0, l=itemSections.length; i<l; i++) {
			$itemSection = $aoItem.find(itemSections[i].selector);
			if (($itemSection.length === 0) && $aoItem.is(itemSections[i].selector)) {
				$itemSection = $aoItem;
			}
			if ($itemSection.length > 0) {
				$itemSection.css('height', 'auto');
				sectionHeight = $itemSection.height();
				if (sectionHeight > itemSections[i].maxHeight) {
					itemSections[i].maxHeight = sectionHeight;
				}
			}
		}
	};


	var resizeTimeout;
	$(window).resize(function () {
		clearTimeout(resizeTimeout);
		resizeTimeout = setTimeout(function () {
			//updateBreakpointClass();
			if ($aoItems.length > 0) {
				resizeAOItems();
			}
		}, 500);
	});

	//updateBreakpointClass();
	if ($aoItems.length > 0) {
		resizeAOItems();
	}


});
