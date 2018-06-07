<?php

/**
 * WolfNet SearchList
 *
 * This class lists saved searched in a table
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 *
 */

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Wolfnet_SearchList extends WP_List_Table {

	/**
	* This property holds the current instance of the Wolfnet_Plugin.
	* @var Wolfnet_Plugin
	*/
	protected $plugin = null;


	public function __construct ($plugin) {
		$this->plugin = $plugin;

		parent::__construct([
			'singular' => __('Saved Search', 'sp'),
			'plural'   => __('Saved Searches', 'sp'),
			'ajax'     => false
		]);

	}


	/**
	 * Retrieve searches
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_searches ($per_page=5, $page_number=1) {

		$result = $GLOBALS['wolfnet']->searchManager->getSavedSearchesArray();

		return $result;

	}


	/**
	 * Delete a search
	 *
	 * @param int $id search ID
	 */
	public static function delete_search ($id) {

	}


	/** Text displayed when no searches are found */
	public function no_items () {
		_e('No saved searches avaliable.', 'sp');
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name ($item) {

		// create a nonce
		$delete_nonce = wp_create_nonce( 'sp_delete_search' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'delete' => sprintf('<a href="?page=%s&action=%s&post=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint($item['ID']), $delete_nonce)
		];

		return $title . $this->row_actions($actions);
	}

}
