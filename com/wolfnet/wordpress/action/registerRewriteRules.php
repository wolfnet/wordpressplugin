<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         registerRewriteRules.php
 * @extends       com_greentiedev_wppf_action_action
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
class com_wolfnet_wordpress_action_registerRewriteRules
extends com_greentiedev_wppf_action_action
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
		$rule    = '^wolfnet/admin/shortcodebuilder/optionform/([^/]*)?';
		$rewrite = 'index.php?pagename=wolfnet-admin-shortcodebuilder-optionform&formpage=$matches[1]';
		add_rewrite_rule( $rule, $rewrite, 'top' );
		add_rewrite_tag( '%formpage%', '([^&]+)' );

		$rule    = '^wolfnet/admin/searchmanager/save?';
		$rewrite = 'index.php?pagename=wolfnet-admin-searchmanager-save';
		add_rewrite_rule( $rule, $rewrite, 'top' );
	}


}
