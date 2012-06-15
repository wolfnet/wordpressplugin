<?php

/**
 * This is an HTML template file for the Listing Film Strip Widget. This template is meant to wrap a 
 * set of listing views. This file should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress.listing.template
 * @title         ListingFilmStrip.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
 
?>

<div id="<?php echo $instanceId; ?>" class="mlsFinder_widget mlsFinder_listingFilmStrip">
	<?php echo ( isset($listingContent) ) ? $listingContent : 'No Listings to Display.'; ?>
</div>

<script type="text/javascript">
	
	jQuery( '#<?php echo $instanceId; ?>' ).mlsFinderFilmStrip( {
		'wait'    : <?php echo $wait; ?>, 
		'waitLen' : <?php echo $waitLen; ?>,
		'speed'   : <?php echo $speed; ?>
	} );
	
</script>