<?php

/**
 *
 * @title         agentPagesContactOffice.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_contactOffice">

	<?php
	if(array_key_exists("REDIRECT_URL", $_SERVER)) {
		$linkBase = $_SERVER['REDIRECT_URL'];
	} else {
		$linkBase = $_SERVER['PHP_SELF'] . '/';
	}

	$link = $linkBase;
	$formAction = $linkBase;
	if(!preg_match('/\/office/', $linkBase)) {
		// Landing on this page without a redirect URL.
		$link .= 'office/' . $officeId;
		$formAction = $link . '/contact';
	} else {
		// Should just be able to remove /contact from the link.
		$formAction = $linkBase;
		$link = preg_replace('/\/contact.*/', '', $linkBase);
	}

	echo '<p><a href="' . $link . '">Back</a> to office.</p>';

	?>

	<div class="wolfnet_officePreview">
		<div class="wolfnet_officeName">
			<?php echo $office['name']; ?>
		</div>

		<div class="wolfnet_officeContact wolfnet_aoContact">
			<?php
			echo $office['address_1'] . ' ' . $office['address_2'];
			echo '<br>';
			echo $office['city'] . ', ' . $office['state'] . ' ';
			echo $office ['postal_code'];

			if(strlen($office['phone_number']) > 0) {
				echo '<div class="wolfnet_officePhone">';
				echo "<strong>Office</strong>: " . $office['phone_number'];
				echo '</div>';
			}

			if(strlen($office['fax_number']) > 0) {
				echo '<div class="wolfnet_officeFax">';
				echo "<strong>Fax</strong>: " . $office['fax_number'];
				echo '</div>';
			}

			if(strlen($office['email']) > 0) {
				echo '<div class="wolfnet_officeEmail">';
				echo $office['email'];
				echo '</div>';
			}
			?>
		</div>
	</div>

	<?php
	if(array_key_exists('thanks', $_REQUEST) && $_REQUEST['thanks']) {
		echo '<div class="wolfnet_contactThanks">';
		echo 'Thank you for contacting us!<br />';
		echo 'We will respond as quickly as possible.';
		echo '</div>';
	} else {
	?>

	<form class="wolfnet_contactForm" action="<?php echo $formAction; ?>" method="post">
		<input type="hidden" name="office_id" value="<?php echo $office['office_id']; ?>" />

		<?php
		if(array_key_exists('errorField', $_REQUEST)) {
			echo '<span class="wolfnet_red">Please correct the errors below.</span><br />';
			$errorField = $_REQUEST['errorField'];
		} else {
			$errorField = '';
		}
		?>

		(<span class="wolfnet_red">*</span> Indicates a required field.)<br />

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

	<?php
	}
	?>

</div>

<script type="text/javascript">
jQuery(function($) {

	function validateEmail(email) {
	    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	    return re.test(email);
	}

	$(window).load(function() {
		$('#wolfnet_submit').click(function(event) {
			event.preventDefault();

			var message = '';
			var error = false;
			var validEmail = true;
			var missingFields = [];

			if($('.wolfnet_name').val() == '') {
				error = true;
				missingFields.push('Name');
			}

			if($('.wolfnet_email').val() == '') {
				error = true;
				missingFields.push("Email");
			} else {
				if(validateEmail($('.wolfnet_email').val()) == false) {
					error = true;
					validEmail = false;
				}
			}

			if(error) {
				if(missingFields.length > 0) {
					message += 'Please fill out the required fields: ';
					$(missingFields).each(function() {
						message += '\n' + this;
					});
				} else if(!validEmail) {
					message += 'Please enter a valid email address.';
				}
				alert(message);
			}

			if(!error) {
				$('.wolfnet_contactForm').submit();
			}
		});
	});

});
</script>
