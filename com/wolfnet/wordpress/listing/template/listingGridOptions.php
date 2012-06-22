<?php

/**
 * This is an HTML template file for the Grid Widget instance Options Form page in the 
 * WordPress admin. This file should ideally contain very little PHP.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         listingGridOptions.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div class="wolfnet_listingGridOptions">
	
	<p>
		<label>Price:</label>
		<select id="<?php echo $minPriceId; ?>" name="<?php echo $minPriceName; ?>">
			<option value="">Min. Price</option>
			<?php foreach ( $prices as $price ) { ?>
			<option value="<?php echo $price['value']; ?>"<?php echo ( $minPriceValue == $price['value'] ) ? ' selected="selected"' : '' ; ?>>
				<?php echo $price['label']; ?>
			</option>
			<?php } ?>
		</select>
		<span>to</span>
		<select id="<?php echo $maxPriceId; ?>" name="<?php echo $maxPriceName; ?>">
			<option value="">Max. Price</option>
			<?php foreach ( $prices as $price ) { ?>
			<option value="<?php echo $price['value']; ?>"<?php echo ( $maxPriceValue == $price['value'] ) ? ' selected="selected"' : '' ; ?>>
				<?php echo $price['label']; ?>
			</option>
			<?php } ?>
		</select>
	</p>
	
	<p>
		<label>City:</label>
		<input id="<?php echo $cityId; ?>" name="<?php echo $cityName; ?>" 
			type="text" value="<?php echo $cityValue; ?>" />
	</p>
	
	<p>
		<label>Zipcode:</label>
		<input id="<?php echo $zipcodeId; ?>" name="<?php echo $zipcodeName; ?>" 
			type="text" value="<?php echo $zipcodeValue; ?>" />
	</p>
	
	<p>
		<label>Agent/Broker:</label>
		<select id="<?php echo $ownerTypeId; ?>" name="<?php echo $ownerTypeName; ?>">
			<?php foreach ( $ownerTypes as $ownerType ) { ?>
			<option value="<?php echo $ownerType['value']; ?>"<?php echo ( $ownerTypeValue == $ownerType['value'] ) ? ' selected="selected"' : '' ; ?>>
				<?php echo $ownerType['label']; ?>
			</option>
			<?php } ?>
		</select>
	</p>
	
	<p>
		<label>Max Results:</label>
		<input id="<?php echo $maxResultsId; ?>" name="<?php echo $maxResultsName; ?>" 
			type="text" value="<?php echo $maxResultsValue; ?>" />
	</p>
	
</div>