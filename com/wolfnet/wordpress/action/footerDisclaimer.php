<?php

/**
 * This action is responsible for enqueuing any admin resources such as JavaScript and CSS that are
 * needed for any code generated in the WordPress admin for the plugin.
 * 
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         footerDisclaimer.php
 * @extends       com_ajmichels_wppf_action_action
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
class com_wolfnet_wordpress_action_footerDisclaimer
extends com_ajmichels_wppf_action_action
{
	
	
	/* PROPERTIES ******************************************************************************* */
	
	/**
	 * This property holds a reference to the Market Disclaimer Service object.
	 * 
	 * @type  com_wolfnet_wordpress_market_disclaimer_service  
	 * 
	 */
	private $marketDisclaimerService;
	
	
	/* PUBLIC METHODS *************************************************************************** */
	
	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return  void
	 * 
	 */
	public function execute ()
	{
		$this->log( 'Action footerDisclaimer' );
		
		/* If it has been established that we need to output the market disclaimer do so now in the 
		 * site footer, otherwise do nothing. */
		if ( array_key_exists( 'wolfnet_includeDisclaimer', $_REQUEST ) ) {
			
			echo '<div class="wolfnet_marketDisclaimer">';
			echo $this->getMarketDisclaimerService()->getDisclaimerByType()->getContent();
			echo '</div>';
			
		}
		
	}
	
	
	/* ACCESSOR METHODS ************************************************************************* */
	
	/**
	 * GETTER:  This method is a getter for the marketDisclaimerService property.
	 * 
	 * @return  com_wolfnet_wordpress_market_disclaimer_service
	 * 
	 */
	public function getMarketDisclaimerService ()
	{
		return $this->marketDisclaimerService;
	}
	
	
	/**
	 * SETTER:  This method is a setter for the marketDisclaimerService property.
	 * 
	 * @param   com_wolfnet_wordpress_market_disclaimer_service  $service
	 * @return  void
	 * 
	 */
	public function setMarketDisclaimerService ( com_wolfnet_wordpress_market_disclaimer_service $service )
	{
		$this->marketDisclaimerService = $service;
	}
	
	
}