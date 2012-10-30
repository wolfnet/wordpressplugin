<?php

/**
 * This action is responsible for registering any widgets the plugin makes available.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         registerWidgets.php
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
 *
 */
class com_wolfnet_wordpress_action_registerWidgets
extends com_greentiedev_wppf_action_action
{


	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return  void
	 *
	 */
	public function execute ()
	{
		register_widget( 'com_wolfnet_wordpress_listing_featuredListingsWidget' );
		register_widget( 'com_wolfnet_wordpress_listing_listingGridWidget' );
		register_widget( 'com_wolfnet_wordpress_listing_propertyListWidget' );
		register_widget( 'com_wolfnet_wordpress_listing_quickSearchWidget' );
	}


}
