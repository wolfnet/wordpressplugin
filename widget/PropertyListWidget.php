<?php

/**
 *
 * @title         PropertyListWidget.php
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

class Wolfnet_PropertyListWidget extends Wolfnet_ListingGridWidget
{

    public $idBase = 'wolfnet_propertyListWidget';

    public $name = 'WolfNet Property List';

    public $options = array(
        'description' => 'Define criteria to display a text list of matching properties. The text display includes the property address and price for each property.'
        );


    public function widget($args, $instance)
    {

        echo $args['before_widget'];
        echo $this->plugin->listingGrid($this->collectData($args, $instance), 'list');
        echo $args['after_widget'];

    }


}
