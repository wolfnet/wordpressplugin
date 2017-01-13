<?php

/**
 * @title         Wolfnet_Admin.php
 * @copyright     Copyright (c) 2012 - 2015, WolfNet Technologies, LLC
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
 */


/**
 * This class us used when the user is logged in as an admin user.
 */
class Wolfnet_Admin extends Wolfnet_Plugin
{


	/* Properties ******************************************************************************* */
	/*  ____                            _   _                                                     */
	/* |  _ \ _ __ ___  _ __   ___ _ __| |_(_) ___  ___                                           */
	/* | |_) | '__/ _ \| '_ \ / _ \ '__| __| |/ _ \/ __|                                          */
	/* |  __/| | | (_) | |_) |  __/ |  | |_| |  __/\__ \                                          */
	/* |_|   |_|  \___/| .__/ \___|_|   \__|_|\___||___/                                          */
	/*                 |_|                                                                        */
	/* ****************************************************************************************** */

	/**
	 * This property contains the admin CSS as defined in the Edit CSS page.
	 * @var string
	 */
	public $adminCssOptionKey = "wolfnetCss_adminCss";


	/* Constructor Method *********************************************************************** */
	/*   ____                _                   _                                                */
	/*  / ___|___  _ __  ___| |_ _ __ _   _  ___| |_ ___  _ __                                    */
	/* | |   / _ \| '_ \/ __| __| '__| | | |/ __| __/ _ \| '__|                                   */
	/* | |__| (_) | | | \__ \ |_| |  | |_| | (__| || (_) | |                                      */
	/*  \____\___/|_| |_|___/\__|_|   \__,_|\___|\__\___/|_|                                      */
	/*                                                                                            */
	/* ****************************************************************************************** */

	/**
	 * prepare the class for use.
	 * @param Object $wolfnet Pass in an instance or the Wolfnet class
	 * @return void
	 */
	public function __construct($wolfnet)
	{
		$this->pluginFile = dirname(dirname(__FILE__)) . '/wolfnet.php';
		// sets url
		$this->setUrl();

		// Register admin only actions.
		do_action('wolfnet_pre_adminMenu');
		add_action('admin_menu', array(&$this, 'adminMenu'));
		do_action('wolfnet_post_adminMenu');

		do_action('wolfnet_pre_adminInit');
		add_action('admin_init', array(&$this, 'adminInit'));
		do_action('wolfnet_post_adminInit');

		do_action('wolfnet_pre_adminScripts');
		add_action('admin_enqueue_scripts', array(&$this, 'adminScripts'));
		do_action('wolfnet_post_adminScripts');

		do_action('wolfnet_pre_adminStyles');
		add_action('admin_enqueue_scripts', array(&$this, 'adminStyles'));
		do_action('wolfnet_post_adminStyles');

		do_action('wolfnet_pre_adminPrintStyles');
		add_action('admin_print_styles', array(&$this, 'adminPrintStyles'), 1000);
		do_action('wolfnet_post_adminPrintStyles');

		do_action('wolfnet_pre_adminEndSession');
		add_action('wp_logout', array(&$this, 'adminEndSession'));
		do_action('wolfnet_post_adminEndSession');

		do_action('wolfnet_pre_adminEndSession');
		add_action('wp_login', array(&$this, 'adminEndSession'));
		do_action('wolfnet_post_adminEndSession');

		// Register admin only filters.
		$this->addFilter(array(
			array('mce_external_plugins', 'sbMcePlugin'),
			array('mce_buttons',          'sbButton'),
		));

	}


    /* Public Methods *************************************************************************** */
    /*  ____        _     _ _        __  __      _   _               _                            */
    /* |  _ \ _   _| |__ | (_) ___  |  \/  | ___| |_| |__   ___   __| |___                        */
    /* | |_) | | | | '_ \| | |/ __| | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                       */
    /* |  __/| |_| | |_) | | | (__  | |  | |  __/ |_| | | | (_) | (_| \__ \                       */
    /* |_|    \__,_|_.__/|_|_|\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                       */
    /*                                                                                            */
    /* ****************************************************************************************** */


