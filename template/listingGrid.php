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

?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget <?php echo $class; ?>">

    <?php if (trim($title) != '') { ?>
        <h2 class="widget-title"><?php echo $title; ?></h2>
    <?php } ?>

    <?php echo $toolbarTop; ?>

    <div class="wolfnet_listings">
        <?php echo (isset($listingsHtml)) ? $listingsHtml : 'No Listings to Display.'; ?>
    </div>

    <?php echo $toolbarBottom; ?>

</div>

<div class="wolfnet_clearfix"></div>

<script type="text/javascript">

    jQuery(function($){
        var instance = <?php echo "'#" . $instance_id . "';"; ?>

        $(instance).wolfnetToolbar({
             numrows          : <?php echo $numrows . "\n"; ?>
            ,criteria         : <?php echo ((trim($criteria)!='') ? $criteria : '{}')  . "\n"; ?>
            ,maxResults       : <?php echo $maxresults . "\n"; ?>
            ,itemsPerPageData : <?php echo json_encode($itemsPerPage) . "\n"; ?>
            ,sortOptionsData  : <?php echo json_encode($sortOptions) . "\n"; ?>
        });
        $(instance).filter('.wolfnet_listingGrid').wolfnetListingGrid();
        $(instance).filter('.wolfnet_propertyList').wolfnetPropertyList();

    });

</script>
