<?php

/**
 * This view is responsible for displaying a listing record.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         listingView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_listingView
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
		$data['id']                  = $data['listing']->getPropertyId();
		$data['url']                 = $data['listing']->getPropertyUrl();
		$data['address']             = $data['listing']->getDisplayAddress();
		$data['address_full']        = $data['listing']->getFullAddress();
		$data['image']               = $data['listing']->getPhotoUrl();
		$data['price']               = $data['listing']->getListingPrice();
		$data['location']            = $data['listing']->getLocation();
		$data['fullLocation']        = $data['listing']->getLocation();
		$data['bedbath']             = $data['listing']->getBedsAndBaths( 'abbreviated' );
		$data['bedbath_full']        = $data['listing']->getBedsAndBaths( 'full' );
		$data['branding_brokerLogo'] = $data['listing']->getBranding()->getBrokerLogo();
		$data['branding_content']    = $data['listing']->getBranding()->getContent();
		$data['rawData']             = $data['listing']->getMemento();
		$data['rawData_branding']    = $data['rawData']['branding']->getMemento();
		$data['listing_class']       = '';

		if ( trim( $data['branding_brokerLogo'] ) != '' || trim( $data['branding_content'] ) != '' ) {

			$data['listing_class'] = ' wolfnet_branded';

		}

		/* Trim data to ensure that it fits in the alloted space. */
		$len = 20;
		$suf = '...';
		$this->truncateString( $data['location'], $len, $suf );
		$this->truncateString( $data['address'],  $len, $suf );

		return parent::render( $data );
	}


	/* PRIVATE METHODS ************************************************************************** */

	private function truncateString ( &$string, $length, $sufix='' )
	{
		if ( strlen( $string ) > $length ) {

			$substrlen = $length;

			if ( trim( $sufix ) != '' ) {
				$substrlen = $substrlen - strlen( $sufix );
			}

			$string = substr( $string, 0, $substrlen ) . $sufix;

		}

		return $string;
	}


	/* ACCESSOR METHODS ************************************************************************* */

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


}