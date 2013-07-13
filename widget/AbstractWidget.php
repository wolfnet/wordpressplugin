<?php

abstract class Wolfnet_AbstractWidget extends WP_Widget
{


    public $idBase = false;
    public $options = array();
    public $controlOptions = array();


    final public function __construct()
    {
        if (!array_key_exists('wolfnet', $GLOBALS)) {
            throw new Exception('Could not find the Wolfnet plugin in the global scope.');
        }
        else {
            $this->plugin = $GLOBALS['wolfnet'];
        }

        if (!property_exists($this, 'name')) {
            throw new Exception('Concrete Widget class does not have a public property "name".');
        }

        parent::__construct($this->idBase, $this->name, $this->options, $this->controlOptions);

    }


    public function update($defaultOptions, $new_instance, $old_instance)
    {
        /* processes widget options to be saved */
        $newData = $this->getOptions($defaultOptions, $new_instance);
        $saveData = array();

        foreach ($defaultOptions as $opt => $defaultValue) {
            $saveData[$opt] = strip_tags($newData[$opt]);
        }

        return $saveData;

    }


    protected function getOptions(array $defaultOptions, $instance=null)
    {
        $idCallback = array(&$this, 'get_field_id');
        $nameCallback = array(&$this, 'get_field_name');

        return $this->plugin->getOptions($defaultOptions, $instance, $idCallback, $nameCallback);

    }


}
