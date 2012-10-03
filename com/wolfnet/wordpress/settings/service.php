<?php

/**
 * This class is the Settings Service and is a Facade used to interact with all other
 * market information.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    settings
 * @title         service.php
 * @extends       com_ajmichels_wppf_abstract_service
 * @implements    com_ajmichels_wppf_interface_iService
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
class com_wolfnet_wordpress_settings_service
extends com_ajmichels_wppf_abstract_service
implements com_ajmichels_wppf_interface_iService
{


	/* SINGLETON ENFORCEMENT ******************************************************************** */

	/**
	 * This private static property holds the singleton instance of this class.
	 *
	 * @type  com_wolfnet_wordpress_market_disclaimer
	 *
	 */
	private static $instance;


	/**
	 * This static method is used to retrieve the singleton instance of this class.
	 *
	 * @return  com_wolfnet_wordpress_market_disclaimer
	 *
	 */
	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a reference to the WPPF Data Service instance within the plugin instance.
	 *
	 * @type  com_ajmichels_wppf_data_service
	 *
	 */
	private $dataService;


	/**
	 * This property holds a reference to the Web Service URL object which represents the URI which
	 * will be used to retrieve data from a remove service.
	 *
	 * @type  com_ajmichels_wppf_data_webServiceUrl
	 *
	 */
	private $webServiceUrl;


	/**
	 * This property holds a reference to the WPPF Option Manager instance within the plugin
	 * instance.
	 *
	 * @type  com_ajmichels_wppf_option_manager
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
	public function getSettings ()
	{

		$wsu = $this->getWebServiceUrl();
		$productKey = $this->getOptionManager()->getOptionValueFromWP( 'wolfnet_productKey' );

		/* Cache for 24 hours */
		$wsu->setCacheSetting( 1440 );
		$wsu->setScriptPath( '/setting/' . $productKey );
		$wsu->setParameter( 'setting',  'getallsettings' );

		$this->log( (string) $wsu );

		$data = $this->getDataService()->getData( $wsu );

		$this->getDAO()->setData( array( $data['settings'] ) );

		return $this->getDAO()->findAll();

	}


	public function getSiteBaseUrl ()
	{

		$wsu = $this->getWebServiceUrl();
		$productKey = $this->getOptionManager()->getOptionValueFromWP( 'wolfnet_productKey' );

		/* Cache for 24 hours */
		$wsu->setCacheSetting( 1440 );
		$wsu->setScriptPath( '/setting/' . $productKey );
		$wsu->setParameter( 'setting',  'SITE_BASE_URL' );

		$data = $this->getDataService()->getData( $wsu );

		if ( array_key_exists( 'site_base_url', $data ) ) {
			return $data['site_base_url'];
		}
		else {
			return '';
		}

	}


	public function isKeyValid ()
	{
		$isValid    = false;
		$productKey = $this->getOptionManager()->getOptionValueFromWP( 'wolfnet_productKey' );
		$wsu        = $this->getWebServiceUrl();
		$wsu->setScriptPath( '/validateKey/' . $productKey );
		$http       = wp_remote_get( (string) $wsu );
		if ( !is_wp_error( $http ) && $http['response']['code'] == '200' ) {
			$data = json_decode( $http['body'] );
			if ( property_exists( $data, 'error') && property_exists( $data->error, 'status') && $data->error->status === false ) {
				$isValid = true;
			}
		}
		return $isValid;
	}


	/* ACCESSOR METHODS ************************************************************************* */

	/**
	 * GETTER: This method is a getter for the dataService property.
	 *
	 * @return  com_ajmichels_wppf_data_service
	 *
	 */
	public function getDataService ()
	{
		return $this->dataService;
	}


	/**
	 * SETTER: This method is a setter for the dataService property.
	 *
	 * @param   com_ajmichels_wppf_data_service  $service
	 * @return  void
	 *
	 */
	public function setDataService ( com_ajmichels_wppf_data_service $service )
	{
		$this->dataService = $service;
	}


	/**
	 * GETTER: This method is a getter for the webServiceUrl property.
	 *
	 * @return  com_ajmichels_wppf_data_webServiceUrl
	 *
	 */
	public function getWebServiceUrl ()
	{
		return clone $this->webServiceUrl;
	}


	/**
	 * SETTER: This method is a setter for the webServiceUrl property.
	 *
	 * @param   com_ajmichels_wppf_data_webServiceUrl  $url
	 * @return  void
	 *
	 */
	public function setWebServiceUrl ( com_ajmichels_wppf_data_webServiceUrl $url )
	{
		$this->webServiceUrl = $url;
	}


	/**
	 * GETTER: This method is a getter for the optionManager property.
	 *
	 * @return  com_ajmichels_wppf_option_manager
	 *
	 */
	public function getOptionManager ()
	{
		return $this->optionManager;
	}


	/**
	 * SETTER: This method is a setter for the optionManager property.
	 *
	 * @param   com_ajmichels_wppf_option_manager  $manager
	 * @return  void
	 *
	 */
	public function setOptionManager ( com_ajmichels_wppf_option_manager $manager )
	{
		$this->optionManager = $manager;
	}


}
