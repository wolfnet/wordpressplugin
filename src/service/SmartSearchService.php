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
	protected $url;

	/* CONSTRUCTOR ****************************************************************************** */
	/**
	 * This constructor method sets up the object with necessary data.
	 *
	 * @param  string  $key - API Key to be used in API calls from this service.
	 * @param  string  $url - The path for public wolfnet plugin resources.
	 *
	 */
	public function __construct($key,$url)
	{
		$this->setKey($key);
		$this->setUrl($url);
	}

	/* PUBLIC METHODS *************************************************************************** */
	/**
     * Aggregates data required for SmartSearch plugin.
     * Data built from consumed json and API request retrieving criteria.
     *
     * @return array  Array of fields.
     *
     */
	public function getFields() {

		$fields = $this->getSmartSearchCriteria();
		$params = $this->getSearchParameters();

		$l = sizeof($fields);

		for ($i=0; $i < $l; $i++) {
			$field = $fields[$i];
			$fieldParams = $params->$field;
			$fields[$i] = $fieldParams[0];
		}

		return $fields;
	}

	public function getFieldMap() {
		$fieldMap = array();

		return $fieldMap;
	}


	/* PRIVATE METHODS ************************************************************************** */
	private function getSmartSearchCriteria()
	{

		try {

			// Retrieve available smart search criteria
			$data = $GLOBALS['wolfnet']->apin->sendRequest(
				$this->getKey(),
				'/search_criteria/smart_search',
				'GET'
			);

			$criteria = $data['responseData']['data'];

		} catch (Wolfnet_Exception $e) {

			// Return empty array on error
			$criteria = array();

		}

		return $criteria;
	}


	private function getSearchParameters()
	{
		return json_decode(file_get_contents($this->getUrl().'SearchParams.json'));
	}


	private function setKey(&$key)
	{
		$this->key = $key;
	}


	private function getKey()
	{
		return $this->key;
	}


	private function setUrl(&$url)
	{
		$this->url = $url;
	}


	private function getUrl()
	{
		return $this->url;
	}

}
