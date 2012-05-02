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
	
	
	
	/**
	 * This private static property holds the singleton instance of this class.
	 *
	 * @type com_mlsfinder_wordpress_listing_service
	 * 
	 */
	private static $instance;
	
	
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
	
	
	/**
	 * This method returns all property listings avaiable to this WordPress plugin instance. This 
	 * data is retrieved from the listingDao object.
	 * 
	 * @return	array	An array of listing objects (com_mlsfinder_wordpress_listing_entity)
	 * 
	 */
	public function getListings ()
	{
		$dao = $this->getDAO();
		$objs = $dao->findAll();
		return $objs;
	}
	
	
}