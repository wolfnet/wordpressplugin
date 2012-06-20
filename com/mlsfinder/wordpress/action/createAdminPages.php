<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    action
 * @title         createAdminPages.php
 * @extends       com_ajmichels_wppf_action_action
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_mlsfinder_wordpress_action_createAdminPages
extends com_ajmichels_wppf_action_action
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property holds a reference to the view page which will be displayed in the plugin admin.
	 * 
	 * @type  com_ajmichels_wppf_interface_iView
	 * 
	 */
	private $pluginSettingsView;
	
	
	/**
	 * This property holds the absolute URL to the plugin directory. This property is used to define 
	 * resources such as images and javascript files.
	 * 
	 * @type  string  The absolute URL to this plugin's directory.
	 * 
	 */
	private $pluginUrl;
	
	
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
		$url = $this->getPluginUrl();
		add_menu_page(	'MLS Finder', 
						'MLS Finder', 
						'administrator', 
						'mlsfinder_plugin_settings', 
						array( &$this, 'pluginSettingsPage' ),
						$url . '/img/wp_mlsfinder_nav_on.png' );
	}
	
	
	/**
	 * This method is responsible for creating and outputing the plugin settings page within the
	 * WordPress admin.
	 *
	 * @return	void
	 * 
	 */
	public function pluginSettingsPage ()
	{
		$this->getPluginSettingsView()->out();
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * GETTER: This method is a getter for the pluginSettingsView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 * 
	 */
	public function getPluginSettingsView ()
	{
		return $this->pluginSettingsView;
	}
	
	
	/**
	 * SETTER: This method is a setter for the pluginSettingsView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $view
	 * @return  void
	 * 
	 */
	public function setPluginSettingsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->pluginSettingsView = $view;
	}
	
	
	/**
	 * GETTER: This method is a getter for the pluginUrl property.
	 *
	 * @return  string  The absolute URL to this plugin's directory.
	 * 
	 */
	public function getPluginUrl ()
	{
		return $this->pluginUrl;
	}
	
	
	/**
	 * SETTER: This method is a setter for the pluginUrl property.
	 *
	 * @param   string  $url  The absolute URL to this plugin's directory.
	 * @return  void
	 * 
	 */
	public function setPluginUrl ( $url )
	{
		$this->pluginUrl = $url;
	}
	
	
}