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
 * @title         featuredListingsShortcode.php
 * @extends       com_greentiedev_wppf_shortcode_shortcode
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */
class com_wolfnet_wordpress_listing_featuredListingsShortcode
extends com_greentiedev_wppf_shortcode_shortcode
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the tag name which is used to identify shorcodes when they are encountered
	 * in Posts and Pages.
	 *
	 * @type  string
	 *
	 */
	public $tag = 'WolfNetFeaturedListings,woflnetfeaturedlistings,WOFLNETFEATUREDLISTINGS,wnt_featured';


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
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $featuredListingsView;


	/**
	 * This property holds an array of different options that are available for each widget instance.
	 *
	 * @type  array
	 *
	 */
	protected $attributes = array(
		'title'       => '',
		'direction'   => 'left',
		'autoplay'    => true,
		'speed'       => 5,
		'ownertype'   => 'agent_broker',
		'maxresults'  => 50
		);


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
		$options = $this->getAttributesData( $attr );
		$featuredListings = $this->getListingService()->getFeaturedListings(
			$options['ownertype']['value'],
			$options['maxresults']['value']
			);
		$data = array(
			'listings' => $featuredListings,
			'options'  => $options
			);
		return $this->getFeaturedListingsView()->render( $data );
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
	 * GETTER:  This method is a getter for the featuredListingsView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getFeaturedListingsView ()
	{
		return $this->featuredListingsView;
	}


	/**
	 * SETTER:  This method is a setter for the featuredListingsView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setFeaturedListingsView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->featuredListingsView = $view;
	}


}
