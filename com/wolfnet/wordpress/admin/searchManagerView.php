<?php

/**
 * This view is responsible for displaying the plugin admin page.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    admin
 * @title         searchManagerView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class com_wolfnet_wordpress_admin_searchManagerView
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
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\searchManager.php' );
	}
	
	
}