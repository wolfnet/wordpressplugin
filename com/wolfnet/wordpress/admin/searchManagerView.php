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

	/**
	 * This property holds a reference to the OptionManager object.
	 *
	 * @type  string
	 *
	 */
	public $optionManager;


	private $settingsService;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\searchManager.php' );
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
		// TODO: Update this to perform a validation check on the product key.
		//$optionManager = $this->getOptionManager();
		//$productKey    = $optionManager->getOptionValueFromWP( 'wolfnet_productKey' );
		$validKey      = true;
		if ( !$validKey ) {
			$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\invalidProductKey.php' );
		}
		$data['search_form'] = $this->getSearchForm();
		return parent::render( $data );
	}


	/* PRIVATE ********************************************************************************** */

	private function getSearchForm ()
	{
		$settings = $this->getSettingsService()->getSettings();

		//$url = 'http://aj.vm.mlsfinder.com/mn_rmls/2point5c/index.cfm?action=wpshortcodebuilder&search_mode=form';
		//
		//$http = wp_remote_get( $url );
		//
		//return $http['body'];

		return '';

	}


	/* ACCESSORS ******************************************************************************** */

	/**
	 * GETTER: This method is a getter for the optionManager property.
	 *
	 * @return com_ajmichels_wppf_option_manager
	 *
	 */
	public function getOptionManager ()
	{
		return $this->optionManager;
	}


	/**
	 * SETTER: This method is a setter for the optionManager property.
	 *
	 * @param   com_ajmichels_wppf_option_manager  $om
	 *
	 * @return  void
	 *
	 */
	public function setOptionManager ( com_ajmichels_wppf_option_manager $om )
	{
		$this->optionManager = $om;
	}


	/**
	 * GETTER: This method is a getter for the settingsService property.
	 *
	 * @return  string  The absolute URL to this plugin's directory.
	 *
	 */
	public function getSettingsService ()
	{
		return $this->settingsService;
	}


	/**
	 * SETTER: This method is a setter for the settingsService property.
	 *
	 * @param   string  $url  The absolute URL to this plugin's directory.
	 * @return  void
	 *
	 */
	public function setSettingsService ( com_wolfnet_wordpress_settings_service $service )
	{
		$this->settingsService = $service;
	}


}