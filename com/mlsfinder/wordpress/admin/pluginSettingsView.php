<?php

/**
 * This view is responsible for displaying the plugin admin page.
 * 
 * @package       com.mlsfinder.wordpress
 * @subpackage    admin
 * @title         pluginSettingsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class com_mlsfinder_wordpress_admin_pluginSettingsView
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
	
	
	/** CONSTRUCTOR METHOD ********************************************************************** */
	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\pluginSettings.php' );
	}
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	public function render ( $data = array() )
	{
		$optionManager             = $this->getOptionManager();
		$data['formHeader']        = $optionManager->getSettingsFormHeader();
		$data['productKey']        = $optionManager->getOptionValueFromWP('wnt_productKey');
		$data['searchSolutionURL'] = $optionManager->getOptionValueFromWP('wnt_searchSolutionURL');
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