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
abstract class WNT_WP_Widget_AbstractWidget extends WP_Widget
{


    /* PROPERTIES ******************************************************************************* */


    /**
     * This property holds a reference to the Service Factory retrieved from the plugin instance.
     *
     * @type  com_greentiedev_phpSpring_bean_factory_default
     *
     */
    protected $fac;


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
    public function __construct ($id_base=false, $name, $widget_options=array(), $control_options=array())
    {
        $this->fac = WNT_WP_Factory::getInstance();
        parent::__construct($id_base, $name, $widget_options, $control_options);
    }


}
