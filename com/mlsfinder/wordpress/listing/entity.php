<?php

/**
 * This class is the Listing Entity and is a container for listing data.
 *
 * @package			com.mlsfinder.wordpress.listing
 * @title			entity.php
 * @extends			com_ajmichels_wppf_abstract_entity
 * @implements		com_ajmichels_wppf_interface_iEntity
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
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
	private $id			=	0;
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $linktext	=	'';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $url		=	'';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $photo		=	'';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $city		=	'';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $state		=	'';
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $beds		=	0;
	
	/**
	 * 
	 * @type string
	 * 
	 */
	private $baths		=	0;
	
	
	/**
	 * This method is used to set instance data for the entity. Though it is public by necessity, 
	 * this method should not be accessed by any object other than the listingDao.
	 * @see Memento Design Pattern
	 * 
	 * @param	array	$data	The primary key of a single listing.
	 * @return	void
	 * 
	 */
	public function _setMemento ( $data )
	{
		$this->id		=	$data['id'];
		$this->linktext	=	$data['linktext'];
		$this->url		=	$data['url'];
		$this->photo	=	$data['photo'];
		$this->city		=	$data['city'];
		$this->state	=	$data['state'];
		$this->beds		=	$data['beds'];
		$this->baths	=	$data['baths'];
		
		/* using html_entity_decode to make sure that character that were encoded as part of the 
		   JSON encoding process are converted back into HTML for display. */
		$this->body		=	html_entity_decode( $data['body'] );
	}
	
	
	/*	ACCESSORS ******************************************************************************* */
	
	
	/**
	 * GETTER: This getter method is used to get the 'id' property, which is an inherited property.
	 * 
	 * @return	mixed
	 * 
	 */
	public function getID ()
	{
		return $this->id;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'linkText' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getLinkText ()
	{
		return $this->linktext;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'url' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getUrl ()
	{
		return $this->url;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'photo' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getPhoto ()
	{
		return $this->photo;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'body' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getBody ()
	{
		return $this->body;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'city' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getCity ()
	{
		return $this->city;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'state' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getState ()
	{
		return $this->state;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'beds' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getBeds ()
	{
		return $this->beds;
	}
	
	
	/**
	 * GETTER: This getter method is used to get the 'baths' property, which is an inherited property.
	 * 
	 * @return	string
	 * 
	 */
	public function getBaths ()
	{
		return $this->baths;
	}
	
	
}