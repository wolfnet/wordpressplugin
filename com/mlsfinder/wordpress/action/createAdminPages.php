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
		$url = $this->getPluginUrl();
		add_menu_page(	'MLS Finder', 
						'MLS Finder', 
						'administrator', 
						'mlsfinder_plugin_settings', 
						array( &$this, 'pluginSettingsPage' ),
						$url . '/img/wp_mlsfinder_nav_on.png' );
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
	
	
	/**
	 * SETTER: This method is a setter for the pluginUrl property.
	 *
	 * @param	string	$url	The absolute URL to this plugin's directory.
	 * @return	void
	 * 
	 */
	public function setPluginUrl ( $url )
	{
		$this->pluginUrl = $url;
	}
	
	
	/**
	 * GETTER: This method is a getter for the pluginUrl property.
	 *
	 * @return	string	The absolute URL to this plugin's directory.
	 * 
	 */
	public function getPluginUrl ()
	{
		return $this->pluginUrl;
	}
	
	
}