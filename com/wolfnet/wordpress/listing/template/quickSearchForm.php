<?php

/**
 * This is an HTML template file for the Quick Search Form Widget. This file should ideally contain
 * very little PHP.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         quickSearchForm.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */

?>

<div id="<?php echo $instanceId; ?>" class="wolfnet_widget wolfnet_quickSearch">


	<?php if ( array_key_exists( 'title', $options ) && trim( $options['title']['value'] ) != '' ) { ?>
		<span class="wolfnet_widgetTitle"><?php echo $options['title']['value']; ?></span>
	<?php } ?>

	<form id="<?php echo $instanceId; ?>_quickSearchForm" class="wolfnet_quickSearch_form"
		name="<?php echo $instanceId; ?>_quickSearchForm" method="get"
		action="<?php echo $formAction; ?>">

		<input name="action" type="hidden" value="newsearchsession" />
		<input name="submit" type="hidden" value="Search" />

		<ul class="wolfnet_searchType">
			<li><a href="javascript:;" wolfnet:search_type="opentxt"><span>Location</span></a></li>
			<li><a href="javascript:;" wolfnet:search_type="mlsnum"><span>Listing Number</span></a></li>
		</ul>

		<div class="wolfnet_searchTypeField">
			<input id="<?php echo $instanceId; ?>_search_text" class="wolfnet_quickSearch_searchText"
				name="search_text" type="text" />
		</div>

		<div class="wolfnet_widgetPrice">

			<label>Price</label>

			<div>
				<select id="<?php echo $instanceId; ?>_min_price" name="min_price">
					<option value="">Min. Price</option>
					<?php foreach ( $prices as $price ) { ?>
					<option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
					<?php } ?>
				</select>
			</div>

			<div>
				<select id="<?php echo $instanceId; ?>_max_price" name="max_price">
					<option value="">Max. Price</option>
					<?php foreach ( $prices as $price ) { ?>
					<option value="<?php echo $price['value']; ?>"><?php echo $price['label']; ?></option>
					<?php } ?>
				</select>
			</div>

			<div class="clearfix"></div>

		</div>

		<div class="wolfnet_widgetBedBath">

			<div class="wolfnet_widgetBeds">
				<label for="<?php echo $instanceId; ?>_min_beds">Beds</label>
				<select id="<?php echo $instanceId; ?>_min_beds" name="min_bedrooms">
					<option value="">Any</option>
					<?php foreach ( $beds as $bed ) { ?>
					<option value="<?php echo $bed['value']; ?>"><?php echo $bed['label']; ?></option>
					<?php } ?>
				</select>
			</div>

			<div class="wolfnet_widgetBaths">

				<label for="<?php echo $instanceId; ?>_min_baths">Baths</label>
				<select id="<?php echo $instanceId; ?>_min_baths" name="min_bathrooms">
					<option value="">Any</option>
					<?php foreach ( $baths as $bath ) { ?>
					<option value="<?php echo $bath['value']; ?>"><?php echo $bath['label']; ?></option>
					<?php } ?>
				</select>

			</div>

		</div>

		<div class="wolfnet_quickSearchFormButton">

			<button class="wolfnet_quickSearchForm_submitButton" name="search" type="submit">Search!</button>

		</div>

	</form>

</div>

<script type="text/javascript">

	if ( typeof jQuery != 'undefined' ) {

		( function ( $ ) {

			$( '#<?php echo $instanceId; ?>' ).wolfnetQuickSearch();

		} )( jQuery ); /* END: jQuery IIFE */

	} /* END: If jQuery Exists */

</script>