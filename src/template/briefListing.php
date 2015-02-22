<?php

/**
 *
 * @title         briefListing.php
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

<div id="wolfnet_listing_<?php echo $listing['property_id']; ?>" class="wolfnet_listing" itemscope>
    <a href="<?php echo $listing['property_url']; ?>" title="<?php echo $listing['address'] . ' - ' . $listing['listing_price']; ?>" rel="follow">
        <span class="wolfnet_full_address"><?php echo $listing['address']; ?></span>
        <span class="wolfnet_price" itemprop="price"><?php echo $listing['listing_price']; ?></span>
        <span itemprop="street-address" style="display:none;"><?php echo $listing['address']; ?></span>
    </a>
</div>
