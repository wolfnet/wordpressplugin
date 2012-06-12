<?php

/**
 * This view is responsible for displaying the Quick Search Form, which is a widget component.
 * 
 * @package			com.mlsfinder.wordpress.listing
 * @title			quickSearchView.php
 * @extends			com_ajmichels_wppf_abstract_view
 * @implements		com_ajmichels_wppf_interface_iView
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_quickSearchView
extends com_ajmichels_wppf_abstract_view
implements com_ajmichels_wppf_interface_iView
{
	
	
	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type	string
	 * 
	 */
	public $template;
	
	
	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return	void
	 * 
	 */
	public function __construct ()
	{
		$this->log( 'Init com_mlsfinder_wordpress_listing_filmStripView' );
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\quickSearchForm.php' );
	}
	
	
}