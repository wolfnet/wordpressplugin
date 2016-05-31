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

.wolfnet_widget.wolfnet-theme-cedar.wolfnet_featuredListings .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo,
.wolfnet_widget.wolfnet-theme-cedar.wolfnet_listingGrid .wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo {
	background-color: rgba(<?php echo getRGBA($args['colors'][0], $args['opacity']); ?>);
}
