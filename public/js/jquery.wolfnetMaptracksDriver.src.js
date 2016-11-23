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


		pinHouseovers: function(wntMapContainer, houseoverData, icon) {

			var wntMap = wntMapContainer.data('map');
			var mapListings = [];

			for (var i=0, l=houseoverData.length; i<l; i++) {

				// Only add pin if coordinates are valid
				if (
					((houseoverData[i].lat !== 0) || (houseoverData[i].lng !== 0)) &&
					(!isNaN(houseoverData[i].lat) || !isNaN(houseoverData[i].lng)) &&
					(houseoverData[i].lat !== '' || houseoverData[i].lng !== '') &&
					((houseoverData[i].lat >= -180) && (houseoverData[i].lat <= 180)) &&
					((houseoverData[i].lng >= -180) && (houseoverData[i].lng <= 180))
				) {

					mapListings.push({
						propertyId:    houseoverData[i].propertyId,
						lat:           houseoverData[i].lat,
						lng:           houseoverData[i].lng,
						icon:          { src: icon, width:  20, height: 20 },
						propertyType:  'default',
						isGroup:       0,
						html:          '<div class="single">' + houseoverData[i].content + '</div>'
					});


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

			wntMapContainer.mapTracks('addListings', mapListings, true);

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
