<?php

/**
 * This is the quickSearchWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         quickSearchWidget.php
 * @extends       com_wolfnet_wordpress_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_quickSearchWidget
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
		'title'        => 'QuickSearch',
		'description'  => 'Configure a quick search to include on your website.  When executed, the user is directed to matching properties within your WolfNet property search.'
		);

	/**
	 * This property holds a reference to the Listing Service object.
	 *
	 * @type  com_wolfnet_wordpress_listing_service
	 *
	 */
	private $listingService;


	/**
	 * This property holds a reference to the Quick Search View object.
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 *
	 */
	private $quickSearchView;

	/**
	 * This property holds an instance of the Quicksearch Options View object
	 *
	 * @type  com_ajmichels_wppf_interface_iView
	 */
	private $quickSearchOptionsView;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	/**
	 * This constructor method establishes some default values for the widget and forwards the
	 * instantiation on to the parent constructor.
	 *
	 * @return  void
	 *
	 */
	public function __construct ()
	{
		parent::__construct( 'wolfnet_quickSearchWidget', 'WolfNet Quick Search' );
		/* The 'sf' property is set in the abstract widget class and is pulled from the plugin instance */
		$this->setListingService( $this->sf->getBean( 'ListingService' ) );
		$this->setQuickSearchView( $this->sf->getBean( 'QuickSearchView' ) );
		$this->setQuickSearchOptionsView( $this->sf->getBean( 'QuickSearchOptionsView' ) );
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is the primary output for the widget. This is the information the end user of the
	 * site will see.
	 *
	 * @param  array  $args      An array of arguments passed to a widget.
	 * @param  array  $instance  An array of widget instance data
	 * @return  void
	 *
	 */
	public function widget ( $args, $instance )
	{
		$ls = $this->getListingService();
		$options = $this->getOptionData( $instance );
		$data = array(
					'options'  => $options
					);
		$this->getQuickSearchView()->out( $data );
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
			'fields' => $this->getOptionData( $instance )
			);

		$this->getQuickSearchOptionsView()->out( $data );
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
	 * GETTER:  This method is a getter for the listingsService property.
	 *
	 * @return  com_wolfnet_wordpress_listing_service
	 *
	 */
	public function getListingService ()
	{
		return $this->listingService;
	}


	/**
	 * SETTER:  This method is a setter for the listingsService property.
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
	 * GETTER:  This method is a getter for the quickSearchView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getQuickSearchView ()
	{
		return $this->quickSearchView;
	}


	/**
	 * SETTER:  This method is a setter for the quickSearchView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setQuickSearchView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->quickSearchView = $view;
	}

	/**
	 * GETTER:  This method is a getter for the quickSearchOptionsView property.
	 *
	 * @return  com_ajmichels_wppf_interface_iView
	 *
	 */
	public function getQuickSearchOptionsView ()
	{
		return $this->quickSearchOptionsView;
	}


	/**
	 * SETTER:  This method is a setter for the quickSearchOptionsView property.
	 *
	 * @param   com_ajmichels_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setQuickSearchOptionsView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->quickSearchOptionsView = $view;
	}

}