<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         createAdminPages.php
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
class com_wolfnet_wordpress_action_createAdminPages
extends com_greentiedev_wppf_action_action
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a reference to the view page which will be displayed on the primary
	 * plugin admin page.
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $pluginSettingsView;


	/**
	 * This property holds a reference to the view page which will be displayed on the plugin admin
	 * support page.
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $pluginInstructionsView;


	/**
	 * This property holds a reference to the view page which will be displayed on the plugin admin
	 * search manager page.
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $searchManagerView;


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

		add_menu_page(
			'WolfNet',
			'WolfNet',
			'administrator',
			'wolfnet_plugin_settings',
			null,
			$url . '/img/wp_wolfnet_nav.png'
		);

		add_submenu_page(
			'wolfnet_plugin_settings',
			'General Settings',
			'General Settings',
			'administrator',
			'wolfnet_plugin_settings',
			array( &$this, 'pluginSettingsPage' )
		);

		add_submenu_page(
			'wolfnet_plugin_settings',
			'Search Manager',
			'Search Manager',
			'administrator',
			'wolfnet_plugin_search_manager',
			array( &$this, 'searchManagerPage' )
		);

		add_submenu_page(
			'wolfnet_plugin_settings',
			'Support',
			'Support',
			'administrator',
			'wolfnet_plugin_support',
			array( &$this, 'pluginInstructionsPage' )
		);

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


	/**
	 * This method is responsible for creating and outputing the plugin search manager page within
	 * the WordPress admin.
	 *
	 * @return	void
	 *
	 */
	public function searchManagerPage ()
	{
		$this->getSearchManagerView()->out();
	}


	/**
	 * This method is responsible for creating and outputing the plugin support page within the
	 * WordPress admin.
	 *
	 * @return	void
	 *
	 */
	public function pluginInstructionsPage ()
	{
		$this->getPluginInstructionsView()->out();
	}


	/* ACCESSORS ******************************************************************************** */

	/**
	 * GETTER: This method is a getter for the pluginSettingsView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getPluginSettingsView ()
	{
		return $this->pluginSettingsView;
	}


	/**
	 * SETTER: This method is a setter for the pluginSettingsView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setPluginSettingsView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->pluginSettingsView = $view;
	}


	/**
	 * GETTER: This method is a getter for the pluginInstructionsView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getPluginInstructionsView ()
	{
		return $this->pluginInstructionsView;
	}


	/**
	 * SETTER: This method is a setter for the pluginInstructionsView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setPluginInstructionsView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->pluginInstructionsView = $view;
	}


	/**
	 * GETTER: This method is a getter for the searchManagerView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getSearchManagerView ()
	{
		return $this->searchManagerView;
	}


	/**
	 * SETTER: This method is a setter for the searchManagerView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setSearchManagerView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->searchManagerView = $view;
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
