<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         registerRewriteRules.php
 * @extends       com_ajmichels_wppf_action_action
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_action_registerRewriteRules
extends com_ajmichels_wppf_action_action
{


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return  void
	 *
	 */
	public function execute ()
	{
		$rule    = '^wolfnet/shortcode-builder/option-form/([^/]*)?';
		$rewrite = 'index.php?pagename=wolfnet_shortcode_builder_option_form&formpage=$matches[1]';
		add_rewrite_rule( $rule, $rewrite, 'top' );
		add_rewrite_tag( '%formpage%', '([^&]+)' );
	}


}