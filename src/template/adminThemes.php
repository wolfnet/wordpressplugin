
	<form method="post" action="options.php">

		<?php echo $formHeader; ?>

		<p>Select the appearance of the widgets.</p>

		<div class="notice notice-warning below-h2" style="clear: both;">
			<p>
				Updating to the 'Modern' widget themes may cause display conflicts on your website.
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

					<div class="wolfnet_box wolfnet_widget_theme <?php if ($themeOptSelected) echo esc_attr('wolfnet_widget_theme_active'); ?>"
					 tabindex="0">

						<div class="wolfnet_widget_theme_thumb">

							<div class="wolfnet_themePreviewBody">
								<div class="wolfnet_widget wolfnet_listingGrid wolfnet-theme-<?php echo $themeOpt['name']; ?>">
									<div class="wolfnet_listings">
										<?php echo $sampleListing; ?>
									</div>
								</div>
							</div>

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
									<a href="#wolfnet-color-options"
									 class="button-primary"><?php echo _e('Customize'); ?></a>
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


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var btnClasses = {
					primary:    'button-primary',
					secondary:  'button-secondary'
				},
				btnSelector = 'button[name="wolfnet_widgetTheme"], input[name="wolfnet_widgetTheme"]';

			var $widgetTheme = $('.wolfnet_widget_theme');

			$widgetTheme.mouseover(function () {
				var $btn = $(this).find(btnSelector);
				$btn.removeClass(btnClasses.secondary).addClass(btnClasses.primary);
			});

			$widgetTheme.mouseout(function () {
				var $btn = $(this).find(btnSelector);
				$btn.removeClass(btnClasses.primary).addClass(btnClasses.secondary);
			});


		});

	}

</script>
