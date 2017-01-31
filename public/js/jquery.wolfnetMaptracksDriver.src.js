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
			var mapListings = [];

			for (var i=0, l=houseoverData.length; i<l; i++) {

				var coords = { lat: Number(houseoverData[i].lat), lng: Number(houseoverData[i].lng) };

				// Only add pin if coordinates are valid
				if (
					!isNaN(coords.lat) && !isNaN(coords.lng) &&
					(coords.lat !== 0) && (coords.lng !== 0) &&
					(coords.lat >= -180) && (coords.lat <= 180) &&
					(coords.lng >= -180) && (coords.lng <= 180)
				) {

					mapListings.push({
						propertyId:    houseoverData[i].propertyId,
						lat:           coords.lat,
						lng:           coords.lng,
						icon:          { src: icon, width: 30, height: 30 },
						propertyType:  'default',
						isGroup:       0,
						html:          '<div class="single">' + houseoverData[i].content + '</div>'
					});

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

			// Check if MapTracks is loaded
			if (wntMapContainer.data('mapViewType') || wntMapContainer.data('map')) {
				var mapSize = wntMapContainer.mapTracks('getSize');
				var mapOuterWidth = wntMapContainer.outerWidth();
				var mapOuterDiff = mapOuterWidth - mapSize.width;

				// If mapWidth does not equal parentWidth, reset size
				if (mapOuterWidth != parentWidth) {
					wntMapContainer.mapTracks('setSize', parentWidth - mapOuterDiff, mapSize.height);
				}

				// Fit map to listings
				wntMapContainer.mapTracks('bestFit');

			}
		},


		// Map-Tracking
		mapTrack: function () {
			/*if (links.hasOwnProperty('mapTrack')) {
				$.ajax({
					dataType: "json",
					type:     "POST",
					url:      links.mapTrack,
					data:     { mapType: $map.mapTracks('getCurrentView') }
				});
			}*/
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
