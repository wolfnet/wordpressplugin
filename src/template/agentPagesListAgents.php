<?php

/**
 *
 * @title         agentPagesListAgents.php
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
	$linkBase = $_SERVER['PHP_SELF'];
}

$postHash = '#post-' . get_the_id();

$allAgentsLink = $linkBase . '?search' . $postHash;

if (!function_exists('paginate')) {

	function paginate ($page, $total, $numPerPage, $postHash, $officeId = '', $search = null, $sort = 'name') {
		/*
		 * Note: We're using "agentpage" instead of just "page" as out URL variable
		 * here because Wordpress uses page internally for their own pagination
		 * and causes things to not work for us if we try to coopt it.
		 */

		if ($total <= $numPerPage) {
			return '';
		}

		$output = '<ul class="wolfnet_agentPagination">';
		$iterate = ceil($total / $numPerPage);

		if (!is_null($search)) {
			$linkBase = '?search&agentCriteria=' . $search . '&';
		} else {
			$linkBase = '?';
		}

		if ($officeId != '') {
			$linkBase .= 'officeId=' . $officeId . '&';
		}

		$linkBase .= 'agentSort=' . $sort . '&';

		if (($page * $numPerPage) > $numPerPage) {
			$output .= '<li><a href="' . $linkBase . 'agentpage=' . ($page - 1) . '">Previous</a></li>';
		}

		for ($i = 1; $i <= $iterate; $i++) {
			if ($i == $page) {
				$output .= '<li class="wolfnet_selected">' . $i . '</li>';
			} else {
				$output .= '<li><a href="' . $linkBase . 'agentpage=' . $i . $postHash . '">' . $i . '</a></li>';
			}
		}

		if(($page * $numPerPage) < $total) {
			$output .= '<li><a href="' . $linkBase . 'agentpage=' . ($page + 1) . $postHash . '">Next</a></li>';
		}

		$output .= "</ul>";

		return $output;

	}

}

?>


