<?php

/**
 * This view is repsondible for displaying the Grid Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         listingGridOptionsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_listing_listingGridOptionsView
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
			'maxResultsId'      => esc_attr( $data['fields']['maxresults']['id'] ),
			'maxResultsName'    => esc_attr( $data['fields']['maxresults']['name'] ),
			'maxResultsValue'   => esc_attr( $data['fields']['maxresults']['value'] )
			) );
		return parent::render( $data );
	}
	
	
}