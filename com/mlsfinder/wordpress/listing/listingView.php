<?php

/**
 * This view is responsible for displaying a listing record.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    listing
 * @title         listingView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_mlsfinder_wordpress_listing_listingView
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
	
	public function __construct ()
	{
		$this->setTemplate();
	}
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This is an overwritten version of the parent class method. It must call parent::render at 
	 * some point.
	 * 
	 * @param   array  $data  And array of data which will be available as local variables to the 
	 *                        template page used in the render process.
	 * @return  string
	 */
	public function render ( $data = array() )
	{
		$data['id']       = $data['listing']->getPropertyId();
		$data['url']      = $data['listing']->getPropertyUrl();
		$data['address']  = $data['listing']->getDisplayAddress();
		$data['image']    = $data['listing']->getThumbnailUrl();
		$data['price']    = $data['listing']->getListingPrice();
		$data['location'] = $data['listing']->getLocation();
		$data['bedbath']  = $data['listing']->getBedsAndBaths();
		$data['rawData']  = $data['listing']->_getMemento();
		
		return parent::render( $data );
	}
	
	
	/* ACCESSOR METHODS ************************************************************************* */
	
	/**
	 * This method creates a way to dynamically specify which HTML template file should be used to 
	 * render the view.  This is done because listings can be rendered in several different ways using
	 * the same set of data.
	 * 
	 * @param   string  $type  The template type/file to use for rendering.
	 * @return  void
	 * 
	 */
	public function setTemplate ( $type = 'full' )
	{
		switch ( $format ) {
			
			default:
			case 'simple':
				$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\SimpleListing.php' );
				break;
				
		}
	}
	
	
}