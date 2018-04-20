/**
 * This jQuery plugin can be applied to any number of containers which hold child elements which
 * will then be able to be expanded and collapsed.
 *
 * @title         jquery.wolfnetListingGrid.js
 * @copyright     Copyright (c) 2018, WolfNet Technologies, LLC
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

;(function($, window, document, undefined) {

	// Plugin info
	var plugin = 'wolfnetCollapsible';
	var stateKey = plugin + '.state';

	var resources = {
		item: {
			main:            'wnt-collapsible',
			collapseDefault: 'wnt-collapse-default'
		},
		trigger: {
			main:    'wnt-collapsible-trigger',
			active:  'wnt-trigger-active',
			target:  'data-wnt-target'
		},
		icon: {
			main:       'wnt-icon',
			collapsed:  'wnt-icon-triangle-down',
			expanded:   'wnt-icon-triangle-up'
		}
	};


	var CollapsibleGroup = function ($el, options) {
		this.$el      = $el;
		this.items    = [];
		return this.init(options || {});
	};

		CollapsibleGroup.prototype.init = function (options) {
			options = options || {};
			this.items = this.getItems();
			return this;
		};

		CollapsibleGroup.prototype.getItems = function () {
			var group = this;
			var items = group.items || [];
			var $parent = group.$el;

			$parent.find('.' + resources.item.main).each(function () {
				var item    = this,
					$item   = $(item),
					exists  = false;
				for (var i=0, l=items.length; i<l; i++) {
					if ($item.attr('id') && (items[i].$el.attr('id') == $item.attr('id'))) {
						exists = true;
						break;
					}
				}
				if (!exists) items.push(new Collapsible(item, $parent.get(0)));
			});

			group.items = items;

			return items;

		};

		CollapsibleGroup.prototype.collapseAll = function () {
			for (var i=0, l=this.items.length; i<l; i++) {
				this.items[i].collapse(false);
			}
			return this;
		};


	var Collapsible = function (el, parent) {
		this.el  = el;
		this.$el = $(el);
		this.parent = parent;
		this.$parent = $(parent);
		this.active = true;
		this.trigger = null;
		return this.init();
	};

		Collapsible.prototype.init = function () {
			this.trigger = this.getTrigger();
			if (this.$el.is('.' + resources.item.collapseDefault)) {
				this.collapse();
			}
			return this;
		};

		Collapsible.prototype.getTrigger = function () {
			if (!this.trigger || (this.trigger.$el.length === 0)) {
				var $trigger = this.$parent.find(
					'.' + resources.trigger.main +
					'[' + resources.trigger.target + '="#' + this.$el.attr('id') + '"]'
				);
				this.trigger = new Trigger($trigger.get(0));
				this.trigger.$el.on(plugin + '.toggle', { self: this }, this.onToggle);
			}
			return this.trigger;
		};

		Collapsible.prototype.collapse = function () {
			this.active = false;
			this.trigger.collapse();
			this.$el.hide().trigger(plugin + '.expand');
			return this;
		};

		Collapsible.prototype.expand = function () {
			this.active = true;
			this.trigger.expand();
			this.$el.show().trigger(plugin + '.expand');
			return this;
		};

		Collapsible.prototype.onToggle = function (e) {
			var self = e.data.self;

			if (self.active) {
				self.collapse();
			} else {
				self.expand();
			}

		};


	var Trigger = function (el) {
		this.$el     = $(el);
		this.$icon   = null;
		return this.init();
	};

		Trigger.prototype.init = function () {
			this.$icon   = $('<span>').addClass(resources.icon.main + ' ' + resources.icon.expanded).css({
				'float':        'right',
				'margin-left':  '1em',
				'font-size':    '0.8em',
				'line-height':  '1.3rem',
				'color':        'inherit'
			});

			// Remove icons, classes, and handlers
			this.$el
				.off('.' + resources.item.main)
				.removeClass(resources.trigger.active)
				.find('.' + resources.icon.main).filter(
					'.' + resources.icon.collapsed + ', ' + '.' + resources.icon.expanded
				).remove();

			// Add icon, style, and handler
			this.$el
				.append(this.$icon)
				.css('cursor', 'pointer')
				.on('click.' + resources.item.main, { self: this }, this.onClick);

			return this;

		};

		Trigger.prototype.collapse = function () {
			this.$el.removeClass(resources.trigger.active);
			this.$icon.removeClass(resources.icon.expanded).addClass(resources.icon.collapsed);
			return this;
		};

		Trigger.prototype.expand = function () {
			this.$el.addClass(resources.trigger.active);
			this.$icon.removeClass(resources.icon.collapsed).addClass(resources.icon.expanded);
			return this;
		};

		Trigger.prototype.onClick = function (e) { e.data.self.$el.trigger(plugin + '.toggle'); };


	/* Plugin methods *************************************************************************** */

		var methods = {

			init: function(options)
			{

				return this.each(function () {
					var $this = $(this);
					var pluginData = {};

					// Set up Collapsible
					pluginData.collapsibleGroup = new CollapsibleGroup($this, options);

					// Save data
					$this.data(stateKey, pluginData);

				});

			}

		};


	/* jQuery plugin setup ********************************************************************** */

		$.fn[plugin] = function(method)
		{
			if (methods.hasOwnProperty(method)) {
				return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
			} else if ((typeof method === 'object') || !method) {
				return methods.init.apply(this, arguments);
			} else {
				$.error('Method ' + method + ' does not exist on jQuery.' + plugin);
			}

		};

})(jQuery, window, document);

