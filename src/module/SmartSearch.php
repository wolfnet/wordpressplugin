<?php

/**
 * WolfNet SmartSearch module
 *
 * This module represents the Smart Search and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_SmartSearch
{
    /**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }


    public function scSmartSearch($attrs, $content = '')
    {

        try {

            $defaultAttributes = $this->getDefaults();

            $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());
			$criteria['isCanada'] = $this->plugin->data->isCanada();
            $this->plugin->decodeCriteria($criteria);

            $out = $this->smartSearch($criteria);

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function getDefaults()
    {
        return array(
            'title' => '',
        );
    }


	/**
	 * Get markup for Smart Search form
	 * @param  array  $criteria
	 * @return string form markup
	 */
	public function smartSearch(array $criteria)
	{

		$markets = array();
		$productKey = $this->plugin->keyService->getDefault();

		// Multi market logic
		if (isset($criteria['keyids'])) {

			$keyIds = explode(',',$criteria['keyids']);
			$keys = json_decode($this->plugin->keyService->get());

			// Get market datasource
			for ($i=0; $i<count($keys); $i++) {
				$key = $keys[$i]->key;

				try {

					$market = $GLOBALS['wolfnet']->data->getMarketName($key);

					if (!is_wp_error($market)) {
						$keys[$i]->market = strtoupper($market);
					}

				} catch (Exception $e) {
					$market = '';
				}

			}


			//Loop keyids and build array of market datasource in multi-market search scenarios
			foreach ($keyIds as $id) {

				$thisKey = $this->plugin->keyService->getById($id);

				foreach ($keys as $key) {
					if ($thisKey == $key->key) {

						// Get site base url for smart search form submit
						$siteUrlResponse = $this->plugin->api->sendRequest(
							$thisKey,
							'/settings/site_base_url',
							'GET',
							array('key'=>$thisKey)
						);

						// Build site/market metadata array to be passed in paramaterized json
						$marketArray = array(
							'market_label'    => $key->label,
							'datasource_name' => strtolower($key->market),
							'product_key'     => $thisKey,
							'site_url'        => $siteUrlResponse['responseData']['data']['SITE_BASE_URL']
						);

						array_push($markets,$marketArray);
					}
				}
			}

			$multiMarket = true;

		} else {

			$multiMarket = false;
		}

		if (is_wp_error($productKey)) {
			return $this->plugin->getWpError($productKey);
		}

		// Get data
		$prices = $this->plugin->data->getPrices($productKey);
		$beds = $this->plugin->data->getBeds();
		$baths = $this->plugin->data->getBaths();
		$formAction = $this->plugin->data->getBaseUrl($productKey);

		if (is_wp_error($prices)) {
			return $this->plugin->getWpError($prices);
		}

		if (is_wp_error($beds)) {
			return $this->plugin->getWpError($beds);
		}

		if (is_wp_error($baths)) {
			return $this->plugin->getWpError($baths);
		}

		if (is_wp_error($formAction)) {
			return $this->plugin->getWpError($formAction);
		}

		$vars = array(
			'instance_id'  => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
			'siteUrl'      => site_url(),
			'markets'      => $markets,
			'multiMarket'  => $multiMarket,
			'prices'       => $prices,
			'beds'         => $beds,
			'baths'        => $baths,
			'formAction'   => $formAction,
		);

		$args = $this->plugin->convertDataType(array_merge($criteria, $vars));

		// Instantiate SmartSearch Service
		$smartSearchService = $this->plugin->ioc->get(
			'Wolfnet_Service_SmartSearchService',
			array(
				'key' => $this->plugin->keyService->getDefault(),
				'url' => $this->plugin->url
			)
		);

		$args['smartSearchFields'] = json_encode($smartSearchService->getFields());
		$args['smartSearchFieldMap'] = json_encode($smartSearchService->getFieldMap());
		$args['smartSearchPlaceholder'] = $smartSearchService->getPlaceholder($criteria['isCanada']);
		$args['componentId'] = uniqid('-');

		return $this->plugin->views->smartSearchView($args);
	}


    /**
     * Get search suggestion list from API.
     * @param  string $term
     * @return json array $suggestionsObject
     */
    public function getSuggestions($term,$marketList)
    {
        try {

            $key = $this->plugin->keyService->getDefault();
            $marketList = str_replace("\\",'',$marketList);

            $response = $this->plugin->api->sendRequest(
                $key,
                '/search_criteria/suggestion',
                'GET',
                array('term'=>$term,'market_list'=>$marketList)
            );

            $suggestionsObject = array();
            $suggestions = $response['responseData']['data']['suggestions'];

            foreach ($suggestions as $suggestion) {
                $suggestionsObject[] = $suggestion;
            }

        } catch (Wolfnet_Exception $e) {
            throw new Exception($this->displayException($e));
        }

        return $suggestionsObject;
    }


}
?>
