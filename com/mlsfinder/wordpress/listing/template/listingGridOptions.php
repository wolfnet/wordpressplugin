<?php

/**
 * This is an HTML template file for the Grid Widget instance Options Form page in the 
 * WordPress admin. This file should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing.template
 * @title         listingGridOptions.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

?>

<div class="mlsFinder_listingGridOptions">
	
	<p>
		<label>Price:</label>
		<select id="<?php echo $minPriceId; ?>" name="<?php echo $minPriceName; ?>">
			<option value="">Min. Price</option>
			<?php foreach ( $prices as $price ) { ?>
			<option value="<?php echo $price['value']; ?>"<?php ( $minPriceValue==$price['value'] ) ? ' selected="selected"' : '' ; ?>>
				<?php echo $price['label']; ?>
			</option>
			<?php } ?>
		</select>
		<span>to</span>
		<select id="<?php echo $maxPriceId; ?>" name="<?php echo $maxPriceName; ?>">
			<option value="">Max. Price</option>
			<?php foreach ( $prices as $price ) { ?>
			<option value="<?php echo $price['value']; ?>"<?php ( $maxPriceValue==$price['value'] ) ? ' selected="selected"' : '' ; ?>>
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
		<select id="<?php echo $agentBrokerId; ?>" name="<?php echo $agentBrokerName; ?>">
			<option></option>
			<option></option>
			<option></option>
		</select>
	</p>
	
	<p>
		<label>Max Results:</label>
		<input id="<?php echo $maxResultsId; ?>" name="<?php echo $maxResultsName; ?>" 
			type="text" value="<?php echo $maxResultsValue; ?>" />
	</p>
	
</div>