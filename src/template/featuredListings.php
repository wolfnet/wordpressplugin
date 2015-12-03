<?php

/**
 *
 * @title         featuredListings.php
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

<?php if (trim($title)!='') { ?>
    <h2><?php echo $title ?></h2>
<?php } ?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_featuredListings">
    <?php echo (isset($listingsHtml)) ? $listingsHtml : 'No Listings to Display.'; ?>
</div>

<script type="text/javascript">

    jQuery(function($){
        $('#<?php echo $instance_id; ?>').wolfnetScrollingItems({
            autoPlay : <?php echo ($autoplay) ? 'true' : 'false'; ?>,
            direction : <?php echo "'" . $direction . "'"; ?>,
            speed : <?php echo $speed; ?>,
            componentClass: 'wolfnet_featuredListings',
            withControlsClass: 'wolfnet_withControls',
            controlClass: 'wolfnet_control',
            controlLeftClass: 'wolfnet_leftControl',
            controlRightClass: 'wolfnet_rightControl',
            itemClass: 'wolfnet_listing'
        });
    });

</script>
