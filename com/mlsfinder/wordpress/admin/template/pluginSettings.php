<?php

/**
 * This is an HTML template file for the Plugin Settings page in the WordPress admin. This file should
 * ideally contain very little PHP.
 *
 * @package			com.mlsfinder.wordpress.admin.template
 * @title			pluginSettings.php
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 * @todo: Add Ajax Key validation.
 * 
 */

?>
<div class="wrap">
	
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
							servers So that we can provide you with accurate data tailored to your<br/>
							needs. You will not be able to received any data without a valid key.
						</p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><label for="wnt_searchSolutionURL">Search Solution URL</label></th>
					<td>
						<input id="wnt_searchSolutionURL" name="wnt_searchSolutionURL" type="text" 
							value="<?php echo $searchSolutionURL; ?>" size="50" />
						<p class="description">
							This URL is used by the plugin to determine where to take users <br/>
							when they click on a property listing or perform a property search.<br/>
							Ex. http://search.mydomain.com
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