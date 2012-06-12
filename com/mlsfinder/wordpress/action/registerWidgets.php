<?php

/**
 * This action is responsible for registering any widgets the plugin makes available.
 * 
 * @package			com.mlsfinder.wordpress.action
 * @title			registerWidgets.php
 * @extends			com_ajmichels_wppf_action_action
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_action_registerWidgets
extends com_ajmichels_wppf_action_action
{
	
	
	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.  It is currently handling the following resources: MLSFinder.min.js,
	 * jquery.filmStrip.min.js, and MLSFinder.min.css.
	 * 
	 * @return void
	 * 
	 */
	public function execute ()
	{
		register_widget( 'com_mlsfinder_wordpress_listing_filmStripWidget' );
		register_widget( 'com_mlsfinder_wordpress_listing_quickSearchWidget' );
	}
	
	
}