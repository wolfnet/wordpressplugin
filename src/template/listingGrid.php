<?php

/**
 *
 * @title         listingGrid.php
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

// HACK: Make sure the API key does not get included in the criteria
unset($wpMeta['key']);

?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget <?php echo $class; ?> <?php echo $widgetThemeClass; ?>">

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

    jQuery(function ($) {

        var instance = <?php echo "'#" . $instance_id . "';"; ?>
        var $listingGrid = $(instance);

        var setupThumbnailScroller = function () {
            var $listing = $listingGrid.find('.wolfnet_listing');
            $listing.wolfnetThumbnailScroller({
                keyid: <?php echo (array_key_exists('keyid', $wpMeta) ? $wpMeta['keyid'] : ''); ?>,
                photoSelector: '.wolfnet_listingImage img',
                hideControls: !wolfnet.hasFeature('touch') // If on a touch screen, always show the controls
            });
            $listing
                .on('wolfnet.controlover', function () {
                    $(this).find('.wolfnet_detailsLink').addClass('wolfnet_hidden');
                })
                .on('wolfnet.controlout', function () {
                    $(this).find('.wolfnet_detailsLink').removeClass('wolfnet_hidden');
                });
        };

        var setupToolbar = function () {
            $listingGrid.filter('.wolfnet_withPagination,.wolfnet_withSortOptions').wolfnetToolbar({
                 numrows          : <?php echo $wpMeta['maxrows'] . "\n"; ?>,
                 criteria         : <?php echo json_encode($wpMeta) . "\n"; ?>,
                 maxResults       : <?php echo $maxresults . "\n"; ?>,
                 itemsPerPageData : <?php echo json_encode($itemsPerPage) . "\n"; ?>,
                 sortOptionsData  : <?php echo json_encode($sortOptions) . "\n"; ?>,
                 defaultSort      : <?php echo json_encode($defaultSort) . "\n"; ?>
            });
        };

        var setupListingGrid = function () {
            $listingGrid.filter('.wolfnet_listingGrid').wolfnetListingGrid({
                containerClass: 'wolfnet_listings',
                itemClass: 'wolfnet_listing',
                clearfixClass: 'wolfnet_clearfix',
                gridAlign: '<?php echo $gridalign; ?>'
            });
        };


        setupToolbar();
        setupListingGrid();

        <?php if ($widgetThemeName != 'ash') { ?>
            $listingGrid.on('wolfnet.updated', setupThumbnailScroller);
            setupThumbnailScroller();
        <?php } ?>

    });


    var maptype = '<?php echo "$maptype"; ?>'
    if (maptype == 'map_only') {

        var collapseListingsId = '<?php echo $collapseListingsId; ?>';
        var hideId             = '<?php echo $hideListingsId; ?>';
        var showId             = '<?php echo $showListingsId; ?>';

        wolfnet.hideListings(collapseListingsId,hideId,showId);
    }

</script>
