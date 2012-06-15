<?php

/**
 * This is an HTML template file for the Plugin Settings page in the WordPress admin. This file 
 * should ideally contain very little PHP.
 * 
 * @package       com.mlsfinder.wordpress
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
	
	<div id="icon-options-mlsfinder" class="icon32"><br></div>
	
	<h2>MLS Finder - Plugin Settings</h2>
	
	<form method="post" action="options.php">
		
		<?php echo $formHeader; ?>
		
		<fieldset>
			
			<legend><h3>General Settings</h3></legend>
			
			<table class="form-table">
				
				<tr valign="top">
					<th scope="row"><label for="wnt_productKey">Product Key</label></th>
					<td>
						<input id="wnt_productKey" name="wnt_productKey" type="text" 
							value="<?php echo $productKey; ?>" size="50" />
						<p class="description">
							This Product Key ties your WordPress site back to the MLS Finder<br/>
							servers so that we can provide you with accurate data tailored to your<br/>
							needs. You will not be able to received any data without a valid key.<br/>
							If you do not have a key please contact WolfNet Technologies at </br>
							612-342-0088 or toll free at 1-866-WOLFNET. You may also reach us <br/>
							online via our website <a href="http://wolfnet.com/contact_us.cfm" target="_blank">WolfNet.com</a>.
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