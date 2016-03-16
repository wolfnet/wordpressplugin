<?php

/**
 * WolfNet Property List module
 *
 * This module represents the property list and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_PropertyList
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;

    /**
    * This property holds the current instance of the Wolfnet_Views.
    * @var Wolfnet_Views
    */
    protected $views = null;


    public function __construct($plugin, $views) {
        $this->plugin = $plugin;
        $this->views = $views;
    }


    public function scPropertyList($attrs = array())
    {
        try {
            $default_maxrows = '50';
            $criteria = array_merge($this->getDefaults(), (is_array($attrs)) ? $attrs : array());

            // TODO: Default this elsewhere, and clean up maxrows vs numrows
            if ($criteria['maxresults'] > $default_maxrows) {
                $criteria['maxresults'] = $default_maxrows;
            }

            $this->plugin->decodeCriteria($criteria);

            $out = $this->plugin->listingGrid->listingGrid($criteria, 'list');

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function getDefaults()
    {
        return array(
            'title'       => '',
            'class'       => 'wolfnet_propertyList ',
            'criteria'    => '',
            'ownertype'   => 'all',
            'maptype'     => 'disabled',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50, // needed??
            'maxrows'     => 50,
            'mode'        => 'advanced',
            'savedsearch' => '',
            'zipcode'     => '',
            'city'        => '',
            'exactcity'   => null,
            'minprice'    => '',
            'maxprice'    => '',
            'keyid'       => 1,
            'key'         => $this->plugin->keyService->getDefault(),
            'startrow'    => 1,
            );

    }


    public function getOptions($instance = null)
    {
        return $this->plugin->listingGrid->getOptions($instance);
    }
}

?>
