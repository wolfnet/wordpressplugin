<?php

/**
 * This action is responsible for registering any widgets the plugin makes available.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         registerWidgets.php
 * @extends       com_ajmichels_wppf_action_action
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_action_registerWidgets
extends com_ajmichels_wppf_action_action
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
		register_widget( 'com_wolfnet_wordpress_listing_listingListWidget' );
		register_widget( 'com_wolfnet_wordpress_listing_quickSearchWidget' );
	}
	
	
}