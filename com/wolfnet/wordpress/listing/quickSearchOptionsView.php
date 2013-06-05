<?php

/**
 * This view is responsible for displaying the QuickSearch Widget Options in the WordPress admin.
 * Each widget instance has its own instance of this view.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         quickSearchOptionsView.php
 * @extends       com_wolfnet_wordpress_abstract_widget
 * @implements    com_greentiedev_wppf_interface_iView
 * @contributors  Andrew Baumgart
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 */

class com_wolfnet_wordpress_listing_quickSearchOptionsView
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
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\quickSearchOptions.php' );
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
		$data = array_merge( array(
			'titleId'            => esc_attr( $data['fields']['title']['id'] ),
			'titleName'          => esc_attr( $data['fields']['title']['name'] ),
			'titleValue'         => $data['fields']['title']['value']
			));
		return parent::render( $data );
	}

}
