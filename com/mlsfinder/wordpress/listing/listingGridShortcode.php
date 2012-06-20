<?php

/**
 * This is the filmStripWidget object. This object inherites from the base WP_Widget object and 
 * defines the display and functionality of this specific widget.
 * 
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing
 * @title         listingGridShortcode.php
 * @extends       com_ajmichels_wppf_shortcode_shortcode
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_mlsfinder_wordpress_listing_listingGridShortcode
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
	public $tag = 'ListingGrid';
	
	
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
	private $listingGridView;
	
	
	/**
	 * This property holds an array of different options that are available for each widget instance.
	 *
	 * @type  array
	 * 
	 */
	protected $attributes = array( 
		'min_price'   => '', 
		'max_price'   => '', 
		'city'        => '', 
		'zipcode'     => '', 
		'agentBroker' => '', 
		'maxResults'  => 50 
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
			$options['maxPrice']['value'],
			$options['minPrice']['value'],
			$options['city']['value'],
			$options['zipcode']['value'],
			$options['agentBroker']['value'],
			$options['maxResults']['value']
			);
		$data = array(
			'listings' => $gridListings,
			'options'  => $options
			);
		return $this->getListingGridView()->render( $data );
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * GETTER:  This method is a getter for the listingService property.
	 * 
	 * @return  com_mlsfinder_wordpress_listing_service
	 * 
	 */
	public function getListingService ()
	{
		return $this->listingService;
	}
	
	
	/**
	 * SETTER:  This method is a setter for the listingService property.
	 * 
	 * @param   com_mlsfinder_wordpress_listing_service  $service
	 * @return  void
	 * 
	 */
	public function setListingService ( com_mlsfinder_wordpress_listing_service $service )
	{
		$this->listingService = $service;
	}
	
	
	/**
	 * GETTER:  This method is a getter for the listingGridView property.
	 * 
	 * @return  com_ajmichels_wppf_interface_iView
	 * 
	 */
	public function getListingGridView ()
	{
		return $this->listingGridView;
	}
	
	
	/**
	 * SETTER:  This method is a setter for the listingGridView property.
	 * 
	 * @param   com_ajmichels_wppf_interface_iView  $view
	 * @return  void
	 * 
	 */
	public function setListingGridView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->listingGridView = $view;
	}
	
	
}