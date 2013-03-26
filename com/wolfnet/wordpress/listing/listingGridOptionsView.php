<?php

/**
 * This view is repsondible for displaying the Grid Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         listingGridOptionsView.php
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
class com_wolfnet_wordpress_listing_listingGridOptionsView
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


	/* CONSTRUCTOR METHOD *********************************************************************** */

	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return  void
	 *
	 */
	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\listingGridOptions.php' );
	}


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
		$data = array_merge( $data, array(
			'titleId'           => esc_attr( $data['fields']['title']['id'] ),
			'titleName'         => esc_attr( $data['fields']['title']['name'] ),
			'titleValue'        => $data['fields']['title']['value'],
			'criteriaId'        => esc_attr( $data['fields']['criteria']['id'] ),
			'criteriaName'      => esc_attr( $data['fields']['criteria']['name'] ),
			'criteriaValue'     => esc_attr( $data['fields']['criteria']['value'] ),
			'modeId'            => esc_attr( $data['fields']['mode']['id'] ),
			'modeName'          => esc_attr( $data['fields']['mode']['name'] ),
			'modeBasic'         => ( $data['fields']['mode']['value'] == 'basic' )    ? 'checked="checked"' : '',
			'modeAdvanced'      => ( $data['fields']['mode']['value'] == 'advanced' ) ? 'checked="checked"' : '',
			'savedSearchId'     => esc_attr( $data['fields']['savedsearch']['id'] ),
			'savedSearchName'   => esc_attr( $data['fields']['savedsearch']['name'] ),
			'savedSearchValue'  => $data['fields']['savedsearch']['value'],
			'maxPriceId'        => esc_attr( $data['fields']['maxprice']['id'] ),
			'maxPriceName'      => esc_attr( $data['fields']['maxprice']['name'] ),
			'maxPriceValue'     => $data['fields']['maxprice']['value'],
			'minPriceId'        => esc_attr( $data['fields']['minprice']['id'] ),
			'minPriceName'      => esc_attr( $data['fields']['minprice']['name'] ),
			'minPriceValue'     => $data['fields']['minprice']['value'],
			'cityId'            => esc_attr( $data['fields']['city']['id'] ),
			'cityName'          => esc_attr( $data['fields']['city']['name'] ),
			'cityValue'         => esc_attr( $data['fields']['city']['value'] ),
			'zipcodeId'         => esc_attr( $data['fields']['zipcode']['id'] ),
			'zipcodeName'       => esc_attr( $data['fields']['zipcode']['name'] ),
			'zipcodeValue'      => esc_attr( $data['fields']['zipcode']['value'] ),
			'ownerTypeId'       => esc_attr( $data['fields']['ownertype']['id'] ),
			'ownerTypeName'     => esc_attr( $data['fields']['ownertype']['name'] ),
			'ownerTypeValue'    => $data['fields']['ownertype']['value'],
			'paginatedId'	    => esc_attr( $data['fields']['paginated']['id'] ),
			'paginatedName'	    => esc_attr( $data['fields']['paginated']['name'] ),
			'paginatedTrue'	    => selected( $data['fields']['paginated']['value'], 'true', false ),
			'paginatedFalse'    => selected( $data['fields']['paginated']['value'], 'false', false ),
			'sortoptionsId'     => esc_attr( $data['fields']['sortoptions']['id'] ),
			'sortoptionsName'   => esc_attr( $data['fields']['sortoptions']['name'] ),
			'sortoptionsTrue'   => selected( $data['fields']['sortoptions']['value'], 'true', false ),
			'sortoptionsFalse'  => selected( $data['fields']['sortoptions']['value'], 'false', false ),
			'maxResultsId'      => esc_attr( $data['fields']['maxresults']['id'] ),
			'maxResultsName'    => esc_attr( $data['fields']['maxresults']['name'] ),
			'maxResultsValue'   => esc_attr( $data['fields']['maxresults']['value'] )
			) );
		$data['instanceId']	= uniqid( 'wolfnet_listingsOptions_' );
		return parent::render( $data );
	}

}
