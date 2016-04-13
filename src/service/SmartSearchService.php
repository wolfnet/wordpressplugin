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
class Wolfnet_Service_SmartSearchService
{

	protected $key;
	protected $url;
	protected $smartSearchFields;
    protected $localeLabels;

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
		$this->setSearchFields();
        $this->setLocaleLabels();
	}

	/* PUBLIC METHODS *************************************************************************** */
	/**
	 * Aggregates data required for a parameter for SmartSearch plugin.
	 * (parameter: smartSearchFields)
	 * Data built from consumed json and API request retrieving criteria.
	 *
	 * TODO: Move this to an API endpoint so data can be retrieved from any app.
	 *
	 * @return array  Array of criteria params.
	 *
	 */
	public function getFields() {

		$fields = $this->getSearchFields();
		$params = $this->getSearchParameters();

		for ($i=0; $i < sizeof($fields); $i++) {
			$field = $fields[$i];
			$fieldParams = $params->$field;
			$fields[$i] = $fieldParams[0];
		}

		return $fields;
	}


	/**
	 * Aggregates data required for a parameter for SmartSearch plugin.
	 * (parameter: smartSearchFieldMap)
	 *
	 * TODO: Move this to an API endpoint so data can be retrieved from any app.
	 *
	 * @return array  Associative array (map).
	 *
	 */
	public function getFieldMap() {

		$map = array();
		$params = $this->getSearchParameters();

        foreach ((array) $params as $paramArray) {
            for ($i=0; $i < sizeof($paramArray); $i++) {
                $map[$paramArray[$i]] = $paramArray[$i];
            }
        }

		return $map;
	}


	public function getPlaceholder() {

        $labelLimit = 2;
        $labelsAdded = 0;

		// TODO: Add "Zip" with logic for Canadian markets to be "Postal Code"
		$placeholder = 'Search by City, Address, ';
        $searchTypes = $this->getLocaleLabels();

        foreach($searchTypes as &$searchType)
        {
            if ($labelsAdded < $labelLimit)
            {
                $placeholder .= $searchType['label'] . ', ';
                $labelsAdded++;
            }
        }

        $placeholder .= '& more!';

		return $placeholder;

	}


	/* PRIVATE METHODS ************************************************************************** */
	private function getSmartSearchCriteria()
	{

		try {

			// Retrieve available smart search criteria
			$data = $GLOBALS['wolfnet']->api->sendRequest(
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


    private function getLabels()
    {

        try {

            // Retrive customer-facing labels
            $data = $GLOBALS['wolfnet']->api->sendRequest(
                $this->getKey(),
                '/search_criteria/locale',
                'GET'
            );

            $labels = $data['responseData']['data'];

        } catch (Wolfnet_Exception $e) {
            // Return empty array on error
            $labels = array();
        }

        return $labels;
    }


	private function getSearchParameters()
	{
        $url = $this->getUrl().'SearchParams.json';
        return json_decode(wp_remote_fopen($url));
	}


	private function getSearchFields()
	{
		return $this->smartSearchFields;
	}

	private function setSearchFields()
	{
		$this->smartSearchFields = $this->getSmartSearchCriteria();
	}


    private function getLocaleLabels()
    {
        return $this->localeLabels;
    }

    private function setLocaleLabels()
    {
        $this->localeLabels = $this->getLabels();
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
