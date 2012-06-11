<?php

/**
 * This is an HTML template file for the Listing Film Strip Widget. This template is meant to wrap a 
 * set of listing views. This file should ideally contain very little PHP.
 *
 * @package			com.mlsfinder.wordpress.listing.template
 * @title			ListingFilmStrip.php
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

$instanceId = uniqid('mlsFinder_listingFilmStrip_');

?>

<div id="<?php echo $instanceId; ?>" class="mlsFinder_widget mlsFinder_listingFilmStrip">
	<?php echo ( isset($listingContent) ) ? $listingContent : 'No Listings to Display.'; ?>
</div>

<script type="text/javascript">
	
	jQuery('#<?php echo $instanceId; ?>').mlsFinderFilmStrip({
		'wait' : <?php echo ($options['wait']) ? 'true' : 'false'; ?>, 
		'waitLen' : <?php echo $options['waitLen'] * 1000 ; ?>,
		'speed' : <?php echo round(($options['speed'] != 0) ? 10 / ($options['speed'] / 100) : 40) ; ?>
	});
	
</script>