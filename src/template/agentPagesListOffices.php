<?php

/**
 *
 * @title         agentPagesListOffices.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_widget wolfnet_officesList">

<?php
if(array_key_exists("REDIRECT_URL", $_SERVER)) {
	$linkBase = $_SERVER['REDIRECT_URL'];
} else {
	$linkBase = $_SERVER['PHP_SELF'];
}
?>

<form name="wolfnet_agentSearch" class="wolfnet_agentSearch" method="POST" 
	action="<?php echo $linkBase . "?search"; ?>">
	<?php // No office ID as a hidden field. We want to search all offices ?>

	<input type="text" name="agentCriteria" class="wolfnet_agentCriteria"
		value="<?php echo (strlen($agentCriteria) > 0) ? $agentCriteria : ''; ?>" /> 
	<input type="submit" name="agentSearch" class="wolfnet_agentSearchButton" value="Search" />
	<div class="wolfnet_clearfix"></div>
</form>

<?php

foreach($offices as $office) {
	$officeLink = $linkBase . '?office_id=' . $office['office_id'];
?>

	<div class="wolfnet_officePreview">
		<?php 
		if(strlen($office['photo_url']) > 0) {
			echo '<div class="wolfnet_officeImage">';
			echo '<a href="' . $officeLink . '">';
			echo "<img src=\"{$office['photo_url']}\" />";
			echo '</a>';
			echo '</div>';
		} 
		?>

		<div class="wolfnet_officeContact">
			<div class="wolfnet_officeName">
				<?php 
					echo '<a href="' . $officeLink . '">';
					echo $office['name'];
					echo '</a>';
				?>
			</div>

			<?php 
			if(strlen($office['mailing_address']) > 0) {
				echo '<div class="wolfnet_officeAddress">';
				echo $office['mailing_address'];
				echo '</div>';
			}
			if(strlen($office['phone_number']) > 0) {
				echo '<div class="wolfnet_officePhone">';
				echo "Phone: " . $office['phone_number'];
				echo '</div>';
			}

			if(strlen($office['fax_number']) > 0) {
				echo '<div class="wolfnet_officeFax">';
				echo "Fax: " . $office['fax_number'];
				echo '</div>';
			}

			if(strlen($office['fax_number']) > 0) {
				echo '<div class="wolfnet_officeFax">';
				echo "Fax: " . $office['fax_number'];
				echo '</div>';
			}
			?>
		</div>
	</div>

<?php
} // end foreach
?>

</div>

<script type="text/javascript">
jQuery(function($) {
	$(window).load(function() {
		// Resize office boxes to height of tallest one.
		var $offices = $('.wolfnet_officePreview');
		var maxHeight = 0;
		$offices.each(function() {
			if($(this).height() > maxHeight) {
				maxHeight = $(this).height();
			}
		});
		$('.wolfnet_officePreview').height(maxHeight);
	});
});
</script>