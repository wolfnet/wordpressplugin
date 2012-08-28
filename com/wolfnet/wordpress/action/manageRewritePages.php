<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         manageRewritePages.php
 * @extends       com_ajmichels_wppf_action_action
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_action_manageRewritePages
extends com_ajmichels_wppf_action_action
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a references to the Listing Service object.
	 *
	 * @type  com_wolfnet_wordpress_listing_service
	 *
	 */
	private $listingService;


	/**
	 * This property holds an instance of the Listing Grid Options View object
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $listingGridOptionsView;


	/**
	 * This property holds an instance of the Featured Listings Options View object.
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $featuredListingsOptionsView;


	/**
	 * This property holds an instance of the Property List Options View object
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $propertyListOptionsView;


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return  void
	 *
	 */
	public function execute ()
	{
		$pagename = get_query_var('pagename');

		if ( $pagename == 'wolfnet_shortcode_builder_option_form' ) {

			$formpage       = get_query_var('formpage');

			switch( $formpage ) {

				case 'grid-options':
					$this->renderListingGridOptions();
					exit;
					break;

				case 'list-options':
					$this->renderPropertyListOptions();
					exit;
					break;

				case 'featured-options':
					$this->renderFeaturedListingsOptions();
					exit;
					break;

			}

		}

	}


	/* PRIVATE METHODS ************************************************************************** */

	private function renderListingGridOptions ()
	{
		$this->updateHttpHeader();
		$data = array(
			'fields' => array(
				'title'      => array( 'name' => 'title' ),
				'maxprice'   => array( 'name' => 'maxprice' ),
				'minprice'   => array( 'name' => 'minprice' ),
				'city'       => array( 'name' => 'city' ),
				'zipcode'    => array( 'name' => 'zipcode' ),
				'ownertype'  => array( 'name' => 'ownertype' ),
				'maxresults' => array( 'name' => 'maxresults' )
			),
			'prices' => $this->getListingService()->getPriceData(),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData()
		);
		$this->getListingGridOptionsView()->out( $data );
	}


	private function renderFeaturedListingsOptions ()
	{
		$this->updateHttpHeader();
		$data = array(
			'fields'     => array(
				'title'      => array( 'name' => 'title' ),
				'autoplay'   => array( 'name' => 'autoplay' ),
				'direction'  => array( 'name' => 'direction' ),
				'speed'      => array( 'name' => 'speed' ),
				'ownertype'  => array( 'name' => 'ownertype' ),
				'maxresults' => array( 'name' => 'maxResults' )
			),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData()
		);
		$this->getFeaturedListingsOptionsView()->out( $data );
	}


	private function renderPropertyListOptions ()
	{
		$this->updateHttpHeader();
		$data = array(
			'fields' => array(
				'title'      => array( 'name' => 'title' ),
				'maxprice'   => array( 'name' => 'maxprice' ),
				'minprice'   => array( 'name' => 'minprice' ),
				'city'       => array( 'name' => 'city' ),
				'zipcode'    => array( 'name' => 'zipcode' ),
				'ownertype'  => array( 'name' => 'ownertype' ),
				'maxresults' => array( 'name' => 'maxresults' )
			),
			'prices' => $this->getListingService()->getPriceData(),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData()
		);
		$this->getPropertyListOptionsView()->out( $data );
	}


	private function updateHttpHeader ()
	{
		status_header( 200 );
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
	 * GETTER:  This method is a getter for the listingGridOptionsView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getListingGridOptionsView ()
	{
		return $this->listingGridOptionsView;
	}


	/**
	 * SETTER:  This method is a setter for the listingGridOptionsView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setListingGridOptionsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->listingGridOptionsView = $view;
	}


	/**
	 * GETTER: This method is a getter for the featuredListingsOptionsView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getFeaturedListingsOptionsView ()
	{
		return $this->featuredListingsOptionsView;
	}


	/**
	 * SETTER: This method is a setter for the featuredListingsOptionsView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setFeaturedListingsOptionsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->featuredListingsOptionsView = $view;
	}


	/**
	 * GETTER:  This method is a getter for the propertyListOptionsView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getPropertyListOptionsView ()
	{
		return $this->propertyListOptionsView;
	}


	/**
	 * SETTER:  This method is a setter for the propertyListOptionsView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setPropertyListOptionsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->propertyListOptionsView = $view;
	}


}