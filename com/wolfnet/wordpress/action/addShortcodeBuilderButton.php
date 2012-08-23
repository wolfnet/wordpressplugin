<?php

/**
 * This action is responsible for enqueuing any admin resources such as JavaScript and CSS that are
 * needed for any code generated in the WordPress admin for the plugin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         addShortcodeBuilderButton.php
 * @extends       com_ajmichels_wppf_action_action
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 */
class com_wolfnet_wordpress_action_addShortcodeBuilderButton
extends com_ajmichels_wppf_action_action
{


	/* PROPERTIES ******************************************************************************* */

	/**
	 * This property holds the URL string to the plugin directory. This URL is needed to accurately
	 * define the path to the resource files.
	 *
	 * @type  string
	 *
	 */
	private $pluginUrl = '';


	/* PUBLIC METHODS *************************************************************************** */

	/**
	 * This method is executed by the ActionManager when any hooks that this action is registered to
	 * are encountered.
	 *
	 * @return  void
	 *
	 */
	public function execute ()
	{
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
			return;
		}
		else {
			if ( get_user_option( 'rich_editing' ) == 'true' ) {
				add_filter( 'mce_external_plugins', array( &$this, 'addPluginJavaScript' ) );
				add_filter( 'mce_buttons', array( &$this, 'registerButton' ) );
			}
		}
	}


	public function addPluginJavaScript ( array $plugins )
	{
		$plugins['wolfnetShortcodeBuilder'] = $this->getPluginUrl() . 'js/tinymce.wolfnet_shortcode_builder.src.js';
		return $plugins;
	}


	public function registerButton ( array $buttons )
	{
		array_push( $buttons, '|', 'wolfnetShortcodeBuilderButton' );
		return $buttons;
	}


	/* ACCESSOR METHODS ************************************************************************* */

	/**
	 * GETTER: This method is a getter for the pluginUrl property.
	 *
	 * @return  string  The absolute URL to this plugin's directory.
	 *
	 */
	public function getPluginUrl ()
	{
		return $this->pluginUrl;
	}


	/**
	 * SETTER: This method is a setter for the pluginUrl property.
	 *
	 * @param   string  $url  The absolute URL to this plugin's directory.
	 * @return  void
	 *
	 */
	public function setPluginUrl ( $url )
	{
		$this->pluginUrl = $url;
	}


}