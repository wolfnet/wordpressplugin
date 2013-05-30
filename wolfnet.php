<?php

/**
 * Plugin Name:  WolfNet IDX for WordPress
 * Plugin URI:   http://wordpress.wolfnet.com
 * Description:  The WolfNet IDX for WordPress plugin provides IDX search solution integration with any WordPress website.
 * Version:      {X.X.X}
 * Author:       WolfNet Technologies, LLC.
 * Author URI:   http://www.wolfnet.com
 *
 * @title         wolfnet.php
 * @contributors  AJ Michels (http://aj.michels@wolfnet.com)
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
 */

require_once dirname(__FILE__) . '/vendor/wnt_autoload.php';

$wolfnetConfig = new WNT_WP_Config(
    plugins_url(dirname(__FILE__))
    );

$wolfnet = WNT_WP_Plugin::run($wolfnetConfig);
