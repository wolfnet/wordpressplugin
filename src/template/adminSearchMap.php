<?php

?>

<div class="wolfnet_wntMainMap" id="<?php echo $mapParams['mapId']; ?>" style="height: 300px;"></div>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var mapParams = <?php echo json_encode($mapParams); ?>;
			var houseoverJson = <?php echo json_encode($houseoverData); ?>;

			var $map = $('#' + mapParams.mapId);

			var onMapLoaded = function () {
				$map.wolfnetMaptracksDriver({
					keyid         : '<?php echo $keyid; ?>',
					houseoverData : houseoverJson,
					houseoverIcon : mapParams.houseoverIcon
				});
			};

			$map.mapTracks({
				mapName:          'pluginMap',
				mapZoomLevel:     mapParams.zoomLevel,
				centerLat:        mapParams.centerLat,
				centerLng:        mapParams.centerLng,
				isMovable:        true,
				allowMouseWheel:  false,
				loaded:           onMapLoaded
			});

		});

	}

</script>
