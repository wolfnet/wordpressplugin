<?php

/**
 * This view is responsible for displaying the plugin admin page.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    admin
 * @title         searchManagerView.php
 * @extends       com_greentiedev_wppf_abstract_view
 * @implements    com_greentiedev_wppf_interface_iView
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
class com_wolfnet_wordpress_admin_searchManagerView
extends com_greentiedev_wppf_abstract_view
implements com_greentiedev_wppf_interface_iView
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type  string
	 *
	 */
	public $template;


	private $settingsService;


	private $pluginUrl;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\searchManager.php' );
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method establishes variable values which will be used by the template when it is render,
	 * then the data is passed to to inharited render method.
	 *
	 * @param   array  $data  An associative array of data for the template. Each array key will be
	 *                        transformed into a variable.
	 * @return  string
	 *
	 */
	public function render ( $data = array() )
	{
		if ( !$this->getSettingsService()->isKeyValid() ) {
			$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\invalidProductKey.php' );
		}
		else {
			$data['search_form']  = '<script type="text/javascript">';
			$data['search_form'] .= 'var wntcfid = "' . $this->getSearchService()->getCfId() . '";';
			$data['search_form'] .= 'var wntcftoken = "' . $this->getSearchService()->getCfToken() . '";';
			$data['search_form'] .= '</script>';
			$data['search_form'] .= $this->getSearchService()->getSearchManagerHtml();
		}
		$this->log( $this->getSearchService()->getCfId() );
		$this->log( $this->getSearchService()->getCfToken() );
		$data['pluginUrl'] = $this->getPluginUrl();
		return parent::render( $data );
	}


	/* ACCESSORS ******************************************************************************** */


	/**
	 * GETTER: This method is a getter for the settingsService property.
	 *
	 * @return  com_wolfnet_wordpress_settings_service
	 *
	 */
	public function getSettingsService ()
	{
		return $this->settingsService;
	}


	/**
	 * SETTER: This method is a setter for the settingsService property.
	 *
	 * @param   com_wolfnet_wordpress_settings_service  $service
	 * @return  void
	 *
	 */
	public function setSettingsService ( com_wolfnet_wordpress_settings_service $service )
	{
		$this->settingsService = $service;
	}


	/**
	 * GETTER: This method is a getter for the searchService property.
	 *
	 * @return  com_wolfnet_wordpress_search_service
	 *
	 */
	public function getSearchService ()
	{
		return $this->searchService;
	}


	/**
	 * SETTER: This method is a setter for the searchService property.
	 *
	 * @param   com_wolfnet_wordpress_search_service  $service
	 * @return  void
	 *
	 */
	public function setSearchService ( com_wolfnet_wordpress_search_service $service )
	{
		$this->searchService = $service;
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
