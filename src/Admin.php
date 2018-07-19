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
                'title' => 'WolfNet <span class="wolfnet_sup">&reg;</span>',
                'key'   => 'wolfnet_plugin_settings',
                // Raster:
                //'icon'  => $this->url . 'img/wp_wolfnet_nav.png',
                // Outline:
                //'icon'  => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAzLjIzIiBoZWlnaHQ9IjExMjIuNSIgdmVyc2lvbj0iMS4xIiB2aWV3Qm94PSIwIDAgODAzLjIzIDExMjIuNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyB0cmFuc2Zvcm09Im1hdHJpeCgxLjMzMzMsMCwwLC0xLjMzMzMsMCwxMTIyLjUpIiBmaWxsPSIjZmNiODEzIj48ZyBzaGFwZS1yZW5kZXJpbmc9ImF1dG8iPjxwYXRoIHRyYW5zZm9ybT0ibWF0cml4KC43NTAwMiAwIDAgLS43NTAwMiAwIDg0MS45KSIgZD0ibTcxNy45NyAwLjA2NjQwNi0xNjUuMjUgMTkxLjg4LTE1MS4yMS0yMi4zNTktMTUxLjIyIDIyLjM1OS0xNjUuMi0xOTEuNzctODAuMjUgMTk5LjQyIDQ5LjIxNSAxODkuOTgtNDcuNzg5IDE0My41IDM0LjY2LTEwLjEwNS00MC45MjggMTQ4LjI4IDMzLjkxNi05Ljc3MzQgMzQuMjY4IDI1Ljk4Ni0xNS4yNjYgNzkuOTI0IDgxLjg3MyA3OC4yMTcgNzcuODcxIDE3Mi4wOCAxODguODUgMTA0LjgyIDE4OC44Ny0xMDQuOTEgNzcuODkzLTE3Mi4wOSA4MS44NzEtNzguMjE3LTE1LjI2Mi03OS44ODEgMzQuMzExLTI2LjAyMSAzNC4wMjcgOS44MTI1LTQyLjk4NC0xNDguODggMzYuNTU5IDEwLjY1Mi00Ny43ODktMTQzLjQ5IDQ5LjIxNS0xODkuOTh6bS00LjIwNyAyNS4yOTUgNDMuNTEgMTA4LjEyLTMyLjEgMTcuNjM3LTAuMzMyMDQgMC4xODM2LTg0Ljc3OSA0Ni42MTktMjkuMjM0IDEwMC42Mi03My4yNS0zNS45NzUgNS42NTI0LTE5LjQyNCAxMC45MjYtMzcuNTA4IDAuMjMyNDIgMC4wMzUyIDMuNjMyOCAwLjUzNTE1em0tNjI0LjQ4IDAuMTA1NDcgMTU1LjY5IDE4MC43MyAzLjg3My0wLjU3MjI2IDE2LjY0MSA1Ny4xNzgtNzMuMjYgMzUuODM4LTI5LjIzMi0xMDAuNjItODQuNzgxLTQ2LjYyMy0wLjMzMDA4LTAuMTgxNjQtMy43NDgtMi4wNTg2LTI4LjM1NS0xNS41ODJ6bTY2OC41IDEwOS4yNiAyNi40MjggNjUuNjcyLTQ4LjY0NSAxODcuNzgtNTUuNDU1LTcuMzIyMy02Ny44MjItODEuMTggMTA5LjcyLTYwLjIyNSA0LjEzNDgtODcuMzQyem0tNzEyLjUgMC4xMDE1NiAzMS42NDEgMTcuMzg1IDQuMTM2NyA4Ny4zNDIgMTA5LjczIDYwLjIzLTE5LjkgMjMuODItNDcuOTI2IDU3LjM2My01NS40NjEgNy4zMjIzLTQ4LjY0NS0xODcuNzl6bTY3OS41IDE4LjAzNS00LjA2MjUgODUuNzc3LTEwOC4zNyA1OS40ODQgMjguODU1LTk5LjMwMXptLTY0Ni40OCAwLjEwMzUxIDgzLjU3OCA0NS45NTkgMjguODUyIDk5LjI5Ny0xMDguMzctNTkuNDh6bTMyMy4yMiAzMC4wODYgMTUxLjMyIDIyLjM3Ny0xNC41MTQgNDkuODY5LTIuMTk5MiA3LjU1MDgtMC4xNTYyNSAwLjUzOTA2aDJlLTNsLTg1LjY0MyAyOTQuMjYtNDYuOTk2LTMwLjIxOS0xLjgwNDctMS4xNjIxLTQ4Ljc2NiAzMS40OS0zLjE3NTgtMTAuOTA0LTE2LjE0MS01NS41MSA4ZS0zIDRlLTMgLTY2LjcyNy0yMjkuMTQtNmUtMyAyZS0zIC0xNi41MjctNTYuNzgzem0xMzUuNjcgODAuOTM0IDczLjY0OCAzNi4wMjcgNjguNTk2IDgyLjEwNyA1NS44MDkgNy4zNjUyLTAuMTIxMDkgMC40NjY3OSA0MC45OTggMTIzLjExLTM1LjQwMi0xMC4zMTIgMS45NjQ4IDYuODA0Ny0zOS41MzMtMTEuNTIzLTQzLjIzMi0xMi42MDUgMC4yNjU2Mi0wLjQ0OTIyLTEuMTM4NyAwLjE2MDE2LTg4LjA5OC00OC4zMDEtOTkuMTIzIDUxLjc1MnptLTI3MS4zIDAuMTIxMDkgMTMuMjEzIDQ1LjM3MSA1Mi4xNyAxNzkuMjItOTkuMTE1LTUxLjc1Ni04OC4xIDQ4LjMwMy0xLjE0MDYtMC4xNjAxNSAwLjI2MzY3IDAuNDQ3MjYtODQuODk4IDI0Ljc1IDEuODc1LTYuNzkxLTMzLjE4NiA5LjY3MzggNDAuOTk4LTEyMy4xMS0wLjEyMTA5LTAuNDY0ODQgNTUuODE0LTcuMzY5MSA2OC41ODgtODIuMDk2em0zMDUuMDQgMTc0LjI1IDg1Ljg4NyA0Ny4wOTItNDguODc5IDYuODQ5Ni0yNS40NzMtMTQuMDEtODQuMDgyIDM2LjAxOC0xMC40NDUgMzUuODgxLTM1LjAxNCA5LjY3MzgtMC4zMzM5OS0wLjc1MzkxLTEuMDg0LTAuNjk3MjYgMTkuODA5LTY4LjA0M3ptLTMzOC43NiAwLjExNTI0IDk5LjYyOSA1Mi4wMiAxNi4xMjEgNTUuMzg1IDMuNjc1OCAxMi42NDMtMS4wODIgMC42OTkyMi0wLjMzMzk5IDAuNzUzOTEtMzUuMDE2LTkuNjczOC0xMC40NDEtMzUuODcxLTg0LjA4Mi0zNi4wMjEtMjUuNDYxIDE0LjAwOC00OC44OTYtNi44NTE2em0zNTAuMjMgNDEuMjk3IDI0LjMxMSAxMy4zNzEtMjcuMjc1IDI2LjczNC04OS45NjMgMjkuODI4IDEwLjAyLTM0LjQyem0tMzYxLjY5IDAuMTE3MTkgODIuOTA4IDM1LjUxNiAxMC4wMTYgMzQuNDA2LTg5Ljk1Ny0yOS44MjQtMjcuMjY4LTI2Ljczem00MzguNTEgNi43MzQ0IDgzLjkwOCAyNC40NjkgNDAuNjAyIDE0MC42MS0xNy4yNTItNC45NzI3LTUuOTY2OCA0LjUyNTQtMTY5LjgxLTQ4Ljk1OXptLTEuNTk3NyAwLjA2NjQtNjguMjUyIDExNS4yMS0xMzUuNTgtNDAuMDg4LTAuMzE4MzYtMC43MjA3IDM1LjE5MS05LjcyMDcgMC4wODk5LTAuMDI5MyAwLjI1OTc2LTAuMDcyMyA0ZS0zIC0wLjAxNTYgOTEuMTI1LTMwLjIxMyAyNy45NzEtMjcuNDE4em0tNTEzLjczIDAuMDQxIDU2Ljc5NyA5NS44NzkgMTEuNzIxIDE5Ljc4Ny0xNjkuNzggNDguOTUxLTUuOTYwOS00LjUyMTUtMTcuNDU3IDUuMDMxMiAzOC42NTgtMTQwLjA1IDQwLjYzMS0xMS44NDh6bTEuNTk3NiAwLjA3MjMgNDkuNTE2IDYuOTM5NSAyNy45NjMgMjcuNDEyIDkxLjExNyAzMC4yMDkgMC4wMSAwLjAzMzIgMzUuNTM5IDkuODE4NC0wLjMxODM2IDAuNzIyNjYtMTM1LjU4IDQwLjA4em0yNTYuMDUgNDcuNDAyIDQ1Ljc2NiAyOS40MjYgMS4zOTI2IDMuMTUyMyAzNi45NzMgMjYyLjYxaC0xNjguODlsMzcuNjMzLTI2Mi41MSAxLjM5MDYtMy4xNTA0em01Mi45NTkgMjkuMjIxIDEzNC40NCAzOS43NDgtMjguMjAxIDIwMC43MnptLTEwNS44OSAwLjEwNTQ2LTEwNi4yNCAyNDAuNDktNC4zNTE2LTMwLjk3MS0yMy44MzYtMTY5Ljc0IDEwOS4zNS0zMi4zNjN6bTEwNi4wMiAxNi42NjggMTAxLjAyIDIyOC42OC02NS44MjIgMjAuMTMzLTQuMzIwMy0yOS40OXptLTEwNi4yIDAuMjUtMzUuNTQ1IDI0Ny45Ni02NS44MTgtMTguNTI1em0yNDEuODEgMjMuMDk0IDE2OC45NSA0OC43MTMtMzguOTQzIDI5LjUzMyAwLjgzMDA4IDQuMzQ3Ny0xNTkuMjIgMTE5LjQ1IDIxLjg2NS0xNTUuNjZ6bS0zNzcuMzcgMC4xMjMwNCAxOS4xMjEgMTM2LjA3IDkuMzUzNSA2Ni42MDQtMC4yMjA3IDAuNTAxOTYtMTU4LjkxLTEyMS42IDAuNjQ4NDQtMy4zOTI2LTM4Ljg4Ny0yOS40OXptLTEzMS43OCA4Ny40MzYgMTU4LjQzIDEyMS4yNCAyMS4yMDkgMTAxLjY1IDEuMDc4MSAwLjk1NzAzLTI2LjQzIDU3Ljk5Ni0xNC4yMTMgMzEuMTM3LTc1LjI2OC0xNjYuMzItNzguNDk2LTc0Ljk5em02NDEuMTEgMC44NjcxOSAxMy41MDggNzAuNjk1LTc4LjQ5NCA3NC45OS03NS4yNzcgMTY2LjMxLTQwLjY0MS04OS4xNjIgMS4wNzQyLTAuOTU3MDQgMjEuNDkyLTEwMi44OS0wLjA2ODQtMC4xNTQzem0tMTY1LjgzIDEyMi4yNy0yMC4wNzIgOTYuMDkyLTM2LjMxMSAzMi4yOTktOTMuNjI1IDIxLjU4NiA1NS41OS02My44OTYgMjYuODQtNjUuNDA2em0tMzA5LjM5IDEuMTcxOSA2Ny4xNTQgMTguOTAyIDI3LjEgNjYuMTkzIDYwLjI1NiAxNC41NzYgNTcuMDg2LTEzLjY1OC01NC40NjkgNjMuMzExLTIuNTcyMyAwLjU5Mzc1LTk4LjUyMy0yMi43MDctMzYuMTk3LTMyLjE0NnptMjMwLjY0IDE3Ljk4IDEuMzc4OSA0LjUxNTYgOC4xNDg0LTIuNDkyMi0yNi4zMDcgNjQuMS01OS4zNTQgMTQuMTk3LTU5LjI3OS0xNC4zMzgtMjYuNDUxLTY0LjYwNSAzLjgwNjYgMS4wNzIzIDAuNjkxNDEtMi40NDE0em02Mi42MjkgODIuMzQ0IDQwLjk1MyA4OS44NTktMS4xMjcgMi40ODgzLTE3MC42NiA5NC44MDkgOTAuNzIxLTE1My40NiAzLjAwNzgtMC42OTMzNnptLTI3Ny4zNiAwLjE3MTg3IDM2Ljk2OSAzMi44MzQgMy4wMjE1IDAuNjk1MzEgOTAuODAxIDE1My40Ni0xNzAuNjUtOTQuNzE5LTEuMTA5NC0yLjQ1MTIgMTUuMzItMzMuNjExem00MS43ODkgMzMuOTQ1IDk2Ljg1OSAyMi4zMTYgOTYuNzg3LTIyLjMxNi05MS4yMjcgMTU0LjMzLTUuNTQ4OCAzLjA4Mi01LjU1ODYtMy4wODU5eiIvPjwvZz48cGF0aCBkPSJtMzQ1LjMgMTY2LjY2LTQ0LjE2NS0xMC41NjUtNDUuMjM2IDEwLjQzMiA0NS4yMjgtNTEuMjA2em0tOC4zMjYtNS4wNzYtMzUuODc3LTQxLjY5OC0zNi43NTcgNDEuNjE1IDM2LjEyMS04LjMzIDAuNjg3LTAuMTU4IDAuNjg1IDAuMTY0eiIvPjxnIHNoYXBlLXJlbmRlcmluZz0iYXV0byI+PHBhdGggZD0ibTI2My45OCA0MTguNDItNzkuNjg4IDI3My43NSAyLjg4MDkgMC44Mzc4OSA3OC41NjItMjY5Ljg4IDM1LjQwMiAyMi44NjMgMzUuNDM0LTIyLjc4MSA3OC41MjcgMjY5LjggMi44Nzg5LTAuODM3ODktNzkuNjQ4LTI3My42Ni0zNy4xODggMjMuOTA4eiIgZG9taW5hbnQtYmFzZWxpbmU9ImF1dG8iLz48cGF0aCBkPSJtMTU4Ljg5IDM4Ni44NC01NC4yMyA5MS41NDMgNjkuNDIgMzguMDYxIDc2LjM0OC0zOS44NjUtMi4zMTQ0LTQuNDMxNi03My45NzcgMzguNjI3LTYyLjU0MS0zNC4yODkgNDkuNjExLTgzLjc0NiAxMDIuOTQgMzAuNDMyIDEuNDE2LTQuNzk0OXoiIGRvbWluYW50LWJhc2VsaW5lPSJhdXRvIi8+PHBhdGggZD0ibTQ0My40MSAzODYuOTItMTA2LjY3IDMxLjUzNyAxLjQxOCA0Ljc5NDkgMTAyLjk0LTMwLjQzNCA0OS42MDcgODMuNzQ2LTYyLjUzNyAzNC4yODktNzMuOTU3LTM4LjYxMy0yLjMxNDQgNC40MzE2IDc2LjMyOCAzOS44NTQgNjkuNDE4LTM4LjA2MnoiIGRvbWluYW50LWJhc2VsaW5lPSJhdXRvIi8+PHBhdGggZD0ibTQ1Ljk1MSA1NDguMDQtMC4zOTI1OCAyLjk3NDYgNDYuMTM3IDYuMDkxOCA1MS4yMTcgNjEuMzAzIDU2LjE0NiAyNy40NjUgMS4zMTg0LTIuNjkzNC01NS41NzItMjcuMTg2LTUxLjU1Ny02MS43MTF6IiBkb21pbmFudC1iYXNlbGluZT0iYXV0byIvPjxwYXRoIGQ9Im01NTYuMzUgNTQ4LjEyLTQ3LjI5NyA2LjI0NDEtNTEuNTU5IDYxLjcxMS01NS41NTkgMjcuMjgzIDEuMzIyMyAyLjY5MzQgNTYuMTMxLTI3LjU2NCA1MS4yMTctNjEuMzAzIDQ2LjEzNy02LjA4OTh6IiBkb21pbmFudC1iYXNlbGluZT0iYXV0byIvPjxwYXRoIGQ9Im00MjEuNzUgMjMzLjE4LTIuOTcwNyAwLjQxNzk3IDIxLjk4NCAxNTYuNDggMi45NzA3LTAuNDE3OTd6IiBkb21pbmFudC1iYXNlbGluZT0iYXV0byIvPjxwYXRoIGQ9Im0xODAuNTUgMjMzLjEtMjEuOTc1IDE1Ni40OSAyLjk3MDcgMC40MTYwMiAyMS45NzUtMTU2LjQ4eiIgZG9taW5hbnQtYmFzZWxpbmU9ImF1dG8iLz48L2c+PHBhdGggZD0ibTIzNS45NiA0MjguNzYtNjguNTIxIDIyLjcxOC0yMC45ODMgMjAuNTg5IDE5LjAzNSAxMC40NzEgNjIuNjI0LTI2LjgyNnoiLz48cGF0aCBkPSJtMTQzLjg2IDYxNy4yLTgyLjU4NiA0NS4zMzItMy4xMDIgNjUuNTA4IDYzLjY1NS0zNS4wMDR6Ii8+PHBhdGggZD0ibTM2Ni4zNCA0MjguODQgNjguNTIxIDIyLjcxOCAyMC45ODMgMjAuNTg5LTE5LjAzNSAxMC40NzEtNjIuNjI0LTI2LjgyNnoiLz48cGF0aCBkPSJtNDU4LjQ0IDYxNy4yOCA4Mi41ODYgNDUuMzMyIDMuMTAyIDY1LjUwOC02My42NTUtMzUuMDA0eiIvPjxwYXRoIGQ9Im0zNjcuMTIgMjE4LjM0LTIxLjEwMS01MS40MjMtNDQuODc4LTEwLjgxNi00NC44NDMgMTAuODE2LTIxLjA3MSA1MS40MjN6Ii8+PC9nPjwvc3ZnPg==',
                // Inverse outline:
                'icon'  => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAzLjIzIiBoZWlnaHQ9IjExMjIuNSIgdmVyc2lvbj0iMS4xIiB2aWV3Qm94PSIwIDAgODAzLjIzIDExMjIuNSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Zz48cGF0aCBkPSJtNzEzLjc2IDI1LjM2MS0xNTUuNzQgMTgwLjgzLTIuNTMzMi0wLjM3MzA0LTE2LjMxMSA1Ni4wMzkgNzIuMDI3IDM1LjM3MSAyOC44NTctOTkuMzE2IDg0Ljc3OS00Ni42MTkgMC4zMzIwNC0wLjE4MzYgMzIuMS0xNy42Mzd6bS02MjQuNDggMC4xMDU0Ny00My41MDQgMTA4LjExIDI4LjM1NSAxNS41ODIgMy43NDggMi4wNTg2IDAuMzMwMDggMC4xODE2NCA4NC43ODEgNDYuNjIzIDI4Ljg1NSA5OS4zMTggNzIuMDU3LTM1LjI0OC0xNi4zODMtNTYuMjczLTIuNTUwOCAwLjM3Njk1em02NjguNSAxMDkuMjYtMzEuNjM5IDE3LjM4NS00LjEzNDggODcuMzQyLTEwOC41NCA1OS41NzQgNjcuMzI2IDgwLjU4IDU1LjEwMiA3LjI3MzQgNDguMzA5LTE4Ni40OHptLTcxMi41IDAuMTAxNTYtMjYuNDI4IDY1LjY3NiA0OC4zMDcgMTg2LjQ4IDU1LjEwOS03LjI3NTQgNjcuMzIyLTgwLjU4Mi0xMDguNTMtNTkuNTc2LTQuMTM2Ny04Ny4zNDJ6bTM1Ni4yMyA0OC4yMjUtMTQ5Ljk4IDIyLjE3NiA4MS45ODYgMjgxLjY0IDAuMzk2NDggMC4yMDcwMy0wLjIxNjc5IDAuNDE0MDcgMjAuMjM0IDY5LjUwOCA0Ny41OS0zMC43MzIgMS44MDQ3IDEuMTYyMSA0NS44MyAyOS40NjkgMjAuMjQ2LTY5LjU2MS0wLjIwMTE4LTAuMzg0NzcgMC4zNjkxNC0wLjE5MzM1IDgxLjkzOS0yODEuNTN6bTEzNi41NCA4Mi43MTEtNjMuNTc2IDIxOC40MyA5Ni40ODYtNTAuMzc5IDkyLjU1NyA1MC43NDgtMC45NjY3OSAxLjYzMjggNDAuNTg0IDExLjgzNCAzOS41MzMgMTEuNTIzLTEuOTY0OC02LjgwNDcgMzUuNDAyIDEwLjMxMi00MC42OTktMTIyLjIxLTU2LjY3Ni03LjQ4MDUtNjguNzQ0LTgyLjI3OXptLTI3My4wMSAwLjIyNjU2LTcxLjk2NyAzNS4yMDUtNjguNzQgODIuMjc5LTU2LjY4MiA3LjQ4NDQtNDAuNjk5IDEyMi4yMSAzMy4xODYtOS42NzM4LTEuODc1IDYuNzkxIDgyLjI1Mi0yMy45NzktMC45NjY4LTEuNjMyOCA5Mi41NTktNTAuNzQ2IDk2LjQ4MiA1MC4zNzl6bTMwNS44NCAxNzUuMzktOTguNjA3IDUxLjQ4NC0wLjIxMDk0LTAuNDAyMzQtMTkuMjg5IDY2LjI3OSAyLjAxMTcgMC41OTU3MSAzMy4xMzEtOS4xNTQzIDEwLjQ0NS0zNS44ODEgODQuMDgyLTM2LjAxOCAyNS40NzMgMTQuMDEgNDQuNDU5LTYuMjMwNXptLTMzOC43IDAuMTA1NDctODEuNTEyIDQ0LjY4OSA0NC40ODYgNi4yMzQ0IDI1LjQ2MS0xNC4wMDggODQuMDgyIDM2LjAyMSAxMC40NDEgMzUuODcxIDMzLjEyOSA5LjE1MjMgMi4wMjE1LTAuNTk3NjUtMTkuMjgxLTY2LjIzMi0wLjE5MzM2IDAuMzcxMXptNDIyLjA1IDQ1LjY3LTQ2LjE0MyA2LjQ2NDgtMjcuOTcxIDI3LjQxOC05MS4xMjUgMzAuMjEzLTRlLTMgMC4wMTU2LTAuMjU5NzYgMC4wNzIzLTAuMDg5OSAwLjAyOTMtMzEuNDM0IDguNjgzNiAxMzAuOTIgMzguNzA1em0tNTA1LjM5IDAuMTEzMjggNjYuMTA3IDExMS41OSAxMzAuODktMzguNjk3LTMxLjc2Ni04Ljc3NTQtMC4wMS0wLjAzMzItOTEuMTE3LTMwLjIwOS0yNy45NjMtMjcuNDEyem01MTMuMDEgMC4xMTkxNC02OC41MjEgMTE1LjY2IDE2Ny4xNyA0OC4xOTcgNS45NjY4LTQuNTI1NCAxNy4yNTIgNC45NzI3LTQwLjYwMi0xNDAuNjF6bS01MjAuNjIgMC4xMDc0Mi00Mi43NDQgMTIuNDU5LTQwLjYzMSAxMS44NDgtMzguNjU4IDE0MC4wNSAxNy40NTctNS4wMzEyIDUuOTYwOSA0LjUyMTUgMTY3LjEzLTQ4LjE4OHptMjYwLjMgNDYuNzAzLTQ1LjczNCAyOS41MzMtMS4zOTA2IDMuMTUwNC0zNy42MzMgMjYyLjUxaDE2OC44OWwtMzYuOTczLTI2Mi42MS0xLjM5MjYtMy4xNTIzem01NC4zNzEgMzIuNDE2IDEwMy44MSAyMzQuOTcgMjcuNTUzLTE5Ni4xM3ptLTEwOC43MSAwLjEwNTQ3LTEzMS4zNSAzOC44MzYgMjcuNTQ1IDE5Ni4xNHptMTA3LjQzIDEzLjQ3MyAzMC44NzkgMjE5LjMyIDQuMzIwMyAyOS40OSA2NS44MjItMjAuMTMzem0tMTA2LjIgMC4yNS0xMDEuMzYgMjI5LjQ0IDY1LjgxOCAxOC41MjV6bTI0NC4yMiAyMy43ODctMS40MTYgMi4zOTA2LTAuMDc4MS0wLjAyMzQtMjcuNzk5IDE5Ny44NiAxNTcuNzMtMTE4LjMyLTAuODMwMDgtNC4zNDc3IDM4Ljk0My0yOS41MzN6bS0zODIuMTggMC4xMjEwOS0xNjYuNSA0OC4wMDQgMzguODg3IDI5LjQ5LTAuNjQ4NDQgMy4zOTI2IDE1Ny43NCAxMjAuNy0yNy45NzctMTk5LjI0LTAuMDkxOCAwLjAyNzN6bS0xMjkuMzggODYuNzQ0LTEzLjY4OCA3MS42NzIgNzguNDk2IDc0Ljk5IDc1LjI2OCAxNjYuMzIgMTQuMjEzLTMxLjEzNyAyNi40My01Ny45OTYtMS4wNzgxLTAuOTU3MDMtMjEuMjA5LTEwMS42NXptNjQxLjExIDAuODY3MTktMTU4LjQxIDExOC44MyAwLjA2ODQgMC4xNTQzLTIxLjQ5MiAxMDIuODktMS4wNzQyIDAuOTU3MDQgNDAuNjQxIDg5LjE2MiA3NS4yNzctMTY2LjMxIDc4LjQ5NC03NC45OXptLTE2NS44MyAxMjIuMjctNjcuNTc4IDIwLjY3NC0yNi44NCA2NS40MDYtNTUuNTkgNjMuODk2IDkzLjYyNS0yMS41ODYgMzYuMzExLTMyLjI5OXptLTMwOS4zOSAxLjE3MTkgMTkuODM0IDk1LjA2NCAzNi4xOTcgMzIuMTQ2IDk0LjcxOSAyMS44My01Ni40NzUtNjMuOTM5LTAuMDIxNS02ZS0zIC0yNy4xLTY2LjE5M3ptMjAyLjM1IDkxLjY2Mi00Ni44NTQgMTEuMjA5LTAuOTEyMTEgMC4yMTg3NS0wLjkxNjAxLTAuMjEwOTQtNDguMTYtMTEuMTA3IDQ5LjAwOCA1NS40ODZ6bTkwLjkyNCA4LjY2MjEtMzcuMTA1IDMzLjAwNi0zLjAwNzggMC42OTMzNi05MC43MjEgMTUzLjQ2IDE3MC42Ni05NC44MDkgMS4xMjctMi40ODgzem0tMjc3LjM2IDAuMTcxODctMjUuNjUyIDU2LjIwNy0xNS4zMiAzMy42MTEgMS4xMDk0IDIuNDUxMiAxNzAuNjUgOTQuNzE5LTkwLjgwMS0xNTMuNDYtMy4wMjE1LTAuNjk1MzF6bTQxLjc4OSAzMy45NDUgOTEuMzEyIDE1NC4zMiA1LjU1ODYgMy4wODU5IDUuNTQ4OC0zLjA4MiA5MS4yMjctMTU0LjMzLTk2Ljc4NyAyMi4zMTZ6IiBmaWxsPSIjZmNiODEzIi8+PC9nPjwvc3ZnPg==',
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
