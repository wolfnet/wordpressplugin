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
class com_wolfnet_wordpress_listing_quickSearchShortcode
extends com_greentiedev_wppf_shortcode_shortcode
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
	public $tag = 'WolfNetListingQuickSearch,wolfnetlistingquicksearch,WOLFNETLISTINGQUICKSEARCH,wnt_search,WolfNetQuickSearch,wolfnetquicksearch,WOLFNETQUICKSEARCH';


	/**
	 * This property holds an array of different options that are available for each widget instance.
	 *
	 * @type  array
	 *
	 */
	protected $attributes = array(
		'title'        => 'QuickSearch',
		'ownertype'    => 'all',
		'maxresults'   => 50
		);


	/**
	 * This property contains a instance of the Quick Search View object
	 *
	 * @type  com_greentiedev_wppf_interface_iView
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
		$options = $this->getAttributesData( $attr );
		$ls = $this->getListingService();
		$data = array(
					'options' => $options,
					'prices'  => $ls->getPriceData(),
					'beds'    => $ls->getBedData(),
					'baths'   => $ls->getBathData()
					);
		return $this->getQuickSearchView()->render( $data );
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
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getQuickSearchView ()
	{
		return $this->quickSearchView;
	}


	/**
	 * SETTER:  This method is a setter for the quickSearchView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setQuickSearchView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->quickSearchView = $view;
	}


}
