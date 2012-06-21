<?php

/**
 * This is an HTML template file for the listing entity. This specific template is meant to be very 
 * simple with limited information. If more information is need to be displayed on the page a new 
 * template file should be created and made avaiable in the ListingView class. This file should 
 * ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress.listing.template
 * @title         SimpleListing.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div id="mlsFinder_listing_<?php echo $id; ?>" class="mlsFinder_listing">
	<a href="<?php echo $url; ?>" 
		title="<?php echo $address; ?>" target="_blank">
		<span class="listingImage"><img src="<?php echo $image; ?>" /></span>
		<span class="price">$<?php echo $price; ?></span>
		<span class="location"><?php echo $location; ?></span>
		<span class="bed-bath"><?php echo $bedbath; ?></span>
		<?php if ( $branding_brokerLogo != '' || $branding_content != '' ) { ?>
		<div class="branding">
			<span class="brokerLogo"><img src="<?php echo $branding_brokerLogo; ?>" /></span>
			<span class="brandingMessage"><?php echo $branding_content; ?></span>
		</div>
		<?php } ?>
	</a>
	<!-- RAW DATA ----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------------------
	property_id:      <?php echo $rawData['property_id']         . "\n"; ?>
	property_url:     <?php echo $rawData['property_url']        . "\n"; ?>
	listing_price:    <?php echo $rawData['listing_price']       . "\n"; ?>
	agent_listing:    <?php echo $rawData['agent_listing']       . "\n"; ?>
	display_address:  <?php echo $rawData['display_address']     . "\n"; ?>
	city:             <?php echo $rawData['city']                . "\n"; ?>
	state:            <?php echo $rawData['state']               . "\n"; ?>
	thumbnail_url:    <?php echo $rawData['thumbnail_url']       . "\n"; ?>
	bathroom:         <?php echo $rawData['bathroom']            . "\n"; ?>
	bedrooms:         <?php echo $rawData['bedrooms']            . "\n"; ?>
	branding:         <?php echo $rawData['bedrooms']            . "\n"; ?>
		brokerLogo:   <?php echo $rawData_branding['brokerLogo'] . "\n"; ?>
		content:      <?php echo $rawData_branding['content']    . "\n"; ?>
	-------------------------------------------------------------------------------------------- -->
</div>