<?php

/**
 * This is the filmStripWidget object. This object inherites from the base WP_Widget object and
 * defines the display and functionality of this specific widget.
 *
 * @see http://codex.wordpress.org/Widgets_API
 * @see http://core.trac.wordpress.org/browser/tags/3.3.2/wp-includes/widgets.php
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing
 * @title         propertyListShortcode.php
 * @extends       com_wolfnet_wordpress_listing_listingGridShortcode
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_listing_propertyListShortcode
extends com_wolfnet_wordpress_listing_listingGridShortcode
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the tag name which is used to identify shorcodes when they are encountered
	 * in Posts and Pages.
	 *
	 * @type  string
	 *
	 */
	public $tag = 'WolfNetPropertyList,wolfnetpropertylist,WOLFNETPROPERTYLIST,wnt_list';


}