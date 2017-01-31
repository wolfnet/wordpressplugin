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
	$linkBase = esc_url_raw($_SERVER['REDIRECT_URL']);

	//Build an array of all the parts of URL string.
	$linkNames = preg_split("/\//", $linkBase);
	//Get the agent name (last trailing slash).
	$agentName = $linkNames[count($linkNames) - 2];
	//Strip out extraneous commas and periods.
	$agentName = preg_replace("/[\.,]/", "", $agentName);
	$linkBase2 = "";
	//Build the link base back up from the beginning.
	for ($i = 0; $i < count($linkNames) - 2; $i++) {
		$linkBase2 = $linkBase2 . $linkNames[$i] . "/";
	}
	$linkBase2 = $linkBase2 . $agentName . "/";
	$contactLink = $linkBase2 . 'contact';
} else {
	$linkBase = esc_url_raw($_SERVER['PHP_SELF'] . '/');
	$agentName = sanitize_text_field($_REQUEST['agentId']);
	//Strip out extraneous periods and commas.
	$agentName = preg_replace("/[\.,]/", "", $agentName);
	$contactLink = $linkBase . 'agnt/' . $agentName . '/contact';
}

// Remove /agent/* from link base.
if(preg_match('/agnt\/.*/', $linkBase)) {
	$linkBase = preg_replace('/agnt\/.*/', '', $linkBase);
}

$agentsLink  = $linkBase . 'agnts';

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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_ao wolfnet_aoAgentDetails">

	<a id="agentTop_<?php echo $instance_id; ?>" class="wolfnet_aoTop"></a>

<?php

	if (strlen($detailtitle) > 0) {
		echo '<h2>' . $detailtitle . '</h2>';
	}

	if (array_key_exists('REDIRECT_URL', $_SERVER) && $officeId != '') {

		$link = esc_url_raw($_SERVER['REDIRECT_URL']) . "?";
		if (array_key_exists('agentCriteria', $_REQUEST) && strlen($_REQUEST['agentCriteria']) > 0) {
			$link .= 'agentCriteria=' . sanitize_text_field($_REQUEST['agentCriteria']) . '&';
		}
		if ($officeId != '' && strpos($link, 'officeId') === false) {
			$link .= 'officeId=' . $officeId;
		}
		$link .= '#post-' . get_the_id();
		echo '<div class="wolfnet_back"><a href="' . $link . '">Back</a></div>';

	} else {

?>

		<div class="wolfnet_aoViewAll">
			<a href="<?php echo $agentsLink; ?>">Click here</a> to view all agents and staff.
		</div>

<?php

	}

?>

<div class="wolfnet_agent">

	<?php if ($agent['display_agent']) { ?>

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
			</div>

			<div class="wnt-clearfix"></div>

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

	<?php } ?>

	<div class="wolfnet_aoMain">

		<?php if ($agent['display_agent']) { ?>

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
						'specialty'          => 'Specialty',
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
						echo '<div class="wolfnet_aoSectionTitle">Favorite Links</div>'
							. '<div class="wolfnet_aoSection">'
							. '<ul class="wolfnet_agentLinks">' . $favoriteLinks . '</ul>'
							. '</div>';
					}
				?>

			</div>

		<?php } ?>


		<?php if (($activeListingCount > 0) || ($soldListingCount > 0)) { ?>

			<div class="wolfnet_aoListings">

				<div class="wolfnet_aoListingNavArea"></div>

				<?php if ($activeListingCount > 0) { ?>

					<div class="wolfnet_aoFeaturedListings">

						<div class="wolfnet_aoTitle">
							Agent's
							<?=($soldListingCount == 0 ? 'Active' : '')?>
							Listings
						</div>

						<hr />

						<?php echo $activeListingHTML; ?>

						<?php if ($activeListingCount > 10) {
							echo '<a href="' . $searchUrl . '">'
								. 'View all ' . $activeListingCount . ' of '
								. $agent['first_name'] . "'s listings."
								. '</a>';
						} ?>

					</div>

				<?php }

				if ($soldListingCount > 0) { ?>

					<div class="wolfnet_aoSoldListings">

						<div class="wolfnet_aoTitle">
							Agent's
							<?=($activeListingCount == 0 ? 'Sold' : '')?>
							Listings
						</div>

						<hr />

						<?php echo $soldListingHTML; ?>

						<?php if ($soldListingCount > 10) {
							echo '<a href="' . $soldSearchUrl . '">'
								. 'View all ' . $soldListingCount . ' of ' . $agent['first_name'] . "'s sold listings."
								. '</a>';
						} ?>

					</div>

				<?php } ?>

			</div>

		<?php } ?>

	</div>

</div>

<div class="wnt-clearfix"></div>

<a class="wnt-btn wnt-btn-round wnt-btn-primary wolfnet_top" href="#agentTop_<?php echo $instance_id; ?>"
 title="<?php esc_attr_e('Back to Top'); ?>">
	<span class="wnt-icon wnt-icon-triangle-up-stop"></span>
	<span class="wnt-visuallyhidden"><?php _e('Back to Top'); ?></span>
</a>

<div class="wnt-clearfix"></div>

