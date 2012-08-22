<?php

/**
 * This is an HTML template file for the Listing Film Strip Widget. This template is meant to wrap a 
 * set of listing views. This file should ideally contain very little PHP.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         featuredListings.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
 
?>

<?php if ( trim( $options['title']['value'] ) != '' ) { ?>
	<h2><?php echo $options['title']['value']; ?></h2>
<?php } ?>

<div id="<?php echo $instanceId; ?>" class="wolfnet_widget wolfnet_featuredListings">
	<?php echo ( isset($listingContent) ) ? $listingContent : 'No Listings to Display.'; ?>
</div>

<script type="text/javascript">
				
	if ( typeof jQuery != 'undefined' ) {
		
		( function ( $ ) {
			
			$( '#<?php echo $instanceId; ?>' ).wolfnetScrollingItems( {
				'autoPlay'  : <?php echo $autoPlay; ?>, 
				'direction' : '<?php echo $direction; ?>', 
				'speed'     : <?php echo $speed; ?>
			} );
			
		} )( jQuery ); /* END: jQuery IIFE */
		
	} /* END: If jQuery Exists */
	
</script>