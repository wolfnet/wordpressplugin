<?php

/**
 * This view is repsondible for displaying the Film Strip Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         featuredListingsOptionsView.php
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
class com_wolfnet_wordpress_listing_featuredListingsOptionsView
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


	/* CONSTRUCTOR ****************************************************************************** */

	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return  void
	 *
	 */
	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\featuredListingsOptions.php' );
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method establishes variable values which will be used by the template when it is render,
	 * then the data is passed to to inharited render method.
	 *
	 * @param   array  $data  An associative array of data for the template. Each array key will be
	 *                        transformed into a variable.
	 * @return  string
	 *
	 */
	public function render ( $data = array() )
	{
		$data = array_merge( $data, array(
			'titleId'            => esc_attr( $data['fields']['title']['id'] ),
			'titleName'          => esc_attr( $data['fields']['title']['name'] ),
			'titleValue'         => $data['fields']['title']['value'],
			'autoPlayId'         => esc_attr( $data['fields']['autoplay']['id'] ),
			'autoPlayName'       => esc_attr( $data['fields']['autoplay']['name'] ),
			'autoPlayTrue'       => selected( $data['fields']['autoplay']['value'], 'true', false ),
			'autoPlayFalse'      => selected( $data['fields']['autoplay']['value'], 'false', false ),
			'directionId'        => esc_attr( $data['fields']['direction']['id'] ),
			'directionName'      => esc_attr( $data['fields']['direction']['name'] ),
			'autoDirectionLeft'  => selected( $data['fields']['direction']['value'], 'left', false ),
			'autoDirectionRight' => selected( $data['fields']['direction']['value'], 'right', false ),
			'speedId'            => esc_attr( $data['fields']['speed']['id'] ),
			'speedName'          => esc_attr( $data['fields']['speed']['name'] ),
			'speedValue'         => esc_attr( $data['fields']['speed']['value'] ),
			'ownerTypeId'        => esc_attr( $data['fields']['ownertype']['id'] ),
			'ownerTypeName'      => esc_attr( $data['fields']['ownertype']['name'] ),
			'ownerTypeValue'     => $data['fields']['ownertype']['value'],
			'maxResultsId'       => esc_attr( $data['fields']['maxresults']['id'] ),
			'maxResultsName'     => esc_attr( $data['fields']['maxresults']['name'] ),
			'maxResultsValue'    => esc_attr( $data['fields']['maxresults']['value'] )
			) );
		$data['instanceId']	= uniqid( 'wolfnet_featuredListingsOptions_' );
		return parent::render( $data );
	}


}