<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_agentsList">

	<div class="wolfnet_agentOfficeHeader">

		<?php

			if (strlen($agenttitle) > 0) {
				echo '<h2>' . $agenttitle . '</h2>';
			}

			echo $agentsNav;

		?>

	</div>

	<div class="wolfnet_clearfix"></div>

	<?php if ($officeCount > 1) { ?>
		<label for="agentSort">Sort By:</label>
		<select name="agentSort" class="wolfnet_agentSort">
			<option value="name" <?php echo ($agentSort == 'name') ? 'selected="selected"' : ''; ?>>Name</option>
			<option value="office_id" <?php echo ($agentSort == 'office_id') ? 'selected="selected"' : ''; ?>>Office</option>
		</select>
		<div class="wolfnet_clearfix"></div>
	<?php } ?>

	<?php
		if (count($agents) == 0) {
			if (array_key_exists('agentCriteria', $_REQUEST) && strlen($_REQUEST['agentCriteria']) > 0) {
	?>
				<p class="wolfnet_noResults">There are no matching agents. Please try your search again.</p>
	<?php
			} elseif (strlen($officeId) > 0) {
	?>
				<p class="wolfnet_noResults">
					There are currently no agents in this office. Go
					<a href="<?php echo $allAgentsLink; ?>">back</a>
					to see agents in our other offices.
				</p>
	<?php
			}
		}
	?>

	<div class="wolfnet_agents">

		<?php

			foreach ($agents as $agent) {

				if ($agent['display_agent']) {

					$agentLink = $linkBase . '?agentId=' . $agent['agent_id']
						. (
							array_key_exists('agentCriteria', $_REQUEST) && (strlen($_REQUEST['agentCriteria']) > 0) ?
							'&agentCriteria=' . $_REQUEST['agentCriteria'] : ''
						)
						. ($officeId != '' ? '&officeId=' . $officeId : '')
						. $postHash;

					$contactLink = '?contact=' . $agent['agent_id'] . $postHash;

		?>

					<div class="wolfnet_agentPreview">

						<div class="wolfnet_agentImage">
							<?php
								if (strlen($agent['thumbnail_url']) > 0) {
									echo '<a href="' . $agentLink . '">';
									echo '<img src="' . $agent['thumbnail_url'] . '" />';
									echo '</a>';
								}
							?>
						</div>

						<div class="wolfnet_agentInfo">

							<div class="wolfnet_agentContact">

								<div class="wolfnet_agentName">
									<?php
										echo '<a href="' . $agentLink . '">';
										echo $agent['first_name'] . ' ' . $agent['last_name'];
										echo '</a>';
									?>
								</div>

								<hr />

								<?php if (strlen($agent['business_name']) > 0) {
									echo '<div class="wolfnet_agentBusiness">';
									echo $agent['business_name'];
									echo '</div>';
								} ?>

							</div>

							<ul class="wolfnet_aoLinks">

								<?php

									if (strlen($agent['office_phone_number']) > 0) {
										echo '<li><span class="wnt-icon wnt-icon-phone"></span> '
											. '<span class="wnt-visuallyhidden">Office phone:</span> '
											. $agent['office_phone_number'] . '</li>';
									}

									if (strlen($agent['mobile_phone']) > 0) {
										echo '<li><span class="wnt-icon wnt-icon-mobile"></span> '
											. '<span class="wnt-visuallyhidden">Mobile phone:</span> '
											. $agent['mobile_phone'] . '</li>';
									}

									if (strlen($agent['email_address']) > 0) {
										echo '<li><span class="wnt-icon wnt-icon-envelope"></span> '
											. '<span class="wnt-visuallyhidden">Email:</span> '
											. '<a href="' . $contactLink . '">'
											. $agent['first_name'] . ' ' . $agent['last_name']
											. '</a></li>';
									}

								?>

							</ul>

						</div>

					</div>

		<?php

				} // end if display_agent

			} // end foreach

		?>

	</div>

	<div class="wolfnet_clearfix"></div>

	<?php echo paginate($page, $totalrows, $numperpage, $postHash, $officeId, $agentCriteria, $agentSort); ?>

</div>


<script type="text/javascript">

	jQuery(function ($) {

		$(window).load(function () {

			// Resize agent boxes to height of tallest one.
			var $agents = $('#<?php echo $instance_id; ?> .wolfnet_agentPreview');
			var maxHeight<?php echo $instance_id; ?> = 0;
			$agents.each(function() {
				if($(this).height() > maxHeight<?php echo $instance_id; ?>) {
					maxHeight<?php echo $instance_id; ?> = $(this).height();
				}
			});
			$('#<?php echo $instance_id; ?> .wolfnet_agentPreview').height(maxHeight<?php echo $instance_id; ?>);

			<?php if ($officeCount > 1) { ?>

				$('#<?php echo $instance_id; ?> .wolfnet_agentSort').change(function () {
					var href = $(location).attr('href');
					var sortPos = href.indexOf('agentSort');

					if (sortPos > -1) {
						if ($(this).val() == 'name') {
							href = href.replace('agentSort=office_id', 'agentSort=name');
						} else {
							href = href.replace('agentSort=name', 'agentSort=office_id');
						}
					} else {
						// We need to put the sort param before the anchor.
						var hashPos = href.indexOf('#');
						if (hashPos > -1) {
							href = href.replace('#', '&agentSort=' + $(this).val() + '#');
						} else {
							href += '&agentSort=' + $(this).val();
						}
					}

					// Remove page is there since the pages won't correlate between
					// offices and agents.
					var pagePos = href.indexOf('agentpage');
					if (pagePos > -1) {
						href = href.replace(/&agentpage=[0-9]+/gi, '');
					}

					window.location.href = href;

				});

			<?php } ?>

		});

	});

</script>
