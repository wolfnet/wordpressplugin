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

$instanceId = uniqid('mlsFinder_quickSearchForm_');

?>

<div id="<?php echo $instanceId; ?>" class="mlsFinder_widget mlsFinder_quickSearchForm">
	
	<h1 class="quicksearchtitle">QuickSearch</h1>
	<form id="quickSearchForm" name="quickSearchForm" action="index.php?page_id=61">
	<input type="text" name="property_id" size="13" value="MLS#" style="width: 89px;">
	<br>
	<span class="or">OR</span>
	<br>
	<select name="tmp_property_type" onchange="updatePropertyType(this);">
				<option value="">- Property Type -</option>
				<option value="single_family">Single Family</option>
				<option value="condo">Condo</option>
				<option value="townhouse">Townhouse</option>
				<option value="investment">Income</option>
				<option value="lots_acreage">Lots &amp; Acreage</option>
				<option value="loft">Loft</option>
				<option value="duplex">Duplex</option>
				<option value="farm_hobby">Farm/Ranch</option>
			</select>
			<input type="hidden" name="single_family" value="">
			<input type="hidden" name="condo" value="">
			<input type="hidden" name="townhouse" value="">
			<input type="hidden" name="investment" value="">
			<input type="hidden" name="lots_acreage" value="">
			<input type="hidden" name="loft" value="">
			<input type="hidden" name="duplex" value="">
		<input type="hidden" name="farm_hobby" value="">
	<br>
	<select name="citylist">
				<option value="">- City -</option>
			</select>
	<br>
	<select name="min_price">
				<option value="">- Min Price -</option>
				<option value="">No Minimum</option>
				<option value="10000">$10,000</option>
				<option value="20000">$20,000</option>
				<option value="30000">$30,000</option>
			</select>
	<br>
	<select name="max_price">
				<option value="">- Maximum Price -</option>
				<option value="">No Maximum</option>
				<option value="10000">$10,000</option>
				<option value="20000">$20,000</option>
				<option value="30000">$30,000</option>
	</select>
	<br>
	</form>
	<button name="search" type="submit" style="margin-top:9px;">Search!</button>
	
</div>

<script type="text/javascript">
	
</script>