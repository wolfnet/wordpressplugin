<?php

/**
 *
 * @title         agentPagesShowAgent.php
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

<?php
function formatUrl($url) {
	$cleanUrl = $url;
	if(strpos($url, "http://") === false) {
		$cleanUrl = "http://" . $cleanUrl;
	}
	return '<a href="' . $cleanUrl . '">' . str_replace("http://", "", $cleanUrl) . '</a>';
}
?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_agentShow">

<?php
if($agent['display_agent']) {
?>

	<div class="wolfnet_agent">
		<div class="wolfnet_agentImage">
			<?php 
			if(strlen($agent['image_url']) > 0) {
				echo "<img src=\"{$agent['image_url']}\" />";
			} 
			?>
		</div>

		<div class="wolfnet_agentInfo">
			<div class="wolfnet_agentName">
				<?php 
					echo $agent['first_name'] . " " . $agent['last_name']; 
				?>
			</div>

			<?php 
			if(strlen($agent['business_name']) > 0) {
				echo '<div class="wolfnet_agentBusiness">';
				echo $agent['business_name'];
				echo '</div>';
			}
			?>

			<div class="wolfnet_agentContact">
				<?php 
				if(strlen($agent['office_phone_number']) > 0) {
					echo '<div class="wolfnet_agentOfficePhone">';
					echo "<strong>Office:</strong> " . $agent['office_phone_number'];
					echo '</div>';
				}

				if(strlen($agent['mobile_phone']) > 0) {
					echo '<div class="wolfnet_agentMobilePhone">';
					echo "<strong>Mobile:</strong> " . $agent['mobile_phone'];
					echo '</div>';
				}

				if(strlen($agent['email_address']) > 0) {
					echo '<div class="wolfnet_agentOfficePhone">';
					echo "<strong>Email:</strong> " . $agent['email_address'];
					echo '</div>';
				}

				if(strlen($agent['web_url']) > 0) {
					echo '<div class="wolfnet_agentUrl">';
					echo "<strong>Website:</strong> " . formatUrl($agent['web_url']);
					echo '</div>';
				}

				if(strlen($agent['facebook_url']) > 0) {
					echo '<div class="wolfnet_agentFacebook">';
					echo "<strong>Facebook:</strong> " . formatUrl($agent['facebook_url']);
					echo '</div>';
				}

				if(strlen($agent['areas_served']) > 0) {
					echo '<div class="wolfnet_agentAreasServed">';
					echo "<strong>Areas Served:</strong> " . $agent['areas_served'];
					echo '</div>';
				}
				?>
			</div>
		</div>
		<div class="wolfnet_agentBio">
			<?php
			if(strlen($agent['bio']) > 0) {
				echo '<span class="wolfnet_agentSection">';
				echo '<p><strong>Bio</strong><br />' . $agent['bio'] . '</p>';
				echo '</span>';
			}

			if(strlen($agent['experience']) > 0) {
				echo '<span class="wolfnet_agentSection">';
				echo '<p><strong>Experience</strong><br />' . $agent['experience'] . '</p>';
				echo '</span>';
			}

			if(strlen($agent['education']) > 0) {
				echo '<span class="wolfnet_agentSection">';
				echo '<p><strong>Education</strong><br />' . $agent['education'] . '</p>';
				echo '</span>';
			}

			if(strlen($agent['services_available']) > 0) {
				echo '<span class="wolfnet_agentSection">';
				echo '<p><strong>Services</strong><br />' . $agent['services_available'] . '</p>';
				echo '</span>';
			}

			if(strlen($agent['awards']) > 0) {
				echo '<span class="wolfnet_agentSection">';
				echo '<p><strong>Awards</strong><br />' . $agent['awards'] . '</p>';
				echo '</span>';
			}
			?>
		</div>
	</div>

<?php
} // end if display_agent
?>

</div>