(function($){

	var wntPlugin = 'wolfnetMaptracksDriver';
	var stateKey = wntPlugin + '.state';

	var methods =
	{

		/**
		 * This function initializes the plugin.
		 * @param  {Object}  args  An object/map of arguments for the plugin.
		 * @return {jQuery}  The jQuery selection object which the plugin is being applied to.
		 */
		init: function(args) {

			return this.each(function() {

				var wntMapContainer = $('#' + args.mapId);
				var houseoverData = args.houseoverData || [];

				methods.pinHouseovers.call(
					this,
					wntMapContainer,
					houseoverData,
					args.houseoverIcon
				);

				// Size and fit map instance
				methods.autoSizeMap.call(this,wntMapContainer);

				// Bind map auto size to window resize for all maps
				$(window).resize(methods.responsiveMaps);

			});
		},


		pinHouseovers: function(wntMapContainer,houseoverData,icon) {

			var wntMap = wntMapContainer.data('map');

			for (var i in houseoverData) {

				var lat = houseoverData[i].lat;
				var lng = houseoverData[i].lng;

				// Only add pin if coordinates are valid
				if (
					((lat !== 0) || (lng !== 0)) &&
					(!isNaN(lat) || !isNaN(lng)) &&
					(lat !== '' || lng !== '') &&
					((lat >= -180) && (lat <= 180)) &&
					((lng >= -180) && (lng <= 180))
				) {


					// TODO: Build houseover icon object
					//var houseoverIcon = wntMap.mapIcon(icon,20,20);

					// TODO: Build houseover as poi object
					//var houseover = wntMap.poi(
					//	lat,
					//	lng,
					//	houseoverIcon,
					//	houseoverData[i].content,
					//	houseoverData[i].propertyId,
					//	houseoverData[i].propertyUrl
					//);

					// TODO: Pin houseover poi to map if it's within bound ranges
					//if (
					//	(lat >= (wntMap.getBounds().lr.lat) &&
					//	lat <= (wntMap.getBounds().ul.lat)) &&
					//	(lng >=  (wntMap.getBounds().ul.lng) &&
					//	lng <= (wntMap.getBounds().lr.lng))
					//) {
						//wntMap.addPoi(houseover);
					//}

				}


			}
		},


		// Call autoSizeMap on each map instance
		responsiveMaps: function() {
			$('.wolfnet_wntMainMap').each(function() {
				methods.autoSizeMap.call(this,$(this));
			});
		},


		// Resizes a map instance based on parent element width
		autoSizeMap: function(wntMapContainer) {
			var parentWidth = wntMapContainer.parent().width();
			var wntMap = wntMapContainer.data('map');

			if (typeof wntMap !== 'undefined') {

				var mapWidth = wntMap.getSize().width;
				var mapHeight = wntMap.getSize().height;

				// If mapWidth does not equal parentWidth, reset size
				if (mapWidth != parentWidth) {

					// TODO: Set size of map to new width/height
					//wntMap.setSize(parentWidth,mapHeight);
				}

				// Fit map to listings
				// TODO: find Maptracks 3 equivalent of this function (fit map to POI's)
				//wntMap.bestFit();

			}
		}


	};

	$.fn[wntPlugin] = function(method)
	{

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.' + pluginName );
		}

	}

})(jQuery);
