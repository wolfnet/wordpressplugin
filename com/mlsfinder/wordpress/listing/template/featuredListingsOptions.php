<?php

/**
 * This is an HTML template file for the Listing Film Strip Widget instance Options Form page in the 
 * WordPress admin. This file should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing.template
 * @title         featuredListingsOptions.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div class="mlsFinder_featuredListingsOptions">
	
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
	
	<p>
		<label>Agent/Broker:</label>
		<select id="<?php echo esc_attr( $fields['ownerType']['id'] ); ?>" name="<?php echo esc_attr( $fields['ownerType']['name'] ); ?>">
			<?php foreach ( $ownerTypes as $ownerType ) { ?>
			<option value="<?php echo $ownerType['value']; ?>"<?php echo ( $fields['ownerType']['value'] == $ownerType['value'] ) ? ' selected="selected"' : '' ; ?>>
				<?php echo $ownerType['label']; ?>
			</option>
			<?php } ?>
		</select>
	</p>
	
	<p>
		<label>Max Results:</label>
		<input type="text" size="2" maxlength="2" id="<?php echo esc_attr( $fields['maxResults']['id'] ); ?>" 
			name="<?php echo esc_attr( $fields['maxResults']['name'] ); ?>" 
			value="<?php echo esc_attr( $fields['maxResults']['value'] ); ?>" />
	</p>
	
</div>