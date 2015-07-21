<?php

/**
 *
 * @title         map.php
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
	 class="wolfnet_wntMainMap"
	 data-wnt-map
	 data-wnt-map-name="pluginMap"
	 data-wnt-map-hasMiniMap="false"
	 data-wnt-map-centerLat="<?php echo $mapParams['centerLat']; ?>"
	 data-wnt-map-centerLng="<?php echo $mapParams['centerLng']; ?>"
	 data-wnt-map-mapZoomLevel="<?php echo $mapParams['zoomLevel'] ?>"
	 data-wnt-map-provider="<?php echo $mapParams['mapProvider']; ?>"
	 data-wnt-map-view="map"
	 data-wnt-map-hasHouseView="false"
	 data-wnt-map-hasStreetView="false"
	 data-wnt-map-isMoveable="false"
	 data-wnt-map-showScales="false"
	 data-wnt-map-showZoom="true"
	 data-wnt-map-showView="false"
	 data-wnt-map-mapViewType="map"
	 data-wnt-map-mapDragType="move"
	 data-wnt-map-allowMouseWheel="false" >
</div>


<script type="text/javascript">

    jQuery(function($){
		var mapId = "<?php echo $mapParams['mapId']; ?>";

        $('#' + mapId).wolfnetMapTracks({
        	houseoverData : <?php echo json_encode($houseoverData); ?>,
        	houseoverIcon : "<?php echo $mapParams['houseoverIcon']; ?>",
        	mapId         : "<?php echo $mapParams['mapId']; ?>"
    	});
    });

</script>