    /**
     * This method is a callback for the 'admin_enqueue_scripts' hook. Any JavaScript files (and
     * their dependencies) which are needed by the plugin for admin interfaces are registered in
     * this method.
     * @return void
     */
    public function adminScripts()
    {
        do_action($this->preHookPrefix . 'enqueueAdminResources');

        // JavaScript
        $scripts = array(
            'wp-color-picker',
            'jquery-ui-slider',
            'wolfnet-admin',
            'wolfnet-shortcode-builder',
            'wolfnet-search-manager',
        );

        foreach ($scripts as $script) {
            wp_enqueue_script($script);
        }

    }


	/**
	 * This method is a callback for the 'admin_enqueue_scripts' hook. Any CSS files which are
	 * needed by the plugin for areas areas are registered in this method.
	 * @return void
	 */
	public function adminStyles()
	{

		// CSS
		$styles = array(
			'wolfnet-jquery-ui',
			'wp-color-picker',
			'wolfnet',
			'wolfnet-agent',
			'icomoon',
			'google-fonts',
			'wolfnet-admin',
		);

		$widgetThemes = $GLOBALS['wolfnet']->widgetTheme->getThemeOptions();
		foreach ($widgetThemes as $widgetTheme) {
			array_push($styles, $widgetTheme['styleName']);
		}

		array_push($styles, 'wolfnet-theme-custom');

		foreach ($styles as $style) {
			wp_enqueue_style($style);
		}

		do_action($this->postHookPrefix . 'enqueueAdminResources');

	}


    /**
     * This method is a callback for the 'admin_init' hook. Any processes which are unique to the
     * admin interface of WordPress and have not been run as either part of the constructor method
     * or the 'init' hook are run in this method.
     * @return void
     */
    public function adminInit()
    {
        // Register Options
        register_setting($this->optionGroup, Wolfnet_Service_ProductKeyService::PRODUCT_KEY_OPTION);
        register_setting($this->optionGroup, Wolfnet_Plugin::SSL_WP_OPTION);
        register_setting($this->WidgetThemeOptionGroup, $this->widgetThemeOptionKey);
        register_setting($this->ColorOptionGroup, $this->themeColorsOptionKey);
        register_setting($this->ColorOptionGroup, $this->themeOpacityOptionKey);
        register_setting($this->CssOptionGroup, $this->publicCssOptionKey);
        register_setting($this->CssOptionGroup, $this->adminCssOptionKey);

        // Register Shortcode Builder Button
        $canEditPosts = current_user_can('edit_posts');
        $canEditPages = current_user_can('edit_pages');
        $richEditing  = get_user_option('rich_editing');

        // Register Ajax Actions
        $GLOBALS['wolfnet']->ajax->registerAdminAjaxActions();

        $this->adminStartSession();

        // Set the key properly in session. This is mainly for the search manager.
        if(!array_key_exists('keyid', $_SESSION) && !array_key_exists('keyid', $_REQUEST)) {
            $_SESSION['keyid'] = 1;
        }
        if(array_key_exists('keyid', $_REQUEST)) {
            $_SESSION['keyid'] = $_REQUEST['keyid'];
        }

         /* If we are serving up the search manager page we need to get the search manager HTML from
          * the MLSFinder server now so that we can set cookies. */

        $pageKeyExists = array_key_exists('page', $_REQUEST);
        $pageIsSM = ($pageKeyExists) ? ($_REQUEST['page']=='wolfnet_plugin_search_manager') : false;

        if ($pageKeyExists && $pageIsSM) {
            try {
                /* Now that we know we are dealing with a page that needs the search manager check
                   if the key is valid. */
                $productKey = $GLOBALS['wolfnet']->keyService->getById($_SESSION['keyid']);

                if ($GLOBALS['wolfnet']->keyService->isValid($productKey)) {
                    $GLOBALS['wolfnet']->smHttp = $GLOBALS['wolfnet']->searchManager->searchManagerHtml($productKey);
                }

            } catch (Wolfnet_Exception $e) {
                $GLOBALS['wolfnet']->smHttp = $GLOBALS['wolfnet']->displayException($e);
            }

        }

    }


