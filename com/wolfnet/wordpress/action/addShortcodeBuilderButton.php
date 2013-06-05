<?php

/**
 * This action is responsible for enqueuing any admin resources such as JavaScript and CSS that are
 * needed for any code generated in the WordPress admin for the plugin.
 *
 * @package       com.wolfnet.wordpress
 * @subpackage    action
 * @title         addShortcodeBuilderButton.php
 * @extends       com_greentiedev_wppf_action_action
 * @contributors  AJ Michels (aj.michels@wolfnet.com)
 * @version       1.0
 * @copyright     Copyright (c) 2012, WolfNet Technologies, LLC
 *
 *                This program is free software; you can redistribute it and/or
 *                modify it under the terms of the GNU General Public License
 *                as published by the Free Software Foundation; either version 2
 *                of the License, or (at your option) any later version.
 *
 *                This program is distributed in the hope that it will be useful,
 *                but WITHOUT ANY WARRANTY; without even the implied warranty of
 *                MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *                GNU General Public License for more details.
 *
 *                You should have received a copy of the GNU General Public License
 *                along with this program; if not, write to the Free Software
 *                Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */
class com_wolfnet_wordpress_action_addShortcodeBuilderButton
extends com_greentiedev_wppf_action_action
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
		$url = $this->getPluginUrl();
		echo '<script type="text/javascript">var wordpressBaseUrl = "' . site_url() . '";</script>';
		wp_enqueue_script(
			'wolfnetshortcodebuilder',
			$url . 'js/jquery.wolfnet_shortcode_builder.src.js',
			array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-effects-core' )
		);
		$plugins['wolfnetShortcodeBuilder'] = $url . 'js/tinymce.wolfnet_shortcode_builder.src.js';
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
