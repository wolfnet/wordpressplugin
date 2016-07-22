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
			'birch'    => $this->createTheme('birch',   'Modern Lite'),
			'cedar'    => $this->createTheme('cedar',   'Modern Contrast'),
			'dogwood'  => $this->createTheme('dogwood', 'Modern Tile'),
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
			'opacity'     => 80,
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


	public function getStyleArgs ($styleOptions)
	{
		$styleOptions = array_merge($this->getDefaults(), $styleOptions);
		$styleArgs = array();

		foreach ($styleOptions as $optKey => $optVal) {

			$optValStr = '';

			if ($optKey == 'colors') {
				$optVal = str_replace('#', '', implode(',', $optVal));
			}

			array_push($styleArgs, $optKey . '=' . $optVal);

		}

		return implode('&', $styleArgs);

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
		);

		return $widgetTheme;

	}

}
