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

$linkExtra = (
		array_key_exists('agentCriteria', $_REQUEST) && (strlen($_REQUEST['agentCriteria']) > 0) ?
		'&agentCriteria=' . $_REQUEST['agentCriteria'] : ''
	)
	. ($officeId != '' ? '&officeId=' . $officeId : '')
	. $postHash;

$allAgentsLink = $linkBase . '?agentSearch' . $postHash;

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
			$linkBase = '?agentSearch&agentCriteria=' . $search . '&';
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


<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_aoAgentsList">

	<div class="wolfnet_agentOfficeHeader">

		<?php

			if (strlen($agenttitle) > 0) {
				echo '<h2>' . $agenttitle . '</h2>';
			}

			echo $agentsNav;

		?>

	</div>

	<div class="wolfnet_clearfix"></div>

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

	<div class="wolfnet_aoAgents">

		<?php

			foreach ($agents as $agent) {

				if ($agent['display_agent']) {

					$agentLink = $linkBase . '?agentId=' . $agent['agent_id'] . $linkExtra;

					$contactLink = $linkBase . '?contact=' . $agent['agent_id'] . $linkExtra;

		?>

					<div class="wolfnet_aoItem">

						<div class="wolfnet_aoBody">


							<a href="<?php echo $agentLink; ?>">
								<div class="wolfnet_aoImage"
								 style="background-image: url('<?php echo $agent['medium_url']; ?>');">
									<img src="<?php echo $agent['medium_url']; ?>"
									 onerror="this.className += ' wnt-hidden';" />
								</div>
							</a>

							<div class="wolfnet_aoInfo">

								<div class="wolfnet_aoContact">

									<div class="wolfnet_aoTitle">
										<?php
											echo '<a href="' . $agentLink . '">';
											echo $agent['first_name'] . ' ' . $agent['last_name'];
											echo '</a>';
										?>
									</div>

									<hr />

									<div class="wolfnet_aoSubTitle">
										<?php $agent['business_name']; ?>
									</div>

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
											echo '<li><span class="wnt-icon wnt-icon-mail"></span> '
												. '<span class="wnt-visuallyhidden">Contact:</span> '
												. '<a href="' . $contactLink . '">'
												. $agent['first_name'] . ' ' . $agent['last_name']
												. '</a></li>';
										}

										if (strlen($agent['address_1']) > 0) {
											echo '<li><span class="wnt-icon wnt-icon-location"></span> '
												. '<span class="wnt-visuallyhidden">Address:</span> '
												. $agent['address_1'] . ' ' . $agent['address_2']
												. '<br />'
												. $agent['city'] . ', ' . $agent['state'] . ' '
												. $agent ['zip_code'];
										}

									?>

								</ul>

								<div class="wolfnet_aoActions wolfnet_clearfix">
									<div class="wolfnet_aoAction">
										<a class="wnt-btn wnt-btn-secondary"
										 href="<?php echo $agentLink; ?>">View Profile</a>
									</div>
									<?php if (strlen($agent['web_url']) > 0) { ?>
										<div class="wolfnet_aoAction">
											<a class="wnt-btn wnt-btn-primary" target="_blank"
											 href="<?php echo $agent['web_url']; ?>">View Website</a>
										</div>
									<?php } ?>
								</div>

							</div>

						</div>

						<div class="wolfnet_aoFooter">
							<div class="wolfnet_aoActions">
								<div class="wolfnet_aoAction">
									<a class="wnt-btn wnt-btn-secondary"
									 href="<?php echo $agentLink; ?>">View Profile</a>
								</div>
								<?php if (strlen($agent['web_url']) > 0) { ?>
									<div class="wolfnet_aoAction">
										<a class="wnt-btn wnt-btn-primary" target="_blank"
										 href="<?php echo $agent['web_url']; ?>">View Website</a>
									</div>
								<?php } ?>
							</div>
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

		var $aoWidget = $('#<?php echo $instance_id; ?>');

		// Resize item boxes to height of tallest one.

		var $aoItems = $aoWidget.find('.wolfnet_aoItem'),
			itemSections = [
				{ selector: '.wolfnet_aoContact',  maxHeight: 0 },
				{ selector: '.wolfnet_aoLinks',    maxHeight: 0 },
				{ selector: '.wolfnet_aoInfo .wolfnet_aoActions',  maxHeight: 0 },
				{ selector: '.wolfnet_aoBody',     maxHeight: 0 },
				{ selector: '.wolfnet_aoFooter',   maxHeight: 0 },
				{ selector: '.wolfnet_aoItem',     maxHeight: 0 }
			];

		wolfnet.resizeAOItems($aoItems, itemSections);

		var resizeTimeout;
		$(window).resize(function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function () {
				wolfnet.resizeAOItems($aoItems, itemSections);
			}, 500);
		});

	});

</script>
