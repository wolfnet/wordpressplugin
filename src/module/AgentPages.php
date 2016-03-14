<?php

/**
 * WolfNet Agent Pages module
 *
 * This module represents the agent pages feature and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_AgentPages
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    /**
    * This property holds an instance of the Wolfnet_AgentPagesHandler.
    * @var Wolfnet_AgentPagesHandler
    */
    protected $handler = null;


    public function __construct($plugin, $handler) {
        $this->plugin = $plugin;
        $this->handler = $handler;
    }


    public function scAgentPages($attrs)
    {
        if(!$this->showAgentFeature()) {
            return '';
        }

        try {
            $defaultAttributes = $this->getDefaults();

            $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

            $this->plugin->decodeCriteria($criteria);

            $out = $this->agentPageHandler($criteria);

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function showAgentFeature()
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->plugin->keyService->getDefault(),
                '/settings',
                'GET'
            );
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $leadsEnabled = $data['responseData']['data']['site']['my_agents_leads'];

        return $leadsEnabled;
    }


    public function getDefaults()
    {

        return array(
            'officetitle'    => '',
            'agenttitle'     => '',
            'detailtitle'    => '',
            'showoffices'    => true,
            'activelistings' => true,
            'soldlistings'   => false,
            'excludeoffices' => '',
            'numperpage'     => 10,
        );

    }


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        return $options;

    }


    public function agentPageHandler(array $criteria = array())
    {
        $key = $this->plugin->keyService->getFromCriteria($criteria);

        if (!$this->plugin->keyService->isSaved($key)) {
            return false;
        }

        $vars = array(
            'instance_id' => str_replace('.', '', 'wolfnet_agentPages_' . $this->plugin->createUUID()),
            'criteria'    => $criteria,
        );

        $args = $this->plugin->convertDataType(array_merge($criteria, $vars));

        $this->handler->setKey($key);
        $this->handler->setArgs($args);

        return $this->handler->handleRequest();
    }
}

?>
