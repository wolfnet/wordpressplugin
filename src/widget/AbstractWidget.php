<?php

/**
 *
 * @title         AbstractWidget.php
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

abstract class Wolfnet_Widget_AbstractWidget extends WP_Widget
{


    public $idBase = false;
    public $options = array();
    public $controlOptions = array();

    /**
     * A reference to an instance of the plugin class.
     * @var WolfNetPlugin
     */
    protected $plugin;


    final public function __construct()
    {
        /* NOTE: The widgets rely on methods within the plugin class to function correctly. */
        // If the plugin instance cannot be found in the global scope throw an exception.
        if (!array_key_exists('wolfnet', $GLOBALS)) {
            throw new Exception('Could not find the Wolfnet plugin in the global scope.');
        } else {
            // Else set a protected property equal to a reference to the instance.
            $this->plugin = $GLOBALS['wolfnet'];
        }

        parent::__construct($this->idBase, $this->name, $this->options, $this->controlOptions);

    }


    public function updateWithDefault(array $defaultOptions, $new_instance, $old_instance)
    {
        /* processes widget options to be saved */
        $newData = $this->getOptions($new_instance);
        $saveData = array();

        foreach ($defaultOptions as $opt => $defaultValue) {
            $saveData[$opt] = strip_tags($newData[$opt]);
        }

        return $saveData;

    }


    abstract protected function getOptions($instance = null);


    protected function prepOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (substr($key, -5) == '_wpid') {
                $options[$key] = $this->get_field_id($value);
            } elseif (substr($key, -7) == '_wpname') {
                $options[$key] = $this->get_field_name($value);
            }
        }

        return $options;

    }


}
