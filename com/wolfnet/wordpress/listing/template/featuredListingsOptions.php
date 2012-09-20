<?php

/**
 * This is an HTML template file for the Listing Film Strip Widget instance Options Form page in the
 * WordPress admin. This file should ideally contain very little PHP.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         featuredListingsOptions.php
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


<div id="<?php echo $instanceId; ?>" class="wolfnet_featuredListingsOptions">

	<input id="<?php echo $directionId; ?>" name="<?php echo $directionName; ?>" type="hidden"
		class="wolfnet_featuredListingsOptions_dirField" />

	<table class="form-table">
		<tr>
			<td><label>Title:</label></td>
			<td><input id="<?php echo $titleId; ?>" name="<?php echo $titleName; ?>" value="<?php echo $titleValue; ?>" type="text" /></td>
		</tr>

		<tr>
			<td><label for="<?php echo $autoPlayId; ?>">Scroll Control</label></td>
			<td>
				<select id="<?php echo $autoPlayId; ?>" name="<?php echo $autoPlayName; ?>"
					class="wolfnet_featuredListingsOptions_autoPlayField">
					<option value="true"<?php echo $autoPlayTrue; ?>>Automatic & Manual</option>
					<option value="false"<?php echo $autoPlayFalse; ?>>Manual Only</option>
				</select>
				<span class="wolfnet_moreInfo">
					Define user control of the scrolling featured properties. When set to Automatic
					&amp; Manual, featured properties scroll upon page load, but the user can
					override the animation via controls displayed on hover. When set to Manual Only,
					the user must activate animation via controls displayed on hover.
				</span>
			</td>
		</tr>



		<tr class="wolfnet_featuredListingsOptions_autoPlayOptions">
			<td colspan="2">

				<fieldset>

					<legend>Automatic Playback Options</legend>

					<table class="form-table">

						<tr>
							<td><label for="<?php echo $directionId; ?>">Direction:</label></td>
							<td>
								<select id="<?php echo $directionId; ?>" name="<?php echo $directionName; ?>"
									class="wolfnet_featuredListingsOptions_autoDirField">
									<option value="right"<?php echo $autoDirectionRight; ?>>Left to Right</option>
									<option value="left"<?php echo $autoDirectionLeft; ?>>Right to Left</option>
								</select>
							</td>
						</tr>

						<tr>
							<td><label for="<?php echo $speedId; ?>">Animation Speed:</label></td>
							<td>
								<input id="<?php echo $speedId; ?>" name="<?php echo $speedName; ?>" type="text"
									value="<?php echo $speedValue; ?>" size="2" maxlength="2" />
								<span class="wolfnet_moreInfo">
									Set the speed for the scrolling animation. Enter a value between
									1 and 99; the higher the number, the slower the speed.
								</span>
							</td>
						</tr>

					</table>

				</fieldset>

			</td>
		</tr>

		<tr>
			<td><label for="<?php echo $ownerTypeId; ?>">Agent/Broker:</label></td>
			<td>
				<select id="<?php echo $ownerTypeId; ?>" name="<?php echo $ownerTypeName; ?>">
					<?php foreach ( $ownerTypes as $ownerType ) { ?>
					<option value="<?php echo $ownerType['value']; ?>"<?php selected( $ownerTypeValue, $ownerType['value'] ); ?>>
						<?php echo $ownerType['label']; ?>
					</option>
					<?php } ?>
				</select>
				<span class="wolfnet_moreInfo">
					Define the properties to be featured. Appropriate properties are included as
					indicated by the name of the option (ie, Agent Then Broker, Agent Only, Broker
					Only).
				</span>
			</td>
		</tr>

		<tr>
			<td><label for="<?php echo $maxResultsId; ?>">Max Results:</label></td>
			<td>
				<input id="<?php echo $maxResultsId; ?>" name="<?php echo $maxResultsName; ?>"
					value="<?php echo $maxResultsValue; ?>" type="text" size="2" maxlength="2" />
				<span class="wolfnet_moreInfo">
					Define the number of properties to be featured.  The maximum number of
					properties that can be included is 50.
				</span>
			</td>
		</tr>

	</table>

</div>

<script type="text/javascript">

	if ( typeof jQuery != 'undefined' ) {

		( function ( $ ) {

			$('#<?php echo $instanceId; ?>').wolfnetFeaturedListingsControls();

			wolfnet.initMoreInfo( $( '#<?php echo $instanceId; ?> .wolfnet_moreInfo' ) );

		} )( jQuery ); /* END: jQuery IIFE */

	} /* END: If jQuery Exists */

</script>