<?php

/**
 * This is an HTML template file for the Plugin Settings page in the WordPress admin. This file 
 * should ideally contain very little PHP.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    admin.template
 * @title         pluginSettings.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 * @todo: Add Ajax Key validation.
 * 
 */

?>
<div class="wrap">
	
	<div id="icon-options-wolfnet" class="icon32"><br></div>
	
	<h2>WolfNet - General Settings</h2>
	
	<form method="post" action="options.php">
		
		<?php echo $formHeader; ?>
		
		<fieldset>
			
			<legend><h3>General Settings</h3></legend>
			
			<table class="form-table">
				
				<tr valign="top">
					<th scope="row"><label for="wolfnet_productKey">Product Key</label></th>
					<td>
						<input id="wolfnet_productKey" name="wolfnet_productKey" type="text" 
							value="<?php echo $productKey; ?>" size="50" />
						<p class="description" style="width:400px;">
							This Product Key ties your WordPress site back to the WolfNet
							servers (MLS Finder) so that we can provide you with accurate data 
							tailored to your needs. You will not be able to received any data 
							without a valid key. If you do not have a key please contact WolfNet 
							Technologies at 612-342-0088 or toll free at 1-866-WOLFNET. You may also 
							reach us online via our website <a href="http://wolfnet.com/contact_us.cfm" target="_blank">WolfNet.com</a>.
						</p>
					</td>
				</tr>
				
			</table>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
			
		</fieldset>
		
	</form>
	
</div>