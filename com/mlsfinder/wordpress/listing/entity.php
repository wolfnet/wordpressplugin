<?php

/**
 * This class is the Listing Entity and is a container for listing data.
 * 
 * @package       com.mlsfinder.wordpress.listing
 * @title         entity.php
 * @extends       com_ajmichels_wppf_abstract_entity
 * @implements    com_ajmichels_wppf_interface_iEntity
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_entity
extends com_ajmichels_wppf_abstract_entity
implements com_ajmichels_wppf_interface_iEntity
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * 
	 * @type mixed
	 * 
	 */
	private $property_id     = '';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $property_url    = '';
	
	/**
	 * 
	 * @type float
	 * 
	 */
	private $listing_price   = 0;
	
	/**
	 * 
	 * @type boolean
	 * 
	 */
	private $agent_listing   = 0;
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $display_address = '';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $city            = '';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $state           = '';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $thumbnail_url   = '';
	
	/**
	 * 
	 * @type numeric
	 * 
	 */
	private $bathroom        = 0;
	
	/**
	 * 
	 * @type numeric
	 * 
	 */
	private $bedrooms        = 0;
	
	
	/**
	 * This method is used to set instance data for the entity. Though it is public by necessity, 
	 * this method should not be accessed by any object other than the listingDao.
	 * @see Memento Design Pattern
	 * 
	 * @param   array  $data  The primary key of a single listing.
	 * @return  void
	 * 
	 */
	public function _setMemento ( $data )
	{
		$this->property_id     = $data['property_id'];
		$this->property_url    = $data['property_url'];
		$this->listing_price   = $data['listing_price'];
		$this->agent_listing   = $data['agent_listing'];
		$this->display_address = $data['display_address'];
		$this->city            = $data['city'];
		$this->state           = $data['state'];
		$this->thumbnail_url   = $data['thumbnail_url'];
		$this->bathroom        = $data['bathroom'];
		$this->bedrooms        = $data['bedrooms'];
	}
	
	
	public function _getMemento ()
	{
		return array( 
			'property_id'     => $this->property_id,
			'property_url'    => $this->property_url,
			'listing_price'   => $this->listing_price,
			'agent_listing'   => $this->agent_listing,
			'display_address' => $this->display_address,
			'city'            => $this->city,
			'state'           => $this->state,
			'thumbnail_url'   => $this->thumbnail_url,
			'bathroom'        => $this->bathroom,
			'bedrooms'        => $this->bedrooms
			);
	}
	
	/* PUBLIC METHODS *************************************************************************** */
	
	public function getLocation ()
	{
		$location = $this->getCity();
		if ( $this->getCity() != '' && $this->getState() != '' ) {
			$location .= ', ';
		}
		$location .= $this->getState();
		return $location;
		
	}
	
	
	public function getBedsAndBaths ()
	{
		$bedsAndBaths = '';
		if ( is_numeric( $this->getBedrooms() ) ) {
			$bedsAndBaths .= $this->getBedrooms() . ' Beds';
		}
		if ( is_numeric( $this->getBedrooms() ) && is_numeric( $this->getBathroom() ) ) {
			$bedsAndBaths .= ', ';
		}
		if ( is_numeric( $this->getBathroom() ) ) {
			$bedsAndBaths .= $this->getBathroom() . ' Baths';
		}
		return $bedsAndBaths;
	}
	
	
	/*	ACCESSORS ******************************************************************************* */
	
	public function getPropertyId ()
	{
		return $this->property_id;
	}
	
	
	public function getPropertyUrl ()
	{
		return $this->property_url;
	}
	
	
	public function getListingPrice ()
	{
		if ( is_numeric( $this->listing_price ) ) {
			return number_format( $this->listing_price, 0, '.', ',' );
		}
		else {
			return $this->listing_price;
		}
	}
	
	
	public function getAgentListing ()
	{
		return $this->agent_listing;
	}
	
	
	public function getDisplayAddress ()
	{
		return $this->display_address;
	}
	
	
	public function getCity ()
	{
		return $this->city;
	}
	
	
	public function getState ()
	{
		return $this->state;
	}
	
	
	public function getThumbnailUrl ()
	{
		return $this->thumbnail_url;
	}
	
	
	public function getBathroom ()
	{
		return $this->bathroom;
	}
	
	
	public function getBedrooms ()
	{
		return $this->bedrooms;
	}
	
	
}