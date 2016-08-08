<?php

/**
 * @title         Wolfnet_AgentPagesHandler.php
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

class Wolfnet_AgentPagesHandler extends Wolfnet_Plugin
{

    protected $key;
    protected $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function handleRequest()
    {
        global $wp_query;
        $query = $wp_query->query;

        $action = '';

        if(array_key_exists('agentSearch', $_REQUEST)) {
            // All of the logic for searching is in the agentList function since
            // we're just passing more criteria to the API call.
            $action = 'agentList';
        } elseif(array_key_exists('agent', $query) && sizeof(trim($query['agent']) > 0)) {

            // Check if we're requesting the contact form. If not, show agent detail.
            if(preg_match('/\/contact.*/', $query['agent'])) {
                // Requesting the contact form.
                $_REQUEST['contact'] = preg_replace('/\/contact.*/', '', $query['agent']);
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $action = 'contactProcess';
                } else {
                    $action = 'contactForm';
                }
            } else {
                // Show the agent detail.
                $_REQUEST['agentId'] = $query['agent'];
                $action = 'agent';
            }
        } elseif(array_key_exists('office', $query) && sizeof(trim($query['office']) > 0)) {

            // Check if we're requesting the contact form. If not, show list of office's agents.
            if(preg_match('/\/contact.*/', $query['office'])) {
                // Requesting the contact form.
                $_REQUEST['contactOffice'] = preg_replace('/\/contact.*/', '', $query['office']);
                if($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $action = 'contactProcess';
                } else {
                    $action = 'contactFormOffice';
                }
            } else {
                // Office is passed in; list their agents.
                $_REQUEST['officeId'] = $query['office'];
                $action = 'agentList';
            }
        } elseif(!$this->args['showoffices']) {
            // If none of the above match and they don't want to show an office list, show all agents.
            $action = 'agentList';
        } else {
            $action = 'officeList';
        }

        // Run the function associated with the action.
        return $this->$action();
	}


    /*
     *
     * ACTIONS
     *
     */


	protected function officeList()
	{

		// This will be populated if an office search is being performed.
		if (array_key_exists('officeCriteria', $_REQUEST) && strlen($_REQUEST['officeCriteria']) > 0) {
			$this->args['criteria']['name'] = $_REQUEST['officeCriteria'];
		}

		$this->args['criteria']['omit_office_id'] = $this->args['excludeoffices'];

		if (array_key_exists('officeSort', $_REQUEST)) {
			$officeSort = $_REQUEST['officeSort'];
			$this->args['criteria']['sort'] = ($_REQUEST['officeSort'] == 'office_id') ? 'office_id' : 'name';
		} else {
			$officeSort = 'name';
		}

		$officeData = $this->getOfficeData();

		// agentCriteria is set to null if not passed along. Do not change this
		// or it will screw up agent pagination when running a search.
		$args = array(
			'offices' => $officeData,
			'agentCriteria' => (array_key_exists('agentCriteria', $_REQUEST)) ? $_REQUEST['agentCriteria'] : null,
			'officeCriteria' => (array_key_exists('officeCriteria', $_REQUEST)) ? $_REQUEST['officeCriteria'] : null,
			'isAgent' => false,
		);
		$args = array_merge($args, $this->args);
		$args['agentsNav'] = $this->plugin->views->agentsNavView($args);

		return $this->plugin->views->officesListView($args);

	}


    protected function agentList()
    {
        global $wp_query;

        if(array_key_exists("agentpage", $_REQUEST) && $_REQUEST['agentpage'] > 1) {
            /*
             * $startrow needs to be calculated based on the requested page. If $page == 2
             * and numPerPage is 10, for example, we would need to get agents 11 through 20.
             * The below equation will set the starting row accordingly.
             */
            $startrow = $this->args['criteria']['numperpage'] * ($_REQUEST['agentpage'] - 1) + 1;
        } else {
            $startrow = 1;
            $_REQUEST['agentpage'] = 1;
        }

        $this->args['criteria']['omit_office_id'] = $this->args['excludeoffices'];
        if(array_key_exists('agentSort', $_REQUEST)) {
            $agentSort = $_REQUEST['agentSort'];
            $this->args['criteria']['sort'] = ($_REQUEST['agentSort'] == 'name') ? 'name' : 'office_id';
        } else {
            $agentSort = 'name';
        }

        $endpoint = '/agent';
        $separator = "?";
        if(array_key_exists('office', $wp_query->query)) {
            $endpoint .= '?office_name=' . $wp_query->query['office'];
            $separator = "&";
        }
        $endpoint .= $separator . "startrow=" . $startrow;
        $endpoint .= "&maxrows=" . $this->args['criteria']['numperpage'];

        // This will be populated if an agent search is being performed.
        if(array_key_exists('agentCriteria', $_REQUEST) && strlen($_REQUEST['agentCriteria']) > 0) {
            $this->args['criteria']['name'] = $_REQUEST['agentCriteria'];
        }

        try {
            $data = $this->plugin->api->sendRequest($this->key, $endpoint, 'GET', $this->args['criteria']);
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        if(!array_key_exists('officeId', $_REQUEST)) {
            $officeCount = count($this->getOfficeData());
        } else {
            $officeCount = 1;
        }

        $agentsData = array();

        if (is_array($data['responseData']['data']['agent'])) {
            $agentsData = $data['responseData']['data']['agent'];
        }

        $args = array(
            'agents' => $agentsData,
            'totalrows' => $data['responseData']['data']['total_rows'],
            'page' => $_REQUEST['agentpage'],
            'agentSort' => $agentSort,
            'officeId' => (array_key_exists('officeId', $_REQUEST)) ? $_REQUEST['officeId'] : '',
            'officeCount' => $officeCount,
            'agentCriteria' => (array_key_exists('agentCriteria', $_REQUEST)) ? $_REQUEST['agentCriteria'] : '',
            'officeCriteria' => (array_key_exists('officeCriteria', $_REQUEST)) ? $_REQUEST['officeCriteria'] : '',
            'isAgent' => true,
        );
        $args = array_merge($args, $this->args);
        $args['agentsNav'] = $this->plugin->views->agentsNavView($args);

        return $this->plugin->views->agentsListView($args);

    }


    protected function agent()
    {
        global $wp_query;

        $agentData = $this->getAgentById($_REQUEST['agentId']);

        // We need to get a product key that we can pull this agent's listings with.
        // Each key entered into the Settings page has a market name associated with it.
        // We can get the appropriate key for this agent based on their market.
        $this->key = $this->plugin->keyService->getByMarket($agentData['market']);

        if($this->args['criteria']['activelistings']) {
            $featuredListings = $this->agentFeaturedListings($this->key, $agentData['mls_agent_id']);
            $count = $featuredListings['totalRows'];
            $listings = ($count > 0) ? $featuredListings['listings'] : null;
        } else {
            $count = 0;
            $listings = null;
        }

        if($this->args['criteria']['soldlistings'] && $this->plugin->data->soldListingsEnabled()) {
            $soldListings = $this->agentSoldListings($this->key, $agentData['mls_agent_id']);
            $soldCount = $soldListings['totalRows'];
            $soldListings = ($soldCount > 0) ? $soldListings['listings'] : null;
        } else {
            $soldCount = 0;
            $soldListings = null;
        }

        $searchUrl = $this->plugin->data->getBaseUrl($this->key);
        $searchUrl .= "?action=newsearchsession&agent_id=" . $agentData['mls_agent_id'];

        $args = array(
            'agent' => $agentData,
            'officeId' => (array_key_exists('officeId', $_REQUEST)) ? $_REQUEST['officeId'] : '',
            'activeListingCount' => $count,
            'activeListingHTML' => $listings,
            'soldListingCount' => $soldCount,
            'soldListingHTML' => $soldListings,
            'searchUrl' => $searchUrl,
            'soldSearchUrl' => $searchUrl . "&sold=y",
        );
        $args = array_merge($args, $this->args);

        return $this->plugin->views->agentView($args);
    }


    protected function contactForm()
    {
        $agentData = $this->getAgentById($_REQUEST['contact']);

        $args = array(
            'agent' => $agentData,
            'agentId' => $_REQUEST['contact'],
            'officeId' => (array_key_exists('officeId', $_REQUEST)) ? $_REQUEST['officeId'] : '',
        );
        $args = array_merge($args, $this->args);

        return $this->plugin->views->agentContact($args);
    }


    protected function contactFormOffice()
    {
        $officeData = $this->getOfficeByOfficeId($_REQUEST['contactOffice']);

        $args = array(
            'office' => $officeData,
            'officeId' => $_REQUEST['contactOffice'],
        );
        $args = array_merge($args, $this->args);

        return $this->plugin->views->officeContact($args);
    }


    protected function getForm($formType = 'agent') {
        if($formType == 'agent') {
            return $this->contactForm();
        } else {
            return $this->contactFormOffice();
        }
    }


    protected function contactProcess()
    {
        // Get form type.
        $formType = 'agent';
        if(array_key_exists('contactOffice', $_REQUEST)) {
            $formType = 'office';
        }

        // Do basic validation to check if fields are populated before
        // trying to do an API call.
        $_REQUEST['errorField'] = '';

        if($_REQUEST['wolfnet_name'] == '') {
            $_REQUEST['errorField'] = 'wolfnet_name';
        } elseif($_REQUEST['wolfnet_email'] == '') {
            $_REQUEST['errorField'] = 'wolfnet_email';
        }

        if($_REQUEST['errorField'] != '') {
            // Show contact form again.
            return $this->getForm($formType);
        }

        // Translate form fields into request args. Using field name prefixes
        // on form fields since Wordpress has reserved field names.
        $this->args['criteria']['name'] = $_REQUEST['wolfnet_name'];
        $this->args['criteria']['email'] = $_REQUEST['wolfnet_email'];
        $this->args['criteria']['phone'] = $_REQUEST['wolfnet_phone'];
        $this->args['criteria']['contact_by'] = $_REQUEST['wolfnet_contacttype'];
        $this->args['criteria']['message'] = $_REQUEST['wolfnet_comments'];

        // If this is the agent contact page, agent_guid will be passed along, otherwise
        // this was submitted via the office contact and we'll pass office_id.
        if($formType == 'agent') {
            $this->args['criteria']['agent_id'] = $_REQUEST['contact'];
        } else {
            $this->args['criteria']['office_id'] = $_REQUEST['contactOffice'];
        }

        try {
            $data = $this->plugin->api->sendRequest(
                $this->key,
                '/agent_inquire',
                'POST',
                $this->args['criteria']
            );
        } catch (Wolfnet_Exception $e) {
            $data = $e->getData();
            $errorInfo = json_decode($data['body'])->metadata->status->extendedInfo;

            if(strpos($errorInfo, 'email')) {
                $_REQUEST['errorField'] = 'wolfnet_email';
            } elseif(strpos($errorInfo, 'phone')) {
                $_REQUEST['errorField'] = 'wolfnet_phone';
            }

            // Show contact form again.
            return $this->getForm($formType);
        }

        if($data['responseStatusCode'] == 200) {
            $_REQUEST['thanks'] = true;
            return $this->getForm($formType);
        }
    }


    /*
     *
     * HELPER FUNCTIONS
     *
     */


    protected function getOfficeData()
    {
        try {
            $data = $this->plugin->api->sendRequest($this->key, '/office', 'GET', $this->args['criteria']);
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $officeData = array();

        if (is_array($data['responseData']['data']['office'])) {
            $officeData = $data['responseData']['data']['office'];
        }

        return $officeData;
    }


    protected function agentFeaturedListings($key, $agentId)
    {
        return $this->getAgentListings($key, $agentId);
    }


    protected function agentSoldListings($key, $agentId)
    {
        return $this->getAgentListings($key, $agentId, 1);
    }


    protected function getAgentListings($key, $agentId, $sold = 0)
    {
        $criteria = $this->plugin->listingGrid->getDefaults();
        $count = ($sold) ? 6 : 10;
        $criteria['maxrows'] = $count;
        $criteria['maxresults'] = $count;
        $criteria['gridalign'] = 'left';

        // Override the default key with the agent's key
        $criteria['key'] = $key;
        $criteria['keyid'] = $this->plugin->keyService->getIdByKey($key);

        $this->args['criteria'] = array_merge($this->args['criteria'], $criteria);

        $agentListings = $this->getListingsByAgentId($key, $agentId, $sold);

		if (
			($agentListings == null) ||
			!array_key_exists('responseData', $agentListings) ||
			(count($agentListings['responseData']['data']['listing']) == 0)
		) {
			return array('totalRows' => 0, 'listings' => '');
		}

        $this->decodeCriteria($criteria);
        $agentListings['requestData'] = array_merge($agentListings['requestData'], $criteria);

        return array(
            'totalRows' => $agentListings['responseData']['data']['total_rows'],
            'listings' => $this->plugin->listingGrid->listingGrid($criteria, 'grid', $agentListings),
        );
    }


	protected function getListingsByAgentId($key, $agentId, $sold = 0)
	{
		try {

			$data = $this->plugin->api->sendRequest(
				$key,
				'/listing/?agent_id=' . $agentId . "&sold=" . $sold,
				'GET',
				$this->args['criteria']
			);

		} catch (Wolfnet_Exception $e) {

			$data = $e->getData();
			$errorCode = '';

			if (array_key_exists('body', $data)) {
				$responseBody = json_decode($data['body']);
				if (is_object($responseBody)) {
					$errorCode = $responseBody->metadata->status->errorCode;
				}
			}

			if ($errorCode == 'Auth1004' || $errorCode == 'Auth1001') {
				$data = null;
			} else {
				$this->plugin->displayException($e);
			}

		}

		return $data;

	}


    protected function getAgentById($agentId)
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->key,
                '/agent/' . $agentId,
                'GET',
                $this->args['criteria']
            );
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $agentData = array();
        if(is_array($data['responseData']['data'])) {
            $agentData = $data['responseData']['data'];
        }

        return $agentData;
    }


    protected function getOfficeByOfficeId($officeId)
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->key,
                '/office?office_id=' . $officeId,
                'GET',
                $this->args['criteria']
            );
        } catch(Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $officeData = array();
        if(is_array($data['responseData']['data'])) {
            $matches = count($data['responseData']['data']['office']);
            // Due to possible data duplication, get the last result... There
            // will only be one office returned for most implementations.
            $officeData = $data['responseData']['data']['office'][$matches-1];
        }

        return $officeData;
    }


    public function setKey(&$key)
    {
        $this->key = $key;
    }

    public function setArgs(&$args)
    {
        $this->args = $args;
    }
}
