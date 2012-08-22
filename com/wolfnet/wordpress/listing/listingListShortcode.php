<?php

/**
 * This is the filmStripWidget object. This object inherites from the base WP_Widget object and 
 * defines the display and functionality of this specific widget.
 * 
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         listingListShortcode.php
 * @extends       com_ajmichels_wppf_shortcode_shortcode
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_listing_listingListShortcode
extends com_ajmichels_wppf_shortcode_shortcode
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property holds the tag name which is used to identify shorcodes when they are encountered
	 * in Posts and Pages.
	 * 
	 * @type  string
	 * 
	 */
	public $tag = 'WolfNetListingList,wolfnetlistinglist,WOLFNETLISTINGLIST,wnt_list';
	
	
	/**
	 * This property holds a reference to the Listing Service object.
	 * 
	 * @type  
	 * 
	 */
	private $listingService;
	
	
	/**
	 * This property holds an instance of the Listing Grid View object.
	 * 
	 * @type  
	 * 
	 */
	private $listingListView;
	
	
	/**
	 * This property holds an array of different options that are available for each widget instance.
	 *
	 * @type  array
	 * 
	 */
	protected $attributes = array( 
		'title'        => '', 
		'minprice'     => '', 
		'maxprice'     => '', 
		'city'         => '', 
		'zipcode'      => '', 
		'ownertype'    => 'all', 
		'maxresults'   => 50 
		);
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method is called whenever an instance of the shortcode is encountered in a post or page.
	 * 
	 * @param   array   $attr
	 * @param   string  $content
	 * @return  string
	 * 
	 */
	public function execute ( $attr, $content = null ) {
		$options = $this->getAttributesData( $attr );
		$gridListings = $this->getListingService()->getGridListings(
			$options['minprice']['value'],
			$options['maxprice']['value'],
			$options['city']['value'],
			$options['zipcode']['value'],
			$options['ownertype']['value'],
			$options['maxresults']['value']
			);
		$data = array(
			'listings' => $gridListings,
			'options'  => $options
			);
		return $this->getListingListView()->render( $data );
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * GETTER:  This method is a getter for the listingService property.
	 * 
	 * @return  com_wolfnet_wordpress_listing_service
	 * 
	 */
	public function getListingService ()
	{
		return $this->listingService;
	}
	
	
	/**
	 * SETTER:  This method is a setter for the listingService property.
	 * 
	 * @param   com_wolfnet_wordpress_listing_service  $service
	 * @return  void
	 * 
	 */
	public function setListingService ( com_wolfnet_wordpress_listing_service $service )
	{
		$this->listingService = $service;
	}
	
	
	/**
	 * GETTER:  This method is a getter for the listingListView property.
	 * 
	 * @return  com_ajmichels_wppf_interface_iView
	 * 
	 */
	public function getListingListView ()
	{
		return $this->listingListView;
	}
	
	
	/**
	 * SETTER:  This method is a setter for the listingListView property.
	 * 
	 * @param   com_ajmichels_wppf_interface_iView  $view
	 * @return  void
	 * 
	 */
	public function setListingListView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->listingListView = $view;
	}
	
	
}