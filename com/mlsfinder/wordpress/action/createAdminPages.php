<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 *
 * @package			com.mlsfinder.wordpress.action
 * @title			createAdminPages.php
 * @extends			com_ajmichels_wppf_action_action
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_action_createAdminPages
extends com_ajmichels_wppf_action_action
{
	
	
	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return	void
	 * 
	 */
	public function execute ()
	{
		add_menu_page(	'MLSFinder', 
						'MLSFinder', 
						'administrator', 
						'mlsfinder_plugin_settings', 
						array( &$this, 'pluginSettingsPage' ) );
	}
	
	
	/**
	 * This method is responsible to creating and outputing the plugin settings page within the
	 * WordPress admin.
	 *
	 * @return	void
	 * 
	 */
	public function pluginSettingsPage ()
	{
		$view = $this->getPluginSettingsView();
		$view->out();
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	
	public function getPluginSettingsView ()
	{
		return $this->pluginSettingsView;
	}
	
	
	public function setPluginSettingsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->pluginSettingsView = $view;
	}
	
	
}