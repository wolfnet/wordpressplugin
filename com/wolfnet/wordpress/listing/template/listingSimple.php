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
 *
 */

?>

<div id="wolfnet_listing_<?php echo $id; ?>" class="wolfnet_listing<?php echo $listing_class; ?>" itemscope>
	<a href="<?php echo $url; ?>" rel="follow">
		<span class="wolfnet_listingImage"><img src="<?php echo $image; ?>" /></span>
		<span class="wolfnet_price" itemprop="price">$<?php echo $price; ?></span>
		<span class="wolfnet_bed_bath" title="<?php echo $bedbath_full; ?>"><?php echo $bedbath; ?></span>
		<span title="<?php echo $address_full; ?>">
			<span class="wolfnet_location" itemprop="locality">
				<?php echo $location; ?>
			</span>
			<span class="wolfnet_address">
				<?php echo $address; ?>
			</span>
			<span class="wolfnet_full_address" itemprop="street-address" style="display:none;">
				<?php echo $address_full; ?>
			</span>
		</span>
		<?php if ( $branding_brokerLogo != '' || $branding_content != '' ) { ?>
		<div class="wolfnet_branding">
			<span class="wolfnet_brokerLogo"><img src="<?php echo $branding_brokerLogo; ?>" /></span>
			<span class="wolfnet_brandingMessage"><?php echo $branding_content; ?></span>
		</div>
		<?php } ?>
	</a>
	<?php if ( array_key_exists( '-debug', $_REQUEST ) ) { ?>
	<!-- RAW DATA ----------------------------------------------------------------------------------
	property_id:      <?php echo $rawData['property_id']         . "\n"; ?>
	property_url:     <?php echo $rawData['property_url']        . "\n"; ?>
	listing_price:    <?php echo $rawData['listing_price']       . "\n"; ?>
	agent_listing:    <?php echo $rawData['agent_listing']       . "\n"; ?>
	display_address:  <?php echo $rawData['display_address']     . "\n"; ?>
	city:             <?php echo $rawData['city']                . "\n"; ?>
	state:            <?php echo $rawData['state']               . "\n"; ?>
	thumbnail_url:    <?php echo $rawData['thumbnail_url']       . "\n"; ?>
	photo_url:        <?php echo $rawData['photo_url']           . "\n"; ?>
	bathroom:         <?php echo $rawData['bathroom']            . "\n"; ?>
	bedrooms:         <?php echo $rawData['bedrooms']            . "\n"; ?>
	branding:         <?php echo $rawData['bedrooms']            . "\n"; ?>
		brokerLogo:   <?php echo $rawData_branding['brokerLogo'] . "\n"; ?>
		content:      <?php echo $rawData_branding['content']    . "\n"; ?>
	-------------------------------------------------------------------------------------------- -->
	<?php } ?>
</div>
