<?php

/**
 * This class is the Listing Entity and is a container for listing data.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    market.disclaimer
 * @title         entity.php
 * @extends       com_ajmichels_wppf_abstract_entity
 * @implements    com_ajmichels_wppf_interface_iEntity
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_wolfnet_wordpress_market_disclaimer_entity
extends com_ajmichels_wppf_abstract_entity
implements com_ajmichels_wppf_interface_iEntity
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * 
	 * @type  string
	 * 
	 */
	private $content = '';
	
	
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
		$this->content = $data['content'];
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
		return array( 
			'content' => $this->content,
			);
	}
	
	
	/*	ACCESSORS ******************************************************************************* */
	
	/**
	 * GETTER: This method is a getter for the content property.
	 * 
	 * @return  string
	 * 
	 */
	public function getContent ()
	{
		return $this->content;
	}
	
	
}