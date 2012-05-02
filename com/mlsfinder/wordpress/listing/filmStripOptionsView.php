<?php

/**
 * This view is repsondible for displaying the Film Strip Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 *
 * @package			com.mlsfinder.wordpress.listing
 * @title			filmStripOptionsView.php
 * @extends			com_ajmichels_wppf_abstract_view
 * @implements		com_ajmichels_wppf_interface_iView
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_filmStripOptionsView
extends com_ajmichels_wppf_abstract_view
implements com_ajmichels_wppf_interface_iView
{
	
	
	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type string
	 * 
	 */
	public $template;
	
	
	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return void
	 * 
	 */
	public function __construct ()
	{
		$this->log( 'Init com_mlsfinder_wordpress_listing_filmStripOptionsView' );
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\ListingFilmStripOptions.php' );
	}
	
	
}