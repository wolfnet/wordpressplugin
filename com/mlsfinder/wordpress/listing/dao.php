<?php

/**
 * This class is the listingDAO (data access object) and is responsible for translating the incoming
 * and outgoing data to and from the listingEntity objects. This object should be replaced in the 
 * event that the method used for data i/o is changed.
 *
 * @package			com.mlsfinder.wordpress.listing
 * @title			ListingDAO.php
 * @extends			com_ajmichels_wppf_abstract_dao
 * @implements		com_ajmichels_wppf_interface_iDao
 * @singleton		True
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 * @todo			Re-work the way data is retrieved from the data service. The implementation is 
 * 					currently not flexible enough.
 * 
 */

class com_mlsfinder_wordpress_listing_dao
extends com_ajmichels_wppf_abstract_dao
implements com_ajmichels_wppf_interface_iDao
{
	
	
	/**
	 * This private static property holds the singleton instance of this class.
	 *
	 * @type com_mlsfinder_wordpress_listing_dao
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
		$this->log( 'Init com_mlsfinder_wordpress_listing_dao' );
	}
	
	
	/**
	 * This static method is used to retrieve the singleton instance of this class.
	 * 
	 * @return com_mlsfinder_wordpress_listing_dao
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
	 * This method returns all property listings avaiable to this WordPress plugin instance.
	 * 
	 * @return	array	An array of listing objects (com_mlsfinder_wordpress_listing_entity)
	 * 
	 */
	public function findAll ()
	{
		if ( isset( $this->_findAllResults ) ) {
			return $this->_findAllResults;
		}
		else {
			$listings = array ();
			$listingPrototype = $this->getEntityPrototype();
			$data = $this->getData();
			if ( is_array($data) && count($data) > 0 ) {
				foreach ($data as $listingData) {
					$listing = clone $listingPrototype;
					$listing->_setMemento( $listingData );
					// Push Object to Array
					array_push($listings, $listing);
				}
				
			}
			$this->_findAllResults = $listings;
		}
		
		return $listings;
	}
	
	
	/**
	 * This method returns only a single listing object based on the primary key that is passed.
	 * 
	 * @param	mixed	$id	The primary key of a single listing.
	 * @return	com_mlsfinder_wordpress_listing_entity	Listing object with a matching primary key.
	 * 
	 */
	public function findById ( $id = 0 )
	{
		$listing = clone $this->getEntityPrototype();
		$data = $this->getData();
		if ( $id != 0 && is_array($data) && count($data) > 0 ) {
			$this->log( 'Listing Data', $data );
			$listing->_setMemento( $data[0] );
		}
		return $listing;
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * SETTER: This setter method is used to set the data property, which is an inherited property.
	 * 
	 * @param	array	$data	An array of data.
	 * @return	void
	 * 
	 */
	public function setData ( $data )
	{
		$this->data = $data;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the data property, which is an inherited property.
	 * 
	 * @return	array	An array of data.
	 * 
	 */
	public function getData ()
	{
		$data = $this->getDataService()->getData( $this->getWebServiceUrl() );
		$this->setData( $data['listings'] );
		return $this->data;
	}
	
	
}