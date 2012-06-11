<?php

/**
 * This view is responsible for displaying a listing record.
 *
 * @package			com.mlsfinder.wordpress.listing
 * @title			view.php
 * @extends			com_ajmichels_wppf_abstract_view
 * @implements		com_ajmichels_wppf_interface_iView
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_view
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
		$this->log( 'Init com_mlsfinder_wordpress_listing_view' );
		$this->setTemplate();
	}
	
	
	/**
	 * This method creates a way to dynamically specify which HTML template file should be used to 
	 * render the view.  This is done because listings can be rendered in several different ways using
	 * the same set of data.
	 * 
	 * @param	string	$type	The template type/file to use for rendering.
	 * @return	void
	 */
	public function setTemplate ( $type = 'full' )
	{
		switch ( $format ) {
			
			default:
			case 'simple':
				$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\SimpleListing.php' );
				break;
				
		}
	}
	
	
}