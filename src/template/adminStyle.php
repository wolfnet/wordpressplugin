<?php

	$colorSection    = $colorOut;
	$themeSection    = $themeOut;
	$agentSection    = $sampleAgent;
	$officeSection   = $sampleOffice;

?>


<div class="wrap">

	<div id="icon-options-wolfnet" class="icon32"><br /></div>

	<h2>WolfNet <sup>&reg;</sup> - Appearance</h2>

	<p>
		The 'Modern' widget themes streamline the look of the property photos within
		"featured listings" and the "listing grid". With this optional feature enabled,
		property photos will appear larger and will only include the most important property
		listing details within the photo.
	</p>


	<h2>Widget Theme</h2>

	<div>
		<?php echo $themeSection; ?>
	</div>


	<?php /* Color Options */ ?>
	<div id="wolfnet-color-options">
		<?php echo $colorSection; ?>
	</div>


	<?php /* Agent Preview */ ?>
	<?php if (strlen($agentSection) > 0) { ?>

		<details class="wolfnet_colorPreview">

			<summary title="Preview color options">Preview color options</summary>

			<div class="wolfnet_themePreview">

				<div class="wolfnet_themePreviewItem wolfnet_themePreviewBody" id="wnt-theme-preview-agent">


					<h3>Agent Preview</h3>

					<div class="wolfnet_widget wolfnet_ao wolfnet_aoAgentsList">
						<div class="wolfnet_aoAgents">
							<?php echo $agentSection; ?>
						</div>
					</div>

				</div>

				<div class="wolfnet_themePreviewItem wolfnet_themePreviewBody" id="wnt-theme-preview-office">

					<h3>Office Preview</h3>

					<div class="wolfnet_widget wolfnet_ao wolfnet_aoOfficesList">
						<div class="wolfnet_aoOffices">
							<?php echo $officeSection; ?>
						</div>
					</div>

				</div>

			</div>

		</details>

	<?php } ?>

</div>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var updatePreviewTimeout;

			var themeStylesheetBaseUrl = '<?php echo $url; ?>/css/wolfnet.theme.custom.php',
				colorOptionsUrl        = '<?php echo $colorOptionsUrl; ?>';

			var $themePreview       = $('.wolfnet_themePreview'),
				$previewItems       = $themePreview.find('.wolfnet_themePreviewItem'),
				$themeStyles        = $('<style type="text/css"></style>'),
				$spinner            = $('<div class="spinner is-active"></div>'),
				$colorField         = $('.wolfnet_colorPicker'),
				$opacityField       = $('#wolfnet_themeOpacity'),
				$opacitySlider      = $('<div class="wolfnet_opacity_slider"></div>');


			var updateThemePreview = function () {
				var themeArgs = {};
				var colors = [];

				// Turn on spinner
				$spinner.prependTo($themePreview.find('h2')).show();

				// Accent Color
				colors.push($colorField.val());

				// Update the styles
				$.ajax({
					url: themeStylesheetBaseUrl,
					type: 'get',
					dataType: 'html',
					data: {
						'colors':  colors.toString(),
						'opacity': $opacityField.val()
					}
				}).done(onThemeStylesLoad);

			};


			var onThemeStylesLoad = function (data) {
				$themeStyles.html(data).appendTo($('head'));
				$spinner.hide().remove();
			};


			var onWidgetThemeChange = function (e) {
				clearTimeout(updatePreviewTimeout);
				updatePreviewTimeout = setTimeout(function () {
					updateThemePreview();
				}, 500);
			};

			updateThemePreview();

			$(window).on('wnt-color-change', onWidgetThemeChange);




			// Preview tabs

			if ($previewItems.length > 1) {

				var $previewNav = $('<ul>');

				$previewItems.each(function () {
					var $item = $(this);
					var $itemHeading = $item.find('>h1, >h2, >h3, >h4, >h5').first();
					var itemId = $item.attr('id'),
						itemLabel = $itemHeading.text();

					$itemHeading.remove();

					$previewNav.append($('<li><a href="#' + itemId + '">' + itemLabel + '</a></li>'));

				});

				$previewItems.first().before($previewNav);

				$themePreview.tabs();

			}


		});

	}

</script>
