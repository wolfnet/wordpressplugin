<?php

/**
 * This is the quickSearchWidget object. This object inherites from the base WP_Widget object and 
 * defines the display and functionality of this specific widget.
 * 
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 * 
 * @package			com.mlsfinder.wordpress.listing
 * @title			quickSearchWidget.php
 * @extends			com_mlsfinder_wordpress_abstract_widget
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_quickSearchWidget
extends com_mlsfinder_wordpress_abstract_widget
{
	
	
	/**
	 * This constructor method passes some key information up to the parent classes and eventionally 
	 * the information gets registered with the WordPress application.
	 *
	 * @return	void
	 * 
	 */
	public function __construct ()
	{
		parent::__construct( 'mlsFinder_quickSearchWidget', 'Listing Quick Search' );
	}
	
	
	/**
	 * This method is the primary output for the widget. This is the information the end user of the 
	 * site will see.
	 * 
	 * @param	array	$args		An array of arguments passed to a widget.
	 * @param	array	$instance	An array of widget instance data
	 * @return	void
	 * 
	 */
	public function widget ( $args, $instance )
	{
		$view = $this->sf->get( 'QuickSearchView' );
		$view->out();
	}
	
	
	/**
	 * This method is responsible for display of the widget instance form which allows configuration
	 * of each widget instance in the WordPress admin.
	 * 
	 * @param	array	$instance	An array of widget instance data
	 * @return	void
	 * 
	 */
	public function form ( $instance )
	{
	}
	
	
	/**
	 * This method is responsible for saving any data that comes from the widget instance form.
	 * 
	 * @param	array	$new_instance	An array of widget instance data from after the form submit
	 * @param	array	$old_instance	An array of widget instance data from before the form submit
	 * @return	array					An array of data that needs to be saved to the database.
	 * 
	 */
	public function update ( $new_instance, $old_instance )
	{
	}
	
	
}