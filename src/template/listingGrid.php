<?php

/**
 *
 * @title         listingGrid.php
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


// HACK: Make sure the API key does not get included in the criteria
unset($wpMeta['key']);

?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget <?php echo $class; ?>">

    <?php if (trim($title) != '') { ?>
        <h2 class="widget-title"><?php echo $title; ?></h2>
    <?php } ?>

    <?php if (($maptype == 'above' || $maptype == 'map_only') && $mapEnabled) {
        echo $map;
    } ?>

    <?php if ($maptype != 'disabled' && $mapEnabled) {
        echo $hideListingsTools;
    } ?>

    <div id="<?php echo $collapseListingsId; ?>" >

        <?php echo $toolbarTop; ?>

        <div class="wolfnet_listings">
            <?php echo (isset($listingsHtml)) ? $listingsHtml : 'No Listings to Display.'; ?>
        </div>

        <?php echo $toolbarBottom; ?>

    </div>

    <?php if ($maptype == 'below' && $mapEnabled) {
        echo $map;
    } ?>

</div>

<div class="wolfnet_clearfix"></div>

<script type="text/javascript">

    jQuery(function($){
        var instance = <?php echo "'#" . $instance_id . "';"; ?>

        $(instance).filter('.wolfnet_withPagination,.wolfnet_withSortOptions').wolfnetToolbar({
             numrows          : <?php echo $wpMeta['maxrows'] . "\n"; ?>
            ,criteria         : <?php echo json_encode($wpMeta) . "\n"; ?>
            ,maxResults       : <?php echo $wpMeta['total_rows'] . "\n"; ?>
            ,itemsPerPageData : <?php echo json_encode($itemsPerPage) . "\n"; ?>
            ,sortOptionsData  : <?php echo json_encode($sortOptions) . "\n"; ?>
        });

        $(instance).filter('.wolfnet_listingGrid').wolfnetListingGrid({
            containerClass: 'wolfnet_listings',
            itemClass: 'wolfnet_listing',
            clearfixClass: 'wolfnet_clearfix'
        });

    });


    var maptype = '<?php echo "$maptype"; ?>'
    if (maptype == 'map_only') {

        var collapseListingsId = '<?php echo $collapseListingsId; ?>';
        var hideId             = '<?php echo $hideListingsId; ?>';
        var showId             = '<?php echo $showListingsId; ?>';

        wolfnet.hideListings(collapseListingsId,hideId,showId);
    }

</script>
