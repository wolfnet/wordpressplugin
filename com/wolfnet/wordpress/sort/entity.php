<?php

/**
 * This class is the Listing Entity and is a container for listing data.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    sort
 * @title         entity.php
 * @extends       com_greentiedev_wppf_abstract_entity
 * @implements    com_greentiedev_wppf_interface_iEntity
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

class com_wolfnet_wordpress_sort_entity
extends com_greentiedev_wppf_abstract_entity
implements com_greentiedev_wppf_interface_iEntity
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 *
	 * @type  string
	 *
	 */
	private $value = '';

	/**
	 *
	 *
	 * @type  string
	 *
	 */
	private $label = '';


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is used to set instance data for the entity. Though it is public by necessity,
	 * this method should not be accessed by any object other than the listingDao.
	 * ( see Memento Design Pattern )
	 *
	 * @param   array  $data  The primary key of a single listing.
	 * @return  vovalue
	 *
	 */
	public function setMemento ( $data )
	{
		$this->value = $data['value'];
		$this->label = $data['label'];
	}


	/**
	 * This method is used to get instance data from the entity. Though it is public by necessity,
	 * this method should not be accessed by any object other than the listingDao.
	 * ( see Memento Design Pattern )
	 *
	 * @return  array  The primary key of a single listing.
	 *
	 */
	public function getMemento ()
	{
		return array(
			'value' => $this->value,
			'label' => $this->label
			);
	}


	/*	ACCESSORS ******************************************************************************* */

	/**
	 * GETTER: This method is a getter for the content property.
	 *
	 * @return  string
	 *
	 */
	public function getValue ()
	{
		return $this->value;
	}

	/**
	 * GETTER: This method is a getter for the content property.
	 *
	 * @return  string
	 *
	 */
	public function getLabel ()
	{
		return $this->label;
	}


}
