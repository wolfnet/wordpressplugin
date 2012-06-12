<?php

/**
 * This is an HTML template file for the listing entity. This specific template is meant to be very 
 * simple with limited information. If more information is need to be displayed on the page a new 
 * template file should be created and made avaiable in the ListingView class. This file should 
 * ideally contain very little PHP.
 * 
 * @package			com.mlsfinder.wordpress.listing.template
 * @title			SimpleListing.php
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div id="mlsFinder_listing_<?php echo $listing->getID(); ?>" class="mlsFinder_listing">
	<a class="listingImage" href="<?php echo $listing->getUrl(); ?>"><img src="<?php echo $listing->getPhoto(); ?>" /></a>
	<span class="detailsLink"><a href="<?php echo $listing->getUrl(); ?>"><?php echo $listing->getLinktext(); ?></a></span>
	<span class="body"><?php echo $listing->getBody(); ?></span>
</div>