    /**
     * This method is a callback for the 'admin_menu' hook. This method is used to create any admin
     * menu pages for the plugin.
     * @return void
     */
    public function adminMenu()
    {
        $lvl = 'administrator';

        do_action($this->preHookPrefix . 'createAdminPages'); // Legacy hook

        $pgs = array(
            array(
                'title' => 'WolfNet <span class="wolfnet_sup">&reg;</span>',
                'key'   => 'wolfnet_plugin_settings',
                //'icon'  => $this->url . 'img/wp_wolfnet_nav.png',
                'icon'  => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+DQo8c3ZnDQogICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iDQogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIg0KICAgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIg0KICAgeG1sbnM6c3ZnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyINCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyINCiAgIGlkPSJzdmc1Njk2Ig0KICAgdmVyc2lvbj0iMS4xIg0KICAgdmlld0JveD0iMCAwIDEzMi44MjA4NCAxNjkuMzMzMzMiDQogICBoZWlnaHQ9IjY0MCINCiAgIHdpZHRoPSI1MDIiPg0KICA8ZGVmcw0KICAgICBpZD0iZGVmczU2OTAiIC8+DQogIDxtZXRhZGF0YQ0KICAgICBpZD0ibWV0YWRhdGE1NjkzIj4NCiAgICA8cmRmOlJERj4NCiAgICAgIDxjYzpXb3JrDQogICAgICAgICByZGY6YWJvdXQ9IiI+DQogICAgICAgIDxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0Pg0KICAgICAgICA8ZGM6dHlwZQ0KICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPg0KICAgICAgICA8ZGM6dGl0bGU+PC9kYzp0aXRsZT4NCiAgICAgIDwvY2M6V29yaz4NCiAgICA8L3JkZjpSREY+DQogIDwvbWV0YWRhdGE+DQogIDxnDQogICAgIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0zLjEzODI2MjYsMjkuNTcxNDgpIg0KICAgICBpZD0ibGF5ZXIxIj4NCiAgICA8Zw0KICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDIuMTAxMTYwNiwwLDAsLTIuMTAxMTYwNiwxMTQuNDY2MzEsMjMuNjA0MTM4KSINCiAgICAgICBpZD0iZzM3MDAiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzAyIg0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDAuNDE3LDIuNDE3IDEuMjQzLC0wLjExOCAwLDAgMC42NTYsLTIuMTcxIC0wLjIzNiwtMi41MzcgMCwwIC0xLjM2MSwtMC4yOTYgLTEuMTIzLDEuMjkyIDAsMCAwLDAuODMzIDAuMTE2LDEuMzYzIiAvPg0KICAgIDwvZz4NCiAgICA8Zw0KICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDIuMTAxMTYwNiwwLDAsLTIuMTAxMTYwNiw1Ny40MTcxMDQsNjguNjA2NzkyKSINCiAgICAgICBpZD0iZzM3MDQiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzA2Ig0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDEuNzA4LDQuMDY2IDUuNzg2LDQuMDY2IDAsMCAzLjE4MiwwIDYuMDIyLC0zLjI0NiAwLDAgMy4zMDcsLTUuNzI4IDUuMzc1LC03Ljg1NiAwLDAgNi42NjMsLTYuMTQ1IDIuOTQ0LC0xMC43MzIgMCwwIC0xLjQ4LC0zLjEzMiAtNy4yNTgsLTEuNzE2IDAsMCAtNS42MDcsMS43MTYgLTYuNjc5LDEuMzYxIDAsMCAtNC4wNjIsLTAuNDc4IC02LjYwNywtMS4zNjEgMCwwIC01LjYwMywtMS44OTIgLTcuOTY1LDEuNDc5IDAsMCAtMS41NzksMi4zNTcgMC4wNjksNi44OSAwLDAgMS44MjksMy4wMTggMy43Miw0Ljc4NyAwLDAgMy45NDUsNS4yNjkgNC41OTMsNi4zMjgiIC8+DQogICAgPC9nPg0KICAgIDxnDQogICAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMi4xMDExNjA2LDAsMCwtMi4xMDExNjA2LDIwLjk1ODU5Nyw0Mi45MzUyNjEpIg0KICAgICAgIGlkPSJnMzcwOCINCiAgICAgICBzdHlsZT0iZGlzcGxheTppbmxpbmU7ZmlsbDojOWNhMWE2O2ZpbGwtb3BhY2l0eToxIj4NCiAgICAgIDxwYXRoDQogICAgICAgICBpZD0icGF0aDM3MTAiDQogICAgICAgICBzdHlsZT0iZmlsbDojOWNhMWE2O2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIg0KICAgICAgICAgZD0ibSAwLDAgYyAwLDAgMS41MzgsNy40NDEgNy45NTksMi4wMDEgMCwwIDQuNTU5LC01LjAyIDMuNzksLTkuMjAxIDAsMCAtMS42NiwtNy4yMDMgLTguNDk2LC0zLjkxIDAsMCAtNS42MTQsMy4wMjQgLTMuMjUzLDExLjExIiAvPg0KICAgIDwvZz4NCiAgICA8Zw0KICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDIuMTAxMTYwNiwwLDAsLTIuMTAxMTYwNiwyMy4wNjc5MjYsMjMuNjA0MTM4KSINCiAgICAgICBpZD0iZzM3MTIiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzE0Ig0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDAuNTM0LDEuODkyIDEuMTc3LDAuMTE1IDAsMCAwLjU4OCwtMi4wNTkgMC4zMDEsLTIuNDA0IDAsMCAtMC42NTYsLTEuMzE4IC0xLjgyNywwLjE2NyAwLDAgLTAuMDU4LDEuMjMxIDAuMzQ5LDIuMTIyIiAvPg0KICAgIDwvZz4NCiAgICA8Zw0KICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDIuMTAxMTYwNiwwLDAsLTIuMTAxMTYwNiw0Ny4xMjI2MzgsNS45ODg0NDY0KSINCiAgICAgICBpZD0iZzM3MTYiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzE4Ig0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDMuMzcxLDUuMjQ4IDYuNTUsLTAuNTM1IDAsMCAyLjM2OSwtNC42NzUgMi4xODcsLTkuMjYxIDAsMCAwLjM1OCwtNi40ODggLTYuNTQ3LC02LjE0MyAwLDAgLTUuMzEzLDAuNDA3IC01Ljg1Miw3LjQzNyAwLDAgMC4zNTYsNC4zMTEgMy42NjIsOC41MDIiIC8+DQogICAgPC9nPg0KICAgIDxnDQogICAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMi4xMDExNjA2LDAsMCwtMi4xMDExNjA2LDUzLjE5NDE1NSwtMTQuMjE2NzI3KSINCiAgICAgICBpZD0iZzM3MjAiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzIyIg0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDAuNjUsMi4zMTEgMS4yNDIsLTAuMDUzIDAsMCAwLjgyNiwtMi4xMjggLTAuMDU2LC0zLjE5MyAwLDAgLTEuNTMzLC0wLjg4MiAtMS44MzQsMC43NjcgMCwwIC0wLjA1MSwxLjEyIDAuNjQ4LDIuNDc5IiAvPg0KICAgIDwvZz4NCiAgICA8Zw0KICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDIuMTAxMTYwNiwwLDAsLTIuMTAxMTYwNiw3NS44OTg2ODgsMTEuMDczMDIyKSINCiAgICAgICBpZD0iZzM3MjQiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzI2Ig0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDIuNjAyLDcuOTU5IDYuNzkxLDIuMjk3IDAsMCAyLjIzMSwtMi4yOTcgMi43NzMsLTMuMzA2IDAuNTMxLC0xLjAwMyAyLjEyOCwtNS41OTYgMS4wMSwtOC41NjEgMCwwIC0yLjQ4MSwtNi43NzQgLTkuMzkyLC0yLjk5OCAwLDAgLTIuODk0LDEuNzAxIC0yLjEyOSw3Ljc4OCAwLDAgMC4xNzYsMy4xMjEgMC45NDcsNC43OCIgLz4NCiAgICA8L2c+DQogICAgPGcNCiAgICAgICB0cmFuc2Zvcm09Im1hdHJpeCgyLjEwMTE2MDYsMCwwLC0yLjEwMTE2MDYsODEuNjA5MDA2LC0xMy43MjAyMjcpIg0KICAgICAgIGlkPSJnMzcyOCINCiAgICAgICBzdHlsZT0iZGlzcGxheTppbmxpbmU7ZmlsbDojOWNhMWE2O2ZpbGwtb3BhY2l0eToxIj4NCiAgICAgIDxwYXRoDQogICAgICAgICBpZD0icGF0aDM3MzAiDQogICAgICAgICBzdHlsZT0iZmlsbDojOWNhMWE2O2ZpbGwtb3BhY2l0eToxO2ZpbGwtcnVsZTpub256ZXJvO3N0cm9rZTpub25lIg0KICAgICAgICAgZD0ibSAwLDAgYyAwLDAgMC4zNTUsMi40MTUgMS41MzMsMC4wNTMgMCwwIDEuMDU5LC0yLjc3IDAuMjI4LC0yLjk5NyAwLDAgLTEuNzYxLC0wLjU5NiAtMS44NzcsMC41MjggMCwwIC0wLjE4MiwxLjY1IDAuMTE2LDIuNDE2IiAvPg0KICAgIDwvZz4NCiAgICA8Zw0KICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KDIuMTAxMTYwNiwwLDAsLTIuMTAxMTYwNiwxMDEuNzE5ODYsMzcuODgxMzQzKSINCiAgICAgICBpZD0iZzM3MzIiDQogICAgICAgc3R5bGU9ImRpc3BsYXk6aW5saW5lO2ZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MSI+DQogICAgICA8cGF0aA0KICAgICAgICAgaWQ9InBhdGgzNzM0Ig0KICAgICAgICAgc3R5bGU9ImZpbGw6IzljYTFhNjtmaWxsLW9wYWNpdHk6MTtmaWxsLXJ1bGU6bm9uemVybztzdHJva2U6bm9uZSINCiAgICAgICAgIGQ9Im0gMCwwIGMgMCwwIDUuNzczLDUuMDM2IDcuODM4LC0xLjgxOSAwLDAgMS45NTMsLTcuMzg1IC0xLjQ2OSwtMTAuNTU5IDAsMCAtNC42NzQsLTQuOTcyIC04LjYyMywwLjUxOSAwLDAgLTEuODM3LDEuOTUxIC0wLjU5Niw3LjE5MiAwLDAgMS4yNDUsMy4zNzggMi44NSw0LjY2NyIgLz4NCiAgICA8L2c+DQogIDwvZz4NCjwvc3ZnPg0K',
                ),
            array(
                'title' => 'General Settings',
                'key'   => 'wolfnet_plugin_settings',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amSettingsPage')
                ),
            array(
                'title' => 'Appearance',
                'key'   => 'wolfnet_plugin_style',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amStylePage')
            ),
            array(
                'title' => 'Edit CSS',
                'key'   => 'wolfnet_plugin_css',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amEditCssPage')
            ),
            array(
                'title' => 'Search Manager',
                'key'   => 'wolfnet_plugin_search_manager',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amSearchManagerPage')
                ),
            array(
                'title' => 'Support',
                'key'   => 'wolfnet_plugin_support',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amSupportPage')
                ),
            );

        add_menu_page(
            $pgs[0]['title'],
            $pgs[0]['title'],
            $lvl,
            $pgs[0]['key'],
            null,
            $pgs[0]['icon']
        );

        $l = count($pgs);

        for ($i=1; $i<$l; $i++) {
            add_submenu_page(
                $pgs[0]['key'],
                $pgs[$i]['title'],
                $pgs[$i]['title'],
                $lvl,
                $pgs[$i]['key'],
                $pgs[$i]['cb']
            );

        }

        do_action($this->postHookPrefix . 'createAdminPages'); // Legacy hook

    }


    /**
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles()
    {
        $adminCss = $this->getAdminCss();
        echo '<style>' . $adminCss . '</style>';

    }


    public function getAdminCss()
    {
        return get_option($this->adminCssOptionKey);
    }


    public function adminStartSession()
    {
        if(!session_id()) {
            session_start();
        }
    }


    public function adminEndSession()
    {
        session_destroy();
    }


}
