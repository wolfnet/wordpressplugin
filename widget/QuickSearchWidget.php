<?php

require_once dirname(__FILE__) . '/AbstractWidget.php';

class Wolfnet_QuickSearchWidget extends Wolfnet_AbstractWidget
{


    public $idBase = 'wolfnet_quickSearchWidget';

    public $name = 'WolfNet Quick Search';

    public $options = array(
        'description'  => 'Configure a quick search to include on your website.  When executed, the user is directed to matching properties within your WolfNet property search.'
        );


    public function widget($args, $instance)
    {
        $options = $this->getOptions($this->plugin->getQuickSearchDefaults(), $instance);

        echo $this->plugin->quickSearch($options);

    }


    public function form($instance)
    {
        $options = $this->getOptions($this->plugin->getQuickSearchDefaults(), $instance);

        echo $this->plugin->getQuickSearchOptionsForm($options);

    }


    public function update($new_instance, $old_instance)
    {
        return parent::update($this->plugin->getQuickSearchDefaults(), $new_instance, $old_instance);

    }


}
