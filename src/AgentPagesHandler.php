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
    protected $regex = array(
        'contact' => '/\/contact.*/',
    );

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        if(!session_id()) {
            session_start();
        }
    }

    public function handleRequest()
    {
        global $wp_query;
        $query = &$wp_query->query;

        // Do some translation of funky "agnt" and "agnts" back to real, non-hacked-up
        // words. We can't use "agents" in the URL since it's highly likely that a site
        // will use that as a page name, and that will conflict with the rewrite endpoint.
        if(array_key_exists('agnts', $query)) {
            $query['agents'] = $query['agnts'];
            unset($query['agnts']);
        }
        if(array_key_exists('agnt', $query)) {
            $query['agent'] = $query['agnt'];
            unset($query['agnt']);
        }

        $action = '';

        if(array_key_exists('search', $query) || array_key_exists('agents', $query)) {

            // All of the logic for searching is in the agentList function since
            // we're just passing more criteria to the API call.
            $action = 'agentList';

        } elseif(array_key_exists('agent', $query) && sizeof(trim($query['agent']) > 0)) {

            // Check if we're requesting the contact form. If not, show agent detail.
            if($this->findIn('contact', $query['agent'])) {
                // Requesting the contact form.
                // Remove /contact part from agent.
                $_REQUEST['contact'] = $this->removeIn('contact', $query['agent']);
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
            if($this->findIn('contact', $query['office'])) {
                // Requesting the contact form.
                // Remove /contact part from office.
                $_REQUEST['contactOffice'] = $this->removeIn('contact', $query['office']);
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

    public function findIn($key, $string) {
        return preg_match($this->regex[$key], $string);
    }
    public function removeIn($key, $string) {
        return preg_replace($this->regex[$key], '', $string);
    }


    /*
     *
     * ACTIONS
     *
     */


	protected function officeList()
	{
        unset($_SESSION['agentCriteria']);

		// This will be populated if an office search is being performed.
		if (array_key_exists('officeCriteria', $_REQUEST) && strlen($_REQUEST['officeCriteria']) > 0) {
			$this->args['criteria']['name'] = sanitize_text_field($_REQUEST['officeCriteria']);
		} else {
            $_REQUEST['officeCriteria'] = '';
        }

		$this->args['criteria']['omit_office_id'] = $this->args['excludeoffices'];

		if (array_key_exists('officeSort', $_REQUEST)) {
			$officeSort = sanitize_text_field($_REQUEST['officeSort']);
			$this->args['criteria']['sort'] = (sanitize_text_field($_REQUEST['officeSort']) == 'office_id') ? 'office_id' : 'name';
		} else {
			$officeSort = 'name';
		}

		$officeData = $this->getOfficeData();

		// agentCriteria is set to null if not passed along. Do not change this
		// or it will screw up agent pagination when running a search.
		$args = array(
			'offices' => $officeData,
			'agentCriteria' => (array_key_exists('agentCriteria', $_SESSION)) ? $_SESSION['agentCriteria'] : null,
			'officeCriteria' => (array_key_exists('officeCriteria', $_REQUEST)) ? sanitize_text_field($_REQUEST['officeCriteria']) : null,
			'isAgent' => false,
		);
		$args = array_merge($args, $this->args);
		$args['agentsNav'] = $this->plugin->views->agentsNavView($args);

		// Add offices HTML
		$officesHtml = '';

		foreach ($officeData as &$office) {

			if ($office['office_id'] != '') {

				$officeArgs = array_merge($args, array(
					'office'             => $office,
					'officeLink'         => $this->buildLinkToOffice($office),
					'contactLink'        => $this->buildLinkToOfficeContact($office),
					'searchLink'         => $this->buildLinkToOfficeSearch($office),
					'searchResultLink'   => $this->buildLinkToOfficeSearchResults($office),
				));

				$officesHtml .= $this->plugin->views->officeBriefView($officeArgs);

			}
		}

		$args['officesHtml'] = $officesHtml;

		return $this->plugin->views->officesListView($args);

	}


    protected function agentList()
    {
        global $wp_query;
        $query = $wp_query->query;

        // If we're searching for agents in an office, isolate the office name.
        if(array_key_exists('office', $query) && preg_match('/\/search.*/', $query['office'])) {
            $query['office'] = preg_replace('/\/search.*/', '', $query['office']);
        }

        // Office might have a page tacked onto the name, so isolate that and remove it.
        if(array_key_exists('office', $query)) {
            $officePage = array();
            preg_match('/\/([0-9]+)$/', $query['office'], $officePage);
            if(count($officePage) > 0) {
                $_REQUEST['agentpage'] = $officePage[1];
            }
            $query['office'] = preg_replace('/\/[0-9]+.*/', '', $query['office']);
        }

        // The only values associated with these endpoints should be for pagination.
        if(array_key_exists('agents', $query)) {
            if(strlen($query['agents']) > 0) {
                $_REQUEST['agentpage'] = $query['agents'];
            }
        } elseif(array_key_exists('search', $query) && strlen($query['search']) > 0) {
            $_REQUEST['agentpage'] = $query['search'];
        }

        if(array_key_exists("agentpage", $_REQUEST) && $_REQUEST['agentpage'] > 1) {
            /*
             * $startrow needs to be calculated based on the requested page. If $page == 2
             * and numPerPage is 10, for example, we would need to get agents 11 through 20.
             * The below equation will set the starting row accordingly.
             */
            $startrow = $this->args['criteria']['numperpage'] * (sanitize_text_field($_REQUEST['agentpage']) - 1) + 1;
        } else {
            $startrow = 1;
            $_REQUEST['agentpage'] = 1;
        }

        $agentSort = $this->args['criteria']['agentsort'];
        unset($this->args['criteria']['agentsort']);
        $this->args['criteria']['sort'] = $agentSort;

        $this->args['criteria']['omit_office_id'] = $this->args['excludeoffices'];

        $endpoint = '/agent';
        $separator = "?";
        if(array_key_exists('office', $query)) {
            $endpoint .= '?office_name=' . $query['office'];
            $separator = "&";
        }
        $endpoint .= $separator . "startrow=" . $startrow;
        $endpoint .= "&maxrows=" . $this->args['criteria']['numperpage'];

        // This will be populated if an agent search is being performed.
        if(array_key_exists('agentCriteria', $_REQUEST) && strlen($_REQUEST['agentCriteria']) > 0) {
            $this->args['criteria']['name'] = sanitize_text_field($_REQUEST['agentCriteria']);
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
			'agents'          => $agentsData,
            'agentSort'       => $agentSort,
			'totalrows'       => $data['responseData']['data']['total_rows'],
			'page'            => sanitize_text_field($_REQUEST['agentpage']),
			'officeId'        => (array_key_exists('officeId', $_REQUEST)) ? sanitize_text_field($_REQUEST['officeId']) : '',
			'officeCount'     => $officeCount,
			'agentCriteria'   => (array_key_exists('agentCriteria', $_REQUEST)) ? sanitize_text_field($_REQUEST['agentCriteria']) : '',
			'officeCriteria'  => (array_key_exists('officeCriteria', $_REQUEST)) ? sanitize_text_field($_REQUEST['officeCriteria']) : '',
			'isAgent'         => true,
			'agentsHtml'      => '',
			'postHash'        => $this->getPostHash(),
            'showOffices'     => $this->args['showoffices'],
			'allAgentsLink'   => $this->buildLinkToAgents(),
		);

		$args = array_merge($args, $this->args);

		// Add agent/office navigation
		$args['agentsNav'] = $this->plugin->views->agentsNavView($args);

		// Add agents HTML
		$agentsHtml = '';

		foreach ($agentsData as &$agent) {
			if ($agent['display_agent']) {
				$agentArgs = array_merge($args, array(
					'agent'        => $agent,
					'agentLink'    => $this->buildLinkToAgent($agent),
					'contactLink'  => $this->buildLinkToAgentContact($agent),
				));
				$agentsHtml .= $this->plugin->views->agentBriefView($agentArgs);
			}
		}

		$args['agentsHtml'] = $agentsHtml;

		return $this->plugin->views->agentsListView($args);

	}


    protected function agent()
    {
        global $wp_query;

        $agentData = $this->getAgentById(sanitize_text_field($_REQUEST['agentId']));

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
            'officeId' => (array_key_exists('officeId', $_REQUEST)) ? sanitize_text_field($_REQUEST['officeId']) : '',
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
        $agentData = $this->getAgentById(sanitize_text_field($_REQUEST['contact']));

        $args = array(
            'agent' => $agentData,
            'agentId' => sanitize_text_field($_REQUEST['contact']),
            'officeId' => (array_key_exists('officeId', $_REQUEST)) ? sanitize_text_field($_REQUEST['officeId']) : '',
        );
        $args = array_merge($args, $this->args);

        return $this->plugin->views->agentContact($args);
    }


    protected function contactFormOffice()
    {
        // This is being set in session so we don't need to parse the office name and make
        // another API request on the 'thanks' page to get the office data.
        if(!array_key_exists('officeData', $_SESSION)) {
            $_SESSION['officeData'] = $this->getOfficeByName(sanitize_text_field($_REQUEST['contactOffice']));
        }

        $args = array(
            'office' => $_SESSION['officeData'],
            'officeId' => $_SESSION['officeData']['office_id'],
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

        if(strlen($_REQUEST['errorField']) > 0) {
            // Show contact form again.
            return $this->getForm($formType);
        }

        // Translate form fields into request args. Using field name prefixes
        // on form fields since Wordpress has reserved field names.
        $this->args['criteria']['name'] = sanitize_text_field($_REQUEST['wolfnet_name']);
        $this->args['criteria']['email'] = sanitize_email($_REQUEST['wolfnet_email']);
        $this->args['criteria']['phone'] = sanitize_text_field($_REQUEST['wolfnet_phone']);
        $this->args['criteria']['contact_by'] = sanitize_text_field($_REQUEST['wolfnet_contacttype']);
        $this->args['criteria']['message'] = sanitize_text_field($_REQUEST['wolfnet_comments']);

        // If this is the agent contact page, agent_guid will be passed along, otherwise
        // this was submitted via the office contact and we'll pass office_id.
        if($formType == 'agent') {
            $this->args['criteria']['agent_id'] = sanitize_text_field($_REQUEST['agent_id']);
        } else {
            $this->args['criteria']['office_id'] = sanitize_text_field($_REQUEST['office_id']);
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
            $out = $this->getForm($formType);
            if($formType == 'office') {
                // This was carried over so we didn't need to make a new API request on the
                // contact thanks page for office data. It can go away now.
                unset($_SESSION['officeData']);
            }
            return $out;
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
            // Due to possible data duplication, get the last result... There
            // will only be one office returned for most implementations.
            $officeData = $data['responseData']['data']['office'][0];
        }

        return $officeData;
    }


    protected function getOfficeByName($name)
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->key,
                '/office?name=' . $name,
                'GET',
                $this->args['criteria']
            );
        } catch(Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $officeData = array();
        if(is_array($data['responseData']['data'])) {
            // Due to possible data duplication, get the last result... There
            // will only be one office returned for most implementations.
            $officeData = $data['responseData']['data']['office'][0];
        }

        return $officeData;
    }


	protected function buildLink(array $args = array())
	{

		$agentPagesLink = '';

		if (array_key_exists("REDIRECT_URL", $_SERVER)) {
			$linkBase = esc_url_raw($_SERVER['REDIRECT_URL']);
		} else {
			$linkBase = esc_url_raw($_SERVER['PHP_SELF'] . '/');
		}

        // Chop the links down to only the necessary parts.
        if(preg_match('/\/office.*/', $linkBase)) {
            $linkBase = preg_replace('/\/office.*/', '/', $linkBase);
        } elseif(preg_match('/search.*/', $linkBase)) {
            $linkBase = preg_replace('/search.*/', '', $linkBase);
        } elseif(preg_match('/agnts.*/', $linkBase)) {
            $linkBase = preg_replace('/agnts.*/', '', $linkBase);
        }

		$agentPagesLink = $linkBase;

        foreach($args as $key => $value) {
            if(strlen($value) > 0) {
                $agentPagesLink .= "$key/$value/";
            } else {
                $agentPagesLink .= "$key/";
            }
        }

		return $agentPagesLink;

	}


	protected function buildLinkToAgents()
	{
		return $this->buildLink(array( 'agnts' => '' ));
	}


	protected function buildLinkToAgent($agent)
	{
		$args = array();

        $args['agnt'] = $agent['agent_stub'];
		return $this->buildLink($args);
	}


	protected function buildLinkToAgentContact($agent)
	{
		return $this->buildLinkToAgent($agent) . 'contact';
	}


	protected function buildLinkToOffice($office)
	{
		return $this->buildLink(array( 'office' => $office['office_stub'] ));

	}


	protected function buildLinkToOfficeContact($office)
	{
		return $this->buildLink(array( 'office' => $office['office_stub'], 'contact' => '' ));

	}


	protected function buildLinkToOfficeSearch($office)
	{
		return $office['search_solution_url'] . '/?action=newsearchsession';

	}


	protected function buildLinkToOfficeSearchResults($office)
	{
		return $office['search_solution_url'] . '/?action=newsearchsession'
			. '&office_id=' . $office['office_id']
			. '&ld_action=find_office';

	}


	protected function getPostHash()
	{
		return '#post-' . get_the_id();
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
