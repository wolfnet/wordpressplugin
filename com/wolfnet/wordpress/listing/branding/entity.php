<?php

/**
 * This class is an entity bean for the Listing Branding data.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.branding
 * @title         entity.php
 * @extends       com_ajmichels_wppf_abstract_entity
 * @implements    com_ajmichels_wppf_interface_iEntity
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_listing_branding_entity
extends com_ajmichels_wppf_abstract_entity
implements com_ajmichels_wppf_interface_iEntity
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * @type  string
	 */
	private  $brokerLogo = '';
	
	
	/**
	 * @type  string
	 */
	private  $content    = '';
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	* This method is used to set instance data for the entity. Though it is public by necessity,
	* this method should not be accessed by any object other than the listingBrandingDao.
	* ( see Memento Design Pattern )
	*
	* @param   array  $data  The primary key of a single listing.
	* @return  void
	*
	*/
	public function setMemento ( $data )
	{
		$this->brokerLogo = $data['brokerLogo'];
		$this->content    = $data['content'];
	}
	
	
	/**
	* This method is used to get instance data from the entity. Though it is public by necessity,
	* this method should not be accessed by any object other than the listingBrandingDao.
	* ( see Memento Design Pattern )
	*
	* @return  array  The primary key of a single listing.
	*
	*/
	public function getMemento ()
	{
		return array(
		            'brokerLogo' => $this->brokerLogo,
		            'content'    => $this->content
		            );
	}
	
	
	/* ACCESSOR METHODS ************************************************************************* */
	
	/**
	 * GETTER: This method is a getter for the brokerLogo property.
	 * 
	 * @return  string
	 * 
	 */
	public function getBrokerLogo ()
	{
		return $this->brokerLogo;
	}
	
	
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