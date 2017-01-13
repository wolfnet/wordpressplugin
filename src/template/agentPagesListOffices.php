<?php

/**
 *
 * @title         agentPagesListOffices.php
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

if (array_key_exists("REDIRECT_URL", $_SERVER)) {
	$linkBase = $_SERVER['REDIRECT_URL'];
} else {
	$linkBase = $_SERVER['PHP_SELF'] . '/';
}

?>


<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_ao wolfnet_aoOfficesList">

	<div class="wolfnet_agentOfficeHeader">

		<?php

			if (strlen($officetitle) > 0) {
				echo '<h2>' . $officetitle . '</h2>';
			}

			echo $agentsNav;

		?>

	</div>

	<div class="wolfnet_clearfix"></div>

	<?php
		if (
			(count($offices) == 0) &&
			array_key_exists('officeCriteria', $_REQUEST) &&
			(strlen($_REQUEST['officeCriteria']) > 0)
		) {
	?>
			<p class="wolfnet_noResults">There are no matching offices. Please try your search again.</p>
	<?php
		}
	?>

	<div class="wolfnet_aoOffices">

		<?php echo $officesHtml; ?>

	</div>

	<div class="wolfnet_clearfix"></div>

</div>
