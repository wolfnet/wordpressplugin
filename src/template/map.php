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


        var $bindingFields = {
            centerLat:       $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=centerLat]'),
            centerLng:       $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=centerLng]'),
            mapType:         $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=mapType]'),
            lrLat:           $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=lrLat]'),
            lrLng:           $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=lrLng]'),
            ulLat:           $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=ulLat]'),
            ulLng:           $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=ulLng]'),
            //zoom:            $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=zoom]'),
            //mapViewType:     $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=mapViewType]'),
            mapDragType:     $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=mapDragType]'),
            allowMouseWheel: $('[data-wnt-map-name="pluginMap"][data-wnt-map-bind=allowMouseWheel]')
        };

        var setMapBindFields = function () {
            $bindingFields.centerLat.val("<?php echo $mapParams['centerLat']; ?>");
            $bindingFields.centerLng.val("<?php echo $mapParams['centerLng']; ?>");
            //$bindingFields.mapType.val($('#' + mapId).mapTracks("getCurrentView"));
            $bindingFields.lrLat.val("<?php echo $mapParams['brBoundLat']; ?>");
            $bindingFields.lrLng.val("<?php echo $mapParams['brBoundLng']; ?>");
            $bindingFields.ulLat.val("<?php echo $mapParams['tlBoundLat']; ?>");
            $bindingFields.ulLng.val("<?php echo $mapParams['tlBoundLng']; ?>");
            //$bindingFields.zoom.val("<?php echo $mapParams['zoomLevel']; ?>");

            $bindingFields.centerLat.change();
            $bindingFields.centerLng.change();
            //$bindingFields.mapType.change();
            $bindingFields.lrLat.change();
            $bindingFields.lrLng.change();
            $bindingFields.ulLat.change();
            $bindingFields.ulLng.change();
            //$bindingFields.zoom.change();
        }

        setMapBindFields();

    });

</script>
