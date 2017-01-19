<?php

/**
 *
 * @title         agentPagesAgentBrief.php
 * @copyright     Copyright (c) 2016, WolfNet Technologies, LLC
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

<div class="wolfnet_aoItem">

	<div class="wolfnet_aoBody">


		<?php 
		//Strip out the extra periods in the agentLink
		$agentLink = preg_replace("/\./", "", $agentLink); 
		?>
		
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
							. '<span class="address-focus">' . $agent['address_1'] . ' ' . $agent['address_2'] . '</span>'
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
