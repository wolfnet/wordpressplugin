<?php

/**
 * This object provides some additional MLSFinder specific logic to abstract widget class.
 *
 * @package       com.mlsfinder.wordpress
 * @subpackage    abstract
 * @title         widget.php
 * @extends       com_ajmichels_wppf_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */
abstract class com_mlsfinder_wordpress_abstract_widget
extends com_ajmichels_wppf_abstract_widget
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property holds a reference to the plugin instance. It is used to provide access for the
	 * widget to the resources that are available in the plugin such as the service factory. This 
	 * must be done this way as widget objects are called outside of the normal plugin request cycle.
	 *
	 * @type  MLSFinder
	 * 
	 */
	protected $pluginInstance;
	
	
	/**
	 * This property holds a reference to the Service Factory retrieved from the plugin instance.
	 * 
	 * @type  com_ajmichels_phpSpring_bean_factory_default
	 * 
	 */
	protected $sf;
	
	
	/* CONSTRUCTOR METHOD *********************************************************************** */
	
	/**
	 * This constructor method passes data from the concrete Widget object to the wppf abstract 
	 * widget which in turn passes the data to the base WPWidget class.
	 *
	 * @param   mixed  $id_base
	 * @param   mixed  $name
	 * @param   array  $widget_options
	 * @param   array  $control_options
	 * @return  void
	 * 
	 */
	public function __construct ( $id_base = false, $name, $widget_options = array(), $control_options = array() )
	{
		$this->pluginInstance = MLSFinder::getInstance();
		$this->sf = $this->pluginInstance->sf;
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}
	
	
}
