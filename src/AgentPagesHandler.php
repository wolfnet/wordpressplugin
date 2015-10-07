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

        if (is_array($data['responseData']['data'])) {
            $officeData = $data['responseData']['data'];
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
        $endpoint = '/agent';
        if(array_key_exists('office_id', $_REQUEST)) {
            $endpoint .= '?office_id=' . $_REQUEST['office_id'];
        }
        try {
            $data = $this->apin->sendRequest($this->key, $endpoint, 'GET', $this->args['criteria']);
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        $agentsData = array();

        if (is_array($data['responseData']['data'])) {
            $agentsData = $data['responseData']['data'];
        }
        $agentsData = array_reverse($agentsData);

        $args = array('agents' => $agentsData);
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
            $agentData = $data['responseData']['data'][0];
        }

        $args = array('agent' => $agentData);
        $args = array_merge($args, $this->args);

        return $this->views->agentView($args);
    }


    public function setKey(&$key) {
        $this->key = $key;
    }

    public function setArgs(&$args) {
        $this->args = $args;
    }
}