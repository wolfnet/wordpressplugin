<?php

/**
 *
 * @title         agentPagesContactAgent.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_contactAgent">

	<div class="wolfnet_agentPreview">
		<?php 
		if(strlen($agent['thumbnail_url']) > 0) {
			echo '<div class="wolfnet_agentImage">';
			echo "<img src=\"{$agent['thumbnail_url']}\" />";
			echo '</div>';
		} 
		?>

		<div class="wolfnet_agentInfo">
			<?php
			echo $agent['first_name'] . ' ' . $agent['last_name'];
			echo "<br>";
			echo $agent['address_1'] . '<br>' . $agent['address_2'] . '<br>';
			echo $agent['city'] . ', ' . $agent['state'] . ' ' . $agent['zip_code']
			?>

			<div class="wolfnet_agentContact">
				<?php 
				if(strlen($agent['office_phone_number']) > 0) {
					echo '<div class="wolfnet_agentOfficePhone">';
					echo "<strong>Office</strong>: " . $agent['office_phone_number'];
					echo '</div>';
				}

				if(strlen($agent['mobile_phone']) > 0) {
					echo '<div class="wolfnet_agentMobilePhone">';
					echo "<strong>Mobile</strong>: " . $agent['mobile_phone'];
					echo '</div>';
				}

				if(strlen($agent['fax_number']) > 0) {
					echo '<div class="wolfnet_agentFax">';
					echo "<strong>Fax</strong>: " . $agent['fax_number'];
					echo '</div>';
				}
				?>
			</div>
		</div>
		<div class="wolfnet_clearfix"></div>
	</div>

	<form class="wolfnet_contactForm" action="<?php echo $_SERVER['PHP_SELF'] . "?contact=" . $agentId; ?>" method="post">
		(<span class="wolfnet_asterisk">*</span> Indicates a required field.)<br />

		<label for="name"><span class="wolfnet_asterisk">*</span>Name: </label>
		<input type="text" name="wolfnet_name" class="wolfnet_name" />

		<label for="email"><span class="wolfnet_asterisk">*</span>Email: </label>
		<input type="text" name="wolfnet_email" class="wolfnet_email" />

		<label for="phone">Phone Number: </label>
		<input type="text" name="wolfnet_phone" class="wolfnet_phone" />

		<label for="contacttype"><span class="wolfnet_asterisk">*</span>Prefer to be contacted: </label>
		<input type="radio" name="wolfnet_contacttype" class="wolfnet_contacttype" value="email" class="input" checked="checked"> By Email
		<input type="radio" name="wolfnet_contacttype" class="wolfnet_contacttype" value="phone" class="input"> By Phone

		<label for="comments">Questions/Comments: </label>
		<textarea name="wolfnet_comments"></textarea>

		<input type="submit" id="wolfnet_submit" value="Send" />
	</form>

</div>

<script type="text/javascript">
jQuery(function($) {
	$(window).load(function() {
		$('#wolfnet_submit').click(function(event) {
			event.preventDefault();
			
			var error = false;
			var missingFields = [];

			if($('.wolfnet_name').val() == '') {
				error = true;
				missingFields.push('Name');
			}

			if($('.wolfnet_email').val() == '') {
				error = true;
				missingFields.push("Email");
			}

			if(error) {
				var message = 'Please fill out the required fields: ';
				$(missingFields).each(function() {
					message += '\n' + this;
				});
				alert(message);
			}

			if(!error) {
				$('.wolfnet_contactForm').submit();
			}
		});
	});
});
</script>