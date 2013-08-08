<?php

abstract class Wolfnet_AbstractWidget extends WP_Widget
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
        }
        // Else set a protected property equal to a reference to the instance.
        else {
            $this->plugin = $GLOBALS['wolfnet'];
        }

        parent::__construct($this->idBase, $this->name, $this->options, $this->controlOptions);

    }


    public function update(array $defaultOptions, $new_instance, $old_instance)
    {
        /* processes widget options to be saved */
        $newData = $this->getOptions($new_instance);
        $saveData = array();

        foreach ($defaultOptions as $opt => $defaultValue) {
            $saveData[$opt] = strip_tags($newData[$opt]);
        }

        return $saveData;

    }


    abstract protected function getOptions($instance=null);


    protected function prepOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (substr($key, -5) == '_wpid') {
                $options[$key] = $this->get_field_id($value);
            }
            else if (substr($key, -7) == '_wpname') {
                $options[$key] = $this->get_field_name($value);
            }
        }

        return $options;

    }


}
