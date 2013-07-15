<?php

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_ListingGridWidget extends Wolfnet_AbstractWidget
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
        echo $this->plugin->listingGrid($this->collectData($args, $instance));

    }


    public function form($instance)
    {
        $options = $this->getOptions($instance);

        echo $this->plugin->getListingGridOptionsForm($options);

    }


    public function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
        $saveData = parent::update($this->plugin->getListingGridDefaults(), $new_instance, $old_instance);

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

        }

        /* Basic Mode */
        else {
            $criteria = array();
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
        $options = $this->getOptions($instance);
        $criteriaArray = $this->convertCriteriaJsonToArray($options['criteria']);
        $data = array_merge($options, $criteriaArray);

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
