<?php

/**
 * This view is responsible for displaying the Listings Film Strip, which is a widget component.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         featuredListingsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_listing_featuredListingsView
extends com_ajmichels_wppf_abstract_view
implements com_ajmichels_wppf_interface_iView
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type  string
	 * 
	 */
	public $template;
	
	
	/**
	 * This property holds a reference to the listing view.
	 * 
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $listingView;
	
	
	/* CONSTRUCTOR METHOD *********************************************************************** */
	
	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return  void
	 * 
	 */
	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\featuredListings.php' );
	}
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method overwrites the inherited render method and provides some additional functionality.
	 * Specifically it extracts the listings data from the $data param and passes it to the 
	 * renderListings method. This separates the concerns of rendering the film strip from rendering
	 * indevidual listings.
	 *
	 * @param   array  $data  Associative array of data to be injected into the template file.
	 * @return  string
	 * 
	 */
	public function render ( $data = null )
	{
		if ( $data != null && array_key_exists( 'listings', $data ) ) {
			$data['listingContent'] = $this->renderListings( $data['listings'] );
		}
		
		$data['instanceId'] = uniqid( 'wolfnet_featuredListing_' );
		
		$data['autoPlay'] = 'false';
		if ( $data['options']['autoplay']['value'] !== 'false' ) {
			$data['autoPlay'] = 'true';
		}
		
		$data['direction'] = 'left';
		if ( is_string( $data['options']['direction']['value'] ) ) {
			$data['direction'] = $data['options']['direction']['value'];
		}
		
		$data['speed'] = 5000;
		if ( is_numeric( $data['options']['speed']['value'] ) && $data['options']['speed']['value'] != 0 ) {
			$data['speed'] = $data['options']['speed']['value'];
		}
		
		return parent::render( $data );
	}
	
	
	/**
	 * This method accepts an array of listing objects which is loops over and creates new instances 
	 * of the listingView object for each. The listings are then rendered individually and combined 
	 * in a string which is returned.
	 *
	 * @param   array  $listings  An array of listing objects.
	 * @return  string            Rendered listing content.
	 * 
	 */
	private function renderListings ( $listings )
	{
		$listingContent = '';
		foreach ( $listings as $listing ) {
			$view = $this->getListingView();
			$view->setTemplate( 'simple' );
			$listingContent .= $view->render( array( 'listing' => $listing ) );
		}
		return $listingContent;
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * GETTER: This method is a getter for the listingView property.
	 * 
	 * @return  com_ajmichels_wppf_interface_iView
	 * 
	 */
	public function getListingView ()
	{
		return $this->listingView;
	}
	
	
	/**
	 * SETTER: This method is a setter for the listingView property.
	 * 
	 * @type    com_ajmichels_wppf_interface_iView  $view
	 * @return  view
	 * 
	 */
	public function setListingView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->listingView = $view;
	}
	
	
}