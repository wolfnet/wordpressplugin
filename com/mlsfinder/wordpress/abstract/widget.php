<?php

/**
 * This object provides some additional MLSFinder specific logic to abstract widget class.
 *
 * @package			com.mlsfinder.wordpress.abstract
 * @title			widget.php
 * @extends			com_ajmichels_wppf_abstract_widget
 * @contributors	AJ Michels (aj.michels@wolfnet.com)
 * @version			1.0
 * @copyright		Copyright (c) 2012, WolfNet Technologies, LLC
 * 
 */

abstract class com_mlsfinder_wordpress_abstract_widget
extends com_ajmichels_wppf_abstract_widget
{
	
	
	/**
	 * This property is for a reference to the plugin instance. It is used to provide access for the
	 * widget to the resources that are available in the plugin such as the service factory.
	 *
	 * @type MLSFinder
	 * 
	 */
	protected $p;
	
	
	/**
	 * This constructor method passes data from the concrete Widget object to the wppf abstract 
	 * widget which in turn passes the data to the base WPWidget class.
	 *
	 * @param mixed $id_base
	 * @param mixed $name
	 * @param array $widget_options
	 * @param array $control_options
	 * @return void
	 * 
	 */
	public function __construct ( $id_base = false, $name, $widget_options = array(), $control_options = array() )
	{
		$this->p = MLSFinder::getInstance();
		$this->sf = $this->p->sf;
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}
	
	
}