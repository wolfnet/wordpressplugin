<?php

/**
 * This is an HTML template file for the Plugin search manager page in the WordPress admin. This
 * file should ideally contain very little PHP.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    admin.template
 * @title         searchManager.php
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */

?>
<div class="wrap">

	<div id="icon-options-wolfnet" class="icon32"><br></div>

	<h2>WolfNet - Search Manager</h2>

	<p>The <strong>search manager</strong> allows you to create easily create and save custom search
		criteria which you can then use for defining shortcodes and widgets. The wordpress search
		manager works much the same way as the custom URL Search Builder within the MLS Finder
		Admin.</p>

	<p>Custom searches can target any of the search criteria that is available on your property
		search. Keep in mind that some search criteria is more restrictive than others, which means
		less results will be produced. Use the <strong>Results</strong> feature to determine how
		restrictive a search may be. NOTE: the search criteria available on your property search
		is based on the data available in the feed from your MLS. This data is subject to change,
		which may affect custom search strings you generate. WolfNet recommends that you
		periodically review your custom searches to verify that they still produce the expected
		results. If not, you may need to revisit the search manager and create a new custom search.</p>

	<div>



	</div>

	<?php echo $search_form; ?>

</div>