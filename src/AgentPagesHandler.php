<?php

/**
 * @title         Wolfnet_Plugin.php
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
        if(!array_key_exists('office_id', $_REQUEST) && !array_key_exists('agent_id', $_REQUEST)) {
            $action = 'officeList';
        } elseif(array_key_exists('office_id', $_REQUEST) && sizeof(trim($_REQUEST['office_id']) > 0)) {
            $action = 'agentList';
        } elseif(array_key_exists('agent_id', $_REQUEST) && sizeof(trim($_REQUEST['agent_id']) > 0)) {
            $action = 'agent';
        }
        
        // Run the function associated with the action.
        return $this->$action();
	}


    protected function officeList()
    {
        echo "Office List";
        die;
    }


    protected function agentList()
    {
        try {
            $data = $this->apin->sendRequest($this->key, '/agent', 'GET', $this->args['criteria']);
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        $agentsData = array();

        if (is_array($data['responseData']['data'])) {
            $agentsData = $data['responseData']['data'];
        }

        $args = ['agents' => $agentsData];
        $args = array_merge($args, $this->args);

        return $this->views->agentsListView($args);
    }


    protected function agent()
    {
        echo "Agent";
        die;
    }


    public function setKey(&$key) {
        $this->key = $key;
    }

    public function setArgs(&$args) {
        $this->args = $args;
    }
}