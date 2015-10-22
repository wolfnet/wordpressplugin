<?php

/**
 * @title         Wolfnet_AgentPagesHandler.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
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

        if(array_key_exists('agent', $_REQUEST) && sizeof(trim($_REQUEST['agent']) > 0)) {
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
        try {
            $data = $this->apin->sendRequest(
                $this->key, 
                '/agent/' . $_REQUEST['agent'], 
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

        $featuredListings = $this->agentFeaturedListings($agentData['mls_agent_id']);
        $count = $featuredListings['totalRows'];
        $listings = ($count > 0) ? $featuredListings['listings'] : null;

        $args = array(
            'agent' => $agentData,
            'listingCount' => $count,
            'listingHTML' => $listings,
        );
        $args = array_merge($args, $this->args);

        return $this->views->agentView($args);
    }


    protected function agentFeaturedListings($agentId) {
        $criteria = $this->getListingGridDefaults();
        $criteria['maxrows'] = 10;
        $criteria['maxresults'] = 10;

        $this->args['criteria'] = array_merge($this->args['criteria'], $criteria);

        $agentListings = $this->getListingsByAgentId($agentId);

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


    protected function getListingsByAgentId($agentId) {
        try {
            $data = $this->apin->sendRequest(
                $this->key, 
                '/listing/?agent_id=' . $agentId, 
                'GET', 
                $this->args['criteria']
            );
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        return $data;
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