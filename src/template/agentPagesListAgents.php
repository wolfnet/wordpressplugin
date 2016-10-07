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
	$linkBase = $_SERVER['PHP_SELF'] . '/';
}

// Remove any page number from link base
$paginationLinkBase = preg_replace('/\/[0-9]+/', '', $linkBase);

if (!function_exists('paginate')) {

	function paginate ($linkBase, $page, $total, $numPerPage) {
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

		if (($page * $numPerPage) > $numPerPage) {
			$output .= '<li><a href="' . $linkBase . ($page - 1) . '">Previous</a></li>';
		}

		for ($i = 1; $i <= $iterate; $i++) {
			if ($i == $page) {
				$output .= '<li class="wolfnet_selected"><span>' . $i . '</span></li>';
			} else {
				$output .= '<li><a href="' . $linkBase . $i . '">' . $i . '</a></li>';
			}
		}

		if(($page * $numPerPage) < $total) {
			$output .= '<li><a href="' . $linkBase . ($page + 1) . '">Next</a></li>';
		}

		$output .= "</ul>";

		return $output;

	}

}

?>


<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_ao wolfnet_aoAgentsList">

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

		<?php echo $agentsHtml; ?>

	</div>

	<div class="wolfnet_clearfix"></div>

	<?php echo paginate($paginationLinkBase, $page, $totalrows, $numperpage); ?>

</div>


<script type="text/javascript">

	jQuery(function ($) {

		var $aoWidget = $('#<?php echo $instance_id; ?>');

		// Resize item boxes to height of tallest one.

		var $aoItems = $aoWidget.find('.wolfnet_aoItem'),
			itemSections = [
				{ name: 'contact',  selector: '.wolfnet_aoContact',  maxHeight: 0,  origMaxHeight: 0 },
				{ name: 'links',    selector: '.wolfnet_aoLinks',    maxHeight: 0,  origMaxHeight: 0 },
				{ name: 'info',     selector: '.wolfnet_aoInfo .wolfnet_aoActions',  maxHeight: 0,  origMaxHeight: 0 },
				{ name: 'body',     selector: '.wolfnet_aoBody',     maxHeight: 0,  origMaxHeight: 0, alwaysResize: true },
				{ name: 'footer',   selector: '.wolfnet_aoFooter',   maxHeight: 0,  origMaxHeight: 0 },
				{ name: 'item',     selector: '.wolfnet_aoItem',     maxHeight: 0,  origMaxHeight: 0 }
			],
			$aoHeader = $aoWidget.find('.wolfnet_agentOfficeHeader');

		var resizeComplete = function (data) {
			for (var i=0, l=data.length; i<l; i++) {
				if (data[i].hasOwnProperty('name') && (data[i].name === 'body')) {
					$aoItems.find('.wolfnet_aoImage').height(data[i].maxHeight);
					break;
				}
			}
		};

		var resizeTimeout;
		$(window).resize(function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function () {
				wolfnet.resizeAOItems($aoItems, itemSections, $aoHeader, resizeComplete);
			}, 500);
		});

		wolfnet.resizeAOItems($aoItems, itemSections, $aoHeader, resizeComplete);

	});

</script>
