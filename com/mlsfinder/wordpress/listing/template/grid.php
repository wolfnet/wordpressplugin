<?php

/**
 * This is an HTML template file for the Grid Widget. This template is meant to wrap a 
 * set of listing views. This file should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress.listing.template
 * @title         grid.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div id="<?php echo $instanceId; ?>" class="mlsFinder_widget mlsFinder_grid">
	<?php echo ( isset($listingContent) ) ? $listingContent : 'No Listings to Display.'; ?>
	<div class="clearfix"></div>
</div>