<?php

/**
 * This is the propertyListWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         propertyListWidget.php
 * @extends       com_wolfnet_wordpress_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_propertyListWidget
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
		'title'        => '',
		'description'  => 'Display a list of property addresses and prices based on user defined criteria.',
		'minprice'     => '',
		'maxprice'     => '',
		'city'         => '',
		'zipcode'      => '',
		'ownertype'    => 'agent_broker',
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
	 * This property holds an instance of the Property List View object.
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $propertyListView;


	/**
	 * This property holds an instance of the Property List Options View object
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $propertyListOptionsView;


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
		parent::__construct( 'wolfnet_propertyListWidget', 'WolfNet Property List' );
		/* The 'sf' property is set in the abstract widget class and is pulled from the plugin instance */
		$this->setListingService( $this->sf->getBean( 'ListingService' ) );
		$this->setPropertyListView( $this->sf->getBean( 'PropertyListView' ) );
		$this->setPropertyListOptionsView( $this->sf->getBean( 'PropertyListOptionsView' ) );
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
		$this->getPropertyListView( $data )->out( $data );
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
		$ls = $this->getListingService();
		$data = array(
			'fields' => $this->getOptionData( $instance ),
			'prices' => $ls->getPriceData(),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData()
			);
		$this->getPropertyListOptionsView()->out( $data );
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
		$newData = $this->getOptionData( $new_instance );
		$saveData = array();
		foreach ( $newData as $opt => $data ) {
			$saveData[$opt] = strip_tags( $data['value'] );
		}
		return $saveData;
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
	 * GETTER:  This method is a getter for the propertyListView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getPropertyListView ()
	{
		return $this->propertyListView;
	}


	/**
	 * SETTER:  This method is a setter for the propertyListView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setPropertyListView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->propertyListView = $view;
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