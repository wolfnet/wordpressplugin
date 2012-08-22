<?php

/**
 * This is an HTML template file for the Grid Widget. This template is meant to wrap a 
 * set of listing views. This file should ideally contain very little PHP.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         listingList.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div id="<?php echo $instanceId; ?>" class="widget wolfnet_widget wolfnet_listingList">
	
	<?php if ( trim( $options['title']['value'] ) != '' ) { ?>
		
		<h2 class="widget-title"><?php echo $options['title']['value']; ?></h2>
		
	<?php } ?>
	
	<?php echo ( isset($listingContent) ) ? $listingContent : 'No Listings to Display.'; ?>
</div>

<div class="clearfix"></div>

<script type="text/javascript">
	
	if ( typeof jQuery != 'undefined' ) {
		
		( function ( $ ) {
			
			$( '.wolfnet_listingList' ).wolfnetListingList({});
			
		} )( jQuery );
		
	}
	
</script>