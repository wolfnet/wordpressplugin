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


    public function widget($args, $instance)
    {
        $options = $this->getOptions($this->plugin->getFeaturedListingsDefaults(), $instance);

        echo $this->plugin->featuredListings($options);

    }


    public function form($instance)
    {
        $options = $this->getOptions($this->plugin->getFeaturedListingsDefaults(), $instance);

        echo $this->plugin->getFeaturedListingsOptionsForm($options);

    }


    public function update($new_instance, $old_instance)
    {
        return parent::update($this->plugin->getFeaturedListingsDefaults(), $new_instance, $old_instance);

    }


    protected function getOptions($defaultOptions, $instance)
    {
        $options = parent::getOptions($defaultOptions, $instance);

        $options['autoplay_false_wps']  = selected($options['autoplay'], 'false', false);
        $options['autoplay_true_wps']   = selected($options['autoplay'], 'true', false);
        $options['direction_left_wps']  = selected($options['direction'], 'left', false);
        $options['direction_right_wps'] = selected($options['direction'], 'right', false);

        return $options;

    }


}
