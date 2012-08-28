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


	private $sessionKey = 'wolfnetSearchManagerCookies';


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
		if ( !$this->getSettingsService()->isKeyValid() ) {
			$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\invalidProductKey.php' );
		}
		else {
			$data['search_form'] = $this->getSearchForm();
		}
		return parent::render( $data );
	}


	/* PRIVATE ********************************************************************************** */

	private function getSearchForm ()
	{
		$baseUrl   = $this->getSettingsService()->getSettings()->getSITE_BASE_URL();
		$url       = $baseUrl . '/index.cfm?action=wpshortcodebuilder&search_mode=form';
		$resParams = array( 'page', 'action', 'market_guid', 'reinit', 'show_header_footer', 'search_mode' );

		foreach ( $_GET as $param => $paramValue ) {
			if ( !array_search( $param, $resParams ) ) {
				$paramValue = urlencode( $paramValue );
				$url .= "&{$param}={$paramValue}";
			}
		}

		$http    = wp_remote_get( $url, array( 'cookies' => $this->getCookieData() ) );

		if ( !is_wp_error( $http ) && $http['response']['code'] == '200' ) {
			$this->setCookieData( $http['cookies'] );
			return $http['body'];
		}
		else {
			return '';
		}

	}


	private function getCookieData ()
	{
		if ( !array_key_exists( $this->getSessionKey(), $_SESSION ) ) {
			$_SESSION[$this->getSessionKey()] = array();
		}
		return $_SESSION[$this->getSessionKey()];
	}


	private function setCookieData ( array $cookies )
	{
		$_SESSION[$this->getSessionKey()] = $cookies;
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


	/**
	 * GETTER: This method is a getter for the settingsService property.
	 *
	 * @return  string  The absolute URL to this plugin's directory.
	 *
	 */
	public function getSessionKey ()
	{
		return $this->sessionKey;
	}


	/**
	 * SETTER: This method is a setter for the settingsService property.
	 *
	 * @param   string  $url  The absolute URL to this plugin's directory.
	 * @return  void
	 *
	 */
	public function setSessionKey ( $key )
	{
		$this->sessionKey = $key;
	}


}