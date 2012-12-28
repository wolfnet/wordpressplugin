<?php

/**
 * This is an HTML template file for the listing entity. This specific template is meant to be very
 * simple with limited information. If more information is need to be displayed on the page a new
 * template file should be created and made avaiable in the ListingView class. This file should
 * ideally contain very little PHP.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         SimpleListing.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
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
 *
 */

?>

<div id="wolfnet_listing_<?php echo $id; ?>" class="wolfnet_listing" itemscope>
	<a href="<?php echo $url; ?>" title="<?php echo $address_full . ' - ' . $price; ?>">
		<span class="wolfnet_full_address"><?php echo $address_full; ?></span>
		<span class="wolfnet_price" itemprop="price">$<?php echo $price; ?></span>
		<span itemprop="street-address" style="display:none;"><?php echo $address_full; ?></span>
	</a>
	<!-- RAW DATA ----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------------------
	property_id:      <?php echo $rawData['property_id']         . "\n"; ?>
	property_url:     <?php echo $rawData['property_url']        . "\n"; ?>
	listing_price:    <?php echo $rawData['listing_price']       . "\n"; ?>
	display_address:  <?php echo $rawData['display_address']     . "\n"; ?>
	city:             <?php echo $rawData['city']                . "\n"; ?>
	state:            <?php echo $rawData['state']               . "\n"; ?>
	-------------------------------------------------------------------------------------------- -->
</div>
