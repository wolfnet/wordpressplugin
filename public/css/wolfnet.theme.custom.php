<?php

	header('Content-type: text/css; charset: UTF-8');

	$styleDefaults = array(
		'colors'   => array('#333'),
		'opacity'  => 80,
	);

	$userOptions = array();

	if (!empty($_REQUEST['colors'])) {
		$userOptions['colors'] = explode(',', $_REQUEST['colors']);
	}

	if (!empty($_REQUEST['opacity'])) {
		$userOptions['opacity'] = $_REQUEST['opacity'];
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


/* Cedar Theme */

	.wolfnet_widget.wolfnet-theme-cedar.wolfnet_featuredListings .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo,
	.wolfnet_widget.wolfnet-theme-cedar.wolfnet_listingGrid      .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo {
		background-color: rgba(<?php echo getRGBA($args['colors'][0], $args['opacity']); ?>);
	}


/* Dogwood Theme */

	.wolfnet_widget.wolfnet-theme-dogwood.wolfnet_featuredListings .wolfnet_listing .wolfnet_listingHead .wolfnet_detailsLink,
	.wolfnet_widget.wolfnet-theme-dogwood.wolfnet_listingGrid      .wolfnet_listing .wolfnet_listingHead .wolfnet_detailsLink {
		background-color: rgba(<?php echo getRGBA($args['colors'][0], $args['opacity']); ?>);
	}
