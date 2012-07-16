<?php

/* *********************************************************************************************** /

Plugin Name:  WolfNet IDX for WordPress
Plugin URI:   http://wordpress.wolfnet.com
Description:  This plugin provides WordPress integration with mlsfinder.com IDX search solutions.
Version:      {X.X.X}
Author:       WolfNet Technologies, LLC.
Author URI:   http://www.wolfnet.com

/ *********************************************************************************************** */

/* Include and Initialize Class Autoloader */
$autoLoaderPath = '\com\ajmichels\common\autoLoader.php';
require_once( dirname(__FILE__) . str_replace( '\\', DIRECTORY_SEPARATOR, $autoLoaderPath ) );
com_ajmichels_common_autoLoader::getInstance( dirname(__FILE__) );

/**
 * 
 * @title         wolfnet.php
 * @contributors  AJ Michels (http://aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
class wolfnet
extends com_ajmichels_wppf_bootstrap
implements com_ajmichels_common_iSingleton
{
	
	
	/* SINGLETON ENFORCEMENT ******************************************************************** */
	
	private static $instance;
	
	public static function getInstance ()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	/* PROPERTIES ******************************************************************************* */
	
	public $majorVersion = {majorVersion};
	public $minorVersion = {minorVersion};
	public $version      = '{X.X.X}';
	
	
	/* CONSTRUCT PLUGIN ************************************************************************* */
	
	public function __construct ()
	{
		$this->log( 'Init wolfnet Plugin' );
		parent::__construct();
		
		$this->setPluginPath( __FILE__ );
		
		/*	If the debug parameter is passed over the url, output the log. */
		if ( array_key_exists( 'debug', $_REQUEST ) ) {
			$this->loggerSetting( 'enabled', true );
			$this->loggerSetting( 'level',   'debug' );
			$this->loggerSetting( 'minTime', 0 );
		}
		
		/* Create Plugin Service Factory */
		$sfXml = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'com/wolfnet/wordpress/phpSpring.xml';
		$sfProps = array( 
					'pluginUrl'          => $this->getPluginUrl(),
					'webServiceDomain'   => 'http://aj.cfdevel.wnt/com/mlsfinder/services/index.cfm',
					'pluginMajorVersion' => $this->majorVersion,
					'pluginMinorVersion' => $this->minorVersion,
					'pluginVersion'      => $this->version
					);
		$this->sf = new com_ajmichels_phpSpring_bean_factory_default( $sfXml, array(), $sfProps );
		$this->sf->setParent( $this->wppf_serviceFactory );
		
		$defaultUrl = $this->sf->getBean( 'DefaultWebServiceUrl' );
		$defaultUrl->setParameter( 'pluginVersion', $this->version );
		
		/* Notify the bootstrap that we are ready to initialize the plugin. */
		parent::initPlugin();
		
	}
	
	
	/* PLUGIN LIFE-CYCLE HOOKS ****************************************************************** */
	
	/* Runs when the page output is complete and PHP script execution is about to end. */
	public function shutdown ()
	{
		if ( $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '172.28.0.206' ) {
			echo '<!-- Testing Server: ' . $_SERVER['SERVER_ADDR'] . ' -->';
		}
	}
	
	
	/* MANAGER REGISTRATIONS ******************************************************************** */
	
	/* Register Options with the Option Manager */
	protected function options ()
	{
		$this->os->setGroupName( 'wolfnet' );
		$this->os->register( 'wolfnet_productKey' );
	}
	
	
	/* Register Actions with the Action Manager */
	protected function actions ()
	{
		$this->am->register( $this->sf->getBean( 'EnqueueResources' ),      array( 'wp_enqueue_scripts' ) );
		$this->am->register( $this->sf->getBean( 'CreateAdminPages' ),      array( 'admin_menu' ) );
		$this->am->register( $this->sf->getBean( 'RegisterWidgets' ),       array( 'widgets_init' ) );
		$this->am->register( $this->sf->getBean( 'EnqueueAdminResources' ), array( 'admin_enqueue_scripts' ) );
	}
	
	
	/* Register Shortcodes with the Shortcode Manager */
	protected function shortcodes ()
	{
		$this->sm->register( $this->sf->getBean( 'ListingQuickSearchShortcode' ) );
		$this->sm->register( $this->sf->getBean( 'FeaturedListingsShortcode' ) );
		$this->sm->register( $this->sf->getBean( 'ListingGridShortcode' ) );
	}
	
	
}


/* INSTANTIATE PLUGIN *************************************************************************** */

$wolfnet = wolfnet::getInstance();
