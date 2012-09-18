<?php

/**
 * This is the featuredListingsWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         featuredListingsWidget.php
 * @extends       com_wolfnet_wordpress_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_featuredListingsWidget
extends com_wolfnet_wordpress_abstract_widget
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds an array of different options that are available for each widget instance.
	 *
	 * @type  array
	 *
	 */
	public $options = array(
		'title'       => '',
		'description'  => 'Display a scrollable list of featured properties based on user defined criteria.',
		'direction'   => 'left',
		'autoplay'    => true,
		'speed'       => 5,
		'ownertype'   => 'agent_broker',
		'maxresults'  => 50
		);


	/**
	 * This property holds an array of options for the widget admin form.
	 *
	 * @type  array
	 *
	 */
	public $controls = array(
		'width' => '300px'
		);


	/**
	 * This property holds a reference to the Listing Service object.
	 *
	 * @type  com_wolfnet_wordpress_listing_service
	 *
	 */
	private $listingService;


	/**
	 * This property holds an instance of the Featured Listings View object.
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $featuredListingsView;


	/**
	 * This property holds an instance of the Featured Listings Options View object.
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $featuredListingsOptionsView;


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
		parent::__construct( 'wolfnet_featuredListingsWidget', 'WolfNet Featured Listings' );
		/* The 'sf' property is set in the abstract widget class and is pulled from the plugin instance */
		$this->setListingService( $this->sf->getBean( 'ListingService' ) );
		$this->setFeaturedListingsView( $this->sf->getBean( 'FeaturedListingsView' ) );
		$this->setFeaturedListingsOptionsView( $this->sf->getBean( 'FeaturedListingsOptionsView' ) );
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
		$options = $this->getOptionData( $instance );

		$featuredListings = $this->getListingService()->getFeaturedListings(
			$options['ownertype']['value'],
			$options['maxresults']['value']
			);

		$data = array(
			'listings' => $featuredListings,
			'options'  => $options
			);

		$this->getFeaturedListingsView()->out( $data );

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
		$data = array(
			'fields'     => $this->getOptionData( $instance ),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData()
			);
		$this->getFeaturedListingsOptionsView()->out( $data );
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
		/* processes widget options to be saved */
		$newData = $this->getOptionData( $new_instance );
		$saveData = array();
		foreach ( $newData as $opt => $data ) {
			$saveData[$opt] = strip_tags( $data['value'] );
		}
		return $saveData;
	}


	/* ACCESSORS ******************************************************************************** */

	/**
	 * GETTER: This method is a getter for the listingService property.
	 *
	 * @return  com_wolfnet_wordpress_listing_service
	 *
	 */
	public function getListingService ()
	{
		return $this->listingService;
	}


	/**
	 * SETTER: This method is a setter for the listingService property.
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
	 * GETTER: This method is a getter for the featuredListingsView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getFeaturedListingsView ()
	{
		return $this->featuredListingsView;
	}


	/**
	 * SETTER: This method is a setter for the featuredListingsView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setFeaturedListingsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->featuredListingsView = $view;
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


}