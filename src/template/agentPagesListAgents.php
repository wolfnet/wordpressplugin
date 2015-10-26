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

?>

<?php
if(array_key_exists("REDIRECT_URL", $_SERVER)) {
	$linkBase = $_SERVER['REDIRECT_URL'];
} else {
	$linkBase = $_SERVER['PHP_SELF'];
}

function paginate($page, $total, $numPerPage) 
{
	if($total <= $numPerPage) {
		return '';
	}
	
	$output = '<ul class="wolfnet_agentPagination">';
	$iterate = ceil($total / $numPerPage);

	if(($page * $numPerPage) > $numPerPage) {
		$output .= '<li><a href="?page=' . ($page - 1) . '">';
		$output .= 'Previous</a>';
	}

	for($i = 1; $i <= $iterate; $i++) {
		if($i == $page) {
			$output .= '<li class="wolfnet_selected">' . $i . '</li>';
		} else {
			$output .= '<li><a href="?page=' . $i . '">' . $i . '</a></li>';
		}
	}

	if(($page * $numPerPage) < $total) {
		$output .= '<li><a href="?page=' . ($page + 1) . '">';
		$output .= 'Next</a>';
	}

	$output .= "</ul>";
	return $output;
}
?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_agentsList">

<?php
foreach($agents as $agent) {
	if($agent['display_agent']) {
		$agentLink = $linkBase . '?agent=' . $agent['agent_id'];
?>

	<div class="wolfnet_agentPreview">
		<?php 
		if(strlen($agent['thumbnail_url']) > 0) {
			echo '<div class="wolfnet_agentImage">';
			echo '<a href="' . $agentLink . '">';
			echo "<img src=\"{$agent['thumbnail_url']}\" />";
			echo '</a>';
			echo '</div>';
		} 
		?>

		<div class="wolfnet_agentInfo">
			<div class="wolfnet_agentName">
				<?php 
					echo '<a href="' . $agentLink . '">';
					echo $agent['first_name'] . " " . $agent['last_name']; 
					echo '</a>';
				?>
			</div>

			<?php if(strlen($agent['business_name']) > 0) {
				echo '<div class="wolfnet_agentBusiness">';
				echo $agent['business_name'];
				echo '</div>';
			}
			?>

			<div class="wolfnet_agentContact">
				<?php 
				if(strlen($agent['office_phone_number']) > 0) {
					echo '<div class="wolfnet_agentOfficePhone">';
					echo "Office: " . $agent['office_phone_number'];
					echo '</div>';
				}

				if(strlen($agent['mobile_phone']) > 0) {
					echo '<div class="wolfnet_agentMobilePhone">';
					echo "Mobile: " . $agent['mobile_phone'];
					echo '</div>';
				}

				if(strlen($agent['fax_number']) > 0) {
					echo '<div class="wolfnet_agentFax">';
					echo "Fax: " . $agent['fax_number'];
					echo '</div>';
				}
				?>
			</div>
		</div>
	</div>

<?php
	} // end if display_agent
} // end foreach

echo paginate($page, $totalrows, $numperpage); 

?>

</div>

<script type="text/javascript">
jQuery(function($) {
	$(window).load(function() {
		// Resize agent boxes to height of tallest one.
		var $agents = $('.wolfnet_agentPreview');
		var maxHeight = 0;
		$agents.each(function() {
			if($(this).height() > maxHeight) {
				maxHeight = $(this).height();
			}
		});
		$('.wolfnet_agentPreview').height(maxHeight);
	});
});
</script>