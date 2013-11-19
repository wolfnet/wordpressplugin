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

<?php

if ($maptracks_map_provider == 'MapQuest') { 
	$mapClient = 'mapquest';
}	
else {
	$mapClient = 'bing';
}

$centerLat = $map_start_lat;
$centerLng = $map_start_lng;
$zoomLevel = $map_start_scale;
$elemId = uniqid('wntMapTrack');
?>

<div id="<?php echo $elemId; ?>" 
	 class="wolfnet_wntMainMap"
	 data-wnt-map
	 data-wnt-map-name="search"
	 data-wnt-map-hasMiniMap="false"
	 data-wnt-map-centerLat="<?php echo $centerLat; ?>"
	 data-wnt-map-centerLng="<?php echo $centerLng; ?>"
	 data-wnt-map-mapZoomLevel="<?php echo $zoomLevel; ?>"
	 data-wnt-map-provider="<?php echo $mapClient; ?>"
	 data-wnt-map-view="map"
	 data-wnt-map-hasHouseView="false"
	 data-wnt-map-hasStreetView="false"
	 data-wnt-map-isMoveable="false"
	 data-wnt-map-showScales="false"
	 data-wnt-map-showZoom="false"
	 data-wnt-map-showView="false"
	 data-wnt-map-mapViewType="map"
	 data-wnt-map-mapDragType="move"
	 data-wnt-map-allowMouseWheel="false" >
</div>


<script type="text/javascript">
    
    jQuery(function($){
		var elemId = '<?php echo $elemId; ?>';

        $('#' + elemId).wolfnetMapTracks({
        	houseoverData : <?php echo json_encode($houseoverData); ?>,
        	houseoverIcon : '<?php echo $houseoverIcon; ?>',
        	mapId         : '<?php echo $elemId; ?>'
    	});

    });

</script>
