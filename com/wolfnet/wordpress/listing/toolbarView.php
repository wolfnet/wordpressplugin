<?php

/**
 * This view is responsible for displaying the Quick Search Form, which is a widget component.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         toolbarView.php
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
 */
class com_wolfnet_wordpress_listing_toolbarView
extends com_greentiedev_wppf_abstract_view
implements com_greentiedev_wppf_interface_iView
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the path to the HTML template file for this view.
	 *
	 * @type  string
	 */
	public $template;


	/* CONSTRUCTOR METHOD *********************************************************************** */

	/**
	 * This constructor method simply assigns the template property with a path to the HTML template
	 * for this view based on the view files location.
	 *
	 * @return  void
	 */
	public function __construct ()
	{
		$this->template = $this->formatPath( dirname( __FILE__ ) . '\template\toolbar.php' );
	}


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * @param   array  $data  Associative array of data to be injected into the template file.
	 * @return  string
	 */
	public function render ( $data = array() )
	{

		$data['numrows']    = (count($data['listings'])>0) ? $data['listings'][0]->numrows    : 0;
		$data['startrow']   = (count($data['listings'])>0) ? $data['listings'][0]->startrow   : 0;
		$data['maxresults'] = (count($data['listings'])>0) ? $data['listings'][0]->maxresults : 0;
		$data['lastitem']   = $data['startrow'] + $data['numrows'] - 1;

		$data['options']['maxresults']['value'] = $data['maxresults'];

		if ($data['lastitem']>$data['maxresults']) {
			$data['lastitem'] = $data['maxresults'];
		}

		$data['options']['numrows'] = array(
			'name'  => 'numrows',
			'id'    => 'numrows',
			'value' => $data['numrows']
			);

		$prevStart = $data['startrow'] - $data['numrows'];

		if ( $prevStart < 1) {
			$prevStart = $data['maxresults'] - $data['numrows'] + 1;
		}

		if ( $prevStart < 1 ) {
			$prevStart = $data['startrow'];
		}

		$prevParams = array_merge($data['options'], array(
			'startrow' => array(
				'name'  => 'startrow',
				'id'    => 'startrow',
				'value' => $prevStart
				)
			));

		$nextStart = $data['startrow'] + $data['numrows'];

		if ($nextStart >= $data['maxresults']) {
			$nextStart = 1;
		}

		$nextParams = array_merge($data['options'], array(
			'startrow' => array(
				'name'  => 'startrow',
				'id'    => 'startrow',
				'value' => $nextStart
				)
			));

		$data['prevLink']  = $this->pageUrl(site_url('/'), $prevParams);
		$data['nextLink']  = $this->pageUrl(site_url('/'), $nextParams);
		$data['prevClass'] = ($data['startrow']<=1) ? 'wolfnet_disabled' : '';
		$data['nextClass'] = ($data['lastitem']>=$data['maxresults']) ? 'wolfnet_disabled' : '';

		return parent::render( $data );

	}


	/* PRIVATE METHODS ************************************************************************** */

	function pageUrl ($script='', $params=array())
	{
		$url = '/?pagename=wolfnet-listings';

		foreach ( $params as $name => $param ) {
			if ( trim($name) != '' ) {
				$url .= '&' . $param['name'] . '=' . $param['value'];
			}
		}

		return $url;

	}


}
