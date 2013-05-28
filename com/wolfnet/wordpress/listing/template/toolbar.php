<?php

/**
 * This is an HTML template file for the Grid Widget. This template is meant to wrap a
 * set of listing views. This file should ideally contain very little PHP.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    listing.template
 * @title         toolbar.php
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

?>

<div class="wolfnet_toolbar <?php echo $toolbarClass; ?>" data-numrows="<?php echo $numrows ?>" data-startrow="<?php echo $startrow ?>">
	<a href="<?php echo $prevLink; ?>" title="Previous Page" class="wolfnet_page_nav wolfnet_page_nav_prev <?php echo $prevClass; ?>" rel="follow">
		<span>Previous</span>
	</a>
	<span class="wolfnet_page_info">
		<span class="wolfnet_page_items">
			<span class="wolfnet_page_start"><?php echo $startrow; ?></span>-<span class="wolfnet_page_end"><?php echo $lastitem; ?></span>
			 of
			<span class="wolfnet_page_total"><?php echo $maxresults; ?></span>
		</span>
	</span>
	<a href="<?php echo $nextLink; ?>" title="Next Page" class="wolfnet_page_nav wolfnet_page_nav_next <?php echo $nextClass; ?>" rel="follow">
		<span>Next</span>
	</a>
</div>
