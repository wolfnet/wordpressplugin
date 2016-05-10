<?php

/**
 *
 * @title         agentPagesNav.php
 * @copyright     Copyright (c) 2016, WolfNet Technologies, LLC
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

if (array_key_exists("REDIRECT_URL", $_SERVER)) {
	$linkBase = $_SERVER['REDIRECT_URL'];
} else {
	$linkBase = $_SERVER['PHP_SELF'];
}

$searchPlaceholder = ( $isAgent ? 'search by agent last name' : 'search by office name' );

?>

<div class="wolfnet_agentOfficeNav">

	<div class="wnt-btn-group">
		<a class="wnt-btn <?php if ($isAgent) { echo 'wnt-btn-active'; } ?>"
		 href="?search#post-<?php echo get_the_id(); ?>">Agents</a>
		<a class="wnt-btn <?php if (!$isAgent) { echo 'wnt-btn-active'; } ?>"
		 href="<?php echo $linkBase . '#post-' . get_the_id(); ?>">Offices</a>
	</div>

	<form name="wolfnet_agentSearch" class="wolfnet_agentSearch" method="post"
	 action="<?php echo $linkBase . "?search#post-" . get_the_id(); ?>">
		<?php // No office ID as a hidden field. We want to search all offices ?>
		<span class="wolfnet_agentCriteria">
			<span class="wnt-icon wnt-icon-search"></span>
			<input type="text" name="agentCriteria"
			 value="<?php echo (strlen($agentCriteria) > 0) ? $agentCriteria : ''; ?>"
			 placeholder="<?php echo $searchPlaceholder; ?>" />
		</span>
		<button type="submit" name="agentSearch" class="wolfnet_agentSearchButton">Search</button>
	</form>

</div>
