<?php

/**
 * This view is responsible for displaying the Quick Search Form, which is a widget component.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         quickSearchView.php
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
 *
 */
class com_wolfnet_wordpress_listing_quickSearchView
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


	/**
	 * This property holds a reference to the settings service.
	 *
	 * @type  com_wolfnet_wordpress_settings_service
	 *
	 */
	private $settingsService;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return  void
	 *
	 */
	public function __construct ()
	{
		$this->log( 'Init com_wolfnet_wordpress_listing_filmStripView' );
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\quickSearchForm.php' );
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method overwrites the inherited render method and provides some additional functionality.
	 * Specifically it extracts the listings data from the $data param and passes it to the
	 * renderListings method. This separates the concerns of rendering the film strip from rendering
	 * indevidual listings.
	 *
	 * @param   array  $data  Associative array of data to be injected into the template file.
	 * @return  string
	 *
	 */
	public function render ( $data = array() )
	{
		$data['instanceId'] = uniqid( 'wolfnet_quickSearchForm_' );
		$data['formAction'] = $this->getSettingsService()->getSiteBaseUrl();
		return parent::render( $data );
	}


	/* ACCESSOR METHODS ************************************************************************* */

	/**
	 * GETTER: This getter method is used to get the setttingsService property.
	 *
	 * @return  com_wolfnet_wordpress_settings_service.
	 *
	 */
	public function getSettingsService ()
	{
		return $this->settingsService;
	}


	/**
	 * SETTER: This setter method is used to set the setttingsService property.
	 *
	 * @param   com_wolfnet_wordpress_settings_service  $service
	 * @return  void
	 *
	 */
	public function setSettingsService ( com_wolfnet_wordpress_settings_service $service )
	{
		$this->settingsService = $service;
	}


}
