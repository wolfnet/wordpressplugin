<?php

/**
 *
 * @title         map.php
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
 */

?>


<div id="<?php echo $mapParams['hideMapId']; ?>" class="wolfnet_showhide">
	<a href="javascript:void(0)" onclick="wolfnet.hideMap('<?php echo $mapParams['mapId']; ?>','<?php echo $mapParams['hideMapId']; ?>','<?php echo $mapParams['showMapId']; ?>');">
		Hide Map
	</a>
</div>

<div id="<?php echo $mapParams['showMapId']; ?>" style="display:none;" class="wolfnet_showhide">
	<a href="javascript:void(0)" onclick="wolfnet.showMap('<?php echo $mapParams['mapId']; ?>','<?php echo $mapParams['hideMapId']; ?>','<?php echo $mapParams['showMapId']; ?>');">
		Show these properties on a map
	</a>
</div>

<div id="<?php echo $mapParams['mapId']; ?>"
	mapName="pluginMap"
	class="wolfnet_wntMainMap"
	brLat="<?php echo $mapParams['brBoundLat']; ?>"
	brLng="<?php echo $mapParams['brBoundLng']; ?>"
	centerLat="<?php echo $mapParams['centerLat']; ?>"
	centerLng="<?php echo $mapParams['centerLng']; ?>"
	mapViewType="map"
	tlLat="<?php echo $mapParams['tlBoundLat']; ?>"
	tlLng="<?php echo $mapParams['tlBoundLng']; ?>"
	zoomLevel="<?php echo $mapParams['zoomLevel'] ?>"
	allowMouseWheel="false"
	hasHouseView="false"
	hasMiniMap="false"
	hasStreetView="false"
	isMoveable="false"
	mapDragType="move"
	showScales="false"
	showView="false"
	showZoom="true">
</div>


<script type="text/javascript">

	jQuery(function ($) {

		var mapParams = <?php echo json_encode($mapParams); ?>;
		var houseoverJson = <?php echo json_encode($houseoverData); ?>;

		var $map = $('#' + mapParams.mapId);

		var onMapLoaded = function () {
			setMapBindFields();
			$map.wolfnetMaptracksDriver({
				houseoverData : houseoverJson,
				houseoverIcon : mapParams.houseoverIcon,
				mapId         : mapParams.mapId
			});
		};

		$map.mapTracks({
			loaded:onMapLoaded,
			/*startingRect: {
				tlLat: "<?php echo $mapParams['tlBoundLat']; ?>",
				tlLng: "<?php echo $mapParams['tlBoundLng']; ?>",
				brLat: "<?php echo $mapParams['brBoundLat']; ?>",
				brLng: "<?php echo $mapParams['brBoundLng']; ?>"
			}*/
		});

		var $bindingFields = {
			centerLat:       $('[data-wnt-map-name=' + $map.mapTracks("getMapName") + '][data-wnt-map-bind=centerLat]'),
			centerLng:       $('[data-wnt-map-name=' + $map.mapTracks("getMapName") + '][data-wnt-map-bind=centerLng]'),
			lrLat:           $('[data-wnt-map-name=' + $map.mapTracks("getMapName") + '][data-wnt-map-bind=lrLat]'),
			lrLng:           $('[data-wnt-map-name=' + $map.mapTracks("getMapName") + '][data-wnt-map-bind=lrLng]'),
			ulLat:           $('[data-wnt-map-name=' + $map.mapTracks("getMapName") + '][data-wnt-map-bind=ulLat]'),
			ulLng:           $('[data-wnt-map-name=' + $map.mapTracks("getMapName") + '][data-wnt-map-bind=ulLng]'),
		};

		var setMapBindFields = function () {
			$bindingFields.centerLat.val("<?php echo $mapParams['centerLat']; ?>");
			$bindingFields.centerLng.val("<?php echo $mapParams['centerLng']; ?>");
			$bindingFields.lrLat.val("<?php echo $mapParams['brBoundLat']; ?>");
			$bindingFields.lrLng.val("<?php echo $mapParams['brBoundLng']; ?>");
			$bindingFields.ulLat.val("<?php echo $mapParams['tlBoundLat']; ?>");
			$bindingFields.ulLng.val("<?php echo $mapParams['tlBoundLng']; ?>");
		}

	});

</script>
