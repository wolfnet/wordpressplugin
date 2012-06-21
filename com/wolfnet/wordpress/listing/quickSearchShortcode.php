<?php

/**
 * This is the filmStripWidget object. This object inherites from the base WP_Widget object and 
 * defines the display and functionality of this specific widget.
 * 
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         quickSearchShortcode.php
 * @extends       com_ajmichels_wppf_shortcode_shortcode
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_listing_quickSearchShortcode
extends com_ajmichels_wppf_shortcode_shortcode
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property contains the string which will be used as the shortcode tag.  
	 * 
	 * @type  string
	 * 
	 * @TODO  Convert 'tag' property to a constant as it should not be allowed to change during the 
	 *        request.
	 * 
	 */
	public $tag = 'WolfNetListingQuickSearch';
	
	
	/**
	 * This property contains a instance of the Quick Search View object
	 * 
	 * @type  com_ajmichels_wppf_interface_iView
	 * 
	 */
	private $quickSearchView;
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method is called whenever an instance of the shortcode is encountered in a post or page.
	 * 
	 * @param   array   $attr
	 * @param   string  $content
	 * @return  string
	 * 
	 */
	public function execute ( $attr, $content = null )
	{
		return $this->getQuickSearchView()->render();
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
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
	 * @param   com_ajmichels_wppf_interface_iView  $view
	 * @return  void
	 * 
	 */
	public function setQuickSearchView ( com_ajmichels_wppf_interface_iView $view )
	{
		$this->quickSearchView = $view;
	}
	
	
}