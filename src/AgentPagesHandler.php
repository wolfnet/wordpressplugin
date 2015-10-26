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

    public function handleRequest() 
    {
        $action = '';

        if(array_key_exists('contact', $_REQUEST)) {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $action = 'contactProcess';
            } else {
                $action = 'contactForm';
            }
        } elseif(array_key_exists('agent', $_REQUEST) && sizeof(trim($_REQUEST['agent']) > 0)) {
            // If agent is passed through, show the agent detail.
            $action = 'agent';
        } elseif(array_key_exists('office_id', $_REQUEST) && sizeof(trim($_REQUEST['office_id']) > 0)) {
            // office_id is passed in; list their agents.
            $action = 'agentList';
        } elseif(!$this->args['showoffices']) {
            // if none of the above match and they don't want to show an office list, show all agents.
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
        try {
            $data = $this->apin->sendRequest($this->key, '/office', 'GET', $this->args['criteria']);
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        $officeData = array();

        if (is_array($data['responseData']['data']['office'])) {
            $officeData = $data['responseData']['data']['office'];
        }

        // If there is only one office then just display its agents.
        if(count($officeData) == 1) {
            return $this->agentList();
        }

        $args = array('offices' => $officeData);
        $args = array_merge($args, $this->args);

        return $this->views->officesListView($args);
    }


    protected function agentList()
    {
        if(array_key_exists("page", $_REQUEST) && $_REQUEST['page'] > 1) {
            /*
             * $startrow needs to be calculated based on the requested page. If $page == 2
             * and numPerPage is 10, for example, we would need to get agents 11 through 20. 
             * The below equation will set the starting row accordingly.
             */
            $startrow = $this->args['criteria']['numperpage'] * ($_REQUEST['page'] - 1) + 1;
        } else {
            $startrow = 1;
            $_REQUEST['page'] = 1;
        }

        $endpoint = '/agent';
        $separator = "?";
        if(array_key_exists('office_id', $_REQUEST)) {
            $endpoint .= '?office_id=' . $_REQUEST['office_id'];
            $separator = "&";
        }
        $endpoint .= $separator . "startrow=" . $startrow;
        $endpoint .= "&maxrows=" . $this->args['criteria']['numperpage'];

        try {
            $data = $this->apin->sendRequest($this->key, $endpoint, 'GET', $this->args['criteria']);
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        $agentsData = array();

        if (is_array($data['responseData']['data']['agent'])) {
            $agentsData = $data['responseData']['data']['agent'];
        }

        $args = array(
            'agents' => $agentsData,
            'totalrows' => $data['responseData']['data']['total_rows'],
            'page' => $_REQUEST['page'],
        );
        $args = array_merge($args, $this->args);

        return $this->views->agentsListView($args);
    }


    protected function agent()
    {
        $agentData = $this->getAgentById($_REQUEST['agent']);

        // We need to get a product key that we can pull this agent's listings with.
        // Each key entered into the Settings page has a market name associated with it.
        // We can get the appropriate key for this agent based on their market.
        $this->key = $this->getProductKeyByMarket($agentData['market']);

        $featuredListings = $this->agentFeaturedListings($this->key, $agentData['mls_agent_id']);
        $count = $featuredListings['totalRows'];
        $listings = ($count > 0) ? $featuredListings['listings'] : null;


        $searchUrl = $this->getBaseUrl($this->key);
        $searchUrl .= "?action=newsearchsession&agent_id=" . $agentData['mls_agent_id'];

        $args = array(
            'agent' => $agentData,
            'listingCount' => $count,
            'listingHTML' => $listings,
            'searchUrl' => $searchUrl,
        );
        $args = array_merge($args, $this->args);

        return $this->views->agentView($args);
    }


    protected function contactForm() 
    {
        $agentData = $this->getAgentById($_REQUEST['contact']);
        
        $args = array(
            'agent' => $agentData,
            'agentId' => $_REQUEST['contact'],
        );
        $args = array_merge($args, $this->args);

        return $this->views->agentContact($args);
    }


    protected function contactProcess()
    {
        var_dump($_REQUEST);
        die;

        // pass agent_guid to API request so it can retrieve the agent email to send to.
    }


    /*
     *
     * HELPER FUNCTIONS
     *
     */


    protected function agentFeaturedListings($key, $agentId) 
    {
        $criteria = $this->getListingGridDefaults();
        $criteria['maxrows'] = 10;
        $criteria['maxresults'] = 10;

        $this->args['criteria'] = array_merge($this->args['criteria'], $criteria);

        $agentListings = $this->getListingsByAgentId($key, $agentId);

        if(count($agentListings['responseData']['data']['listing']) == 0) {
            return array('totalRows' => 0, 'listings' => '');
        }

        $this->decodeCriteria($criteria);
        $agentListings['requestData'] = array_merge($agentListings['requestData'], $criteria);

        return array(
            'totalRows' => $agentListings['responseData']['data']['total_rows'],
            'listings' => $this->listingGrid($criteria, 'grid', $agentListings),
        );
    }


    protected function getListingsByAgentId($key, $agentId) 
    {
        try {
            $data = $this->apin->sendRequest(
                $key, 
                '/listing/?agent_id=' . $agentId, 
                'GET', 
                $this->args['criteria']
            );
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        return $data;
    }


    protected function getAgentById($agentId) 
    {
        try {
            $data = $this->apin->sendRequest(
                $this->key, 
                '/agent/' . $agentId, 
                'GET', 
                $this->args['criteria']
            );
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        $agentData = array();
        if (is_array($data['responseData']['data'])) {
            $agentData = $data['responseData']['data'];
        }

        return $agentData;
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