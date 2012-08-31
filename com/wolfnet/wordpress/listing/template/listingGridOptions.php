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

<div id="<?php echo $instanceId; ?>" class="wolfnet_listingGridOptions">

	<input id="<?php echo $criteriaId; ?>" name="<?php echo $criteriaName; ?>" value="<?php echo $criteriaValue; ?>" type="hidden" />

	<table class="form-table">

		<tr>
			<td><label>Title:</label></td>
			<td><input id="<?php echo $titleId; ?>" name="<?php echo $titleName; ?>" value="<?php echo $titleValue; ?>" type="text" /></td>
		</tr>

		<tr class="modeField">
			<td><label>Mode:</label></td>
			<td>
				<input id="<?php echo $modeId; ?>" name="<?php echo $modeName; ?>" value="basic" type="radio" <?php echo $modeBasic; ?> /> Basic <br/>
				<input id="<?php echo $modeId; ?>" name="<?php echo $modeName; ?>" value="advanced" type="radio" <?php echo $modeAdvanced; ?> /> Advanced
			</td>
		</tr>

		<tr class="advanced-option savedSearchField">
			<td><label>Saved Search:</label></td>
			<td>
				<select id="<?php echo $savedSearchId; ?>" name="<?php echo $savedSearchName; ?>">
					<?php $foundOne = false; ?>
					<option value="">-- Saved Search --</option>
					<?php foreach ( $savedSearches as $savedSearch ) { ?>
					<?php $selected = ( $savedSearchValue == $savedSearch->ID ) ? ' selected="selected"' : ''; ?>
					<?php if ( $selected != '' ) { $foundOne = true; }; ?>
					<option value="<?php echo $savedSearch->ID; ?>"<?php echo $selected; ?>>
						<?php echo $savedSearch->post_title; ?>
					</option>
					<?php } ?>
					<?php if ( !$foundOne && $modeAdvanced != '' ) { ?>
					<option value="deleted" selected="selected">** DELETED **</option>
					<?php } ?>
				</select>
				<span class="wolfnet_moreInfo">
					Saved searches are created on the "Search Manager" page within the WolfNet plugin admin section.
				</span>
			</td>
		</tr>

		<tr class="basic-option">
			<td><label>Price:</label></td>
			<td>
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
			</td>
		</tr>

		<tr class="basic-option">
			<td><label>City:</label></td>
			<td>
				<input id="<?php echo $cityId; ?>" name="<?php echo $cityName; ?>"
					type="text" value="<?php echo $cityValue; ?>" />
			</td>
		</tr>

		<tr class="basic-option">
			<td><label>Zipcode:</label></td>
			<td>
				<input id="<?php echo $zipcodeId; ?>" name="<?php echo $zipcodeName; ?>"
					type="text" value="<?php echo $zipcodeValue; ?>" />
			</td>
		</tr>

		<tr>
			<td><label>Agent/Broker:</label></td>
			<td>
				<select id="<?php echo $ownerTypeId; ?>" name="<?php echo $ownerTypeName; ?>">
					<option value="all">All</option>
					<?php foreach ( $ownerTypes as $ownerType ) { ?>
					<option value="<?php echo $ownerType['value']; ?>"<?php echo ( $ownerTypeValue == $ownerType['value'] ) ? ' selected="selected"' : '' ; ?>>
						<?php echo $ownerType['label']; ?>
					</option>
					<?php } ?>
				</select>
				<span class="wolfnet_moreInfo">
					If set to 'All' (the default) the grid will show listings regardless of whether
					they are featured by either a broker or the agent.
				</span>
			</td>
		</tr>

		<tr>
			<td><label>Max Results:</label></td>
			<td>
				<input id="<?php echo $maxResultsId; ?>" name="<?php echo $maxResultsName; ?>"
					type="text" value="<?php echo $maxResultsValue; ?>" />
				<span class="wolfnet_moreInfo">
					The maximum number of listings which will be displayed. It is possible for there
					to be fewer listings displayed than the value of this field. This field is
					capped at 50.
				</span>
			</td>
		</tr>

	</table>

</div>

<script type="text/javascript">

	if ( typeof jQuery != 'undefined' ) {

		( function ( $ ) {

			$('#<?php echo $instanceId; ?>').wolfnetListingGridControls();

			wolfnet.initMoreInfo( $( '.wolfnet_moreInfo' ) );

		} )( jQuery ); /* END: jQuery IIFE */

	} /* END: If jQuery Exists */

</script>