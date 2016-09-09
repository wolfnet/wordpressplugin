<?php

/**
 *
 * @title         FeaturedListingsWidget.php
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

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_Widget_FeaturedListingsWidget extends Wolfnet_Widget_AbstractWidget
{


    public $idBase = 'wolfnet_featuredListingsWidget';

    public $name = 'WolfNet Featured Listings';

    public $options = array(
        'description' => 'Configure a scrollable list to feature your properties.'
        );

    public $controlOptions = array(
        'width' => '300px'
        );


    /**
     * [widget description]
     * @param  array  $args      An array of arguments for the widget.
     * @param  array  $instance  Instance data for the active widget.
     * @return void
     */
    public function widget($args, $instance)
    {

        try {
            $options = $this->getOptions($instance);
            $response = $this->plugin->featuredListings->featuredListings($options);

        } catch (Wolfnet_Api_ApiException $e) {
            $response = $this->plugin->displayException($e);
        }

        echo $args['before_widget'] . $response . $args['after_widget'];

    }


    public function form($instance)
    {
        $options = $this->getOptions($instance);

        echo $this->plugin->views->featuredListingsOptionsFormView($options);

    }


    public function update($new_instance, $old_instance)
    {
        return parent::updateWithDefault(
            $this->plugin->featuredListings->getDefaults(), 
            $new_instance, 
            $old_instance
        );

    }


    protected function getOptions($instance = null)
    {
        $options = array();
        try {
            $options = $this->plugin->featuredListings->getOptions($instance);
        } catch (Wolfnet_Api_ApiException $e) {
            echo $this->plugin->displayException($e);
        }
        
        return parent::prepOptions($options);

    }


}
