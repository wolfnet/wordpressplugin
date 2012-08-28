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


	/* PROPERTIES ******************************************************************************* */



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
	}


	/* PUBLIC METHODS *************************************************************************** */

	public function getSearchBuilder ()
	{

	}


	/* PRIVATE METHODS ************************************************************************** */



	/* ACCESSOR METHODS ************************************************************************* */



}