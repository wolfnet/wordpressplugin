<?php

/**
 * This view is responsible for displaying the Listings Grid, which is a widget component.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         listingGridView.php
 * @extends       com_greentiedev_wppf_abstract_view
 * @implements    com_greentiedev_wppf_interface_iView
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
class com_wolfnet_wordpress_listing_listingGridView
extends com_greentiedev_wppf_abstract_view
implements com_greentiedev_wppf_interface_iView
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type  string
	 *
	 */
	public $template;

	/**
	 * This property holds a reference to the settings service.
	 *
	 * @type  com_wolfnet_wordpress_settings_service
	 *
	 */
	public $settingsService;

	/**
	 * This property holds a reference to the toolbar view.
	 *
	 * @type  com_greentiedev_wppf_interface_iView
	 *
	 */
	public $toolbarView;


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method overwrites the inherited render method and provides some additional functionality.
	 * Specifically it extracts the listings data from the $data param and passes it to the
	 * renderListings method. This separates the concerns of rendering the film strip from rendering
	 * indevidual listings.
	 *
	 * @param   array  $data  Associative array of data to be injected into the template file.
	 * @return  void
	 *
	 */
	public function render ( $data = array() )
	{
		if ( $data != null && array_key_exists( 'listings', $data ) ) {
			$data['listingContent'] = $this->renderListings( $data['listings'] );
		}

		$_REQUEST['wolfnet_includeDisclaimer'] = true; // For later use in the site footer.

		$data['instanceId']    = str_replace('.', '', uniqid( 'wolfnet_listings_' ));

		$data['options']['format']['id'] = 'format';
		$data['options']['format']['name'] = 'format';

		$templateParts = explode(DIRECTORY_SEPARATOR, $this->getTemplate());

		switch ($templateParts[count($templateParts)-1]) {

			default:
				$data['options']['format']['value'] = 'grid';
				break;

			case 'propertyList.php':
				$data['options']['format']['value'] = 'list';
				break;

		}

		$toolbarClass  = '';
		$toolbarClass .= ($data['options']['paginated']['value']   == 'true') ? 'wolfnet_withPagination '  : '' ;
		$toolbarClass .= ($data['options']['sortoptions']['value'] == 'true') ? 'wolfnet_withSortOptions ' : '' ;

		$data['toolbarTop']    = $this->getToolbarView()->render(array_merge($data,array('toolbarClass'=>$toolbarClass . 'wolfnet_toolbarTop ')));
		$data['toolbarBottom'] = $this->getToolbarView()->render(array_merge($data,array('toolbarClass'=>$toolbarClass . 'wolfnet_toolbarBottom ')));

		return parent::render( $data );

	}


	/**
	 * This method accepts an array of listing objects which is loops over and creates new instances
	 * of the listingView object for each. The listings are then rendered individually and combined
	 * in a string which is returned.
	 *
	 * @param   array   $listings  An array of listing objects.
	 * @return  string             Rendered listing content.
	 *
	 */
	private function renderListings ( $listings )
	{
		$listingContent = '';
		foreach ( $listings as $listing ) {
			$view = $this->getListingView();
			$listingContent .= $view->render( array( 'listing' => $listing ) );
		}
		return $listingContent;
	}


	/* ACCESSORS ******************************************************************************** */

	/**
	 * GETTER: This method is a getter for the listingView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getListingView ()
	{
		return $this->listingView;
	}


	/**
	 * SETTER: This method is a setter for the listingView property.
	 *
	 * @type    com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setListingView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->listingView = $view;
	}

	/**
	 * GETTER: This method is a getter for the listingView property.
	 *
	 * @return  string
	 *
	 */
	public function getTemplate ()
	{
		return $this->template;
	}


	/**
	 * SETTER: This method is a setter for the listingView property.
	 *
	 * @type    string  $template
	 * @return  void
	 *
	 */
	public function setTemplate ( $template )
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . $template );
	}


	/**
	 * GETTER: This method is a getter for the SettingsService property.
	 *
	 * @return  com_wolfnet_wordpress_settings_service
	 *
	 */
	public function getSettingsService ()
	{
		return $this->settingsService;
	}


	/**
	 * SETTER: This method is a setter for the SettingsService property.
	 *
	 * @type    com_wolfnet_wordpress_settings_service  $settingsService
	 * @return  void
	 *
	 */
	public function setSettingsService ( com_wolfnet_wordpress_settings_service $settingsService )
	{
		$this->settingsService = $settingsService;
	}


	/**
	 * GETTER: This method is a getter for the toolbarView property.
	 *
	 * @return  com_greentiedev_wppf_interface_iView
	 *
	 */
	public function getToolbarView ()
	{
		return $this->toolbarView;
	}


	/**
	 * SETTER: This method is a setter for the toolbarView property.
	 *
	 * @type    com_greentiedev_wppf_interface_iView  $view
	 * @return  void
	 *
	 */
	public function setToolbarView ( com_greentiedev_wppf_interface_iView $view )
	{
		$this->toolbarView = $view;
	}


}
