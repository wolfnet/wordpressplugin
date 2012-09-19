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
 * @extends       com_wolfnet_wordpress_listing_listingGridWidget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_propertyListWidget
extends com_wolfnet_wordpress_listing_listingGridWidget
{


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
		$this->id = 'wolfnet_propertyListWidget';
		$this->name = 'WolfNet Property List';
		$this->options['description'] = 'Define criteria to display a text list of matching properties. The text display includes the property address and price for each property.';
		parent::__construct();
		/* The 'sf' property is set in the abstract widget class and is pulled from the plugin instance */
		$this->setListingGridView( $this->sf->getBean( 'PropertyListView' ) );
	}


}