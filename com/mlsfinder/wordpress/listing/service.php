<?php

/**
 * This class is the listingService and is a Facade used to interact with all other listing information.
 * 
 * @package			com.mlsfinder.wordpress.listing
 * @title			service.php
 * @extends			com_ajmichels_wppf_abstract_service
 * @implements		com_ajmichels_wppf_interface_iService
 * @singleton		True
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_service
extends com_ajmichels_wppf_abstract_service
implements com_ajmichels_wppf_interface_iService
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	private $dataService;
	private $webServiceUrl;
	
	
	/* SINGLETON ENFORCEMENT ******************************************************************** */
	
	/**
	 * This private static property holds the singleton instance of this class.
	 *
	 * @type com_mlsfinder_wordpress_listing_service
	 * 
	 */
	private static $instance;
	
	
	/**
	 * This static method is used to retrieve the singleton instance of this class.
	 * 
	 * @return com_mlsfinder_wordpress_listing_service
	 * 
	 */
	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	/* CONSTRUCTOR ****************************************************************************** */
	
	/**
	 * This constructor method is private becuase this class is a singleton and can only be retrieved
	 * by statically calling the getInstance method.
	 * 
	 * @return void
	 * 
	 */
	private function __construct ()
	{
		$this->log( 'Init com_mlsfinder_wordpress_listing_service' );
	}
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method returns all property listings avaiable to this WordPress plugin instance. This 
	 * data is retrieved from the listingDao object.
	 * 
	 * @return	array	An array of listing objects (com_mlsfinder_wordpress_listing_entity)
	 * 
	 */
	public function getListings ()
	{
		$this->setData();
		return $this->getDAO()->findAll();
	}
	
	
	/* PRIVATE METHODS ************************************************************************** */
	
	private function setData ()
	{
		$dao = $this->getDAO();
		$wsu = $this->getWebServiceUrl();
		$wsu->setCacheSetting( 15 );
		$wsu->setScriptPath( '/propertyBar/3FF15C5C-62F5-4D97-84C8-16243EDEE7F6' );
		
		$data = $this->getDataService()->getData( $wsu );
		
		$dao->setData( $data['listings'] );
	}
	
	
	/* ACCESSOR METHODS ************************************************************************* */
	
	public function getDataService ()
	{
		return $this->dataService;
	}
	
	
	public function setDataService ( com_ajmichels_wppf_data_service $service )
	{
		$this->dataService = $service;
	}
	
	
	public function getWebServiceUrl ()
	{
		return $this->webServiceUrl;
	}
	
	
	public function setWebServiceUrl ( com_ajmichels_wppf_data_webServiceUrl $url )
	{
		$this->webServiceUrl = $url;
	}
	
	
}