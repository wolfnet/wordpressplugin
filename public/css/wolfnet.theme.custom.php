<?php

	header('Content-type: text/css; charset: UTF-8');

	$styleDefaults = array(
		'colors'   => array('#333'),
		'opacity'  => 80,
	);

	$userOptions = array();

	if (!empty($_REQUEST['colors'])) {
		$userOptions['colors'] = explode(',', FILTER_SANITIZE_NUMBER_FLOAT($_REQUEST['colors']));
	}

	if (!empty($_REQUEST['opacity'])) {
		$userOptions['opacity'] = FILTER_SANITIZE_NUMBER_FLOAT($_REQUEST['opacity']);
	}

	$args = array_merge($styleDefaults, $userOptions);


	function getColorPartDec ($colorPartHex, $colorPartLen = 2) {

		// Convert single-digit values to multiples of hex 11
		if ($colorPartLen == 1) {
			$colorPartHex .= $colorPartHex;
		}

		return hexdec($colorPartHex);

	}


	function getColorParts ($colorHex) {

		// Clean hex string
		$colorHex = str_replace('#', '', $colorHex);

		// Extract base-10 RGB values
		$colorPartLen = (strlen($colorHex) == 3 ? 1 : 2);
		$colorHexParts = str_split($colorHex, $colorPartLen);

		return array(
			'r' => array( 'hex' => $colorHexParts[0], 'dec' => getColorPartDec($colorHexParts[0], $colorPartLen) ),
			'g' => array( 'hex' => $colorHexParts[1], 'dec' => getColorPartDec($colorHexParts[1], $colorPartLen) ),
			'b' => array( 'hex' => $colorHexParts[2], 'dec' => getColorPartDec($colorHexParts[2], $colorPartLen) ),
		);

	}


	function getHex (array $color) {
		return '#' . $color['r']['hex'] . $color['g']['hex'] . $color['b']['hex'];
	}

	function getRGB (array $color) {
		return $color['r']['dec'] . ','
			. $color['g']['dec'] . ','
			. $color['b']['dec'];
	}

	function getRGBA (array $color, $opacity) {
		return getRGB($color) . ',' . $opacity;
	}

	function getARGB (array $color, $opacity) {
		$hexOpacity = dechex($opacity);

		if (strlen($hexOpacity) == 1) {
			$hexOpacity = '0' . $hexOpacity;
		}

		return '#' . $hexOpacity . $color['r']['hex'] . $color['g']['hex'] . $color['b']['hex'];

	}


	function vertGradient(array $startColor, array $endColor, $startOpacity=1, $endOpacity=1) {
		$gradientCSS = '';
		$startRGBA   = getRGBA($startColor, $startOpacity);
		$endRGBA     = getRGBA($endColor,   $endOpacity);
		$startARGB   = getARGB($startColor, $startOpacity);
		$endARGB     = getARGB($endColor,   $endOpacity);

		// FF3.6-15
		$gradientCSS .= 'background: -moz-linear-gradient('
			. 'top, rgba(' . $startRGBA . ') 0%, rgba(' . $endRGBA . ') 100%'
			. '); ';

		// Chrome10-25,Safari5.1-6
		$gradientCSS .= 'background: -webkit-linear-gradient('
			. 'top, rgba(' . $startRGBA . ') 0%, rgba(' . $endRGBA . ') 100%'
			. '); ';

		// W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+
		$gradientCSS .= 'background: linear-gradient('
			. 'to bottom, rgba(' . $startRGBA . ') 0%, rgba(' . $endRGBA . ') 100%'
			. '); ';

		// IE6-9
		$gradientCSS .= '-ms-filter: ~"progid:DXImageTransform.Microsoft.gradient('
			. 'startColorstr=\'' . $startARGB . '\', endColorstr=\'' . $endARGB . '\', GradientType=0'
			. ')"; '
			. 'filter: ~"progid:DXImageTransform.Microsoft.gradient('
			. 'startColorstr=\'' . $startARGB . '\', endColorstr=\'' . $endARGB . '\', GradientType=0'
			. ')"; ';

		return $gradientCSS;

	}


	// Get the color parts
	foreach ($args['colors'] as $colorKey => $colorVal) {
		$args['colors'][$colorKey] = getColorParts($colorVal);
	}

	// Make the opacity a percentage
	$args['opacity'] /= 100;

?>

/* Agent Pages */

	.wolfnet_widget.wolfnet_ao .wnt-btn.wnt-btn-primary,
	.wolfnet_widget.wolfnet_ao .wnt-btn.wnt-btn-active {
		background-color: <?php echo getHex($args['colors'][0]); ?>;
	}

	.wolfnet_widget.wolfnet_ao hr {
		border-color: <?php echo getHex($args['colors'][0]); ?>;
	}

	.wolfnet_widget.wolfnet_ao ul.wolfnet_aoLinks li .wnt-icon,
	.wolfnet_widget.wolfnet_ao ul.wolfnet_aoLinks li a,
	.wolfnet_widget.wolfnet_ao ul.wolfnet_aoLinks li a:hover,
	.wolfnet_widget.wolfnet_ao ul.wolfnet_aoLinks li a:active,
	.wolfnet_widget.wolfnet_ao ul.wolfnet_aoLinks li a:visited {
		color: <?php echo getHex($args['colors'][0]); ?>;
	}

	.wolfnet_widget.wolfnet_ao .wolfnet_aoSocial .wnt-icon {
		color: <?php echo getHex($args['colors'][0]); ?>;
	}


/* Birch Theme (Modern Lite) */

	.wolfnet_widget.wolfnet-theme-birch.wolfnet_featuredListings .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo,
	.wolfnet_widget.wolfnet-theme-birch.wolfnet_listingGrid      .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo {
		<?php echo vertGradient($args['colors'][0], $args['colors'][0], 0, $args['opacity']); ?>
	}


/* Cedar Theme (Modern Contrast) */

	.wolfnet_widget.wolfnet-theme-cedar.wolfnet_featuredListings .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo,
	.wolfnet_widget.wolfnet-theme-cedar.wolfnet_listingGrid      .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo {
		background-color: rgba(<?php echo getRGBA($args['colors'][0], $args['opacity']); ?>);
	}


/* Dogwood Theme (Modern Tile) */

	.wolfnet_widget.wolfnet-theme-dogwood.wolfnet_featuredListings .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo .wolfnet_price_rooms,
	.wolfnet_widget.wolfnet-theme-dogwood.wolfnet_listingGrid      .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo .wolfnet_price_rooms {
		background-color: rgba(<?php echo getRGBA($args['colors'][0], $args['opacity']); ?>);
	}
