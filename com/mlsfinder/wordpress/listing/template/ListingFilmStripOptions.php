<?php

/**
 * This is an HTML template file for the Listing Film Strip Widget instance Options Form page in the 
 * WordPress admin. This file should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress.listing.template
 * @title         ListingFilmStripOptions.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div class="mlsFinder_listingFilmStripOptions">
	
	<p>
		<label>Pause after each listing:</label>
		<input type="checkbox" value="true" id="<?php echo esc_attr( $fields['wait']['id'] ); ?>" 
			name="<?php echo esc_attr( $fields['wait']['name'] ); ?>" 
			<?php echo ( $fields['wait']['value'] == true ) ? 'checked="checked"' : '' ; ?> />
	</p>
	<p>
		<label>Pause length (seconds):</label>
		<input type="text" size="2" maxlength="2" id="<?php echo esc_attr( $fields['waitLen']['id'] ); ?>" 
			name="<?php echo esc_attr( $fields['waitLen']['name'] ); ?>" 
			value="<?php echo esc_attr( $fields['waitLen']['value'] ); ?>" />
	</p>
	<p>
		<label>Animation Speed:</label>
		<input type="text" size="3" maxlength="3" id="<?php echo esc_attr( $fields['speed']['id'] ); ?>" 
			name="<?php echo esc_attr( $fields['speed']['name'] ); ?>" 
			value="<?php echo esc_attr( $fields['speed']['value'] ); ?>" />
	</p>
	
</div>