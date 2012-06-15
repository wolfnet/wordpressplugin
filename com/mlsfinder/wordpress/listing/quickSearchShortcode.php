<?php

/**
 * This is the filmStripWidget object. This object inherites from the base WP_Widget object and 
 * defines the display and functionality of this specific widget.
 * 
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 * 
 * @package       com.mlsfinder.wordpress.listing
 * @title         quickSearchShortcode.php
 * @extends       com_ajmichels_wppf_shortcode_shortcode
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_quickSearchShortcode
extends com_ajmichels_wppf_shortcode_shortcode
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	public $tag = 'ListingQuickSearch';
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	public function execute ( $attr, $content = null ) {
		return $this->getQuickSearchView()->render();
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	public function getQuickSearchView ()
	{
		return $this->quickSearchView;
	}
	
	
	public function setQuickSearchView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->quickSearchView = $view;
	}
	
	
}