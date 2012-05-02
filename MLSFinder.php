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
require_once( dirname(__FILE__) . str_replace( '\\', DIRECTORY_SEPARATOR, '\com\ajmichels\common\autoLoader.php' ) );
com_ajmichels_common_autoLoader::getInstance( dirname(__FILE__) );

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
implements com_ajmichels_common_iSingleton
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
		$this->log( 'Init MLSFinder Plugin' );
		parent::__construct();
		
		$this->setPluginPath( __FILE__ );
		
		/*	If the code is running either locally or on test server and the debug parameter is passed
			over the url, output the log. */
		if ( array_key_exists( 'debug', $_REQUEST ) ) {
			$this->loggerSetting( 'enabled', true );
		}
		
		$wsUrl = $this->getPluginUrl() . 'testFeed.xml';
		
		$sfXml = __DIR__ . DIRECTORY_SEPARATOR . 'phpSpring.xml';
		$sfProps = array( 'pluginUrl'=>$this->getPluginUrl(), 'webServiceDomain'=>$wsUrl );
		$this->sf = new com_ajmichels_phpSpring_bean_factory_default( $sfXml, array(), $sfProps );
		$this->sf->setParent( $this->wppf_serviceFactory );
		
		/* Notify the bootstrap that we are ready to initialize the plugin. */
		parent::initPlugin();
		
	}
	
	
	/* Runs when the page output is complete and PHP script execution is about the end. */
	public function shutdown ()
	{
		if ( $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '172.28.0.206' ) {
			echo '<!-- Testing Server: ' . $_SERVER['SERVER_ADDR'] . ' -->';
		}
	}
	
	
	/* Register Actions with the Action Manager */
	protected function actions ()
	{
		$this->am->register( $this->sf->getBean( 'EnqueueResources' ), array( 'init' ) );
		$this->am->register( $this->sf->getBean( 'CreateAdminPages' ), array( 'admin_menu' ) );
		$this->am->register( $this->sf->getBean( 'RegisterWidgets' ),  array( 'widgets_init' ) );
		$this->am->register( $this->sf->getBean( 'EnqueueAdminResources' ), array( 'admin_init' ) );
	}
	
	
}


// INSTANTIATE PLUGIN ***********************
$MLSFinder = MLSFinder::getInstance();
