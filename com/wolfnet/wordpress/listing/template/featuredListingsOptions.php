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
	
	<p>
		<label>Scroll Control</label>
		<select id="<?php echo $autoPlayId; ?>" name="<?php echo $autoPlayName; ?>" 
			class="wolfnet_featuredListingsOptions_autoPlayField">
			<option value="true"<?php echo $autoPlayTrue; ?>>Automatic & Manual</option>
			<option value="false"<?php echo $autoPlayFalse; ?>>Manual Only</option>
		</select>
	</p>
	
	<fieldset class="wolfnet_featuredListingsOptions_autoPlayOptions">
		
		<p>
			<label>Direction:</label>
			<select id="<?php echo $directionId; ?>" name="<?php echo $directionName; ?>" 
				class="wolfnet_featuredListingsOptions_autoDirField">
				<option value="right"<?php echo $directionRight; ?>>Left to Right</option>
				<option value="left"<?php echo $directionLeft; ?>>Right to Left</option>
				<!--<option value="down"<?php echo $directionDown; ?>>Top to Bottom</option>-->
				<!--<option value="up"<?php echo $directionUp; ?>>Bottom to Top</option>-->
			</select>
		</p>
		
		<p>
			<label>Pause after each listing:</label>
			<input id="<?php echo $waitId; ?>" name="<?php echo $waitName; ?>" 
				<?php echo $waitChecked; ?> type="checkbox" value="true" />
		</p>
		
		<p>
			<label>Pause length (seconds):</label>
			<input id="<?php echo $waitLenId; ?>" name="<?php echo $waitLenName; ?>" 
				value="<?php echo $waitLenValue; ?>" type="text" size="2" maxlength="2" />
		</p>
		
		<p>
			<label>Animation Speed:</label>
			<input id="<?php echo $speedId; ?>" name="<?php echo $speedName; ?>" type="text" 
				value="<?php echo $speedValue; ?>" size="4" maxlength="4" />
		</p>
		
		<p>
			<label>Scroll Count:</label>
			<input id="<?php echo $scrollCountId; ?>" name="<?php echo $scrollCountName; ?>" type="text" 
				value="<?php echo $scrollCountValue; ?>" size="4" maxlength="4" />
		</p>
		
	</fieldset>
	
	<fieldset class="wolfnet_featuredListingsOptions_manualPlayOptions">
		
		<p>
			<label>Direction:</label>
			<select id="<?php echo $directionId; ?>" name="<?php echo $directionName; ?>" 
				class="wolfnet_featuredListingsOptions_manDirField">
				<option value="left"<?php echo $directionLeft; ?>>Left and Right</option>
				<!--<option value="up"<?php echo $directionUp; ?>>Up and Down</option>-->
			</select>
		</p>
		
	</fieldset>
	
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
			value="<?php echo $maxResultsValue; ?>" type="text" size="2" maxlength="2" />
	</p>
	
</div>