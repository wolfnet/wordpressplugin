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

$postHash = '#post-' . get_the_id();

$agentsLink  = $linkBase . '?agentSearch' . $postHash;
$officesLink = $linkBase . $postHash;

if ($isAgent) {
	$searchPlaceholder = 'search by agent last name';
	$searchAction = $agentsLink;
	$criteriaName = 'agentCriteria';
	$criteriaVal = (strlen($agentCriteria) > 0) ? $agentCriteria : '';
} else {
	$searchPlaceholder = 'search by office name';
	$searchAction = $officesLink;
	$criteriaName = 'officeCriteria';
	$criteriaVal = (strlen($officeCriteria) > 0 ? $officeCriteria : '');
}

?>

<div class="wolfnet_agentOfficeNav">

	<div class="wnt-btn-group">
		<a class="wnt-btn <?php if ($isAgent) { echo 'wnt-btn-active'; } ?>"
		 href="<?php echo $agentsLink; ?>">Agents</a>
		<a class="wnt-btn <?php if (!$isAgent) { echo 'wnt-btn-active'; } ?>"
		 href="<?php echo $officesLink; ?>">Offices</a>
	</div>

	<form name="wolfnet_aoSearch" class="wolfnet_aoSearch" method="post"
	 action="<?php echo $searchAction; ?>">
		<?php // No office ID as a hidden field. We want to search all offices ?>
		<span class="wolfnet_aoCriteria">
			<span class="wnt-icon wnt-icon-search"></span>
			<input type="text" name="<?php echo $criteriaName; ?>"
			 value="<?php echo $criteriaVal; ?>"
			 placeholder="<?php echo $searchPlaceholder; ?>" />
		</span>
		<button type="submit" class="wolfnet_aoSearchButton">Search</button>
	</form>

</div>


<script type="text/javascript">

	jQuery(function ($) {

		// Search field
		var $searchForm = $('.wolfnet_agentOfficeNav .wolfnet_aoSearch');
		var $criteria = $searchForm.find('.wolfnet_aoCriteria');
		$criteria.css({ cursor: 'text' });
		$criteria.click(function () {
			$(this).find('input[name="<?php echo $criteriaName; ?>"]').focus();
		});

	});

</script>

