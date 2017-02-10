<?php

/**
 *
 * @title         quickSearchOptions.php
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

<div id="<?php echo $instance_id; ?>" class="wolfnet_agentPagesOptions">

	<?php if(count($offices) > 1): ?>
		<input type="hidden" id="<?php echo $excludeoffices_wpid; ?>" class="officeids"
		 name="<?php echo $excludeoffices_wpname; ?>" value="" />
	<?php endif; ?>


	<table class="form-table">

		<tr>
			<th scope="row">
				<label>Lists:</label>
			</th>
			<td>
				<label for="<?php echo $showoffices_wpid; ?>" style="white-space: nowrap; margin-right: 1em;">
					<input type="radio" id="<?php echo $showoffices_wpid; ?>" name="<?php echo $showoffices_wpname; ?>"
					 value="true" <?php if ($showoffices) echo 'checked="checked"'?> />
					List agents and offices
				</label>
				<label for="<?php echo $showoffices_wpid; ?>_f" style="white-space: nowrap;">
					<input type="radio" id="<?php echo $showoffices_wpid; ?>_f" name="<?php echo $showoffices_wpname; ?>"
					 value="false" <?php if (!$showoffices) echo 'checked="checked"'?> />
					List agents only
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="<?php echo $agenttitle_wpid; ?>">
					Agent List Heading:
				</label>
			</th>
			<td>
				<input id="<?php echo $agenttitle_wpid; ?>"
				 name="<?php echo $agenttitle_wpname; ?>"
				 value="<?php echo $agenttitle; ?>" type="text" class="regular-text" />
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label for="<?php echo $numperpage_wpid; ?>">Agent List Options:</label>
			</th>
			<td>
				<p>
					<select id="<?php echo $numperpage_wpid; ?>" name="<?php echo $numperpage_wpname; ?>">
						<option value="10"<?php if($numperpage == 10) echo ' selected="selected"'; ?>>10</option>
						<option value="20"<?php if($numperpage == 20) echo ' selected="selected"'; ?>>20</option>
						<option value="30"<?php if($numperpage == 30) echo ' selected="selected"'; ?>>30</option>
					</select>
					<label for="<?php echo $numperpage_wpid; ?>">
						agents per page
					</label>
				</p>
				<p>
					<select id="<?php echo $agentsort_wpid; ?>" name="<?php echo $agentsort_wpname; ?>">
						<option value="name"<?php if($agentsort == 'name') echo ' selected="selected"'; ?>>
							Ordered by Agent Name
						</option>
						<option value="office_id"<?php if($agentsort == 'office_id') echo ' selected="selected"'; ?>>
							Ordered by Agent's Office ID - Ascending
						</option>
						<option value="office_id_desc"<?php if($agentsort == 'office_id_desc') echo ' selected="selected"'; ?>>
							Ordered by Agent's Office ID - Descending
						</option>
					</select>
				</p>
			</td>
		</tr>

	</table>

	<div class="wnt-office-field">

		<hr />

		<table class="form-table">

			<tr>
				<th scope="row">
					<label for="<?php echo $officetitle_wpid; ?>">Office List Heading:</label>
				</th>
				<td>
					<input id="<?php echo $officetitle_wpid; ?>"
					 name="<?php echo $officetitle_wpname; ?>"
					 value="<?php echo $officetitle; ?>" type="text" class="regular-text" />
				</td>
			</tr>

			<?php if (count($offices) > 1) { ?>
				<tr>
					<th scope="row">
						<label>Offices to Exclude:</label>
					</th>
					<td>
						<?php
							$selectedOffices = array_unique(explode(",", $excludeoffices), SORT_STRING);
							foreach ($offices as $office) {
								if (strlen($office['office_id']) > 0) {
									echo '<input id="officeexclude_' . $office['office_id'] . '"';
									echo ' type="checkbox" class="officeexclude"';
									if (in_array($office['office_id'], $selectedOffices)) {
										echo ' checked="checked"';
									}
									echo ' value="' . $office['office_id'] . '" /> ';
									echo '<label for="officeexclude_' . $office['office_id'] . '">';
									echo $office['name'] . ' (' . $office['office_id'] . ')';
									echo '</label><br />';
								}
							}
						?>
					</td>
				</tr>
			<?php } ?>

		</table>

	</div>

	<hr />


	<table class="form-table">

		<tr>
			<th scope="row">
				<label for="<?php echo $detailtitle_wpid; ?>">
					Agent Details Heading:
				</label>
			</th>
			<td>
				<input id="<?php echo $detailtitle_wpid; ?>"
				 name="<?php echo $detailtitle_wpname; ?>"
				 value="<?php echo $detailtitle; ?>" type="text" class="regular-text" />
			</td>
		</tr>

		<tr>
			<th scope="row">
				<label>Agent Listings:</label>
			</th>
			<td>
				<p>
					<label for="<?php echo $activelistings_wpid; ?>">
						<input type="checkbox" id="<?php echo $activelistings_wpid; ?>" name="<?php echo $activelistings_wpname; ?>"
						 value="true" data-fallback-value="false"
						 <?php if ($activelistings) echo 'checked="checked"'; ?> />
						Show active listings
					</label>
				</p>
				<?php if ($showSoldOption) { ?>
					<p>
						<label for="<?php echo $soldlistings_wpid; ?>">
							<input type="checkbox" id="<?php echo $soldlistings_wpid; ?>" name="<?php echo $soldlistings_wpname; ?>"
							 value="true" data-fallback-value="false"
							 <?php if ($soldlistings) echo 'checked="checked"'; ?> />
							Show sold listings
						</label>
					</p>
				<?php } ?>
			</td>
		</tr>


	</table>

</div>


<script type="text/javascript">

	jQuery (function ($) {

		var $form          = $('.wolfnet_agentPagesOptions');
		var $officeToggle  = $form.find('input, select').filter('[name="<?php echo $showoffices_wpname; ?>"]');
		var $officeFields  = $form.find('.wnt-office-field');
		var $submitButton  = $form.find('button, input').filter('[type="submit"]');

		$officeToggle.change(function () {
			var $toggle = $(this);
			if ($toggle.is(':checked')) {
				if ($toggle.val() === 'true') {
					$officeFields.slideDown();
				} else {
					$officeFields.slideUp();
				}
			}
		});

		<?php if (count($offices) > 1): ?>
			$submitButton.click(function () {
				var <?php echo $instance_id; ?> = [];
				var array = <?php echo $instance_id; ?>;

				$('.officeexclude').each(function () {
					if ($(this).prop('checked') && (array.indexOf($(this).val()) == -1) {
						array.push($(this).val());
					}
				});

				$('#<?php echo $excludeoffices_wpid; ?>').val(array.join(','));

			});
		<?php endif; ?>

	});

</script>
