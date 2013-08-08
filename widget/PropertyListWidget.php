<?php

class Wolfnet_PropertyListWidget extends Wolfnet_ListingGridWidget
{


    public $idBase = 'wolfnet_propertyListWidget';

    public $name = 'WolfNet Property List';

    public $options = array(
        'description' => 'Define criteria to display a text list of matching properties. The text display includes the property address and price for each property.'
        );


    public function widget($args, $instance)
    {
        echo $this->plugin->propertyList($this->collectData($args, $instance));

    }


}
