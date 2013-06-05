<?php

/**
 * This view is responsible for displaying the plugin admin page.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    admin
 * @title         pluginInstructionsView.php
 * @extends       com_greentiedev_wppf_abstract_view
 * @implements    com_greentiedev_wppf_interface_iView
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
class com_wolfnet_wordpress_admin_pluginInstructionsView
extends com_greentiedev_wppf_abstract_view
implements com_greentiedev_wppf_interface_iView
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type  string
	 *
	 */
	public $template;


	public $pluginUrl;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\pluginInstructions.php' );
	}


	/* PUBLIC METHODS *************************************************************************** */

	public function render ( $data = array() )
	{
		$data['imgdir'] = $this->getPluginUrl() . 'img/';
		return parent::render( $data );
	}


	/* ACCESSOR METHODS ************************************************************************* */

	public function getPluginUrl ()
	{
		return $this->pluginUrl;
	}


	public function setPluginUrl ( $url )
	{
		$this->pluginUrl = $url;
	}


}
