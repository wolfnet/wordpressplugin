<?php

/**
 * This class is the searchService and is a Facade used to interact with all other search information.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    search
 * @title         service.php
 * @extends       com_ajmichels_wppf_abstract_service
 * @implements    com_ajmichels_wppf_interface_iService
 * @singleton     True
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_search_service
extends com_ajmichels_wppf_abstract_service
implements com_ajmichels_wppf_interface_iService
{


	/* SINGLETON ENFORCEMENT ******************************************************************** */

	/**
	 * This private static property holds the singleton instance of this class.
	 *
	 * @type  com_wolfnet_wordpress_listing_service
	 *
	 */
	private static $instance;


	/**
	 * This static method is used to retrieve the singleton instance of this class.
	 *
	 * @return  com_wolfnet_wordpress_listing_service
	 *
	 */
	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/* CONSTANTS ******************************************************************************** */

	const WOLFNET_SEARCH_POST = 'wolfnet_search';


	const DEFAULT_SESSION_KEY = 'wolfnetSearchManagerCookies';


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a reference to the SettingsService object.
	 *
	 * @type  com_wolfnet_wordpress_settings_service
	 *
	 */
	private $settingsService;


	/* CONSTRUCTOR ****************************************************************************** */

	/**
	 * This constructor method is private becuase this class is a singleton and can only be retrieved
	 * by statically calling the getInstance method.
	 *
	 * @return  void
	 *
	 */
	private function __construct ()
	{
		$this->setSessionKey( self::DEFAULT_SESSION_KEY );
	}


	/* PUBLIC METHODS *************************************************************************** */


	public function getSearches ()
	{
		$dataArgs = array(
			'numberposts' => -1,
			'post_type'   => self::WOLFNET_SEARCH_POST
		);

		$posts = get_posts( $dataArgs );

		foreach ( $posts as $post ) {

			$customFields = get_post_custom( $post->ID );

			foreach ( $customFields as $field => $value ) {

				if ( substr( $field, 0, 1 ) != '_' ) {
					$post->data[$field] = $value[0];
				}

			}

		}

		return $posts;
	}


	public function getSearchCriteria ( $id = 0 )
	{
		$data = array();

		$customFields = get_post_custom( $id );

		if ( $customFields !== false ) {

			foreach ( $customFields as $field => $value ) {

				if ( substr( $field, 0, 1 ) != '_' ) {
					$data[$field] = $value[0];
				}

			}

		}

		return $data;

	}


	public function saveSearch ( $title, $criteria )
	{
		// Create post object
		$my_post = array(
			 'post_title'  => $title,
			 'post_status' => 'publish',
			 'post_author' => wp_get_current_user()->ID,
			 'post_type'   => self::WOLFNET_SEARCH_POST
		);

		// Insert the post into the database
		$post_id = wp_insert_post( $my_post );

		foreach ( $criteria as $field => $value ) {

			add_post_meta( $post_id, $field, $value, true );

		}
	}


	public function deleteSearch ( $id )
	{
		wp_delete_post( $id, true );
	}


	public function getSearchManagerHtml ()
	{
		$baseUrl   = $this->getSettingsService()->getSettings()->getSITE_BASE_URL();
		$url       = $baseUrl . '/index.cfm?action=wpshortcodebuilder&search_mode=form'
		           . '&cfid=' . $this->getCfId()
		           . '&cftoken=' . $this->getCfToken()
		           . '&jsessionid=' . $this->getJSessionId();
		$resParams = array( 'page', 'action', 'market_guid', 'reinit', 'show_header_footer', 'search_mode' );

		$this->log( $url );

		foreach ( $_GET as $param => $paramValue ) {
			if ( !array_search( $param, $resParams ) ) {
				$paramValue = urlencode( $paramValue );
				$url .= "&{$param}={$paramValue}";
			}
		}

		$http = wp_remote_get( $url, array( 'cookies' => $this->getCookieData() ) );

		//$this->log( $http );

		if ( !is_wp_error( $http ) && $http['response']['code'] == '200' ) {
			$this->setCookieData( $http['cookies'] );
			return $http['body'];
		}
		else {
			return '';
		}

	}


	public function getCfId ()
	{
		return $this->getCookieValue( 'CFID' );
	}


	public function getCfToken ()
	{
		return $this->getCookieValue( 'CFTOKEN' );
	}


	public function getJSessionId ()
	{
		return $this->getCookieValue( 'JSESSIONID' );
	}


	/* PRIVATE METHODS ************************************************************************** */

	private function getCookieData ()
	{
		if ( !array_key_exists( $this->getSessionKey(), $_SESSION ) ) {
			$_SESSION[$this->getSessionKey()] = array();
		}
		return $_SESSION[$this->getSessionKey()];
	}


	private function setCookieData ( array $cookies )
	{
		if ( count( $cookies ) != 0 ) {
			$_SESSION[$this->getSessionKey()] = $cookies;
			$writeCookies = false;
			foreach ( $cookies as $cookie ) {
				if ( !array_key_exists( $cookie->name, $_COOKIE ) ) {
					$writeCookies = true;
				}
			}
			if ( $writeCookies ) {
				$url = get_bloginfo( 'url' ) . '/?pagename=wolfnet-admin-searchmanager-create-cookies';
				$response = wp_remote_get( $url, array( 'cookies' => $this->getCookieData() ) );
			}
		}
	}


	private function getCookieValue ( $name )
	{
		$cookies = $this->getCookieData();
		foreach ( $cookies as $cookie ) {
			if ( $cookie->name == $name ) {
				return $cookie->value;
			}
		}
		return '';
	}


	/* ACCESSOR METHODS ************************************************************************* */

	/**
	 * GETTER: This method is a getter for the settingsService property.
	 *
	 * @return  com_wolfnet_wordpress_settings_service
	 *
	 */
	public function getSettingsService ()
	{
		return $this->settingsService;
	}


	/**
	 * SETTER: This method is a setter for the settingsService property.
	 *
	 * @param   com_wolfnet_wordpress_settings_service  $service
	 * @return  void
	 *
	 */
	public function setSettingsService ( com_wolfnet_wordpress_settings_service $service )
	{
		$this->settingsService = $service;
	}


	/**
	 * GETTER: This method is a getter for the sessionKey property.
	 *
	 * @return  string
	 *
	 */
	public function getSessionKey ()
	{
		return $this->sessionKey;
	}


	/**
	 * SETTER: This method is a setter for the sessionKey property.
	 *
	 * @param   string  $key
	 * @return  void
	 *
	 */
	public function setSessionKey ( $key )
	{
		$this->sessionKey = $key;
	}


}