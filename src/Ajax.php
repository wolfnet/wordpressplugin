<?php

/**
 * @title         Wolfnet_Ajax.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

class Wolfnet_Ajax
{
	/*
	 *
	 * Registration functions
	 *
	 */
	public function registerAdminAjaxActions()
	{
		$ajaxActions = array(
			'wolfnet_validate_key'            => 'remoteValidateProductKey',
			'wolfnet_saved_searches'          => 'remoteGetSavedSearches',
			'wolfnet_save_search'             => 'remoteSaveSearch',
			'wolfnet_delete_search'           => 'remoteDeleteSearch',
			'wolfnet_scb_options_agent'       => 'remoteShortcodeBuilderOptionsAgent',
			'wolfnet_scb_options_featured'    => 'remoteShortcodeBuilderOptionsFeatured',
			'wolfnet_scb_options_grid'        => 'remoteShortcodeBuilderOptionsGrid',
			'wolfnet_scb_options_list'        => 'remoteShortcodeBuilderOptionsList',
			'wolfnet_scb_options_quicksearch' => 'remoteShortcodeBuilderOptionsQuickSearch',
			'wolfnet_scb_savedsearch'         => 'remoteShortcodeBuilderSavedSearch',
			'wolfnet_scb_showagentfeature'    => 'remoteShortcodeBuilderShowAgentFeature',
			'wolfnet_content'                 => 'remoteContent',
			'wolfnet_content_header'          => 'remoteContentHeader',
			'wolfnet_content_footer'          => 'remoteContentFooter',
			'wolfnet_listings'                => 'remoteListings',
			'wolfnet_get_listings'            => 'remoteListingsGet',
			'wolfnet_listing_photos'          => 'remoteListingPhotos',
			'wolfnet_css'                     => 'remotePublicCss',
			'wolfnet_market_name'             => 'remoteGetMarketName',
			'wolfnet_map_enabled'             => 'remoteMapEnabled',
			'wolfnet_price_range'             => 'remotePriceRange',
			'wolfnet_base_url'                => 'remoteGetBaseUrl',
			'wolfnet_route_quicksearch'       => 'remoteRouteQuickSearch',
			'wolfnet_search_manager_ajax'     => 'remoteAjaxRelay',
			'wolfnet_smart_search'            => 'remoteGetSuggestions',
		);

		foreach ($ajaxActions as $action => $method) {
			$GLOBALS['wolfnet']->addAction('wp_ajax_' . $action, array(&$this, $method));
		}

	}

	public function registerAjaxActions()
	{
		$ajaxActions = array(
			'wolfnet_content'              => 'remoteContent',
			'wolfnet_content_header'       => 'remoteContentHeader',
			'wolfnet_content_footer'       => 'remoteContentFooter',
			'wolfnet_listings'             => 'remoteListings',
			'wolfnet_get_listings'         => 'remoteListingsGet',
			'wolfnet_listing_photos'       => 'remoteListingPhotos',
			'wolfnet_css'                  => 'remotePublicCss',
			'wolfnet_base_url'             => 'remoteGetBaseUrl',
			'wolfnet_price_range'          => 'remotePriceRange',
			'wolfnet_route_quicksearch'    => 'remoteRouteQuickSearch',
			'wolfnet_search_manager_ajax'  => 'remoteAjaxRelay',
			'wolfnet_smart_search'         => 'remoteGetSuggestions',
		);

		foreach ($ajaxActions as $action => $method) {
			$GLOBALS['wolfnet']->addAction('wp_ajax_nopriv_' . $action, array(&$this, $method));
		}

	}


	/*
	 *
	 * Remote functions
	 *
	 */
	public function remoteValidateProductKey()
    {
        $productKey = (array_key_exists('key', $_REQUEST)) ? sanitize_key($_REQUEST['key']) : '';

        try {
            $response = ($GLOBALS['wolfnet']->keyService->isValid($productKey)) ? 'true' : 'false';

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteGetSavedSearches($keyid = null)
    {

        try {
            if ($keyid == null) {
                $keyid = (array_key_exists('keyid', $_REQUEST)) ? sanitize_key($_REQUEST['keyid']) : '1';
            }

            $response = $GLOBALS['wolfnet']->searchManager->getSavedSearches(-1, $keyid);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteSaveSearch()
    {

        try {
            if (array_key_exists('post_title', $_REQUEST)) {
                // Create post object
                $my_post = array(
                    'post_title'  => sanitize_title($_REQUEST['post_title']),
                    'post_status' => 'publish',
                    'post_author' => wp_get_current_user()->ID,
                    'post_type'   => $GLOBALS['wolfnet']->customPostTypeSearch
                    );

                // Insert the post into the database
                $post_id = wp_insert_post($my_post);

                foreach ($_REQUEST['custom_fields'] as $field => $value) {
                    add_post_meta($post_id, sanitize_text_field($field), sanitize_text_field($value), true);
                }

                $key = sanitize_key($_REQUEST['custom_fields']['keyid']);

            }

            $this->remoteGetSavedSearches($key);

            $response = null;

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteDeleteSearch()
    {

        try {
            if (array_key_exists('id', $_REQUEST)) {
                wp_delete_post($_REQUEST['id'], true);
            }

            $this->remoteGetSavedSearches();
            $response = null;

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteShortcodeBuilderOptionsAgent()
    {

        try {
            $args = $GLOBALS['wolfnet']->agentPages->getOptions();
            $args['showSoldOption'] = $GLOBALS['wolfnet']->data->soldListingsEnabled();

            $response = $GLOBALS['wolfnet']->views->agentPagesOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsFeatured()
    {

        try {
            $args = $GLOBALS['wolfnet']->featuredListings->getOptions();

            $response = $GLOBALS['wolfnet']->views->featuredListingsOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsGrid()
    {

        try {
            $args = $GLOBALS['wolfnet']->listingGrid->getOptions();

            $response = $GLOBALS['wolfnet']->views->listingGridOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsList()
    {

        try {
            $args = $GLOBALS['wolfnet']->propertyList->getOptions();

            $response = $GLOBALS['wolfnet']->views->listingGridOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderOptionsQuickSearch()
    {

        try {
            $args = $GLOBALS['wolfnet']->quickSearch->getOptions();

            $response = $GLOBALS['wolfnet']->views->quickSearchOptionsFormView($args);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteShortcodeBuilderSavedSearch()
    {

        try {
            $id = (array_key_exists('id', $_REQUEST)) ? sanitize_text_field($_REQUEST['id']) : 0;

            $response = $GLOBALS['wolfnet']->searchManager->getSavedSearch($id);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteShortcodeBuilderShowAgentFeature()
    {
        try {
            $response = $GLOBALS['wolfnet']->agentPages->showAgentFeature();
        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );
        }

        wp_send_json($response);
    }


    public function remoteContent()
    {

        try {
            $response = $GLOBALS['wolfnet']->template->getWpHeader()
            . $GLOBALS['wolfnet']->template->getWpFooter();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteContentHeader()
    {

        try {
            $response = $GLOBALS['wolfnet']->template->getWpHeader();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteContentFooter()
    {

        try {
            $GLOBALS['wolfnet']->template->getWpHeader();

            $response = $GLOBALS['wolfnet']->template->getWpFooter();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteListings()
    {

        try {
            $args = $GLOBALS['wolfnet']->listingGrid->getOptions($_REQUEST);

            $response = $GLOBALS['wolfnet']->template->getWpHeader()
            	. $GLOBALS['wolfnet']->listingGrid->listingGrid($args)
            	. $GLOBALS['wolfnet']->template->getWpFooter();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = $GLOBALS['wolfnet']->displayException($e);

        }

        echo $response;

        die;

    }


    public function remoteListingsGet()
    {

        try {
            $args = $GLOBALS['wolfnet']->listingGrid->getOptions($_REQUEST);

            // used by pagination dropdown "per page"
            if (!empty($_REQUEST['numrows'])) {
                $_REQUEST['maxrows'] = sanitize_text_field($_REQUEST['numrows']);
            }

            $criteria = $GLOBALS['wolfnet']->listings->prepareListingQuery($_REQUEST);

            $keyid = (array_key_exists('keyid', $_REQUEST)) ? sanitize_key($_REQUEST["keyid"]) : null;

            $productKey = $GLOBALS['wolfnet']->keyService->getById($keyid);

            $data = $GLOBALS['wolfnet']->api->sendRequest($productKey, '/listing', 'GET', $criteria);

            $GLOBALS['wolfnet']->listings->augmentListingsData($data, $productKey, array('listing', 'map'));

        } catch (Wolfnet_Exception $e) {
            status_header(500);
            $data = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        // TODO: Do we really need to support AjaxP here?
        $callback = (array_key_exists('callback', $_REQUEST)) ? sanitize_text_field($_REQUEST['callback']) : false;

        if ($callback !== false) {
            header('Content-Type: application/javascript');
            echo $callback . '(' . json_encode($data) . ');';
        } else {
            wp_send_json($data);
        }

        die;

    }


    public function remoteListingPhotos()
    {
        try {

            $propertyId = (array_key_exists('property_id', $_REQUEST)) ? sanitize_text_field($_REQUEST['property_id']) : 0;
            $keyId = (array_key_exists('keyid', $_REQUEST)) ? sanitize_key($_REQUEST['keyid']) : '';

            $response = $GLOBALS['wolfnet']->listings->getPhotos($propertyId, $keyId);

        } catch (Wolfnet_Exception $e) {

            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remotePublicCss()
    {

        try {
            $response = $GLOBALS['wolfnet']->views->getPublicCss();

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            echo $GLOBALS['wolfnet']->displayException($e);
            die;

        }

        header('Content-type: text/css');
        echo $response;

        die;

    }


    public function remotePriceRange()
    {

        try {
            // TODO: Assign default value.
            $keyid = sanitize_key($_REQUEST["keyid"]);

            $productKey = $GLOBALS['wolfnet']->keyService->getById($keyid);

            $response = $GLOBALS['wolfnet']->data->getPrices($productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteGetMarketName()
    {

        try {
            // TODO: Assign default value.
            $productKey = sanitize_key($_REQUEST["productkey"]);

            $marketName = $GLOBALS['wolfnet']->data->getMarketName($productKey);
            $response = strtoupper($marketName);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteMapEnabled()
    {

        try {
            // TODO: Assign default value.
            $keyid = sanitize_key($_REQUEST["keyid"]);

            $productKey = $GLOBALS['wolfnet']->keyService->getById($keyid);

            $response = $GLOBALS['wolfnet']->data->getMaptracksEnabled($productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteGetBaseUrl()
    {

        try {
            // TODO: Assign default value.
            $keyid = sanitize_key($_REQUEST["keyid"]);
            $productKey = $GLOBALS['wolfnet']->keyService->getById($keyid);
            $response = $GLOBALS['wolfnet']->data->getBaseUrl($productKey);

        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);

    }


    public function remoteRouteQuickSearch()
    {
        try {
            $response = $GLOBALS['wolfnet']->quickSearch->routeQuickSearch(sanitize_text_field($_REQUEST['formData']));
        } catch (Wolfnet_Exception $e) {
            status_header(500);

            $response = array(
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            );

        }

        wp_send_json($response);
    }


	public function remoteAjaxRelay()
	{

		try {

			$url       = esc_url_raw($_REQUEST['wnt__url']);
			$reqMethod = $_REQUEST['wnt__method'];
			$params    = $_REQUEST['wnt__params'];
			$dataType  = (array_key_exists('wnt__datatype', $_REQUEST) ? sanitize_text_field($_REQUEST['wnt__datatype']) : '');

			// Relay the request and get the response
			$response = $GLOBALS['wolfnet']->searchManager->searchRelay($url, $requestMethod, $params);

		} catch (Wolfnet_Exception $e) {

			status_header(500);
			$response = var_export(
				array(
					'message' => $e->getMessage(),
					'data' => $e->getData(),
				),
				true
			);

		}

		// For the create_session request (which returns HTML of a full page of 2.5), remove HTML
		if (
			(strpos($params, 'create_session') !== false) &&
			in_array($dataType, array('json', 'script', 'jsonp'))
		) {
			$response = 'true';
		}

		switch ($dataType) {
			case 'json':
				header('Content-Type: text/json');
				break;
			case 'script':
			case 'jsonp':
				header('Content-Type: text/javascript');
				break;
			case 'xml':
				header('Content-Type: application/xml');
				break;
			case 'html':
				header('Content-Type: text/html');
				break;
			case 'text':
				header('Content-Type: text/plain');
				break;
		}

		echo $response;

		die;

	}


	public function remoteGetSuggestions()
	{
		try {

			// Retrieve user's search term from request
			$term = sanitize_text_field($_REQUEST['data']['term']);

			// Make API request to retrieve suggestion data
			$response = $GLOBALS['wolfnet']->smartSearch->getSuggestions($term);

		} catch (Wolfnet_Exception $e) {

			status_header(500);
			$response = array(
				'message' => $e->getMessage(),
				'data' => $e->getData(),
			);

		}

		if(array_key_exists('callback', $_GET)){
			$callback = sanitize_text_field(sanitize_text_field($_REQUEST['callback']));

			header('Content-Type: text/javascript; charset=utf8');
			header('Access-Control-Allow-Origin: http://www.example.com/');
			header('Access-Control-Max-Age: 3628800');
			header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

			echo $callback.'('.json_encode($response).')';
			die;

		} else {
			wp_send_json($response);
		}

	}

}

?>
