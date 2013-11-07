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

if ($mapProvider == 'MapQuest') { 

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

	$mapName = 'search';
	$centerLat = '39.715622999999994';
	$centerLng = '-104.94033299999997';
	$zoomLevel = '6';
	$provider = $mapClient;
	$view = 'map';
	$isMoveable = false;
	$showScales = false;
	$showZoom = false;
	$showView = true;
	$hasMiniMap = false;
	$hasHouseView = false;
	$hasStreetView = false;
	$hasNavControl = false;
	$hasPanControl = false;
	$hasLoading = false;
	$hasExpandedZoom = false;
	$showPoi = false;
	$poiIcon = '';
	$poiLat = '';
	$poiLng = '';
	$id = '';
	$class = 'wntMainMap';
	$fitToRect = false;
	$brLat = '';
	$brLng = '';
	$tlLat = '';
	$tlLng = '';
	$mapViewType = 'map';
	$mapDragType = 'move';
	$allowMouseWheel = 'false';

?>

RENDER MAP.
<div class="wolfnet_map">	
	<div 
		class="<?php echo $class; ?>"
		data-wnt-map
		data-wnt-map-name="<?php echo $mapName; ?>"
		data-wnt-map-hasMiniMap="<?php echo $mapName; ?>"
		data-wnt-map-centerLat="<?php echo $centerLat; ?>"
		data-wnt-map-centerLng="<?php echo $centerLng; ?>"
		data-wnt-map-mapZoomLevel="<?php echo $zoomLevel; ?>"
		data-wnt-map-provider="<?php echo $provider; ?>"
		data-wnt-map-view="<?php echo $view; ?>"
		data-wnt-map-hasHouseView="<?php echo $hasHouseView; ?>"
		data-wnt-map-hasStreetView="<?php echo $hasStreetView; ?>"
		data-wnt-map-isMoveable="<?php echo $isMoveable; ?>"
		data-wnt-map-showScales="<?php echo $showScales; ?>"
		data-wnt-map-showZoom="<?php echo $showZoom; ?>"
		data-wnt-map-showView="<?php echo $showView; ?>"
		data-wnt-map-mapViewType="<?php echo $mapViewType; ?>"
		data-wnt-map-mapDragType="<?php echo $mapDragType; ?>"
		data-wnt-map-allowMouseWheel="<?php echo $allowMouseWheel; ?>"
	>
	</div>
</div>

<script type="text/javascript">
	//initialize php vars needed in script
	var provider = '<?php echo $mapClient; ?>';
</script>
<script type="text/javascript" src="<?php echo $this->url; ?>/js/wolfnetMap.src.js"></script>
