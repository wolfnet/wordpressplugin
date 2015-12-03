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
        $this->addAction(array(
            array('admin_menu',            'adminMenu'),
            array('admin_init',            'adminInit'),
            array('admin_enqueue_scripts', 'adminScripts'),
            array('admin_enqueue_scripts', 'adminStyles'),
            array('admin_print_styles',    'adminPrintStyles',  1000),
            array('wp_logout',             'adminEndSession'),
            array('wp_login',              'adminEndSession'),
            ));

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
            'wolfnet-admin',
            'wolfnet-shortcode-builder',
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
            'jquery-ui',
            'wolfnet-admin',
            );

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
        register_setting($this->optionGroup, $this->productKeyOptionKey);
        register_setting($this->optionGroup, Wolfnet_Plugin::SSL_WP_OPTION);
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
                $productKey = $GLOBALS['wolfnet']->getProductKeyById($_SESSION['keyid']);

                if ($GLOBALS['wolfnet']->productKeyIsValid($productKey)) {
                    $GLOBALS['wolfnet']->smHttp = $GLOBALS['wolfnet']->searchManagerHtml($productKey);
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
                'icon'  => $this->url . 'img/wp_wolfnet_nav.png',
                ),
            array(
                'title' => 'General Settings',
                'key'   => 'wolfnet_plugin_settings',
                'cb'    => array(&$GLOBALS['wolfnet']->views, 'amSettingsPage')
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
