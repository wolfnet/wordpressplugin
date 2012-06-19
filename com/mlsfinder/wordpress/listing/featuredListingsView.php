<?php

/**
 * This view is responsible for displaying the Listings Film Strip, which is a widget component.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing
 * @title         featuredListingsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_featuredListingsView
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
	
	private $listingView;
	
	
	/* CONSTRUCTOR ****************************************************************************** */
	
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
	 * @return  void
	 * 
	 */
	public function render ( $data = null )
	{
		if ( $data != null && array_key_exists( 'listings', $data ) ) {
			$data['listingContent'] = $this->renderListings( $data['listings'] );
		}
		
		$data['instanceId'] = uniqid( 'mlsFinder_featuredListing_' );
		
		$data['autoPlay'] = 'false';
		if ( $data['options']['autoPlay']['value'] ) {
			$data['autoPlay'] = 'true';
		}
		
		$data['direction'] = 'left';
		if ( is_string( $data['options']['direction']['value'] ) ) {
			$data['direction'] = $data['options']['direction']['value'];
		}
		
		$data['wait'] = 'false';
		if ( $data['options']['wait']['value'] ) {
			$data['wait'] = 'true';
		}
		
		$data['waitLen'] = 1000;
		if ( is_numeric( $data['options']['waitLen']['value'] ) ) {
			$data['waitLen'] = $data['options']['waitLen']['value'] * 1000;
		}
		
		$data['speed'] = 5000;
		if ( is_numeric( $data['options']['speed']['value'] ) && $data['options']['speed']['value'] != 0 ) {
			$data['speed'] = $data['options']['speed']['value'];
		}
		
		$data['scrollCount'] = 0;
		if ( is_numeric( $data['options']['scrollCount']['value'] ) ) {
			$data['scrollCount'] = ( $data['options']['scrollCount']['value'] );
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
	
	public function getListingView ()
	{
		return $this->listingView;
	}
	
	
	public function setListingView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->listingView = $view;
	}
	
	
}