<?php

/**
 *
 * @title         agentPagesListAgents.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_agentsList">

<?php

if(array_key_exists("REDIRECT_URL", $_SERVER)) {
	$linkBase = $_SERVER['REDIRECT_URL'];
} else {
	$linkBase = $_SERVER['PHP_SELF'];
}

foreach($agents as $agent) {
	if($agent['display_agent']) {
		$agentLink = $linkBase . '?agent=' . $agent['agent_office_guid'];
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
				?>
			</div>
		</div>
	</div>

<?php
	} // end if display_agent
} // end foreach
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