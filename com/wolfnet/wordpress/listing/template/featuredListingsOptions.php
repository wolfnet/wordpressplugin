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
 */

?>

<div class="wolfnet_featuredListingsOptions">
	
	<input id="<?php echo $directionId; ?>" name="<?php echo $directionName; ?>" type="hidden" 
		class="wolfnet_featuredListingsOptions_dirField" />
	
	<table class="form-table">
		
		<tr>
			<td><label for="<?php echo $autoPlayId; ?>">Scroll Control</label></td>
			<td>
				<select id="<?php echo $autoPlayId; ?>" name="<?php echo $autoPlayName; ?>" 
					class="wolfnet_featuredListingsOptions_autoPlayField">
					<option value="true"<?php echo $autoPlayTrue; ?>>Automatic & Manual</option>
					<option value="false"<?php echo $autoPlayFalse; ?>>Manual Only</option>
				</select>
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
			</td>
		</tr>
		
		<tr>
			<td><label for="<?php echo $maxResultsId; ?>">Max Results:</label></td>
			<td>
				<input id="<?php echo $maxResultsId; ?>" name="<?php echo $maxResultsName; ?>" 
					value="<?php echo $maxResultsValue; ?>" type="text" size="2" maxlength="2" />
			</td>
		</tr>
		
	</table>
	
</div>

<script type="text/javascript">
	
	if ( typeof jQuery != 'undefined' ) {
		
		( function ( $ ) {
			
			$('.wolfnet_featuredListingsOptions').wolfnetFeaturedListingsControls();
			
		} )( jQuery ); /* END: jQuery IIFE */
		
	} /* END: If jQuery Exists */
	
</script>