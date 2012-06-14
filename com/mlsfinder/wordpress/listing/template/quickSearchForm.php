<?php

/**
 * This is an HTML template file for the Quick Search Form Widget. This file should ideally contain 
 * very little PHP.
 * 
 * @package			com.mlsfinder.wordpress.listing.template
 * @title			quickSearchForm.php
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
 
?>

<div id="<?php echo $instanceId; ?>" class="mlsFinder_widget mlsFinder_quickSearchForm">
	
	<span>QuickSearch</span>
	
	<form id="<?php echo $instanceId; ?>_quickSearchForm" name="quickSearchForm">
		
		<ul>
			<li><a href="javascript:;"><span>Location</span></a></li>
			<li><a href="javascript:;"><span>Listing Number</span></a></li>
		</ul>
		
		<div>
			<input id="<?php echo $instanceId; ?>_wnt_property_id" name="property_id" type="text"
				wnt:hint="House #, Street, City, State, or Zip" />
		</div>
		
		<div>
			
			<label>Price</label>
			
			<div>
				<select id="<?php echo $instanceId; ?>_wnt_min_price" name="min_price">
					<option value="">Min. Price</option>
					<?php foreach ( $prices as $price ) { ?>
					<option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
					<?php } ?>
				</select>
			</div>
			
			<div>
				<select id="<?php echo $instanceId; ?>_wnt_max_price" name="max_price">
					<option value="">Max. Price</option>
					<?php foreach ( $prices as $price ) { ?>
					<option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
					<?php } ?>
				</select>
			</div>
			
		</div>
		
		<div>
			<label for="<?php echo $instanceId; ?>_wnt_min_beds">Beds</label>
			<select id="<?php echo $instanceId; ?>_wnt_min_beds" name="min_bedrooms">
				<option value="">Any</option>
				<?php foreach ( $beds as $bed ) { ?>
				<option value="<?php echo $bed['value']; ?>"><?php echo $bed['label']; ?></option>
				<?php } ?>
			</select>
		</div>
		
		<div>
			<label for="<?php echo $instanceId; ?>_wnt_min_baths">Baths</label>
			<select id="<?php echo $instanceId; ?>_wnt_min_baths" name="min_bathrooms">
				<option value="">Any</option>
				<?php foreach ( $baths as $bath ) { ?>
				<option value="<?php echo $bath['value']; ?>"><?php echo $bath['label']; ?></option>
				<?php } ?>
			</select>
		</div>
		
		<button class="mlsFinder_quickSearchForm_submitButton" name="search" type="submit">Search!</button>
		
	</form>
	
</div>

<script type="text/javascript">
	//var <?php echo $instanceId; ?>_wntQuickSearch = new wntQuickSearch( '<?php echo $instanceId; ?>' );
</script>