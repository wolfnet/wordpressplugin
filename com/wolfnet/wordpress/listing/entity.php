<?php

/**
 * This class is the Listing Entity and is a container for listing data.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         entity.php
 * @extends       com_greentiedev_wppf_abstract_entity
 * @implements    com_greentiedev_wppf_interface_iEntity
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

class com_wolfnet_wordpress_listing_entity
extends com_greentiedev_wppf_abstract_entity
implements com_greentiedev_wppf_interface_iEntity
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 *
	 * @type  mixed[string]
	 *
	 */
	private $property_id     = '';

	/**
	 *
	 * @type  string
	 *
	 */
	private $property_url    = '';

	/**
	 *
	 * @type  float
	 *
	 */
	private $listing_price   = 0;

	/**
	 *
	 * @type  boolean
	 *
	 */
	private $agent_listing   = 0;

	/**
	 *
	 * @type  string
	 *
	 */
	private $display_address = '';

	/**
	 *
	 * @type  string
	 *
	 */
	private $city            = '';

	/**
	 *
	 * @type  string
	 *
	 */
	private $state           = '';

	/**
	 *
	 * @type  string
	 *
	 */
	private $thumbnail_url   = '';

	/**
	 *
	 * @type  string
	 *
	 */
	private $photo_url       = '';

	/**
	 *
	 * @type  numeric
	 *
	 */
	private $bathroom        = 0;

	/**
	 *
	 * @type  numeric
	 *
	 */
	private $bedrooms        = 0;

	/**
	 *
	 * @type  com_wolfnet_wordpress_listing_branding_entity
	 *
	 */
	private $branding        = 0;

	/**
	 *
	 * @type  numeric
	 *
	 */
	private $totalrecords    = 0;


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is used to set instance data for the entity. Though it is public by necessity,
	 * this method should not be accessed by any object other than the listingDao.
	 * ( see Memento Design Pattern )
	 *
	 * @param   array  $data  The primary key of a single listing.
	 * @return  void
	 *
	 */
	public function setMemento ( $data )
	{
		$this->property_id     = $data['property_id'];
		$this->property_url    = $data['property_url'];
		$this->listing_price   = $data['listing_price'];
		$this->agent_listing   = $data['agent_listing'];
		$this->display_address = $data['display_address'];
		$this->city            = $data['city'];
		$this->state           = $data['state'];
		$this->thumbnail_url   = $data['thumbnail_url'];
		$this->photo_url       = $data['photo_url'];
		$this->bathroom        = $data['bathroom'];
		$this->bedrooms        = $data['bedrooms'];
		$this->branding        = $data['branding'];
		$this->totalrecords    = $data['total_rows'];
	}


	/**
	 * This method is used to get instance data from the entity. Though it is public by necessity,
	 * this method should not be accessed by any object other than the listingDao.
	 * ( see Memento Design Pattern )
	 *
	 * @return  array  The primary key of a single listing.
	 *
	 */
	public function getMemento ()
	{
		$values = array(
			'property_id'     => $this->property_id,
			'property_url'    => $this->property_url,
			'listing_price'   => $this->listing_price,
			'agent_listing'   => $this->agent_listing,
			'display_address' => $this->display_address,
			'city'            => $this->city,
			'state'           => $this->state,
			'thumbnail_url'   => $this->thumbnail_url,
			'photo_url'       => $this->photo_url,
			'bathroom'        => $this->bathroom,
			'bedrooms'        => $this->bedrooms,
			'branding'        => $this->branding,
			'total_rows'      => $this->totalrecords
			);

		if ( get_class($this->branding) == 'com_wolfnet_wordpress_listing_branding_entity' ) {
			$values['branding'] = $this->branding->getMemento();
		}

		return $values;
	}


	/**
	 * This method combines several properties into a single location string. This is done here to
	 * hold this logic in a centralized place.
	 *
	 * @return  string  Combined location information in a single string.
	 *
	 */
	public function getLocation ()
	{
		$location = $this->getCity();
		if ( $this->getCity() != '' && $this->getState() != '' ) {
			$location .= ', ';
		}
		$location .= $this->getState();
		return $location;

	}


	/**
	 * This method combines the bedroom and bathroom data into a single string for display.
	 *
	 * @return  string
	 *
	 */
	public function getBedsAndBaths ( $format = '' )
	{
		$bedsAndBaths = '';

		switch ( $format ) {

			default:
			case 'full':

				if ( is_numeric( $this->getBedrooms() ) ) {
					$bedsAndBaths .= $this->getBedrooms() . ' Bed Rooms';
				}
				if ( is_numeric( $this->getBedrooms() ) && is_numeric( $this->getBathroom() ) ) {
					$bedsAndBaths .= ' & ';
				}
				if ( is_numeric( $this->getBathroom() ) ) {
					$bedsAndBaths .= $this->getBathroom() . ' Bath Rooms';
				}

				break;

			case 'short':

				if ( is_numeric( $this->getBedrooms() ) ) {
					$bedsAndBaths .= $this->getBedrooms() . ' Beds';
				}
				if ( is_numeric( $this->getBedrooms() ) && is_numeric( $this->getBathroom() ) ) {
					$bedsAndBaths .= ', ';
				}
				if ( is_numeric( $this->getBathroom() ) ) {
					$bedsAndBaths .= $this->getBathroom() . ' Baths';
				}

				break;

			case 'abbreviated' :

				if ( is_numeric( $this->getBedrooms() ) ) {
					$bedsAndBaths .= $this->getBedrooms() . 'bd';
				}
				if ( is_numeric( $this->getBedrooms() ) && is_numeric( $this->getBathroom() ) ) {
					$bedsAndBaths .= '/';
				}
				if ( is_numeric( $this->getBathroom() ) ) {
					$bedsAndBaths .= $this->getBathroom() . 'ba';
				}

				break;

		}

		return $bedsAndBaths;
	}


	/**
	 * This method combines all address information into a full address.
	 *
	 * @return  string
	 *
	 */
	public function getFullAddress ()
	{
		$address = $this->getDisplayAddress();

		if ( $this->getCity() != '' && $address != '' ) {
			$address .= ', ';
		}

		$address .= $this->getCity();

		if ( $this->getState() != '' && $address != '' ) {
			$address .= ', ';
		}

		$address .= $this->getState();

		return $address;

	}


	/*	ACCESSORS ******************************************************************************* */

	/**
	 * GETTER: This method is a getter for the property_id property.
	 *
	 * @return  mixed[string]
	 *
	 */
	public function getPropertyId ()
	{
		return $this->property_id;
	}


	/**
	 * GETTER: This method is a getter for the property_url property.
	 *
	 * @return  string
	 *
	 */
	public function getPropertyUrl ()
	{
		return $this->property_url;
	}


	/**
	 * GETTER: This method is a getter for the listing_price property. In addition this method
	 * formats any numeric strings for display.
	 *
	 * @return  string
	 *
	 */
	public function getListingPrice ()
	{
		if ( is_numeric( $this->listing_price ) ) {
			return number_format( $this->listing_price, 0, '.', ',' );
		}
		else {
			return $this->listing_price;
		}
	}


	/**
	 * GETTER: This method is a getter for the agent_listing property.
	 *
	 * @return  boolean
	 *
	 */
	public function getAgentListing ()
	{
		return $this->agent_listing;
	}


	/**
	 * GETTER: This method is a getter for the display_address property.
	 *
	 * @return  string
	 *
	 */
	public function getDisplayAddress ()
	{
		return $this->display_address;
	}


	/**
	 * GETTER: This method is a getter for the city property.
	 *
	 * @return  string
	 *
	 */
	public function getCity ()
	{
		return $this->city;
	}


	/**
	 * GETTER: This method is a getter for the state property.
	 *
	 * @return  string
	 *
	 */
	public function getState ()
	{
		return $this->state;
	}


	/**
	 * GETTER: This method is a getter for the thumbnail_url property.
	 *
	 * @return  string
	 *
	 */
	public function getThumbnailUrl ()
	{
		return $this->thumbnail_url;
	}


	/**
	 * GETTER: This method is a getter for the thumbnail_url property.
	 *
	 * @return  string
	 *
	 */
	public function getPhotoUrl ()
	{
		return $this->photo_url;
	}


	/**
	 * GETTER: This method is a getter for the bathroom property.
	 *
	 * @return  numeric
	 *
	 */
	public function getBathroom ()
	{
		return $this->bathroom;
	}


	/**
	 * GETTER: This method is a getter for the bedrooms property.
	 *
	 * @return  numeric
	 *
	 */
	public function getBedrooms ()
	{
		return $this->bedrooms;
	}


	/**
	 * GETTER: This method is a getter for the branding property.
	 *
	 * @return  com_wolfnet_wordpress_listing_branding_entity
	 *
	 */
	public function getBranding ()
	{
		return $this->branding;
	}

	/**
	 * GETTER: This method is a getter for the total results
	 *
	 * @return  numeric
	 *
	 */
	public function getTotalResults ()
	{
		return $this->totalrecords;
	}


}
