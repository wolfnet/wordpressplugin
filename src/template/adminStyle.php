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

		<h2>Color Options</h2>

		<div id="wolfnet_themePreview" style="width: 300px;">
			<div class="wolfnet_widget wolfnet_listingGrid">
				<div class="wolfnet_listings">
					<?php echo $sampleListing; ?>
				</div>
			</div>
		</div>

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
						<input type="text" name="wolfnet_themeOpacity" id="wolfnet_themeOpacity"
						 value="<?php echo esc_attr($themeOpacity); ?>" size="3" maxlength="3" /> %
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<button class="button button-primary" type="submit"><?php echo _e('Save Color Options'); ?></button>
		</p>


		<h2>Widget Theme</h2>

		<p>Select the appearance of the widgets.</p>

		<div class="notice notice-warning below-h2" style="clear: both;">
			<p>
				Updating to the 'Modern' widget theme may cause display conflicts on your website.
				If you experience any conflicts, switch back to the 'Classic' widget theme
				and contact your web developer to correct these issues.
			</p>
		</div>

		<fieldset>
			<legend class="screen-reader-text"><span>Widget Theme</span></legend>
			<div class="wolfnet_widget_themes">
				<?php foreach ($widgetThemes as $themeOpt) {
					$themeOptSelected = (
					($widgetTheme == $themeOpt['name'])
					|| (($widgetTheme == '') && ($defaultWidgetTheme == $themeOpt['name']))
				); ?>
					<div class="wolfnet_widget_theme <?php if ($themeOptSelected) echo esc_attr('wolfnet_widget_theme_active'); ?>"
					 tabindex="0">
						<div class="wolfnet_widget_theme_thumb">
							<img src="<?php echo $imgdir . $themeOpt['previewImg']; ?>?v={X.X.X}.2" />
						</div>
						<div class="wolfnet_widget_theme_info">
							<span class="wolfnet_widget_theme_label">
								<span class="wolfnet_widget_theme_flag">
									<?php if ($themeOptSelected) echo _e('Active:') ?>
								</span>
								<?php _e($themeOpt['label']); ?>
							</span>
							<span class="wolfnet_widget_theme_actions">
								<?php if ($themeOptSelected) { ?>
								<?php } else { ?>
									<button type="submit" class="button button-secondary"
									 name="wolfnet_widgetTheme"
									 id="wolfnet_widgetTheme_<?php echo esc_attr($themeOpt['name']); ?>"
									 value="<?php echo esc_attr($themeOpt['name']); ?>"
									 title="<?php esc_attr_e('Apply this widget theme'); ?>">
										<?php echo _e('Apply'); ?>
									</button>
								<?php } ?>
							</span>
						</div>
					</div>
				<?php } ?>
			</div>
		</fieldset>

	</form>

</div>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var $colorField = $('.wolfnet_colorPicker');
			$colorField.wpColorPicker({
				change: onWidgetThemeChange
			}).change(onWidgetThemeChange);

			var $opacityField = $('#wolfnet_themeOpacity'),
			$opacitySlider = $('<div class="wolfnet_opacity_slider"></div>').insertBefore($opacityField).slider({
				min: 0,
				max: 100,
				step: 10,
				value: $opacityField.val(),
				slide: function (e, ui) {
					$opacityField.val(ui.value).trigger('wnt-theme-change');
				}
			});
			$opacityField.change(function () {
				$opacityField.trigger('wnt-theme-change');
				$opacitySlider.slider('value', $(this).val());
			}).on('wnt-theme-change', onWidgetThemeChange);


			var $themePreview = $('#wolfnet_themePreview');

			var updateThemePreview = function () {
			}

			var updatePreviewTimeout;

			var onWidgetThemeChange = function (e) {
				clearTimeout(updatePreviewTimeout);
				updatePreviewTimeout = setTimeout(function () {
					updateThemePreview();
				}, 500);
			};


			updateThemePreview();


            var btnClasses = {
                primary:    'button-primary',
                secondary:  'button-secondary'
            };
            var btnSelector = 'button[name="wolfnet_widgetTheme"], input[name="wolfnet_widgetTheme"]';

            $('.wolfnet_widget_theme').mouseover(function () {
                var $btn = $(this).find(btnSelector);
                $btn.removeClass(btnClasses.secondary).addClass(btnClasses.primary);
            });

            $('.wolfnet_widget_theme').mouseout(function () {
                var $btn = $(this).find(btnSelector);
                $btn.removeClass(btnClasses.primary).addClass(btnClasses.secondary);
            });

		});

	}

</script>
