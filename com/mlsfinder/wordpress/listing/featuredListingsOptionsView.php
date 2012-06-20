<?php

/**
 * This view is repsondible for displaying the Film Strip Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing
 * @title         featuredListingsOptionsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_mlsfinder_wordpress_listing_featuredListingsOptionsView
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
			'autoPlayId'       => esc_attr( $data['fields']['autoPlay']['id'] ),
			'autoPlayName'     => esc_attr( $data['fields']['autoPlay']['name'] ),
			'autoPlayTrue'     => ( $data['fields']['autoPlay']['value'] == 'true' )   ? ' selected="selected"' : '',
			'autoPlayFalse'    => ( $data['fields']['autoPlay']['value'] == 'false' )  ? ' selected="selected"' : '',
			'directionId'      => esc_attr( $data['fields']['direction']['id'] ),
			'directionName'    => esc_attr( $data['fields']['direction']['name'] ),
			'directionLeft'    => ( $data['fields']['direction']['value'] == 'left' )  ? ' selected="selected"' : '',
			'directionRight'   => ( $data['fields']['direction']['value'] == 'right' ) ? ' selected="selected"' : '',
			'directionUp'      => ( $data['fields']['direction']['value'] == 'up' )    ? ' selected="selected"' : '',
			'directionDown'    => ( $data['fields']['direction']['value'] == 'down' )  ? ' selected="selected"' : '',
			'waitId'           => esc_attr( $data['fields']['wait']['id'] ),
			'waitName'         => esc_attr( $data['fields']['wait']['name'] ),
			'waitChecked'      => ( $data['fields']['wait']['value'] == 'true' ) ? ' checked="checked"' : '',
			'waitLenId'        => esc_attr( $data['fields']['waitLen']['id'] ),
			'waitLenName'      => esc_attr( $data['fields']['waitLen']['name'] ),
			'waitLenValue'     => esc_attr( $data['fields']['waitLen']['value'] ),
			'speedId'          => esc_attr( $data['fields']['speed']['id'] ),
			'speedName'        => esc_attr( $data['fields']['speed']['name'] ),
			'speedValue'       => esc_attr( $data['fields']['speed']['value'] ),
			'scrollCountId'    => esc_attr( $data['fields']['scrollCount']['id'] ),
			'scrollCountName'  => esc_attr( $data['fields']['scrollCount']['name'] ),
			'scrollCountValue' => esc_attr( $data['fields']['scrollCount']['value'] ),
			'ownerTypeId'      => esc_attr( $data['fields']['ownerType']['id'] ),
			'ownerTypeName'    => esc_attr( $data['fields']['ownerType']['name'] ),
			'ownerTypeValue'   => esc_attr( $data['fields']['ownerType']['value'] ),
			'maxResultsId'     => esc_attr( $data['fields']['maxResults']['id'] ),
			'maxResultsName'   => esc_attr( $data['fields']['maxResults']['name'] ),
			'maxResultsValue'  => esc_attr( $data['fields']['maxResults']['value'] )
			) );
		return parent::render( $data );
	}
	
	
}