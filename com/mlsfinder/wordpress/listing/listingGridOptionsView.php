<?php

/**
 * This view is repsondible for displaying the Grid Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing
 * @title         listingGridOptionsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_listing_listingGridOptionsView
extends com_ajmichels_wppf_abstract_view
implements com_ajmichels_wppf_interface_iView
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
	
	
	/* CONSTRUCTOR METHOD *********************************************************************** */
	
	public function render ( $data = array() )
	{
		$data = array_merge( $data, array( 
			'maxPriceId'       => esc_attr( $data['fields']['maxPrice']['id'] ),
			'maxPriceName'     => esc_attr( $data['fields']['maxPrice']['name'] ),
			'maxPriceValue'    => esc_attr( $data['fields']['maxPrice']['value'] ),
			'minPriceId'       => esc_attr( $data['fields']['minPrice']['id'] ),
			'minPriceName'     => esc_attr( $data['fields']['minPrice']['name'] ),
			'minPriceValue'    => esc_attr( $data['fields']['minPrice']['value'] ),
			'cityId'           => esc_attr( $data['fields']['city']['id'] ),
			'cityName'         => esc_attr( $data['fields']['city']['name'] ),
			'cityValue'        => esc_attr( $data['fields']['city']['value'] ),
			'zipcodeId'        => esc_attr( $data['fields']['zipcode']['id'] ),
			'zipcodeName'      => esc_attr( $data['fields']['zipcode']['name'] ),
			'zipcodeValue'     => esc_attr( $data['fields']['zipcode']['value'] ),
			'agentBrokerId'    => esc_attr( $data['fields']['agentBroker']['id'] ),
			'agentBrokerName'  => esc_attr( $data['fields']['agentBroker']['name'] ),
			'agentBrokerValue' => esc_attr( $data['fields']['agentBroker']['value'] ),
			'maxResultsId'     => esc_attr( $data['fields']['maxResults']['id'] ),
			'maxResultsName'   => esc_attr( $data['fields']['maxResults']['name'] ),
			'maxResultsValue'  => esc_attr( $data['fields']['maxResults']['value'] )
			) );
		return parent::render( $data );
	}
	
	
}