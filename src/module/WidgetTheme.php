<?php

/**
 * WolfNet Widget Theme module
 *
 * This module represents the widget theme and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2016 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_WidgetTheme
{
    /**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    public function __construct($plugin) {
        $this->plugin = $plugin;
    }


    public function getDefaults()
    {

        return array(
            'widgetTheme' => 'ash',
            'colors'      => array(),
        );

    }


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        if (array_key_exists('keyid', $options) && $options['keyid'] != '') {
            $keyid = $options['keyid'];
        } else {
            $keyid = 1;
        }

        return $options;

    }


    public function getThemeOptions()
    {
        return array(
            'ash'      => $this->getTheme('ash',     'Classic'),
            'birch'    => $this->getTheme('birch',   'Modern'),
            'cedar'    => $this->getTheme('cedar',   'Modern 2'),
            'dogwood'  => $this->getTheme('dogwood', 'Modern 3'),
        );
    }


    public function getTheme($name, $label)
    {
        return array(
            'name'       => $name,
            'label'      => $label,
            'styleName'  => 'wolfnet-' . $name,
            'styleFile'  => 'wolfnet.' . $name . '.src.css',
            'previewImg' => 'theme-preview-' . $name . '.png',
        );

    }

}
