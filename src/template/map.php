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


<div class="wolfnet_wntMainMap" id="<?php echo $mapParams['mapId']; ?>"></div>


<script type="text/javascript">

	jQuery(function ($) {

		var mapParams = <?php echo json_encode($mapParams); ?>;
		var houseoverJson = <?php echo json_encode($houseoverData); ?>;

		var $map = $('#' + mapParams.mapId);

		var onMapLoaded = function () {
			$map.wolfnetMaptracksDriver({
				houseoverData : houseoverJson,
				houseoverIcon : mapParams.houseoverIcon,
				mapId         : mapParams.mapId
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

</script>
