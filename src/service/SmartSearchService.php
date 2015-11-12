<?php

/**
 * WolfNet SmartSearch Service
 *
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Smart_SearchService
{

	protected $key;

	/* CONSTRUCTOR ****************************************************************************** */
	/**
	 * This simple constructor method sets up the object.
	 *
	 * @param  string  $key  API Key to be used in API calls from this service.
	 *
	 */
	public function __construct($key)
	{
		$this->setKey($key);
	}

	/* PUBLIC METHODS *************************************************************************** */
	public function getFields() {

		/* TODO
		See UI code beginning at:  SearchService.getSmartSearchFields
			|--- SearchService.getSmartSearchFieldNames
				|--- SettingsApiDao.getSmartSearchCriteria (array)

			var smartSearchData = performHttpRequest(path="/search_criteria/smart_search", cache=true);
			var smartSearchCriteria = smartSearchData.data;
		 */

		$fields = array();

		try {
			// Retrieve available smart search criteria
			$data = $GLOBALS['wolfnet']->apin->sendRequest(
				$this->getKey(),
				'/search_criteria/smart_search',
				'GET'
			);
		} catch (Wolfnet_Api_ApiException $e) {
			//TODO Handle error appropriately
		}

		return $fields;
	}

	public function getFieldMap() {
		$fieldMap = array();

		return $fieldMap;
	}


	/* PRIVATE METHODS ************************************************************************** */
	private function setKey(&$key)
	{
		$this->key = $key;
	}

	private function getKey()
	{
		return $this->key;
	}


}
