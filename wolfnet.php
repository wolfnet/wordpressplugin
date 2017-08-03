<?php

/**
 * Plugin Name:  WolfNet IDX for WordPress
 * Plugin URI:   https://wordpress.org/plugins/wolfnet-idx-for-wordpress
 * Description:  The WolfNet IDX for WordPress plugin provides IDX search solution integration with
 *               any WordPress website.
 * Version:      {X.X.X}
 * Author:       WolfNet Technologies, LLC.
 * Author URI:   http://www.wolfnet.com
 *
 *
 * @title         wolfnet.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
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
 */

require_once dirname(__FILE__) . '/bootstrap/autoload.php';

$GLOBALS['wolfnet'] = new Wolfnet_Plugin();
$GLOBALS['wolfnet']->setEpFlag(EP_ALL);
