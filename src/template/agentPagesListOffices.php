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

?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_officesList">

	<?php
		if (array_key_exists("REDIRECT_URL", $_SERVER)) {
			$linkBase = $_SERVER['REDIRECT_URL'];
		} else {
			$linkBase = $_SERVER['PHP_SELF'];
		}
	?>

	<div class="wolfnet_officeHeader">

		<?php
			if (strlen($officetitle) > 0) {
				echo '<h2>' . $officetitle . '</h2>';
			}
		?>

		<form name="wolfnet_agentSearch" class="wolfnet_agentSearch" method="POST"
			action="<?php echo $linkBase . "?search#post-" . get_the_id(); ?>">
			<?php // No office ID as a hidden field. We want to search all offices ?>

			<input type="text" name="agentCriteria" class="wolfnet_agentCriteria"
				value="<?php echo (strlen($agentCriteria) > 0) ? $agentCriteria : ''; ?>" />
			<!-- <input type="submit" name="agentSearch" class="wolfnet_agentSearchButton" value="Search" /> -->
		</form>

		<div class="wolfnet_agentOfficeView">
			<div><a href="?search#post-<?php echo get_the_id(); ?>">Agents</a></div>
			<div class="selected">Offices</div>
		</div>

	</div>

<?php

	foreach ($offices as $office) {

		if ($office['office_id'] != '') {

			$officeLink = $linkBase . '?officeId=' . $office['office_id'];
			$officeLink .= '#post-' . get_the_id();

			$searchLink = $office['search_solution_url'] . "/?action=newsearch";
			$searchLink .= "&office_id=" . $office['office_id'];
			$searchLink .= "&ld_action=find_office";

			$searchResultLink = $office['search_solution_url'] . "/?action=newsearchsession";
			$searchResultLink .= "&office_id=" . $office['office_id'];

			$contactLink = "?contactOffice=" . $office['office_id'];
			$contactLink .= "#post-" . get_the_id();

?>

			<div class="wolfnet_officePreview">

				<div class="wolfnet_officeImage">
					<?php
						if (strlen($office['medium_url']) > 0) {
							echo '<a href="' . $officeLink . '">';
							echo '<img src="' . $office['medium_url'] . '" />';
							echo '</a>';
						}
					?>
				</div>


				<div class="wolfnet_officeData">

					<div class="wolfnet_officeContact">

						<div class="wolfnet_officeName">
							<?php echo '<a href="' . $officeLink . '">' . $office['name'] . '</a>'; ?>
						</div>

						<hr class="wolfnet_officeRule" />

						<div class="wolfnet_officeAddress">
							<?php
								if (strlen($office['address_1']) > 0) {
									echo $office['address_1'] . ' ' . $office['address_2'];
									echo '<br />';
									echo $office['city'] . ', ' . $office['state'] . ' ';
									echo $office ['postal_code'];
								} else {
									// TODO: Replace with a min-height style applied to parent
									echo '&nbsp;<br />&nbsp;';
								}
							?>
						</div>

					</div>

					<ul class="wolfnet_officeLinks">

						<?php

							// TODO: Replace extraSpace usage with a min-height style
							$extraSpace = '';

							if (strlen($office['phone_number']) > 0) {
								echo '<li><span class="wnt-icon wnt-icon-phone"></span> ';
								echo $office['phone_number'] . '</li>';
							} else {
								$extraSpace .= '<li>&nbsp;</li>';
							}

							if (strlen($office['fax_number']) > 0) {
								echo '<li><span  class="wnt-icon wnt-icon-fax"></span> ';
								echo $office['fax_number'] . '</li>';
							} else {
								$extraSpace .= '<li>&nbsp;</li>';
							}

						?>

						<li>
							<span class="wnt-icon wnt-icon-mail3"></span>
							<a href="<?php echo $contactLink; ?>">Contact Us</a>
						</li>
						<li>
							<span class="wnt-icon wnt-icon-location"></span>
							<a href="<?php echo $searchLink; ?>">Search All Area Listings</a>
						</li>

						<?php echo $extraSpace; ?>

					</ul>

					<div class="officeButton wolfnet_officeLinkLeft">
						<a href="<?php echo $officeLink; ?>">Meet Our Agents</a>
					</div>
					<div class="officeButton wolfnet_officeLink">
						<a href="<?php echo $searchResultLink; ?>">Featured Listings</a>
					</div>

				</div>

			</div>

<?php
		}
	} // end foreach
?>

	<div class="wolfnet_clearfix"></div>

</div>


<script type="text/javascript">

	jQuery(function ($) {
		$(window).load(function () {
			// Resize office boxes to height of tallest one.
			var $offices = $('#<?php echo $instance_id; ?> .wolfnet_officePreview');
			var maxHeight<?php echo $instance_id; ?> = 0;
			$offices.each(function () {
				if ($(this).height() > maxHeight<?php echo $instance_id; ?>) {
					maxHeight<?php echo $instance_id; ?> = $(this).height();
				}
			});

			$('#<?php echo $instance_id; ?> .wolfnet_officePreview').height(
				maxHeight<?php echo $instance_id; ?>
			);
		});
	});

</script>
