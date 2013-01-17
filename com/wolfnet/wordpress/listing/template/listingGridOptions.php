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
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
				<select id="<?php echo $savedSearchId; ?>" name="<?php echo $savedSearchName; ?>" style="width:200px;">
					<?php $foundOne = false; ?>
					<option value="">-- Saved Search --</option>
					<?php foreach ( $savedSearches as $savedSearch ) { ?>
					<?php $selected = ( $savedSearchValue == $savedSearch->ID ) ? ' selected="selected"' : ''; ?>
					<?php if ( $selected != '' ) { $foundOne = true; }; ?>
					<option value="<?php echo $savedSearch->ID; ?>"<?php echo $selected; ?>>
						<?php echo $savedSearch->post_title; ?>
					</option>
					<?php } ?>
					<?php if ( !$foundOne && ( $criteriaValue != '' && $criteriaValue != '[]' ) ) { ?>
					<option value="deleted" selected="selected">** DELETED **</option>
					<?php } ?>
				</select>
				<span class="wolfnet_moreInfo">
					Select a saved search to define the properties to be displayed. Saved searches
					are created via the Search Manager page within the WolfNet plugin admin section.
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
					Restrict search results by brokerage and/or agent. When All (the default) is
					selected, all matching properties display, regardless of listing brokerage and
					agent. When any of the other options is selected, search results are restricted
					to the site owning agent or brokerage, as indicated by the name of the option
					(ie, Agent Then Broker, Agent Only, Broker Only).
				</span>
			</td>
		</tr>

		<tr>
			<td><label>Pagination Enabled/Disabled:</label></td>
			<td>

				<select id="<?php echo $usesPaginationId; ?>" name="<?php echo $usesPaginationName; ?>" >
					<option value="false" <?php echo $usesPaginationFalse; ?> >Disabled</option>
					<option value="true"  <?php echo $usesPaginationTrue; ?>  >Enabled</option>
				</select>
				<span class="wolfnet_moreInfo">
					Enable to add pagination capabilities to the result set.
					Results per page can be defined below in the Max Results Per Page field.
				</span>


			</td>
		</tr>

		<tr>
			<td><label>Max Results Per Page:</label></td>
			<td>
				<input id="<?php echo $maxResultsId; ?>" name="<?php echo $maxResultsName; ?>"
					type="text" value="<?php echo $maxResultsValue; ?>" />
				<span class="wolfnet_moreInfo">
					Define the number of properties to be included in a search results set.
					The maximum number of properties that can be displayed is 50.
				</span>
			</td>
		</tr>

	</table>

</div>

<script type="text/javascript">

	if ( typeof jQuery != 'undefined' ) {

		( function ( $ ) {

			$('#<?php echo $instanceId; ?>').wolfnetListingGridControls();

			$( document ).ready( function () {
				wolfnet.initMoreInfo( $( '#<?php echo $instanceId; ?> .wolfnet_moreInfo' ) );
			} );

		} )( jQuery ); /* END: jQuery IIFE */

	} /* END: If jQuery Exists */

</script>
