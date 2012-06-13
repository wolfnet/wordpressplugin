<?php

/**
 * This view is responsible for displaying the plugin admin page.
 * 
 * @package			com.mlsfinder.wordpress.admin
 * @title			pluginSettingsView.php
 * @extends			com_ajmichels_wppf_abstract_view
 * @implements		com_ajmichels_wppf_interface_iView
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_admin_pluginSettingsView
extends com_ajmichels_wppf_abstract_view
implements com_ajmichels_wppf_interface_iView
{
	
	
	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type string
	 * 
	 */
	public $template;
	
	
	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return void
	 * 
	 */
	public function __construct ()
	{
		$this->log( 'Init com_mlsfinder_wordpress_admin_pluginSettingsView' );
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\pluginSettings.php' );
	}
	
	
	public function render ( $data = array() )
	{
		$optionManager					= $this->getOptionManager();
		$data['formHeader']				= $optionManager->getSettingsFormHeader();
		$data['productKey']				= $optionManager->getOptionValueFromWP('wnt_productKey');
		$data['searchSolutionURL']		= $optionManager->getOptionValueFromWP('wnt_searchSolutionURL');
		return parent::render( $data );
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	
	public function getOptionManager ()
	{
		return $this->optionManager;
	}
	
	
	public function setOptionManager ( com_ajmichels_wppf_option_manager $om )
	{
		$this->optionManager = $om;
	}
	
	
}