</div>


<script>

jQuery(function ($) {

	var $aoWidget = $('#<?php echo $instance_id; ?>');


	// Collapse info sections

	var $agentInfoSections = $aoWidget.find('.wolfnet_aoInfo .wolfnet_aoSection'),
		infoMaxLen = 200;

	var collapseSection = function () {
		var $this = $(this),
			$summary = $('<div class="wolfnet_aoSectionSummary"></div>'),
			$content = $('<div class="wolfnet_aoSectionContent"></div>'),
			$showMoreBtn = $('<a href="javascript:void(0);">[<span class="wnt-visuallyhidden"><?php _e('Continue reading'); ?></span>...]</a>'),
			fullContent = $this.html(),
			summaryText = $this.text();

			// Just retain BR tags, but nix every other tag that might come into play.
			summaryTextTemp = $this.html()
			summaryTextTemp = summaryTextTemp.replace(/<br>/g, '[[br]]')
			summaryTextTemp = $('<div>').html(summaryTextTemp).text();
			summaryText = summaryTextTemp.replace(/\[\[br\]\]/g, '<br>');

		if (summaryText.length > infoMaxLen) {

			summaryText = summaryText.substring(0, infoMaxLen);

			if (summaryText.lastIndexOf(' ') > -1) {
				summaryText = summaryText.substring(0, summaryText.lastIndexOf(' '));
			}
			if (summaryText.lastIndexOf('\n') > -1) {
				summaryText = summaryText.substring(0, summaryText.lastIndexOf('\n'));
			}

			$this.html($summary.html(summaryText).append(' ', $showMoreBtn));
			$this.append($content.hide().html(fullContent));

			$showMoreBtn.click(onShowMoreClick);

		}

	};

	var onShowMoreClick = function (e) {
		var $section = $(this).closest('.wolfnet_aoSection'),
			$summary = $section.find('.wolfnet_aoSectionSummary'),
			$content = $section.find('.wolfnet_aoSectionContent');

		$summary.hide();
		$content.show();

	};

	$agentInfoSections.each(collapseSection);


	// Agent listings toggle

	var $agentListings = $aoWidget.find('.wolfnet_aoListings'),
		$agentFeatured = $agentListings.find('.wolfnet_aoFeaturedListings'),
		$agentSold = $agentListings.find('.wolfnet_aoSoldListings'),
		$agentFeaturedGrid   = $agentFeatured.find('.wolfnet_listingGrid'),
		$agentSoldGrid       = $agentSold.find('.wolfnet_listingGrid'),
		$agentListingNavArea = $agentListings.find('.wolfnet_aoListingNavArea'),
		agentFeaturedLabel = '<?php _e('Active'); ?>',
		agentSoldLabel = '<?php _e('Sold'); ?>';

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

		$agentListingNavArea.append($agentListingNav);

		$agentSold.hide();

		$agentFeaturedBtn.click(function () {
			$agentFeatured.show();
			$agentSold.hide();
			$agentSoldBtn.removeClass('wnt-btn-active');
			$agentFeaturedBtn.addClass('wnt-btn-active');
			$agentFeaturedGrid.wolfnetListingGrid('refresh');
		});

		$agentSoldBtn.click(function () {
			$agentSold.show();
			$agentFeatured.hide();
			$agentFeaturedBtn.removeClass('wnt-btn-active');
			$agentSoldBtn.addClass('wnt-btn-active');
			$agentSoldGrid.wolfnetListingGrid('refresh');
		});

	}


	// Agent profile follows scroll + Back-to-top

	var $window = $(window),
		$aoMainContent = $aoWidget.find('.wolfnet_aoMain'),
		$aoSidebar = $aoWidget.find('.wolfnet_aoSidebar'),
		$backToTop = $aoWidget.find('.wolfnet_top'),
		sb = {
			lastScrollTop:  $window.scrollTop(),
			leftOffset:     20,
			// The following is set up in setupStickySidebar() and disableStickySidebar()
			enabled:        false,
			// The following are set up in updatePosition()
			windowTop:      0,
			windowHeight:   0,
			sidebarTop:     0,
			sidebarLeft:    0,
			sidebarWidth:   0,
			sidebarHeight:  0,
			limitTop:       0,
			limitBottom:    0,
			sidebarDocTop:  0
		};


	var updatePosition = function () {
		var mainContentHeight = $aoMainContent.height(),
			mainContentOffset = $aoMainContent.offset(),
			windowTop         = $window.scrollTop(),
			sidebarTop        = $aoSidebar.position().top;

		$.extend(sb, {
			windowTop:         windowTop,
			windowHeight:      $window.height(),
			sidebarTop:        sidebarTop,
			sidebarLeft:       $aoSidebar.offset().left,
			sidebarWidth:      $aoSidebar.width(),
			sidebarHeight:     $aoSidebar.outerHeight(),
			limitTop:          mainContentOffset.top,
			limitBottom:       mainContentOffset.top + mainContentHeight,
			sidebarDocTop:     windowTop + sidebarTop
		});

	};


	var canStickSidebar = function () {
		return (
			(sb.sidebarHeight < (sb.limitBottom - sb.limitTop))
			&& (($aoSidebar.offset().top + sb.sidebarHeight) > ($aoMainContent.offset().top + 20))
		);
	};


	var setupStickySidebar = function () {
		updatePosition();

		var resizeTimeout;
		$window.on('resize.wntSticky', function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(onResizeAgent, 500);
		});

		onResizeAgent();

	};


	var enableStickySidebar = function () {
		updatePosition();

		$window.on('scroll.wntSticky touchmove.wntSticky', onScrollAgent);

		onScrollAgent();

		sb.enabled = true;

	};


	var disableStickySidebar = function () {
		$window.off('scroll.wntSticky touchmove.wntSticky');
		detachSidebar();
		sb.enabled = false;
	};


	var attachSidebar = function (top) {
		if (typeof top === 'undefined') {
			top = 0;
		}
		if (!$aoSidebar.is('.wnt-attached')) {
			$aoSidebar.addClass('wnt-attached');
		}
		$aoSidebar.css({
			top:    top,
			left:   sb.sidebarLeft - sb.leftOffset,
			width:  sb.sidebarWidth
		});
	};


	var detachSidebar = function () {
		if ($aoSidebar.is('.wnt-attached')) {
			$aoSidebar.removeClass('wnt-attached').css({
				top:    '',
				left:   '',
				width:  ''
			});
		}
	};


	var onResizeAgent = function () {
		var canStick = canStickSidebar();

		if (canStick) {
			if (!sb.enabled) {
				enableStickySidebar();
			}
			detachSidebar();
			updatePosition();
			onScrollAgent();
		} else if (sb.enabled) {
			disableStickySidebar();
		}

	};


	var onScrollAgent = function () {

		// Get sidebar and window positions
		sb.windowTop     = $window.scrollTop();
		sb.sidebarTop    = $aoSidebar.position().top;
		sb.sidebarDocTop = sb.windowTop + sb.sidebarTop;

		var sidebarAttached       = $aoSidebar.is('.wnt-attached'),
			sidebarWithinLimits   = sb.sidebarHeight < (sb.limitBottom - sb.limitTop),
			sidebarWithinWindow   = sb.sidebarHeight < sb.windowHeight,
			windowPastTopLimit    = sb.windowTop > sb.limitTop,
			windowPastBottomLimit = sb.windowTop + sb.windowHeight > sb.limitBottom,
			scrollHeight          = sb.windowTop - sb.lastScrollTop,
			isScrollingDown       = scrollHeight > 0;
			lowestDocTop          = sb.limitBottom - sb.sidebarHeight;
			lowestTop             = lowestDocTop - sb.windowTop;

		// Determine whether to show the 'back-to-top' button
		if (windowPastTopLimit) {
			$backToTop.css('visibility', 'visible');
		} else {
			$backToTop.css('visibility', 'hidden');
		}

		// Determine whether to attach the sidebar
		if (sidebarWithinLimits && windowPastTopLimit) {

			var newTop = 0;

			if (sidebarWithinWindow) {
				// Sidebar can fit within window - keep it at the top of the screen unless limit has been reached
				if (
					(sb.windowTop >= sb.sidebarDocTop) &&
					(
						(
							isScrollingDown &&
							(sb.sidebarDocTop >= lowestDocTop)
						) || (
							!isScrollingDown &&
							(sb.sidebarDocTop >= lowestDocTop + scrollHeight)
						)
					)
				) {
					// Sidebar has reached the bottom limit - move sidebar opposite of scroll
					newTop = lowestTop;
				} else {
					// Snap to the top of the screen
					newTop = 0;
				}
			} else {
				// Sidebar is taller than window - move sidebar based on scroll direction
				if (isScrollingDown) {
					// Scrolling down
					if (sb.sidebarDocTop >= lowestDocTop) {
						// Sidebar has reached the bottom limit
						newTop = lowestTop;
					} else if (sb.sidebarTop + sb.sidebarHeight <= sb.windowHeight) {
						// Sidebar has reached the bottom of the window - snap to bottom
						newTop = sb.windowHeight - sb.sidebarHeight;
					} else {
						// Sidebar should scroll
						newTop = (sidebarAttached ? sb.sidebarTop : 0) - scrollHeight;
					}
				} else {
					// Scrolling up
					if ((sb.sidebarTop < 0) && (sb.sidebarDocTop >= lowestDocTop + scrollHeight)) {
						// Sidebar has reached the bottom limit - move sidebar opposite of scroll
						newTop = lowestTop;
					} else if (sb.sidebarTop >= 0) {
						// Sidebar has reached the top of the window - snap to top
						newTop = 0;
					} else {
						// Sidebar should scroll
						newTop = (sidebarAttached ? sb.sidebarTop : 0) - scrollHeight;
					}
				}
			}

			attachSidebar(newTop);

			// Get updated position
			sb.sidebarTop = $aoSidebar.position().top;

		} else {
			detachSidebar();
		}

		// Update the lastScrollTop value
		sb.lastScrollTop = sb.windowTop;

	};


	// Set up sticky sidebar
	setTimeout(setupStickySidebar, 500);


});

</script>
