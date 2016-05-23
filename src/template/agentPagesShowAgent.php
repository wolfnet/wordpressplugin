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


$contactLink = $linkBase . '?contact=' . $agent['agent_id'] . $linkExtra;

$agentsLink  = $linkBase . '?agentSearch&agentCriteria=' . $postHash;


// Agent links
$socialLinks = array(
	array( 'field' => 'facebook_url',     'label' => 'Facebook',   'icon'  => 'facebook' ),
	array( 'field' => 'twitter_url',      'label' => 'Twitter',    'icon'  => 'twitter' ),
	array( 'field' => 'linkedin_url',     'label' => 'LinkedIn',   'icon'  => 'linkedin' ),
	array( 'field' => 'google_plus_url',  'label' => 'Google+',    'icon'  => 'googleplus' ),
	array( 'field' => 'youtube_url',      'label' => 'YouTube',    'icon'  => 'youtube' ),
	array( 'field' => 'pinterest_url',    'label' => 'Pinterest',  'icon'  => 'pinterest' ),
	array( 'field' => 'instagram_url',    'label' => 'Instagram',  'icon'  => 'instagram' ),
);

$contactMethods = array(
	array( 'field' => 'office_phone_number',     'label' => 'Office',     'icon'  => 'office' ),
	array( 'field' => 'primary_contact_phone',   'label' => 'Primary',    'icon'  => 'phone' ),
	array( 'field' => 'mobile_phone',            'label' => 'Mobile',     'icon'  => 'mobile' ),
	array( 'field' => 'home_phone_number',       'label' => 'Home',       'icon'  => 'home' ),
	array( 'field' => 'fax_number',              'label' => 'Fax',        'icon'  => 'fax' ),
	array( 'field' => 'pager_number',            'label' => 'Pager',      'icon'  => 'bell' ),
	array( 'field' => 'toll_free_phone_number',  'label' => 'Toll Free',  'icon'  => 'phone' ),
);


if (!function_exists('formatUrl')) {
	function formatUrl ($url) {
		$cleanUrl = $url;
		if (strpos($url, "http://") === false) {
			$cleanUrl = "http://" . $cleanUrl;
		}
		return '<a href="' . $cleanUrl . '">' . str_replace("http://", "", $cleanUrl) . '</a>';
	}
}


?>

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_aoAgentDetails">

