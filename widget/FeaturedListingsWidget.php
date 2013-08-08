<?php

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_FeaturedListingsWidget extends Wolfnet_AbstractWidget
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
        $options = $this->getOptions($instance);

        echo $this->plugin->featuredListings($options);

    }


    public function form($instance)
    {
        $options = $this->getOptions($instance);

        echo $this->plugin->featuredListingsOptionsFormView($options);

    }


    public function update($new_instance, $old_instance)
    {
        return parent::update($this->plugin->getFeaturedListingsDefaults(), $new_instance, $old_instance);

    }


    protected function getOptions($instance=null)
    {
        $options = $this->plugin->getFeaturedListingsOptions($instance);

        return parent::prepOptions($options);

    }


}
