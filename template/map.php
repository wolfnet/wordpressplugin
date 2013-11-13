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

	$mapClient = 'mapquest'; ?>

	<script type="text/javascript">
		Key = 'mjtd%7Clu612007nq%2C20%3Do5-50zah';
		STATSERVER=HYBSERVER=MAPSERVER='tile21.mqcdn.com,tile22.mqcdn.com,tile23.mqcdn.com,tile24.mqcdn.com'.split(',');
		COVSERVER='coverage.mqcdn.com';
		RESSERVER='tile21.mqcdn.com';
		LOGSERVER='btilelog.beta.mapquest.com';
		STATICSERVER='btileprint.access.mapquest.com';
		TRAFFSERVER='btraffic.access.mapquest.com';
		GASSERVER='gasdata.web.mapquest.com';
		MQPLATFORMSERVER='http://platform.beta.mapquest.com';
		MQROUTEURL=MQPLATFORMSERVER+'/directions/v1';
		MQLONGURL=MQPLATFORMSERVER+'/longurl/v1';
		MQLOGURL=MQPLATFORMSERVER+'/logger/v1';
		function $pv() {};
	</script>
	<script src="//www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key=Gmjtd%7Clu6znua2n9%2C7l%3Do5-la70q"></script>

<?php }	

else {

	$mapClient = 'bing'; ?>

	<script type="text/javascript" src="http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2"></script>
	<script type="text/javascript" src="http://ecn.dev.virtualearth.net/mapcontrol/v6.3/js/atlascompat.js"></script>

<?php }

$centerLat = $map_start_lat;
$centerLng = $map_start_lng;
$zoomLevel = $map_start_scale;
$provider  = $mapClient;

?>

<div class="wolfnet_wntMainMap"
	 data-wnt-map
	 data-wnt-map-name="search"
	 data-wnt-map-hasMiniMap="false"
	 data-wnt-map-centerLat="<?php echo $centerLat; ?>"
	 data-wnt-map-centerLng="<?php echo $centerLng; ?>"
	 data-wnt-map-mapZoomLevel="<?php echo $zoomLevel; ?>"
	 data-wnt-map-provider="<?php echo $provider; ?>"
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
	var provider = '<?php echo $mapClient; ?>';
	var listingsData = '<?php echo $listingsData; ?>';
</script>

<script type="text/javascript" src="<?php echo $this->url; ?>/js/wolfnetMaptracks.src.js"></script>

<?php
/*
	$houseoverHtml  = '<table class="wntHOTable"><tbody><tr>';

	foreach ($listingsData as $key) {

	}

	$houseoverHtml .= '</tr></tbody></table>';

	exit;
*/	
?>

<!--<script type="text/javascript" src="<?php echo $this->url; ?>/js/wolfnetHouseovers.src.js"></script>-->