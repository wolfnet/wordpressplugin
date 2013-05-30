<?php

/**
 * This class is the sort Service and is a Facade used to interact with all other
 * market information.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    sort
 * @title         service.php
 * @extends       com_greentiedev_wppf_abstract_service
 * @implements    com_greentiedev_wppf_interface_iService
 * @singleton     True
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
class WNT_WP_Service_SortService
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a reference to the WPPF Data Service instance within the plugin instance.
	 *
	 * @type  com_greentiedev_wppf_data_service
	 *
	 */
	private $dataService;


	/**
	 * This property holds a reference to the Web Service URL object which represents the URI which
	 * will be used to retrieve data from a remove service.
	 *
	 * @type  com_greentiedev_wppf_data_webServiceUrl
	 *
	 */
	private $webServiceUrl;


	/**
	 * This property holds a reference to the WPPF Option Manager instance within the plugin
	 * instance.
	 *
	 * @type  com_greentiedev_wppf_option_manager
	 *
	 */
	private $optionManager;


	/* CONSTRUCTOR ****************************************************************************** */

	/**
	 * This constructor method is private becuase this class is a singleton and can only be retrieved
	 * by statically calling the getInstance method.
	 *
	 * @return  void
	 *
	 */
	private function __construct ()
	{
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method returns all featured property listings.
	 *
	 * @param   string   $type  The type of disclaimer to return. (search_results)
	 * @return  array    An array of listing objects (com_wolfnet_wordpress_listing_entity)
	 *
	 */
	public function getSort ()
	{

		$wsu = $this->getWebServiceUrl();
		$productKey = $this->getOptionManager()->getOptionValueFromWP( 'wolfnet_productKey' );

		/* Cache for 24 hours */
		$wsu->setCacheSetting( 1440 );
		$wsu->setScriptPath( '/sortOptions/' . $productKey );

		$this->log( (string) $wsu );

		$data = $this->getDataService()->getData( $wsu );

		$this->getDAO()->setData(  $data['sort_options'] );

		return $this->getDAO()->findAll();

	}


	/* ACCESSOR METHODS ************************************************************************* */

	/**
	 * GETTER: This method is a getter for the dataService property.
	 *
	 * @return  com_greentiedev_wppf_data_service
	 *
	 */
	public function getDataService ()
	{
		return $this->dataService;
	}


	/**
	 * SETTER: This method is a setter for the dataService property.
	 *
	 * @param   com_greentiedev_wppf_data_service  $service
	 * @return  void
	 *
	 */
	public function setDataService ( com_greentiedev_wppf_data_service $service )
	{
		$this->dataService = $service;
	}


	/**
	 * GETTER: This method is a getter for the webServiceUrl property.
	 *
	 * @return  com_greentiedev_wppf_data_webServiceUrl
	 *
	 */
	public function getWebServiceUrl ()
	{
		return clone $this->webServiceUrl;
	}


	/**
	 * SETTER: This method is a setter for the webServiceUrl property.
	 *
	 * @param   com_greentiedev_wppf_data_webServiceUrl  $url
	 * @return  void
	 *
	 */
	public function setWebServiceUrl ( com_greentiedev_wppf_data_webServiceUrl $url )
	{
		$this->webServiceUrl = $url;
	}


	/**
	 * GETTER: This method is a getter for the optionManager property.
	 *
	 * @return  com_greentiedev_wppf_option_manager
	 *
	 */
	public function getOptionManager ()
	{
		return $this->optionManager;
	}


	/**
	 * SETTER: This method is a setter for the optionManager property.
	 *
	 * @param   com_greentiedev_wppf_option_manager  $manager
	 * @return  void
	 *
	 */
	public function setOptionManager ( com_greentiedev_wppf_option_manager $manager )
	{
		$this->optionManager = $manager;
	}


}
