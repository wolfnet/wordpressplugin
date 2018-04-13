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
            $_SESSION['keyid'] = sanitize_key($_REQUEST['keyid']);
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
                'title' => 'WolfNet &reg;',
                'key'   => 'wolfnet_plugin_settings',
                //'icon'  => $this->url . 'img/wp_wolfnet_nav.png',
                'icon'  => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDUuMjkxNjY2NSA1LjI5MTY2NjUiPjxnIGZpbGw9IiM5Y2ExYTYiPjxwYXRoIGQ9Ik00LjM3NSAxLjUzOHMuMDM0LS4xOTYuMS4wMWMwIDAgLjA1NC4xNzUtLjAxOS4yMDQgMCAwLS4xMS4wMjQtLjA5LS4xMDQgMCAwIDAtLjA2Ny4wMDktLjExTTIuMTc5IDMuMjdzLjEzOC0uMzI5LjQ2OC0uMzI5YzAgMCAuMjU3IDAgLjQ4Ny4yNjMgMCAwIC4yNjcuNDYzLjQzNS42MzUgMCAwIC41MzkuNDk3LjIzOC44NjggMCAwLS4xMi4yNTMtLjU4Ny4xMzkgMCAwLS40NTQtLjEzOS0uNTQtLjExIDAgMC0uMzI5LjAzOS0uNTM1LjExIDAgMC0uNDUzLjE1My0uNjQ0LS4xMiAwIDAtLjEyOC0uMTkuMDA2LS41NTcgMCAwIC4xNDctLjI0NC4zLS4zODcgMCAwIC4zMi0uNDI2LjM3Mi0uNTEyTS43NzUgMi4yODJTLjkgMS42OCAxLjQyIDIuMTJjMCAwIC4zNjkuNDA2LjMwNy43NDQgMCAwLS4xMzUuNTgzLS42ODcuMzE2IDAgMC0uNDU1LS4yNDQtLjI2NC0uODk4TS44NTcgMS41MzhzLjA0My0uMTUzLjA5NS0uMDFjMCAwIC4wNDcuMTY3LjAyNC4xOTUgMCAwLS4wNTMuMTA2LS4xNDgtLjAxNCAwIDAtLjAwNC0uMS4wMjktLjE3MU0xLjc4My44NnMuMjcyLS40MjUuNTMuMDQzYzAgMCAuMTkxLjM3OC4xNzYuNzQ5IDAgMCAuMDMuNTI1LS41My40OTcgMCAwLS40MjktLjAzMy0uNDczLS42MDIgMCAwIC4wMy0uMzQ4LjI5Ny0uNjg3TTIuMDE2LjA4MnMuMDUzLS4xODcuMS4wMDRjMCAwIC4wNjguMTcyLS4wMDQuMjU4IDAgMC0uMTI0LjA3Mi0uMTQ4LS4wNjIgMCAwLS4wMDQtLjA5LjA1Mi0uMk0yLjg5IDEuMDU1UzMuMS40MTIgMy40NC44N2MwIDAgLjE4LjE4NS4yMjQuMjY3LjA0My4wODEuMTcyLjQ1My4wODIuNjkyIDAgMC0uMjAxLjU0OC0uNzYuMjQzIDAgMC0uMjM0LS4xMzgtLjE3Mi0uNjMgMCAwIC4wMTQtLjI1Mi4wNzYtLjM4N00zLjExLjFzLjAyOS0uMTk0LjEyNC0uMDAzYzAgMCAuMDg2LjIyNC4wMTkuMjQyIDAgMC0uMTQzLjA0OC0uMTUyLS4wNDMgMCAwLS4wMTUtLjEzMy4wMS0uMTk1TTMuODg0IDIuMDg3cy40NjctLjQwNy42MzQuMTQ3YzAgMCAuMTU4LjU5OC0uMTE5Ljg1NCAwIDAtLjM3OC40MDMtLjY5Ny0uMDQyIDAgMC0uMTQ5LS4xNTctLjA0OC0uNTgxIDAgMCAuMS0uMjczLjIzLS4zNzgiLz48L2c+PC9zdmc+',
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
                'title' => 'Old Search Manager',
                'key'   => 'wolfnet_plugin_search_manager',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amSearchManagerPage')
                ),
			array(
				'title' => 'Search Manager',
				'key'   => 'wolfnet_plugin_search',
				'cb'    => array(&$GLOBALS['wolfnet']->views, 'amSearchPage')
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
