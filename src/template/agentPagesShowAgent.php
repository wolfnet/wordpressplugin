<?php

/**
 *
 * @title         agentPagesShowAgent.php
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
if(!function_exists('formatUrl')) {
	function formatUrl($url) 
	{
		$cleanUrl = $url;
		if(strpos($url, "http://") === false) {
			$cleanUrl = "http://" . $cleanUrl;
		}
		return '<a href="' . $cleanUrl . '">' . str_replace("http://", "", $cleanUrl) . '</a>';
	}
}
?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_agentShow">

<?php

if(strlen($detailtitle) > 0) {
	echo '<h2>' . $detailtitle . '</h2>';
}

if(array_key_exists('HTTP_REFERER', $_SERVER)) {
		$link = $_SERVER['HTTP_REFERER'];
		if(array_key_exists('agentCriteria', $_REQUEST) && strlen($_REQUEST['agentCriteria']) > 0) {
			$link .= '&agentCriteria=' . $_REQUEST['agentCriteria'];
		}
		if($officeId != '' && strpos($link, 'officeId') === false) {
			$link .= '&officeId=' . $officeId;
		}
		$link .= '#post-' . get_the_id();
		echo '<div class="wolfnet_back"><a href="' . $link . '">Back</a></div>';
} else {
?>

<div class="wolfnet_viewAll">
	<a href="?search">Click here</a> to view all agents and staff.
</div>

<?php
}

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
			if(strlen($agent['title']) > 0) {
				echo '<div class="wolfnet_agentTitle">';
				echo $agent['title'];
				echo '</div>';
			}

			if(strlen($agent['business_name']) > 0) {
				echo '<div class="wolfnet_agentBusiness">';
				echo $agent['business_name'];
				echo '</div>';
			}
			?>

			<div class="wolfnet_agentContact">
				<?php 
				$agentContact = array(
					'office_phone_number' => 'Office',
					'primary_contact_phone' => 'Primary Phone',
					'mobile_phone' => 'Mobile Phone',
					'home_phone_number' => 'Home Phone',
					'fax_number' => 'Fax',
					'pager_number' => 'Pager',
					'toll_free_phone_number' => 'Toll Free',
				);

				foreach($agentContact as $key => $label) {
					if(strlen($agent[$key]) > 0) {
						echo '<div class="wolfnet_$key">';
						echo "<strong>$label:</strong> " . $agent[$key];
						echo '</div>';
					}
				}

				if(strlen($agent['email_address']) > 0) {
					echo '<div class="wolfnet_agentOfficeEmail">';
					echo '<strong>Email:</strong> <a href="?contact=' 
						. $agent['agent_id'] . '#post-' . get_the_id() . '">' 
						. $agent['first_name'] . ' ' 
						. $agent['last_name'] . '</a>';
					echo '</div>';
				}

				if(strlen($agent['web_url']) > 0) {
					echo '<div class="wolfnet_agentUrl">';
					echo "<strong>Website:</strong> " . formatUrl($agent['web_url']);
					echo '</div>';
				}
				?>
			</div>
		</div>
		<div class="wolfnet_agentBio">
			<?php
			
			// Agent links
			$agentLinks = array(
				'facebook_url'    => 'Facebook',
				'twitter_url'     => 'Twitter',
				'linkedin_url'    => 'LinkedIn',
				'google_plus_url' => 'Google+',
				'youtube_url'     => 'YouTube',
				'pinterest_url'   => 'Pinterest',
				'instagram_url'   => 'Instagram',
			);

			echo '<ul class="wolfnet_agentLinks">';
			foreach($agentLinks as $key => $label) {
				if(strlen($agent[$key]) > 0) {
					echo "<li><strong>$label:</strong> " . formatUrl($agent[$key]) . "</li>";
				}
			}
			echo '</ul>';

			// Agent text areas
			$agentBio = array(
				'bio'                => 'Bio',
				'experience'         => 'Experience',
				'education'          => 'Education',
				'areas_served'       => 'Areas Served',
				'services_available' => 'Services Available',
				'awards'             => 'Awards',
				'specialty'          => 'Speciality',
				'motto_quote'        => 'Motto',
				'designations'       => 'Designations',
			);

			foreach($agentBio as $key => $label) {
				if(strlen($agent[$key]) > 0) {
					echo '<span class="wolfnet_agentSection">';
					echo '<p><strong>' . $label . '</strong><br />' . $agent[$key] . '</p>';
					echo '</span>';
				}
			}

			if(strlen($agent['optional_field_label']) > 0 && 
				strlen($agent['optional_field_value']) > 0) {

				echo '<span class="wolfnet_agentSection">';
				echo '<p><strong>' . $agent['optional_field_label'] . '</strong><br />';
				echo $agent['optional_field_value'] . '</p>';
				echo '</span>';
			}

			// Favorite links
			$showFavoriteLinks = false;
			$favoriteLinks = '';
			for($i = 1; $i <= 9; $i++) {
				if(strlen($agent['favorite_link_name_' . $i]) > 0 && 
					strlen($agent['favorite_link_url_' . $i]) > 0) {

					$showFavoriteLinks = true;
					$favoriteLinks .= "<li><strong>" . $agent['favorite_link_name_' . $i] . ":</strong> ";
					$favoriteLinks .= formatUrl($agent['favorite_link_url_' . $i]) . "</li>";
				}
			}

			if($showFavoriteLinks) {
				echo '<strong>Favorite Links</strong>';
				echo '<ul class="wolfnet_agentLinks">';
				echo $favoriteLinks;
				echo '</ul>';
			}
			?>
		</div>
	</div>

<?php
} // end if display_agent

if($activeListingCount > 0) {
	echo '<p><strong>Featured Listings</strong></p>';
	echo $activeListingHTML;
}

if($activeListingCount > 10) {
	echo '<a href="' . $searchUrl . '">';
	echo "View all " . $activeListingCount . " of " . $agent['first_name'] . "'s listings.";
	echo "</a>";
}

if($soldListingCount > 0) {
	echo '<p><strong>Sold Listings</strong></p>';
	echo $soldListingHTML;
}

if($soldListingCount > 10) {
	echo '<a href="' . $soldSearchUrl . '">';
	echo "View all " . $soldListingCount . " of " . $agent['first_name'] . "'s sold listings.";
	echo "</a>";
}
?>

</div>