<?php

	$opacityMin = 0;
	$opacityMax = 100;
	$opacityStep = 10;

?>

	<form method="post" action="options.php">

		<?php echo $formHeader; ?>

		<div class="wolfnet_colorOptions">

			<h2>Color Options</h2>

			<?php if ($widgetTheme == 'ash') { ?>
				<div class="notice notice-info below-h2" style="clear: both;">
					<p>
						A custom color does not apply to the 'Classic' theme.
					</p>
				</div>
			<?php } ?>

			<div class="wolfnet_colorOption">
				<label for="wolfnet_themeColors[0]">
					<?php echo _e('Accent Color'); ?>:
				</label>
				<div class="wolfnet_colorField">
					<input type="text" name="wolfnet_themeColors[0]" id="wolfnet_themeColors[0]"
					 value="<?php echo esc_attr($themeColors[0]); ?>"
					 data-default-color="#333333" class="wolfnet_colorPicker" />
				</div>
			</div>

			<div class="wolfnet_colorOption">
				<label for="wolfnet_opacity">
					<?php echo _e('Opacity'); ?>:
				</label>
				<div class="wolfnet_colorField">
					<select name="wolfnet_themeOpacity" id="wolfnet_themeOpacity">
						<?php for ($i = $opacityMin; $i <= $opacityMax; $i += $opacityStep) {
							echo '<option value="' . $i . '"'
								. ($i == $themeOpacity ? ' selected="selected"' : '')
								. '>' . $i . '%</option>';
						} ?>
					</select>
				</div>
			</div>

			<button class="button button-primary" type="submit"><?php echo _e('Save Color Options'); ?></button>

		</div>

	</form>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var $colorField      = $('.wolfnet_colorPicker'),
				$opacityField    = $('#wolfnet_themeOpacity'),
				$opacitySlider   = $('<div class="wolfnet_opacity_slider"></div>');

			var onColorChange = function () {
				$(window).trigger('wnt-color-change');
			};

			$colorField.wpColorPicker({
				change: onColorChange
			});
			$colorField.change(onColorChange);

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
			}).on('wnt-theme-change', onColorChange);

		});

	}

</script>