<?php

	if (strlen($detailtitle) > 0) {
		echo '<h2>' . $detailtitle . '</h2>';
	}

	if (array_key_exists('REDIRECT_URL', $_SERVER) && $officeId != '') {

		$link = $_SERVER['REDIRECT_URL'] . "?";
		if (array_key_exists('agentCriteria', $_REQUEST) && strlen($_REQUEST['agentCriteria']) > 0) {
			$link .= 'agentCriteria=' . $_REQUEST['agentCriteria'] . '&';
		}
		if ($officeId != '' && strpos($link, 'officeId') === false) {
			$link .= 'officeId=' . $officeId;
		}
		$link .= '#post-' . get_the_id();
		echo '<div class="wolfnet_back"><a href="' . $link . '">Back</a></div>';

	} else {

?>

		<div class="wolfnet_viewAll">
			<a href="<?php echo $agentsLink; ?>">Click here</a> to view all agents and staff.
		</div>

<?php

	}

	if ($agent['display_agent']) {

?>

		<div class="wolfnet_agent">

			<div class="wolfnet_aoHeader">

				<div class="wolfnet_aoName">

					<div class="wolfnet_aoTitle">
						<?php echo $agent['first_name'] . ' ' . $agent['last_name']; ?>
					</div>

					<hr />

					<div class="wolfnet_aoSubTitle">
						<div><?php echo $agent['title']; ?></div>
						<div><?php echo $agent['business_name']; ?></div>
					</div>

				</div>

			</div>

			<div class="wolfnet_aoSidebar">

				<div class="wolfnet_aoExtLinks">
					<?php if (strlen($agent['web_url']) > 0) { ?>
						<a class="wnt-btn wnt-btn-primary" target="_blank"
						 href="<?php echo $agent['web_url']; ?>">View Website</a>
					<?php } ?>
					<div class="wolfnet_aoSocial">
						<?php foreach ($socialLinks as $socialLink) {
							if (strlen($agent[$socialLink['field']]) > 0) {
								echo '<a target="_blank" href="' . $agent[$socialLink['field']] . '">'
									. '<span class="wnt-icon wnt-icon-' . $socialLink['icon'] . '"></span>'
									. '<span class="wnt-visuallyhidden"> ' . $socialLink['label'] . '</span>'
									. '</a>';
							}
						} ?>
					</div>
					<div class="wnt-clearfix"></div>
				</div>

				<div class="wolfnet_aoContact">

					<div class="wolfnet_aoImage wolfnet_agentImage">
						<img src="<?php echo $agent['image_url']; ?>"
						 onerror="this.className += ' wnt-hidden';" />
					</div>

					<div class="wolfnet_aoContactInfo">

						<div class="wolfnet_aoTitle">
							Contact <?php echo $agent['first_name'] . ' ' . $agent['last_name']; ?>
						</div>

						<hr />

						<ul class="wolfnet_aoLinks">

							<?php

								$contactNumbers = array();

								foreach ($contactMethods as $contactMethod) {
									// Filter out duplicate voice numbers
									if (
										(strlen($agent[$contactMethod['field']]) > 0)
										&& (
											($contactMethod['field'] == 'fax_number')
											|| ($contactMethod['field'] == 'pager_number')
											|| !in_array($agent[$contactMethod['field']], $contactNumbers)
										)
									) {
										array_push($contactNumbers, $agent[$contactMethod['field']]);
										echo '<li>'
											. '<span class="wnt-icon wnt-icon-' . $contactMethod['icon'] . '"></span> '
											. '<span class="wnt-visuallyhidden">' . $contactMethod['label'] . ':</span> '
											. $agent[$contactMethod['field']]
											. '</li>';
									}
								}

								if (strlen($agent['email_address']) > 0) {
									echo '<li><span class="wnt-icon wnt-icon-mail"></span> '
										. '<span class="wnt-visuallyhidden">Email:</span> '
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

					</div>

				</div>

			</div>

			<div class="wolfnet_aoInfo">

				<div class="wolfnet_aoName">

					<div class="wolfnet_aoTitle">
						<?php echo $agent['first_name'] . ' ' . $agent['last_name']; ?>
					</div>

					<hr />

					<div class="wolfnet_aoSubTitle">
						<div><?php echo $agent['title']; ?></div>
						<div><?php echo $agent['business_name']; ?></div>
					</div>

				</div>

				<?php

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

					foreach ($agentBio as $key => $label) {
						if (strlen($agent[$key]) > 0) {
							echo '<div class="wolfnet_aoSectionTitle">' . $label . '</div>'
								. '<div class="wolfnet_aoSection">'
								. '<p>' . $agent[$key] . '</p>'
								. '</div>';
						}
					}

					if (
						strlen($agent['optional_field_label']) > 0 &&
						strlen($agent['optional_field_value']) > 0
					) {
						echo '<div class="wolfnet_aoSectionTitle">' . $agent['optional_field_label'] . '</div>'
							. '<div class="wolfnet_aoSection">'
							. '<p>' . $agent['optional_field_value'] . '</p>'
							. '</div>';
					}

					// Favorite links
					$showFavoriteLinks = false;
					$favoriteLinks = '';
					for ($i = 1; $i <= 9; $i++) {
						if (strlen($agent['favorite_link_name_' . $i]) > 0 &&
							strlen($agent['favorite_link_url_' . $i]) > 0) {

							$showFavoriteLinks = true;
							$favoriteLinks .= "<li><strong>" . $agent['favorite_link_name_' . $i] . ":</strong> ";
							$favoriteLinks .= formatUrl($agent['favorite_link_url_' . $i]) . "</li>";
						}
					}

					if ($showFavoriteLinks) {
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


	// Agent's listings

	if (($activeListingCount > 0) || ($soldListingCount > 0)) {

?>

		<div class="wolfnet_aoListings">

			<div class="wolfnet_aoTitle">Agent's Listings</div>

			<hr />

			<?php if ($activeListingCount > 0) { ?>

				<div class="wolfnet_aoFeaturedListings">

					<?php echo $activeListingHTML; ?>

					<?php if ($activeListingCount > 10) {
						echo '<a href="<?php echo $searchUrl; ?>">'
							. 'View all ' . $activeListingCount . ' of '
							. $agent['first_name'] . "'s listings."
							. '</a>';
					} ?>

				</div>

			<?php }

			if ($soldListingCount > 0) { ?>

				<div class="wolfnet_aoSoldListings">

					<?php echo $soldListingHTML; ?>

					<?php if ($soldListingCount > 10) {
						echo '<a href="' . $soldSearchUrl . '">'
							. 'View all ' . $soldListingCount . ' of ' . $agent['first_name'] . "'s sold listings."
							. '</a>';
					} ?>

				</div>

			<?php } ?>

		</div>

<?php

	}

?>

</div>


<script>

jQuery(function ($) {

	var $aoWidget = $('#<?php echo $instance_id; ?>'),
		$agentListings = $aoWidget.find('.wolfnet_aoListings'),
		$agentFeatured = $agentListings.find('.wolfnet_aoFeaturedListings'),
		$agentSold = $agentListings.find('.wolfnet_aoSoldListings'),
		agentFeaturedLabel = 'Active',
		agentSoldLabel = 'Sold';

	if (($agentFeatured.length > 0) && ($agentSold.length > 0)) {

		var $agentListingNav = $('<div class="wnt-btn-group wolfnet_aoListingNav"></div>'),
			$agentFeaturedBtn = $(
				'<a class="wnt-btn wnt-btn-active wolfnet_aoFeaturedListingsLink"' +
				' href="javascript:void(0);">' + agentFeaturedLabel + '</a>'
			).appendTo($agentListingNav),
			$agentSoldBtn = $(
				'<a class="wnt-btn wolfnet_aoSoldListingsLink"' +
				' href="javascript:void(0);">' + agentSoldLabel + '</a>'
			).appendTo($agentListingNav);

		$agentListings.prepend($agentListingNav);

		$agentSold.hide();

		$agentFeaturedBtn.click(function () {
			$agentFeatured.show();
			$agentSold.hide();
			$agentSoldBtn.removeClass('wnt-btn-active');
			$agentFeaturedBtn.addClass('wnt-btn-active');
		});

		$agentSoldBtn.click(function () {
			$agentSold.show();
			$agentFeatured.hide();
			$agentFeaturedBtn.removeClass('wnt-btn-active');
			$agentSoldBtn.addClass('wnt-btn-active');
		});

	}

});

</script>
