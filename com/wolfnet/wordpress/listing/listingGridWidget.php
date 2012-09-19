<?php

/**
 * This is the listingGridWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         listingGridWidget.php
 * @extends       com_wolfnet_wordpress_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_listingGridWidget
extends com_wolfnet_wordpress_abstract_widget
{


	/* PROPERTIES ******************************************************************************* */

	public $id = 'wolfnet_listingGridWidget';


	public $name = 'WolfNet Listing Grid';

	/**
	 * This property holds an array of different options that are available for each widget instance.
	 *
	 * @type  array
	 *
	 */
	public $options = array(
		'title'        => '',
		'description'  => 'Define criteria to display a grid of matching properties. The grid display includes an image, price, number of bedrooms, number of bathrooms, and address.',
		'criteria'     => '',
		'mode'         => 'advanced',
		'savedsearch'  => '',
		'zipcode'      => '',
		'city'         => '',
		'minprice'     => '',
		'maxprice'     => '',
		'ownertype'    => 'all',
		'maxresults'   => 50
	);


	/**
	 * This property holds an array of options for the widget admin form.
	 *
	 * @type  array
	 *
	 */
	public $controls = array(
		'width' => '400px'
		);


	/**
	 * This property holds a references to the Listing Service object.
	 *
	 * @type  com_wolfnet_wordpress_listing_service
	 *
	 */
	private $listingService;


	/**
	 * This property holds a references to the Search Service object.
	 *
	 * @type  com_wolfnet_wordpress_search_service
	 *
	 */
	private $searchService;


	/**
	 * This property holds an instance of the Listing Grid View object.
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $listingGridView;


	/**
	 * This property holds an instance of the Listing Grid Options View object
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $listingGridOptionsView;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	/**
	 * This constructor method passes some key information up to the parent classes and eventionally
	 * the information gets registered with the WordPress application.
	 *
	 * @return  void
	 *
	 */
	public function __construct ()
	{
		parent::__construct( $this->id, $this->name );
		/* The 'sf' property is set in the abstract widget class and is pulled from the plugin instance */
		$this->setListingService( $this->sf->getBean( 'ListingService' ) );
		$this->setSearchService( $this->sf->getBean( 'SearchService' ) );
		$this->setListingGridView( $this->sf->getBean( 'ListingGridView' ) );
		$this->setListingGridOptionsView( $this->sf->getBean( 'ListingGridOptionsView' ) );
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is the primary output for the widget. This is the information the end user of the
	 * site will see.
	 *
	 * @param   array  $args      An array of arguments passed to a widget.
	 * @param   array  $instance  An array of widget instance data
	 * @return  void
	 *
	 */
	public function widget ( $args, $instance )
	{
		$options  = $this->getOptionData( $instance );

		$gridListings = $this->getListingService()->getGridListings(
			$this->convertCriteriaJsonToArray( $options ),
			$options['ownertype']['value'],
			$options['maxresults']['value']
			);

		$data = array(
			'listings' => $gridListings,
			'options'  => $options
		);

		$this->getListingGridView()->out( $data );

	}


	/**
	 * This method is responsible for display of the widget instance form which allows configuration
	 * of each widget instance in the WordPress admin.
	 *
	 * @param   array  $instance  An array of widget instance data
	 * @return  void
	 *
	 */
	public function form ( $instance )
	{
		$ls     = $this->getListingService();
		$fields = $this->getOptionData( $instance );

		$this->convertCriteriaJsonToOptions( $fields );

		$data = array(
			'fields'        => $fields,
			'prices'        => $ls->getPriceData(),
			'ownerTypes'    => $this->getListingService()->getOwnerTypeData(),
			'savedSearches' => $this->getSearchService()->getSearches()
		);

		$this->getListingGridOptionsView()->out( $data );
	}


	/**
	 * This method is responsible for saving any data that comes from the widget instance form.
	 *
	 * @param   array  $new_instance  An array of widget instance data from after the form submit
	 * @param   array  $old_instance  An array of widget instance data from before the form submit
	 * @return  array                 An array of data that needs to be saved to the database.
	 *
	 */
	public function update ( $new_instance, $old_instance )
	{
		// processes widget options to be saved
		$newData  = $this->getOptionData( $new_instance );
		$saveData = array();

		foreach ( $newData as $opt => $data ) {
			$saveData[$opt] = strip_tags( $data['value'] );
		}

		/* Advanced Mode */
		if ( $saveData['mode'] == 'advanced' ) {
			if ( $saveData['savedsearch'] == 'deleted' ) {
				/* Maintain the existing search criteria */
			}
			else {

				$criteria = $this->getSearchService()->getSearchCriteria( $saveData['savedsearch'] );
				$saveData['criteria'] = json_encode( $criteria );

			}
		}

		/* Basic Mode */
		else {
			$criteria = array();
			if ( $saveData['minprice'] != '' ) {
				$criteria['minprice'] = $saveData['minprice'];
			}
			if ( $saveData['maxprice'] != '' ) {
				$criteria['maxprice'] = $saveData['maxprice'];
			}
			if ( $saveData['city'] != '' ) {
				$criteria['city'] = $saveData['city'];
			}
			if ( $saveData['zipcode'] != '' ) {
				$criteria['zipcode'] = $saveData['zipcode'];
			}
			$saveData['criteria'] = json_encode( $criteria );
		}

		/* Remove these values since they have already been included in the criteria */
		unset( $saveData['zipcode'] );
		unset( $saveData['city'] );
		unset( $saveData['minprice'] );
		unset( $saveData['maxprice'] );

		return $saveData;

	}


	/* PRIVATE METHODS ************************************************************************** */

	private function convertCriteriaJsonToOptions ( array &$fields )
	{
		$criteria = $this->convertCriteriaJsonToArray( $fields );

		foreach ( $criteria as $field => $value ) {
			if ( array_key_exists( $field, $fields ) ) {
				$fields[$field]['value'] = $value;
			}
		}

	}


	private function convertCriteriaJsonToArray ( array $fields )
	{
		$criteria = json_decode( $fields['criteria']['value'], true );

		if ( !is_array( $criteria ) ) {
			$criteria = array();
		}

		return $criteria;
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
	 * GETTER:  This method is a getter for the SearchService property.
	 *
	 * @return  com_wolfnet_wordpress_search_service
	 *
	 */
	public function getSearchService ()
	{
		return $this->searchService;
	}


	/**
	 * SETTER:  This method is a setter for the SearchService property.
	 *
	 * @param   com_wolfnet_wordpress_search_service  $service
	 * @return  void
	 *
	 */
	public function setSearchService ( com_wolfnet_wordpress_search_service $service )
	{
		$this->searchService = $service;
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
	 * @param   com_ajmichels_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setListingGridView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->listingGridView = $view;
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


}