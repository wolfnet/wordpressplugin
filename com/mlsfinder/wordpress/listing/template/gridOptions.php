<?php

/**
 * This is an HTML template file for the Grid Widget instance Options Form page in the 
 * WordPress admin. This file should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress.listing.template
 * @title         gridOptions.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div class="mlsFinder_gridOptions">
	
	<p>
		<label>Pause after each listing:</label>
		<input type="checkbox" value="true" id="<?php echo esc_attr( $fields['wait']['id'] ); ?>" 
			name="<?php echo esc_attr( $fields['wait']['name'] ); ?>" 
			<?php echo ( $fields['wait']['value'] == true ) ? 'checked="checked"' : '' ; ?> />
	</p>
	
</div>