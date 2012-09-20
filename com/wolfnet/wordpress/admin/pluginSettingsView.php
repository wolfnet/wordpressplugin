<?php

/**
 * This view is responsible for displaying the plugin admin page.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    admin
 * @title         pluginSettingsView.php
 * @extends       com_ajmichels_wppf_abstract_view
 * @implements    com_ajmichels_wppf_interface_iView
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
class com_wolfnet_wordpress_admin_pluginSettingsView
extends com_ajmichels_wppf_abstract_view
implements com_ajmichels_wppf_interface_iView
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type  string
	 * 
	 */
	public $template;
	
	/**
	 * This property holds a reference to the OptionManager object.
	 *
	 * @type  string
	 * 
	 */
	public $optionManager;
	
	
	/* CONSTRUCTOR METHOD *********************************************************************** */
	
	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\pluginSettings.php' );
	}
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method establishes variable values which will be used by the template when it is render, 
	 * then the data is passed to to inharited render method.
	 * 
	 * @param   array  $data  An associative array of data for the template. Each array key will be 
	 *                        transformed into a variable.
	 * @return  string
	 *
	 */
	public function render ( $data = array() )
	{
		$optionManager      = $this->getOptionManager();
		$data['formHeader'] = $optionManager->getSettingsFormHeader();
		$data['productKey'] = $optionManager->getOptionValueFromWP( 'wolfnet_productKey' );
		
		return parent::render( $data );
		
	}
	
	
	/* ACCESSORS ******************************************************************************** */
	
	/**
	 * GETTER: This method is a getter for the optionManager property.
	 * 
	 * @return com_ajmichels_wppf_option_manager
	 * 
	 */
	public function getOptionManager ()
	{
		return $this->optionManager;
	}
	
	
	/**
	 * SETTER: This method is a setter for the optionManager property.
	 * 
	 * @param   com_ajmichels_wppf_option_manager  $om
	 * 
	 * @return  void
	 * 
	 */
	public function setOptionManager ( com_ajmichels_wppf_option_manager $om )
	{
		$this->optionManager = $om;
	}
	
	
}