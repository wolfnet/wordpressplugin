<?php

/**
 *
 * @title         ListingGridWidget.php
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

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_Widget_ListingGridWidget extends Wolfnet_Widget_AbstractWidget
{


    public $idBase = 'wolfnet_listingGridWidget';

    public $name = 'WolfNet Listing Grid';

    public $options = array(
        'description' => 'Define criteria to display a grid of matching properties. The grid
            display includes an image, price, number of bedrooms, number of bathrooms, and
            address.'
        );

    public $controlOptions = array(
        'width' => '400px'
        );


    public function widget($args, $instance)
    {

        try {

            $instance['maxrows'] = $instance['maxresults'];
            $response = $this->plugin->listingGrid($this->collectData($args, $instance));

        } catch (Wolfnet_Api_ApiException $e) {
            $response = $this->plugin->displayException($e);
        }

        echo $args['before_widget'] . $response . $args['after_widget'];

    }


    public function form($instance)
    {
        $options = $this->getOptions($instance);

        echo $this->plugin->views->listingGridOptionsFormView($options);

    }


    public function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
        $saveData = parent::updateWithDefault($this->plugin->getListingGridDefaults(), $new_instance, $old_instance);

        /* Advanced Mode */
        if ( $saveData['mode'] == 'advanced' ) {
            if ( $saveData['savedsearch'] == 'deleted' ) {
                /* Maintain the existing search criteria */
            }
            else {
                $criteria = $this->plugin->getSavedSearch($saveData['savedsearch']);
                $saveData['criteria'] = json_encode($criteria);
            }

            $saveData['zipcode'] = '';
            $saveData['city'] = '';
            $saveData['minprice'] = '';
            $saveData['maxprice'] = '';

            if ( $saveData['keyid'] != '' ) {
                $criteria['keyid'] = $saveData['keyid'];
            }

        }

        /* Basic Mode */
        else {
            $criteria = array();
            if ( $saveData['keyid'] != '' ) {
                $criteria['keyid'] = $saveData['keyid'];
            }
            if ( $saveData['minprice'] != '' ) {
                $criteria['minprice'] = $saveData['minprice'];
            }
            if ( $saveData['maxprice'] != '' ) {
                $criteria['maxprice'] = $saveData['maxprice'];
            }
            if ( $saveData['city'] != '' ) {
                $criteria['city'] = $saveData['city'];
            }
            if ( $saveData['zipcode'] != '' ) {
                $criteria['zipcode'] = $saveData['zipcode'];
            }
            $saveData['criteria'] = json_encode($criteria);
            $saveData['savedsearch'] = '';
        }

        return $saveData;

    }


    protected function getOptions($instance=null)
    {
        $options = $this->plugin->getListingGridOptions($instance);

        return parent::prepOptions($options);

    }


    protected function collectData($args, $instance)
    {
        $data = $this->getOptions($instance);

        if ($data['mode'] == 'advanced') {
            $criteriaArray = $this->convertCriteriaJsonToArray($data['criteria']);
            // array keys need to be lowercase
            $criteriaArray = array_change_key_case($criteriaArray);
            $data = array_merge($data, $criteriaArray);
        }

        return $data;

    }


    private function convertCriteriaJsonToArray($criteria)
    {
        $criteria = json_decode($criteria, true);

        if (!is_array($criteria)) {
            $criteria = array();
        }

        return $criteria;

    }


}
