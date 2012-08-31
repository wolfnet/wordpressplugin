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


}