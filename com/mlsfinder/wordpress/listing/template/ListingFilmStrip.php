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

$instanceId	= uniqid( 'mlsFinder_listingFilmStrip_' );

$wait		= 'false';
if ( is_bool( $options['wait'] ) && $options['wait'] ) {
	$wait		= 'true';
}

$waitLen	= 1000;
if ( is_numeric( $options['waitLen'] ) ) {
	$waitLen	= $options['waitLen'] * 1000;
}

$speed		= 40;
if ( is_numeric( $options['speed'] ) && $options['speed'] != 0 ) {
	$speed		= round( 10 / ( $options['speed'] / 100 ) );
}

?>

<div id="<?php echo $instanceId; ?>" class="mlsFinder_widget mlsFinder_listingFilmStrip">
	<?php echo ( isset($listingContent) ) ? $listingContent : 'No Listings to Display.'; ?>
</div>

<script type="text/javascript">
	
	jQuery( '#<?php echo $instanceId; ?>' ).mlsFinderFilmStrip( {
		'wait'		: <?php echo $wait; ?>, 
		'waitLen'	: <?php echo $waitLen; ?>,
		'speed'		: <?php echo $speed; ?>
	} );
	
</script>