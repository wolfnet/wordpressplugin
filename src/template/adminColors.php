<?php

	$opacityMin = 0;
	$opacityMax = 100;
	$opacityStep = 10;

?>

<div class="wrap">

	<div id="icon-options-wolfnet" class="icon32"><br /></div>

	<h2>WolfNet <sup>&reg;</sup> - Appearance</h2>

	<p>
		The 'Modern' widget theme streamlines the look of the property photos within
		"featured listings" and the "listing grid". With this optional feature enabled,
		property photos will appear larger and will only include the most important property
		listing details within the photo.
	</p>

	<form method="post" action="options.php">

		<?php echo $formHeader; ?>

		<div class="wolfnet_themePreview">

			<h2>Preview</h2>

			<div class="wolfnet_themePreviewBody">

				<div class="wolfnet_widget wolfnet_listingGrid wolfnet-theme-<?php echo $widgetTheme; ?>">
					<div class="wolfnet_listings">
						<?php echo $sampleListing; ?>
					</div>
				</div>

			</div>

		</div>

		<div class="wolfnet_colorOptions">

			<h2>Color Options</h2>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="wolfnet_themeColors[0]">
								<?php echo _e('Accent Color'); ?>
							</label>
						</th>
						<td>
							<input type="text" name="wolfnet_themeColors[0]" id="wolfnet_themeColors[0]"
							 value="<?php echo esc_attr($themeColors[0]); ?>"
							 data-default-color="#333333" class="wolfnet_colorPicker" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wolfnet_opacity">
								<?php echo _e('Opacity'); ?>
							</label>
						</th>
						<td>
							<select name="wolfnet_themeOpacity" id="wolfnet_themeOpacity">
								<?php for ($i = $opacityMin; $i <= $opacityMax; $i += $opacityStep) {
									echo '<option value="' . $i . '"'
										. ($i == $themeOpacity ? ' selected="selected"' : '')
										. '>' . $i . '%</option>';
								} ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>

			<p class="submit">
				<button class="button button-primary" type="submit"><?php echo _e('Save Color Options'); ?></button>
			</p>

		</div>

	</form>

</div>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var updatePreviewTimeout;

			var $themePreview = $('.wolfnet_themePreview'),
				$colorField = $('.wolfnet_colorPicker'),
				$opacityField = $('#wolfnet_themeOpacity'),
				$opacitySlider = $('<div class="wolfnet_opacity_slider"></div>');

			$colorField.wpColorPicker({
				change: onWidgetThemeChange
			}).change(onWidgetThemeChange);

			$opacitySlider.insertBefore($opacityField).slider({
				min: <?php echo $opacityMin; ?>,
				max: <?php echo $opacityMax; ?>,
				step: <?php echo $opacityStep; ?>,
				value: $opacityField.val(),
				slide: function (e, ui) {
					$opacityField.val(ui.value).trigger('wnt-theme-change');
				}
			});
			$opacityField.change(function () {
				$opacityField.trigger('wnt-theme-change');
				$opacitySlider.slider('value', $(this).val());
			}).on('wnt-theme-change', onWidgetThemeChange);



			var updateThemePreview = function () {
			}


			var onWidgetThemeChange = function (e) {
				clearTimeout(updatePreviewTimeout);
				updatePreviewTimeout = setTimeout(function () {
					updateThemePreview();
				}, 500);
			};

			updateThemePreview();

		});

	}

</script>
