<?php

/**
 * WolfNet Widget Theme module
 *
 * This module represents the widget theme and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2016 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_WidgetTheme
{
    /**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;

	/**
	* This property holds the widget themes.
	* @var array
	*/
	protected $widgetThemes;

	/**
	* This property holds the widget selectors.
	* @var array
	*/
	protected $widgetSelectors;


	public function __construct($plugin) {
		$this->plugin = $plugin;

		$this->widgetThemes = array(
			'ash'      => $this->createTheme('ash',     'Classic'),
			'birch'    => $this->createTheme('birch',   'Modern'),
			'cedar'    => $this->createTheme('cedar',   'Modern 2'),
			'dogwood'  => $this->createTheme('dogwood', 'Modern 3'),
		);

		$this->widgetSelectors = array(
			'featured'   => '.wolfnet_widget.wolfnet-theme-cedar.wolfnet_featuredListings',
			'grid'       => '.wolfnet_widget.wolfnet-theme-cedar.wolfnet_listingGrid',
		);

	}


	public function getDefaults()
	{

		return array(
			'widgetTheme' => 'ash',
			'colors'      => array('#333'),
			'opacity'     => 50,
		);

	}


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        if (array_key_exists('keyid', $options) && $options['keyid'] != '') {
            $keyid = $options['keyid'];
        } else {
            $keyid = 1;
        }

        return $options;

    }


	public function getThemeOptions()
	{
		return $this->widgetThemes;
	}


	public function getTheme($name)
	{
		return $this->widgetThemes[$name];
	}


	private function createTheme($name, $label)
	{
		$widgetTheme = array(
			'name'           => $name,
			'label'          => $label,
			'styleName'      => 'wolfnet-' . $name,
			'styleFile'      => 'wolfnet.' . $name . '.min.css',
			'previewImg'     => 'theme-preview-' . $name . '.png',
			'customStyles'   => array(),
		);

		switch ($name) {
			case 'cedar':
				array_push(
					$widgetTheme['customStyles'],
					array (
						'selector' => '.wolfnet_listing .wolfnet_listingHead .wolfnet_listingInfo',
						'style' => 'background-color: rgba([[color_0_r]], [[color_0_g]], [[color_0_b]], [[opacity]]);',
					)
				);
				break;
			case 'dogwood':
				break;
		}

		return $widgetTheme;

	}


	// Generate styles for a widget theme
	public function generateCss ($widgetTheme, $styleOptions) {
		$themeCss = '';
		$themeStyle = '';

		foreach ($widgetTheme['customStyles'] as $customStyle) {
			$themeStyle = $this->generateStyle($customStyle['style'], $styleOptions);
			$themeCss .= $this->generateStyleRule($customStyle['selector'], $themeStyle);
		}

		return $themeCss;

	}


	// Generate a style from custom options
	private function generateStyle ($styleTemplate, $styleOptions) {
		$styleOptions = array_merge($this->getDefaults(), $styleOptions);

		foreach ($styleOptions as $optKey => $optVal) {

			switch ($optKey) {

				case 'colors':

					$colors = $optVal;

					foreach ($colors as $colorKey => $colorVal) {

						// Clean hex string
						$colorHexFull = str_replace('#', '', $colorVal);

						// Extract base-10 RGB values
						$colorPartLen = (strlen($colorHexFull) == 3 ? 1 : 2);
						$colorHex = str_split($colorHexFull, $colorPartLen);

						$colorParts = array(
							'r' => $colorHex[0],
							'g' => $colorHex[1],
							'b' => $colorHex[2],
						);

						foreach ($colorParts as $colorPartKey => $colorPartVal) {

							// Convert single-digit values to multiples of hex 11
							if ($colorPartLen == 1) {
								$colorParts[$colorPartKey] .= $colorPartVal;
							}

							$colorPartDec = hexdec($colorParts[$colorPartKey]);

							// Replace base-10 color parts
							$placeholder = '[[color_' . $colorKey . '_' . $colorPartKey . ']]';
							$styleTemplate = str_replace($placeholder, $colorPartDec, $styleTemplate);

						}

						// Replace hex color instances
						$placeholderColor = '[[color_' . $colorKey . ']]';
						$styleTemplate = str_replace($placeholder, $colorVal, $styleTemplate);

					}

					break;

				case 'opacity':
					$styleTemplate = str_replace('[[opacity]]', ($optVal / 100), $styleTemplate);
					break;

			}

		}

		return $styleTemplate;

	}


	// Generate a single style rule from a selector and style
	private function generateStyleRule ($selector, $style) {
		$styleRules = array();
		$styleRule = '';

		foreach ($this->widgetSelectors as $widgetSelector) {
			array_push($styleRules, $widgetSelector . ' ' . $selector);
		}

		$styleRule = implode(', ', $styleRules) . '{' . $style . '}';

		return $styleRule;

	}

}
