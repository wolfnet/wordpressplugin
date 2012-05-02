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
	private $body		=	'';
	
	
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
		$this->body		=	$data['body'];
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
	
	
}