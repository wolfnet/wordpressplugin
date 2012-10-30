<?php

/**
 * This action is responsible for creating the plugin admin pages within the WordPress admin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         manageRewritePages.php
 * @extends       com_greentiedev_wppf_action_action
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
class com_wolfnet_wordpress_action_manageRewritePages
extends com_greentiedev_wppf_action_action
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a references to the Listing Service object.
	 *
	 * @type  com_wolfnet_wordpress_listing_service
	 *
	 */
	private $listingService;


	/**
	 * This property holds a references to the Search Service object.
	 *
	 * @type  com_wolfnet_wordpress_search_service
	 *
	 */
	private $searchService;


	/**
	 * This property holds an instance of the Listing Grid Options View object
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $listingGridOptionsView;


	/**
	 * This property holds an instance of the Featured Listings Options View object.
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $featuredListingsOptionsView;


	/**
	 * This property holds an instance of the Property List Options View object
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $propertyListOptionsView;


	/**
	 * This property holds an instance of the Quick Search Options View object
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	private $quickSearchOptionsView;


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return  void
	 *
	 */
	public function execute ()
	{
		$pagename    = strtolower( get_query_var( 'pagename' ) );
		$adminPrefix = 'wolfnet-admin-';
		$isAdmin     = ( current_user_can( 'edit_pages' ) || current_user_can( 'edit_posts' ) );

		if ( substr( $pagename, 0, strlen( $adminPrefix ) ) == $adminPrefix ) {

			if ( !$isAdmin ) {
				$this->statusNotAuthorized();
				exit;
			}

			$method = str_replace( '-', '_', str_replace( $adminPrefix, '', $pagename ) );

			if ( !method_exists( $this, $method ) ) {
				$this->statusNotFound();
				exit;
			}

			call_user_method( $method, $this );

		}

	}


	/* PRIVATE METHODS ************************************************************************** */

	private function shortcodebuilder_options_featured ()
	{
		$this->statusSuccess();

		$data = array(
			'fields'     => array(
				'title'      => array( 'name' => 'title' ),
				'autoplay'   => array( 'name' => 'autoplay' ),
				'direction'  => array( 'name' => 'direction' ),
				'speed'      => array( 'name' => 'speed' ),
				'ownertype'  => array( 'name' => 'ownertype' ),
				'maxresults' => array( 'name' => 'maxResults' )
			),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData()
		);

		$this->getFeaturedListingsOptionsView()->out( $data );

		exit;

	}


	private function shortcodebuilder_options_grid ()
	{
		$this->statusSuccess();

		$data = array(
			'fields' => array(
				'title'       => array( 'name' => 'title' ),
				'criteria'    => array( 'name' => 'criteria' ),
				'mode'        => array( 'name' => 'mode', 'value' => 'advanced' ),
				'savedsearch' => array( 'name' => 'savedsearch' ),
				'maxprice'    => array( 'name' => 'maxprice' ),
				'minprice'    => array( 'name' => 'minprice' ),
				'city'        => array( 'name' => 'city' ),
				'zipcode'     => array( 'name' => 'zipcode' ),
				'ownertype'   => array( 'name' => 'ownertype' ),
				'maxresults'  => array( 'name' => 'maxresults' )
			),
			'prices' => $this->getListingService()->getPriceData(),
			'ownerTypes' => $this->getListingService()->getOwnerTypeData(),
			'savedSearches' => $this->getSearchService()->getSearches()
		);

		$this->getListingGridOptionsView()->out( $data );

		exit;
	}


	private function shortcodebuilder_options_list ()
	{
		$this->shortcodebuilder_options_grid();
	}


	private function shortcodebuilder_options_quicksearch ()
	{
		$this->statusSuccess();

		$data = array(
			'fields' => array(
				'title' => array( 'name' => 'title', 'value' => 'QuickSearch' )
			)
		);

		$this->getQuickSearchOptionsView()->out( $data );

		exit;

	}


	private function shortcodebuilder_saved_search ()
	{

		$this->statusSuccess();

		print( json_encode( $this->getSearchService()->getSearchCriteria( $_GET['ID'] ) ) );

		exit;
	}


	private function searchmanager_get ()
	{

		$this->statusSuccess();

		$data = $this->getSearchService()->getSearches();

		/* Convert the dates to a human readable format. */
		foreach ( $data as &$item ) {
			if ( property_exists( $item, 'post_date' ) ) {
				$item->post_date = date( 'm-d-Y h:i:s a', strtotime( $item->post_date ) );
			}
		}

		print( json_encode( $data ) );

		exit;
	}


	private function searchmanager_save ()
	{
		$canInsert = ( current_user_can( 'edit_pages' ) || current_user_can( 'edit_posts' ) ) ? true : false;

		if ( $canInsert ) {

			$this->getSearchService()->saveSearch( $_REQUEST['post_title'], $_REQUEST['custom_fields'] );

		}

		$this->searchmanager_get();

	}


	private function searchmanager_delete ()
	{
		$canDelete = ( current_user_can( 'delete_pages' ) || current_user_can( 'delete_posts' ) ) ? true : false;

		if ( $canDelete ) {

			$this->getSearchService()->deleteSearch( $_GET['ID'] );

		}

		$this->searchmanager_get();

	}


	private function statusSuccess ()
	{
		status_header( 200 );
	}


	private function statusNotFound ()
	{
		status_header( 404 );
	}


	private function statusNotAuthorized ()
	{
		status_header( 401 );
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
	 * GETTER:  This method is a getter for the searchService property.
	 *
	 * @return  com_wolfnet_wordpress_search_service
	 *
	 */
	public function getSearchService ()
	{
		return $this->searchService;
	}


	/**
	 * SETTER:  This method is a setter for the searchService property.
	 *
	 * @param   com_wolfnet_wordpress_search_service  $service
	 * @return  void
	 *
	 */
	public function setSearchService ( com_wolfnet_wordpress_search_service $service )
	{
		$this->searchService = $service;
	}


	/**
	 * GETTER:  This method is a getter for the listingGridOptionsView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getListingGridOptionsView ()
	{
		return $this->listingGridOptionsView;
	}


	/**
	 * SETTER:  This method is a setter for the listingGridOptionsView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setListingGridOptionsView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->listingGridOptionsView = $view;
	}


	/**
	 * GETTER: This method is a getter for the featuredListingsOptionsView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getFeaturedListingsOptionsView ()
	{
		return $this->featuredListingsOptionsView;
	}


	/**
	 * SETTER: This method is a setter for the featuredListingsOptionsView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setFeaturedListingsOptionsView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->featuredListingsOptionsView = $view;
	}


	/**
	 * GETTER:  This method is a getter for the propertyListOptionsView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getQuickSearchOptionsView ()
	{
		return $this->quickSearchOptionsView;
	}


	/**
	 * SETTER:  This method is a setter for the propertyListOptionsView property.
	 *
	 * @param   com_greentiedev_wppf_interface_iView  $service
	 * @return  void
	 *
	 */
	public function setQuickSearchOptionsView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->quickSearchOptionsView = $view;
	}


}
