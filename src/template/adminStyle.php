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


	<!-- Color options form -->

	<div id="wolfnet-color-options" class="wolfnet_box">

	<h3>Color Options</h3>

	<div class="wolfnet_boxContent">
		<?php echo $colorSection; ?>
	</div>

	</div>


	<!-- Previews -->

	<?php if (strlen($agentSection) > 0) { ?>

		<div id="wolfnet-color-options-preview" class="wolfnet_box">

			<h3>Agent/Office Pages Preview</h3>

			<div class="wolfnet_boxContent">

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

			</div>

		</div>

	<?php } ?>

</div>


<script>

	if (typeof jQuery !== 'undefined') {

		jQuery(function ($) {

			var updatePreviewTimeout;

			var themeStylesheetBaseUrl = '<?php echo $url; ?>/css/wolfnet.theme.custom.php',
				colorOptionsUrl        = '<?php echo $colorOptionsUrl; ?>';

			var $previewBox         = $('#wolfnet-color-options-preview'),
				$previewHeader      = $previewBox.find('> h2, > h3, > h4').first(),
				$previewContent     = $previewBox.find('.wolfnet_boxContent').first(),
				$themePreview       = $previewBox.find('.wolfnet_themePreview'),
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


			// Expandable preview

			if ($previewBox.length > 0) {

				var $previewToggleIcon = $('<span class="wnt-icon wnt-icon-triangle-up"></span>');

				var $previewToggleBtn = $(
					'<a href="javascript:void(0);" title="Show/hide preview" class="wolfnet_boxToggle"></a>'
				).append($previewToggleIcon);

				var expandPreview = function () {
					$previewBox.removeClass('wolfnet_boxCollapsed');
					$previewContent.show();
					$previewToggleIcon.removeClass('wnt-icon-triangle-down').addClass('wnt-icon-triangle-up');
				};

				var collapsePreview = function () {
					$previewBox.addClass('wolfnet_boxCollapsed');
					$previewContent.hide();
					$previewToggleIcon.removeClass('wnt-icon-triangle-up').addClass('wnt-icon-triangle-down');
				};

				var onPreviewToggle = function () {
					if ($previewBox.is('.wolfnet_boxCollapsed')) {
						expandPreview();
					} else {
						collapsePreview();
					}
				};

				collapsePreview();

				$previewHeader.click(onPreviewToggle).css('cursor', 'pointer').append($previewToggleBtn);

			}


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
