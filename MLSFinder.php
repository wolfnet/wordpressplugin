<?php

/* **************************************** /

Plugin Name:	MLSFinder
Plugin URI:		http://www.mlsfinder.com/wordpress
Description:	This plugin provides WordPress integration with MLSFinder.com.
Author:			WolfNet Technologies
Version:		{X.X.X}
Author URI:		http://wolfnet.com

/ ***************************************** */

/* Include and Initialize Class Autoloader */
require_once( dirname(__FILE__) . str_replace( '\\', DIRECTORY_SEPARATOR, '\com\ajmichels\wppf\autoLoader.php' ) );
com_ajmichels_wppf_autoLoader::getInstance( dirname(__FILE__) );

/**
 * 
 * @title			MLSFinder.php
 * @contributors 	AJ Michels (http://aj.michels@wolfnet.com)
 * @version 		1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

class MLSFinder
extends com_ajmichels_wppf_bootstrap
implements com_ajmichels_wppf_interface_iSingleton
{
	
	
	private static $instance;
	
	
	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	public	$wsUrl = 'testFeed.xml';
	public	$optionsGroup = 'mlsfinderPluginOptions';
	
	
	// CONSTRUCT PLUGIN *********************
	public function __construct ()
	{
		$this->setPluginPath( __FILE__ );
		parent::__construct();
		$this->log( 'Init MLSFinder Plugin' );
		
		$wsUrlProperties = array( 'Domain'=>$this->pluginUrl . $this->wsUrl );
		$this->sf->set( 'DefaultWebServiceUrl', 'com_ajmichels_wppf_data_webServiceUrl', null, $wsUrlProperties );
		
		/*	If the code is running either locally or on test server and the debug parameter is passed
			over the url, output the log. */
		if ( array_key_exists( 'debug', $_REQUEST )
			&& ( $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '172.28.0.206' )
		) {
			$this->loggerSetting( 'enabled', true );
		}
		
	}
	
	
	/* Runs when the page output is complete and PHP script execution is about the end. */
	public function shutdown ()
	{
		if ( $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '172.28.0.206' ) {
			echo '<!-- Testing Server: ' . $_SERVER['SERVER_ADDR'] . ' -->';
		}
	}
	
	
	/* Define objects in the Service Factory */
	protected function objects ()
	{
		
		/* Define Model Objects */
		$this->sf->set(	'ListingService',	'com_mlsfinder_wordpress_listing_service',	array( 'DAO'=>'ListingDAO' ) );
		$this->sf->set(	'ListingDAO',		'com_mlsfinder_wordpress_listing_dao',		array( 'DataService'=>'DataService', 'EntityPrototype'=>'Listing', 'WebServiceUrl'=>'DefaultWebServiceUrl' ) );
		$this->sf->set(	'Listing',			'com_mlsfinder_wordpress_listing_entity');
		
		/* Define View Objects */
		$this->sf->set(	'PluginSettingsView',			'com_mlsfinder_wordpress_admin_pluginSettingsView' );
		$this->sf->set(	'ListingView',					'com_mlsfinder_wordpress_listing_view' );
		$this->sf->set(	'ListingFilmStripView',			'com_mlsfinder_wordpress_listing_filmStripView' );
		$this->sf->set(	'ListingFilmStripOptionsView',	'com_mlsfinder_wordpress_listing_filmStripOptionsView' );
		$this->sf->set(	'QuickSearchView',				'com_mlsfinder_wordpress_listing_quickSearchView' );
		
		/* Define Action Objects */
		$this->sf->set(	'EnqueueResources',			'com_mlsfinder_wordpress_action_enqueueResources', null, array( 'pluginUrl'=>$this->pluginUrl ) );
		$this->sf->set(	'CreateAdminPages',			'com_mlsfinder_wordpress_action_createAdminPages' );
		$this->sf->set(	'RegisterWidgets',			'com_mlsfinder_wordpress_action_registerWidgets' );
		$this->sf->set(	'EnqueueAdminResources',	'com_mlsfinder_wordpress_action_enqueueAdminResources', null, array( 'pluginUrl'=>$this->pluginUrl ) );
		
	}
	
	
	/* Register Actions with the Action Manager */
	protected function actions ()
	{
		$this->am->register( $this->sf->get( 'EnqueueResources' ), array( 'init' ) );
		$this->am->register( $this->sf->get( 'CreateAdminPages' ), array( 'admin_menu' ) );
		$this->am->register( $this->sf->get( 'RegisterWidgets' ),  array( 'widgets_init' ) );
		$this->am->register( $this->sf->get( 'EnqueueAdminResources' ), array( 'admin_init' ) );
	}
	
	
}


// INSTANTIATE PLUGIN ***********************
$MLSFinder = MLSFinder::getInstance();
