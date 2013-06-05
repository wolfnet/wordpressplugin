<?php

/**
 * This object provides some additional wolfnet specific logic to abstract widget class.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    abstract
 * @title         widget.php
 * @extends       com_greentiedev_wppf_abstract_widget
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
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
 *
 *
 */
abstract class com_wolfnet_wordpress_abstract_widget
extends com_greentiedev_wppf_abstract_widget
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds a reference to the plugin instance. It is used to provide access for the
	 * widget to the resources that are available in the plugin such as the service factory. This
	 * must be done this way as widget objects are called outside of the normal plugin request cycle.
	 *
	 * @type  wolfnet
	 *
	 */
	protected $pluginInstance;


	/**
	 * This property holds a reference to the Service Factory retrieved from the plugin instance.
	 *
	 * @type  com_greentiedev_phpSpring_bean_factory_default
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
		$this->pluginInstance = wolfnet::getInstance();
		$this->sf = $this->pluginInstance->sf;
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}


}
