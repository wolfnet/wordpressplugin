<?php

/**
 * This is an HTML template file for the Quick Search instance Options Form page in the WP Admin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         quickSearchOptions.php
 * @contributors  Andrew Baumgart
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
?>
<div class="wolfnet_quickSearchOptions">

	<table class="form-table">
		<tr>
			<td><label>Title:</label></td>
			<td><input id="<?php echo $titleId; ?>" name="<?php echo $titleName; ?>" value="<?php echo $titleValue; ?>" type="text" /></td>
		</tr>

	</table>

</div>
