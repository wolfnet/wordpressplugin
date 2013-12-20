<?php

/**
 * Plugin Name:  WolfNet IDX for WordPress
 * Plugin URI:   http://wordpress.org/plugins/wolfnet-idx-for-wordpress
 * Description:  The WolfNet IDX for WordPress plugin provides IDX search solution integration with
 *               any WordPress website.
 * Version:      {X.X.X}
 * Author:       WolfNet Technologies, LLC.
 * Author URI:   http://www.wolfnet.com
 *
 *
 * @title         wolfnet.php
 * @copyright     Copyright (c) 2012, 2013, WolfNet Technologies, LLC
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

class wolfnet
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
     * This property holds the current version number of the plugin. The value is actually generated
     * as part of the Ant build process that is run when the plugin is packaged for distribution.
     * @var string
     */
    private $version = '{X.X.X}';

    /**
     * This property is used to set the option group for the plugin which creates a namespaced
     * collection of variables which are used in saving widget settings.
     * @var string
     */
    private $optionGroup = 'wolfnet';

    /**
     * This property is used to set the option group for the Edit Css page. It creates a namespaced
     * collection of variables which are used in saving page settings.
     * @var string
     */
    private $CssOptionGroup = 'wolfnetCss';

    /**
     * This property is used to define the 'search' custom type which is how "Search Manager"
     * searches are saved.
     * @var string
     */
    private $customPostTypeSearch = 'wolfnet_search';

    /**
     * This property is a unique idenitfier that is used to define a plugin option which saves the
     * product key used by the plugin to retreive data from the WolfNet API.
     * @var string
     */
    private $productKeyOptionKey = 'wolfnet_productKey';

    /**
     * This property contains the public CSS as defined in the Edit CSS page.
     * @var string
     */
    private $publicCssOptionKey = "wolfnetCss_publicCss";

    /**
     * This property contains the admin CSS as defined in the Edit CSS page.
     * @var string
     */
    private $adminCssOptionKey = "wolfnetCss_adminCss";

    /**
     * This property is a unique identifier for a value in the WordPress Transient API where
     * references to other transient values are stored.
     * @var string
     */
    private $transientIndexKey = 'wolfnet_transients';

    /**
     * The maximum amount of time a wolfnet value should be stored in the as a transient object.
     * Currently set to 1 week.
     * @var integer
     */
    private $transientMaxExpiration = 604800;

    /**
     * This property defines a the request parameter which is used to determine if the values which
     * are cached in the Transient API should be cleared.
     * @var string
     */
    private $cacheFlag = '-wolfnet-cache';

    /**
     * This property is used to prefix custom hooks which are defined in the plugin. Specifically
     * this prefix is used for hooks which are executed before a certain portion of code.
     * @var string
     */
    private $preHookPrefix = 'wolfnet_pre_';

    /**
     * This property is used to prefix custom hooks which are defined in the plugin. Specifically
     * this prefix is used for hooks which are executed after a certain portion of code.
     * @var string
     */
    private $postHookPrefix = 'wolfnet_post_';


    /**
     * This property is used as a request scope key for storing the unique session key value for the
     * current user.
     * @var string
     */
    private $requestSessionKey = 'wntSessionKey';


    /**
     * This property is used to determine how long a WNT session should last.
     * @var integer
     */
    private $sessionLength = 3600; // one hour



    private $smHttp = null;


    /* Constructor Method *********************************************************************** */
    /*   ____                _                   _                                                */
    /*  / ___|___  _ __  ___| |_ _ __ _   _  ___| |_ ___  _ __                                    */
    /* | |   / _ \| '_ \/ __| __| '__| | | |/ __| __/ _ \| '__|                                   */
    /* | |__| (_) | | | \__ \ |_| |  | |_| | (__| || (_) | |                                      */
    /*  \____\___/|_| |_|___/\__|_|   \__,_|\___|\__\___/|_|                                      */
    /*                                                                                            */
    /* ****************************************************************************************** */

    /**
     * This constructor method prepares the plugin for use, defining properties and registering
     * hooks to be used during the WordPress request cycle.
     * @return void
     */
    public function __construct()
    {

        $this->dir = dirname(__FILE__);
        $this->url = plugin_dir_url(__FILE__);

        // Clear cache if url param exists.
        $cacheParamExists = array_key_exists($this->cacheFlag, $_REQUEST);
        $cacheParamClear = ($cacheParamExists) ? ($_REQUEST[$this->cacheFlag] == 'clear') : false;
        if ($cacheParamExists && $cacheParamClear) {
            $this->clearTransients();
        }

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Register actions.
        $this->addAction(array(
            array('init',                  'init'),
            array('wp_enqueue_scripts',    'scripts'),
            array('wp_enqueue_scripts',    'styles'),
            array('admin_menu',            'adminMenu'),
            array('admin_init',            'adminInit'),
            array('admin_enqueue_scripts', 'adminScripts'),
            array('admin_enqueue_scripts', 'adminStyles'),
            array('widgets_init',          'widgetInit'),
            array('wp_footer',             'footer'),
            array('template_redirect',     'templateRedirect'),
            array('admin_print_styles',    'adminPrintStyles',  1000),
            array('wp_enqueue_scripts',    'publicStyles',      1000),
            ));

        // Register filters.
        $this->addFilter(array(
            array('do_parse_request',     'doParseRequest'),
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


    /* Hooks ************************************************************************************ */
    /* |_|  _   _  |   _                                                                          */
    /* | | (_) (_) |< _>                                                                          */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function activate()
    {
        // Check for legacy transient data and remove it if it exists.
        $indexkey = 'wppf_cache_metadata';
        $metaData = get_transient($indexkey);

        if (is_array($metaData)) {
            foreach ($metaData as $key => $data) {
                delete_transient($key);
            }
        }

        delete_transient($indexkey);

    }


    public function deactivate()
    {
        // Clear out all transient data as it is purely for caching and performance.
        $this->deleteTransientIndex();

    }


    /**
     * This method is a callback for the 'init' hook. Any processes which must be run before the
     * request continues that are not run as part of the constructor method are run in this method.
     * @return void
     */
    public function init()
    {

        // Register Custom Post Types
        $this->registerCustomPostType();

        // Register Shortcodes
        $this->registerShortCodes();

        // Register Ajax Actions
        $this->registerAjaxActions();

        // Register Scripts
        $this->registerScripts();

        // Register CSS
        $this->registerStyles();

    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. Any JavaScript files (and their
     * dependacies) which are needed by the plugin for public interfaces are registered in this
     * method.
     * @return void
     */
    public function scripts()
    {
        do_action($this->preHookPrefix . 'enqueueResources'); // Legacy hook

        // JavaScript
        $scripts = array(
            'smooth-div-scroll',
            'wolfnet-scrolling-items',
            'wolfnet-quick-search',
            'wolfnet-listing-grid',
            'wolfnet-toolbar',
            'wolfnet-property-list',
            'wolfnet-maptracks',
            'mapquest-api-config',
            'mapquest-api'
            );

        foreach ($scripts as $script) {
            wp_enqueue_script($script);
        }

    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. Any CSS files which are needed
     * by the plugin for public areas are registered in this method.
     * @return void
     */
    public function styles()
    {

        // CSS
        $styles = array(
            'wolfnet',
            );

        foreach ($styles as $style) {
            wp_enqueue_style($style);
        }

        do_action($this->postHookPrefix . 'enqueueResources'); // Legacy hook

    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. This will load CSS files
     * which are needed for the plugin after all the other CSS includes in the even that we
     * need to override styles.
     * @return void
     */
    public function publicStyles() {
        if(strlen($this->getPublicCss())) {
            $styles = array(
                'wolfnet-custom',
            );

            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }

            do_action($this->postHookPrefix . 'enqueueResources'); // Legacy hook
        }
    }


    /**
     * This method is a callback for the 'widgets_init' hook. All widgets for the plugin are
     * registered in this method.
     * @return void
     */
    public function widgetInit()
    {
        do_action($this->preHookPrefix . 'registerWidgets'); // Legacy hook

        require_once $this->dir . '/widget/FeaturedListingsWidget.php';
        register_widget('Wolfnet_FeaturedListingsWidget');

        require_once $this->dir . '/widget/ListingGridWidget.php';
        register_widget('Wolfnet_ListingGridWidget');

        require_once $this->dir . '/widget/PropertyListWidget.php';
        register_widget('Wolfnet_PropertyListWidget');

        require_once $this->dir . '/widget/ResultsSummaryWidget.php';
        register_widget('Wolfnet_ResultsSummaryWidget');        

        require_once $this->dir . '/widget/QuickSearchWidget.php';
        register_widget('Wolfnet_QuickSearchWidget');

        do_action($this->postHookPrefix . 'registerWidgets'); // Legacy hook

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
        register_setting($this->CssOptionGroup, $this->publicCssOptionKey);
        register_setting($this->CssOptionGroup, $this->adminCssOptionKey);

        // Register Shortcode Builder Button
        $canEditPosts = current_user_can('edit_posts');
        $canEditPages = current_user_can('edit_pages');
        $richEditing  = get_user_option('rich_editing');

        // Register Ajax Actions
        $this->registerAdminAjaxActions();

        /* If we are serving up the search manager page we need to get the search manager HTML from
         * the MLSFinder server now so that we can set cookies. */
        $pageKeyExists = array_key_exists('page', $_REQUEST);
        $pageIsSM = ($pageKeyExists) ? ($_REQUEST['page']=='wolfnet_plugin_search_manager') : false;
        if ($pageKeyExists && $pageIsSM) {
            $this->smHttp = $this->searchManagerHtml();
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
                'cb'    => array(&$this, 'amSettingsPage')
                ),
            array(
                'title' => 'Edit CSS',
                'key'   => 'wolfnet_plugin_css',
                'cb'    => array(&$this, 'amEditCssPage')
            ),
            array(
                'title' => 'Search Manager',
                'key'   => 'wolfnet_plugin_search_manager',
                'cb'    => array(&$this, 'amSearchManagerPage')
                ),
            array(
                'title' => 'Support',
                'key'   => 'wolfnet_plugin_support',
                'cb'    => array(&$this, 'amSupportPage')
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
     * This method is a callback for the 'admin_enqueue_scripts' hook. Any JavaScript files (and
     * their dependacies) which are needed by the plugin for admin interfaces are registered in this
     * method.
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
     * This method is a callback for the 'wp_footer' hook. Currently this method is used to display
     * market disclaimer information if necessary for the request.
     * @return void
     */
    public function footer()
    {
        do_action($this->preHookPrefix . 'footerDisclaimer'); // Legacy hook

        /* If it has been established that we need to output the market disclaimer do so now in the
         * site footer, otherwise do nothing. */
        if (array_key_exists('wolfnet_includeDisclaimer', $_REQUEST)) {
            echo '<div class="wolfnet_marketDisclaimer">';
            echo $this->getMarketDisclaimer();
            echo '</div>';
        }

        do_action($this->postHookPrefix . 'footerDisclaimer'); // Legacy hook

    }


    /**
     * This method is a callback for the 'template_redirect' hook. This method intercepts the
     * WordPress request cycle when specific URI are requested. Specifically this method exposes
     * URIs for getting header and footer HTML for the site.
     * @return void
     */
    public function templateRedirect()
    {
        $pagename = (array_key_exists('pagename', $_REQUEST)) ? $_REQUEST['pagename'] : '';
        $pagename = str_replace('-', '_', $pagename);
        $prefix   = 'wolfnet_';

        do_action($this->preHookPrefix . 'manageRewritePages'); // Legacy hook

        if (substr($pagename, 0, strlen($prefix)) == $prefix) {

            global $wp_query;

            if ($wp_query->is_404) {
                $wp_query->is_404 = false;
                $wp_query->is_archive = true;
            }

            status_header(200);

            switch ($pagename) {

                case 'wolfnet_content':
                    $this->remoteContent();
                    break;

                case 'wolfnet_content_header':
                    $this->remoteContentHeader();
                    break;

                case 'wolfnet_content_footer':
                    $this->remoteContentFooter();
                    break;

            }

        }

        do_action($this->postHookPrefix . 'manageRewritePages'); // Legacy hook

    }


    /**
     * This method is a callback for the 'do_parse_request' filter. This method checks for a
     * specific pagename prefix and if it is present the WordPress should not parse the request.
     * @param Boolean $req
     * @return void
     */
    public function doParseRequest($req)
    {
        $pagename = (array_key_exists('pagename', $_REQUEST)) ? $_REQUEST['pagename'] : '';
        $pagename = str_replace('-', '_', $pagename);
        $prefix   = 'wolfnet_';

        global $wp;
        $wp->query_vars = array();

        return (substr($pagename, 0, strlen($prefix)) === $prefix) ? false : $req;

    }


    /**
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles() {
        $adminCss = $this->getAdminCss();
        echo '<style>' . $adminCss . '</style>';
    }


    /* Custom Post Types ************************************************************************ */
    /*  _                         _              ___                                              */
    /* /       _ _|_  _  ._ _    |_) _   _ _|_    |    ._   _   _                                 */
    /* \_ |_| _>  |_ (_) | | |   |  (_) _>  |_    | \/ |_) (/_ _>                                 */
    /*                                              /  |                                          */
    /* ****************************************************************************************** */

    public function cpSearchMetabox()
    {
        add_meta_box(
            'search_criteria',
            'Search Criteria',
            array(&$this, 'cpSearchMetaboxOutput'),
            'wolfnet_search',
            'advanced',
            'core'
            );

    }


    public function cpSearchMetaboxOutput($post)
    {
        $customFields = get_post_custom($post->ID);

        foreach ($customFields as $field=>$value) {
            if (substr($field, 0, 1) != '_') {
                echo "<div><label>{$field}:</label> {$value[0]}</div>";
            }
        }

    }


    /* Shortcode Builder ************************************************************************ */
    /*  __                                  _                                                     */
    /* (_  |_   _  ._ _|_  _  _   _|  _    |_)     o |  _|  _  ._                                 */
    /* __) | | (_) |   |_ (_ (_) (_| (/_   |_) |_| | | (_| (/_ |                                  */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function sbMcePlugin(array $plugins)
    {
        $plugins['wolfnetShortcodeBuilder'] = $this->url . 'js/tinymce.wolfnetShortcodeBuilder.src.js';

        return $plugins;

    }


    public function sbButton(array $buttons)
    {

        do_action($this->preHookPrefix . 'addShortcodeBuilderButton'); // Legacy hook

        array_push($buttons, '|', 'wolfnetShortcodeBuilderButton');

        do_action($this->postHookPrefix . 'addShortcodeBuilderButton'); // Legacy hook

        return $buttons;

    }


    /* Admin Menus ****************************************************************************** */
    /*                                                                                            */
    /*  /\   _| ._ _  o ._    |\/|  _  ._       _                                                 */
    /* /--\ (_| | | | | | |   |  | (/_ | | |_| _>                                                 */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function amSettingsPage()
    {
        ob_start(); settings_fields($this->optionGroup); $formHeader = ob_get_clean();
        $productKey = $this->getProductKey();
        include 'template/adminSettings.php';

    }


    public function amEditCssPage() {
        ob_start(); settings_fields($this->CssOptionGroup); $formHeader = ob_get_clean();
        $publicCss = $this->getPublicCss();
        $adminCss = $this->getAdminCss();
        include 'template/adminEditCss.php';
    }


    public function amSearchManagerPage()
    {
        if (!$this->productKeyIsValid()) {
            include 'template/invalidProductKey.php';
            return;
        }
        else {
            $searchForm = ($this->smHttp !== null) ? $this->smHttp['body'] : '';
            include 'template/adminSearchManager.php';

        }

    }


    public function amSupportPage()
    {
        $imgdir = $this->url . 'img/';
        include 'template/adminSupport.php';

    }


    /* Shortcodes ******************************************************************************* */
    /*  __                                                                                        */
    /* (_  |_   _  ._ _|_  _  _   _|  _   _                                                       */
    /* __) | | (_) |   |_ (_ (_) (_| (/_ _>                                                       */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function scFeaturedListings($attrs, $content='')
    {
        $defaultAttributes = $this->getFeaturedListingsDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->featuredListings($criteria);

    }


    public function scListingGrid($attrs, $content='')
    {
        $defaultAttributes = $this->getListingGridDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        $criteria = $this->getOptions($criteria);

        return $this->listingGrid($criteria);

    }


    public function scPropertyList($attrs, $content='')
    {
        $defaultAttributes = $this->getPropertyListDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->propertyList($criteria);

    }


    public function scResultsSummary($attrs, $content='')
    {
        $defaultAttributes = $this->getResultsSummaryDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->resultsSummary($criteria);

    }    


    public function scQuickSearch($attrs, $content='')
    {
        $defaultAttributes = $this->getQuickSearchDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->quickSearch($criteria);

    }


    /* Ajax Actions ***************************************************************************** */
    /*                                                                                            */
    /*  /\  o  _.       /\   _ _|_ o  _  ._   _                                                   */
    /* /--\ | (_| ><   /--\ (_  |_ | (_) | | _>                                                   */
    /*     _|                                                                                     */
    /* ****************************************************************************************** */

    public function remoteValidateProductKey()
    {
        $productKey = (array_key_exists('key', $_REQUEST)) ? $_REQUEST['key'] : '';

        echo ($this->productKeyIsValid($productKey)) ? 'true' : 'false';

        die;

    }


    public function remoteGetSavedSearchs()
    {
        echo json_encode($this->getSavedSearches());

        die;

    }


    public function remoteSaveSearch()
    {
        if (array_key_exists('post_title', $_REQUEST)) {

            // Create post object
            $my_post = array(
                'post_title'  => $_REQUEST['post_title'],
                'post_status' => 'publish',
                'post_author' => wp_get_current_user()->ID,
                'post_type'   => $this->customPostTypeSearch
                );

            // Insert the post into the database
            $post_id = wp_insert_post($my_post);

            foreach ($_REQUEST['custom_fields'] as $field => $value) {
                add_post_meta($post_id, $field, $value, true);
            }

        }

        $this->remoteGetSavedSearchs();

    }


    public function remoteDeleteSearch()
    {
        if (array_key_exists('id', $_REQUEST)) {
            wp_delete_post($_REQUEST['id'], true);
        }

        $this->remoteGetSavedSearchs();

    }


    public function remoteShortcodeBuilderOptionsFeatured ()
    {
        $args = $this->getFeaturedListingsOptions();

        echo $this->featuredListingsOptionsFormView($args);

        die;

    }


    public function remoteShortcodeBuilderOptionsGrid ()
    {
        $args = $this->getListingGridOptions();

        echo $this->listingGridOptionsFormView($args);

        die;

    }


    public function remoteShortcodeBuilderOptionsList ()
    {
        $args = $this->getPropertyListOptions();
        $this->remoteShortcodeBuilderOptionsGrid($args);

        die;

    }


    public function remoteShortcodeBuilderOptionsResultsSummary ()
    {
        $args = $this->getResultsSummaryOptions();
        $this->remoteShortcodeBuilderOptionsGrid($args);

        die;

    }    


    public function remoteShortcodeBuilderOptionsQuickSearch ()
    {
        $args = $this->getQuickSearchOptions();

        echo $this->quickSearchOptionsFormView($args);

        die;

    }


    public function remoteShortcodeBuilderSavedSearch ()
    {
        $id = (array_key_exists('id', $_REQUEST)) ? $_REQUEST['id'] : 0;

        echo json_encode($this->getSavedSearch($id));

        die;

    }


    public function remoteContent ()
    {
        echo $this->getWpHeader();
        echo $this->getWpFooter();

        die;

    }


    public function remoteContentHeader ()
    {
        echo $this->getWpHeader();

        die;

    }


    public function remoteContentFooter ()
    {
        $this->getWpHeader();

        echo $this->getWpFooter();

        die;

    }


    public function remoteListings ()
    {
        $args = $this->getListingGridOptions($_REQUEST);

        echo $this->getWpHeader();
        echo $this->listingGrid($args);
        echo $this->getWpFooter();

        die;

    }


    public function remoteListingsGet()
    {
        $callback = (array_key_exists('callback', $_REQUEST)) ? $_REQUEST['callback'] : false;
        $args = $this->getListingGridOptions($_REQUEST);

        if ($callback) {
            header('Content-Type: application/javascript');
        }
        else {
            header('Content-Type: application/json');
        }

        echo $callback ? $callback . '(' : '';
        echo json_encode($this->getListings($args));
        echo $callback ? ');' : '';

        die;

    }


    public function remotePublicCss() {
        header('Content-type: text/css');
        $publicCss = $this->getPublicCss();

        if(strlen($publicCss) > 0) {
            echo $publicCss;
        }

        die;
    }


    /* Data ************************************************************************************* */
    /*  _                                                                                         */
    /* | \  _. _|_  _.                                                                            */
    /* |_/ (_|  |_ (_|                                                                            */
    /*                                                                                            */
    /* ****************************************************************************************** */

    /* Featured Listings ************************************************************************ */

    public function getFeaturedListings(array $criteria=array())
    {
        $criteria['numrows']     = $criteria['maxresults'];
        $criteria['max_results'] = $criteria['maxresults'];
        $criteria['owner_type']  = $criteria['ownertype'];

        $productKey = $this->getProductKey();
        $url = 'http://services.mlsfinder.com/v1/propertyBar/' . $productKey . '.json';
        $url = $this->buildUrl($url, $criteria);

        return $this->getApiData($url, 900)->listings;

    }


    public function getFeaturedListingsDefaults()
    {

        return array(
            'title'      => '',
            'direction'  => 'left',
            'autoplay'   => true,
            'speed'      => 5,
            'ownertype'  => 'agent_broker', 'owner_type' => 'agent_broker',
            'maxresults' => 50,
            'numrows'    => 50,
            'startrow'   => 1,
            );

    }


    public function getFeaturedListingsOptions($instance=null)
    {
        $options = $this->getOptions($this->getFeaturedListingsDefaults(), $instance);

        $options['autoplay_false_wps']  = selected($options['autoplay'], 'false', false);
        $options['autoplay_true_wps']   = selected($options['autoplay'], 'true', false);
        $options['direction_left_wps']  = selected($options['direction'], 'left', false);
        $options['direction_right_wps'] = selected($options['direction'], 'right', false);
        $options['ownertypes']          = $this->getOwnerTypes();

        return $options;

    }


    public function featuredListings(array $criteria)
    {

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->getFeaturedListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->listingView($vars);

        }

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'listingsHtml' => $listingsHtml,
            'siteUrl'      => site_url(),
            'criteria'     => json_encode($criteria)
            );

        $args = $this->convertDataType(array_merge($criteria, $vars));

        return $this->featuredListingView($args);

    }


    /* Listing Grid ***************************************************************************** */

    public function getListings(array $criteria=array())
    {
        $keyConversion = array(
            'maxresults' => 'max_results',
            'ownertype'  => 'owner_type',
            'minprice'   => 'min_price',
            'maxprice'   => 'max_price',
            'zipcode'    => 'zip_code',
            );

        foreach ($keyConversion as $key => $value) {
            if (!array_key_exists($value, $criteria) && array_key_exists($key, $criteria)) {
                $criteria[$value] = $criteria[$key];
            }
            unset($criteria[$key]);
        }

        $productKey = $this->getProductKey();
        $url = 'http://services.mlsfinder.com/v1/propertyGrid/' . $productKey . '.json';
        $url = $this->buildUrl($url, $criteria);

        $data = $this->getApiData($url, 900);

        $absMaxResults = $this->getMaxResults();
        $absMaxResults = ($data->total_rows < $absMaxResults) ? $data->total_rows : $absMaxResults;

        foreach ($data->listings as &$listing) {
            $listing->numrows    = $criteria['numrows'];
            $listing->startrow   = $criteria['startrow'];
            $listing->maxresults = $absMaxResults;
        }

        return $data->listings;

    }


    public function getListingGridDefaults()
    {

        return array(
            'title'       => '',
            'criteria'    => '',
            'ownertype'   => 'all',
            'maptype'     => 'disabled',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'mode'        => 'advanced',
            'savedsearch' => '',
            'zipcode'     => '',
            'city'        => '',
            'minprice'    => '',
            'maxprice'    => '',
            );

    }


    public function getListingGridOptions($instance=null)
    {
        $options = $this->getOptions($this->getListingGridDefaults(), $instance);

        $options['mode_basic_wpc']        = checked($options['mode'], 'basic', false);
        $options['mode_advanced_wpc']     = checked($options['mode'], 'advanced', false);
        $options['paginated_false_wps']   = selected($options['paginated'], 'false', false);
        $options['paginated_true_wps']    = selected($options['paginated'], 'true', false);
        $options['sortoptions_false_wps'] = selected($options['sortoptions'], 'false', false);
        $options['sortoptions_true_wps']  = selected($options['sortoptions'], 'true', false);
        $options['ownertypes']            = $this->getOwnerTypes();
        $options['prices']                = $this->getPrices();
        $options['savedsearches']         = $this->getSavedSearches();
        $options['mapEnabled']            = $this->getMaptracksEnabled();
        $options['maptypes']              = $this->getMapTypes();


        return $options;

    }


    public function listingGrid(array $criteria)
    {
        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->getListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->listingView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'criteria'           => json_encode($criteria),
            'class'              => 'wolfnet_listingGrid ',
            'mapEnabled'         => $this->getMaptracksEnabled(),
            'map'                => '',
            'mapType'            => '',
            'hideListingsTools'  => '',
            'hideListingsId'     => uniqid('hideListings'),
            'showListingsId'     => uniqid('showListings'),
            'collapseListingsId' => uniqid('collapseListings'),
            'toolbarTop'         => '',
            'toolbarBottom'      => '',
            'maxresults'         => ((count($listingsData) > 0) ? $listingsData[0]->maxresults : 0),
            );


        $vars = $this->convertDataType(array_merge($criteria, $vars));

        if ($vars['maptype'] != "disabled") {
            $vars['map']     = $this->getMap($listingsData);
            $vars['mapType'] = $vars['maptype'];         
            $vars['hideListingsTools'] = $this->getHideListingTools($vars['hideListingsId']
                                                                   ,$vars['showListingsId']
                                                                   ,$vars['collapseListingsId']
                                                                   ,$vars['instance_id']);
        }
        else {
            $vars['mapType'] = $vars['maptype'];
        }        

        if ($vars['paginated'] || $vars['sortoptions']) {
            $vars['toolbarTop']    = $this->getToolbar($vars, 'wolfnet_toolbarTop ');
            $vars['toolbarBottom'] = $this->getToolbar($vars, 'wolfnet_toolbarBottom ');
        }

        if ($vars['paginated']) {
            $vars['class'] .= 'wolfnet_withPagination ';
        }

        if ($vars['sortoptions']) {
            $vars['class'] .= 'wolfnet_withSortOptions ';
        }

        return $this->listingGridView($vars);

    }


    /* Property List **************************************************************************** */

    public function getPropertyListDefaults()
    {

        return array(
            'title'       => '',
            'ownertype'   => 'all',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'maptype'     => 'disabled'
            );

    }


    public function getPropertyListOptions($instance=null)
    {
        return $this->getListingGridOptions($instance);

    }


    public function propertyList(array $criteria)
    {
        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->getListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->listingBriefView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_propertyList_')),
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'criteria'           => json_encode($criteria),
            'class'              => 'wolfnet_propertyList ',
            'mapEnabled'         => $this->getMaptracksEnabled(),
            'map'                => '',
            'mapType'            => '',
            'hideListingsTools'  => '',
            'hideListingsId'     => uniqid('hideListings'),
            'showListingsId'     => uniqid('showListings'),
            'collapseListingsId' => uniqid('collapseListings'),
            'toolbarTop'         => '',
            'toolbarBottom'      => '',
            'maxresults'         => ((count($listingsData) > 0) ? $listingsData[0]->maxresults : 0),
            );

        $vars = $this->convertDataType(array_merge($criteria, $vars));

        if ($vars['maptype'] != "disabled") {
            $vars['map']     = $this->getMap($listingsData);
            $vars['hideListingsTools'] = $this->getHideListingTools($vars['hideListingsId']
                                                                   ,$vars['showListingsId']
                                                                   ,$vars['collapseListingsId']
                                                                   ,$vars['instance_id']);
            $vars['mapType'] = $vars['maptype']; 
        }
        else {
            $vars['mapType'] = $vars['maptype'];
        }          

        if ($vars['paginated'] || $vars['sortoptions']) {
            $vars['toolbarTop']    = $this->getToolbar($vars, 'wolfnet_toolbarTop ');
            $vars['toolbarBottom'] = $this->getToolbar($vars, 'wolfnet_toolbarBottom ');
        }

        if ($vars['paginated']) {
            $vars['class'] .= 'wolfnet_withPagination ';
        }

        if ($vars['sortoptions']) {
            $vars['class'] .= 'wolfnet_withSortOptions ';
        }

        return $this->propertyListView($vars);

    }


    /* Results Summary ************************************************************************** */

    public function getResultsSummaryDefaults() {

        return array(
            'title'       => '',
            'ownertype'   => 'all',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'maptype'     => 'disabled'
            );

    }


    public function getResultsSummaryOptions($instance=null) {

        return $this->getListingGridOptions($instance);

    }


    public function resultsSummary(array $criteria) {

        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->getListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->listingResultsView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_resultsSummary_')),
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'criteria'           => json_encode($criteria),
            'class'              => 'wolfnet_resultsSummary ',
            'mapEnabled'         => $this->getMaptracksEnabled(),
            'map'                => '',
            'mapType'            => '',
            'hideListingsTools'  => '',
            'hideListingsId'     => uniqid('hideListings'),
            'showListingsId'     => uniqid('showListings'),
            'collapseListingsId' => uniqid('collapseListings'),       
            'toolbarTop'         => '',
            'toolbarBottom'      => '',
            'maxresults'         => ((count($listingsData) > 0) ? $listingsData[0]->maxresults : 0),
            );

        $vars = $this->convertDataType(array_merge($criteria, $vars));

        if ($vars['maptype'] != "disabled") {
            $vars['map']     = $this->getMap($listingsData);
            $vars['hideListingsTools'] = $this->getHideListingTools($vars['hideListingsId']
                                                                   ,$vars['showListingsId']
                                                                   ,$vars['collapseListingsId']
                                                                   ,$vars['instance_id']);
            $vars['mapType'] = $vars['maptype']; 
        }
        else {
            $vars['mapType'] = $vars['maptype'];
        }          

        if ($vars['paginated'] || $vars['sortoptions']) {
            $vars['toolbarTop']    = $this->getToolbar($vars, 'wolfnet_toolbarTop ');
            $vars['toolbarBottom'] = $this->getToolbar($vars, 'wolfnet_toolbarBottom ');
        }

        if ($vars['paginated']) {
            $vars['class'] .= 'wolfnet_withPagination ';
        }

        if ($vars['sortoptions']) {
            $vars['class'] .= 'wolfnet_withSortOptions ';
        }

        return $this->resultsSummaryView($vars);        
    }    


    /* Quick Search ***************************************************************************** */

    public function getQuickSearchDefaults()
    {

        return array(
            'title' => 'QuickSearch'
            );

    }


    public function getQuickSearchOptions($instance=null)
    {
        $options = $this->getOptions($this->getQuickSearchDefaults(), $instance);

        return $options;

    }


    public function quickSearch(array $criteria)
    {
        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
            'siteUrl'      => site_url(),
            'prices'       => $this->getPrices(),
            'beds'         => $this->getBeds(),
            'baths'        => $this->getBaths(),
            'formAction'   => $this->getBaseUrl()
            );

        $args = $this->convertDataType(array_merge($criteria, $vars));

        return $this->quickSearchView($args);

    }


    /* Misc. Data ******************************************************************************* */

    public function getSavedSearches($count=-1)
    {
        // Cache the data in the request scope so that we only have to query for it once per request.
        $cacheKey = 'wntSavedSearches';
        $data = (array_key_exists($cacheKey, $_REQUEST)) ? $_REQUEST[$cacheKey] : null;

        if ($data==null) {

            $dataArgs = array(
                'numberposts' => $count,
                'post_type' => $this->customPostTypeSearch
                );

            $_REQUEST[$cacheKey] = get_posts($dataArgs);
            $data = $_REQUEST[$cacheKey];

        }

        return $data;

    }


    public function getSavedSearch($id=0)
    {
        $data = array();
        $customFields = get_post_custom($id);

        if ($customFields !== false) {
            foreach ($customFields as $field => $value) {
                if (substr($field, 0, 1) != '_') {
                    $data[$field] = $value[0];
                }
            }
        }

        return $data;

    }


    public function getOptions(array $defaultOptions, $instance=null)
    {
        if (is_array($instance)) {
            $options = array_merge($defaultOptions, $instance);
        }
        else {
            $options = $defaultOptions;
        }

        foreach ($options as $key => $value) {
            $options[$key . '_wpid'] = esc_attr($key);
            $options[$key . '_wpname'] = esc_attr($key);
        }

        return $options;

    }


    public function convertDataType($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->convertDataType($val);
            }
        }
        else if (is_string($value) && ($value==='true' || $value==='false')) {
            return ($value==='true') ? true : false;
        }
        else if (is_string($value) && @$int = (integer) $value) {
            return $int;
        }
        else if (is_string($value) && @$float = (float) $value) {
            return $float;
        }

        return $value;

    }


    /* Views ************************************************************************************ */
    /*                                                                                            */
    /* \  / o  _        _                                                                         */
    /*  \/  | (/_ \/\/ _>                                                                         */
    /*                                                                                            */
    /* ****************************************************************************************** */

    public function featuredListingsOptionsFormView(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', uniqid('wolfnet_featuredListing_'))
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/featuredListingsOptions.php', $args);

    }


    public function listingGridOptionsFormView(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'      => str_replace('.', '', uniqid('wolfnet_listingGrid_'))
            );

        $args = array_merge($defaultArgs, $args);

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->parseTemplate('template/listingGridOptions.php', $args);

    }


    public function propertyListOptionsFormView(array $args=array())
    {
        $args = array_merge($args, array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_propertyList_'))
            ));

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->getListingGridOptions($args);

    }


    public function resultsSummaryOptionsFormView(array $args=array())
    {
        $args = array_merge($args, array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_resultsSummary_'))
            ));

        $args['criteria'] = esc_attr($args['criteria']);

        return $this->getListingGridOptions($args);

    }    


    public function quickSearchOptionsFormView(array $args=array())
    {
        $defaultArgs = array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_quickSearch_'))
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/quickSearchOptions.php', $args);

    }


    public function listingView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/listing.php', $args);

        return apply_filters('wolfnet_listingView', ob_get_clean());

    }


    public function listingBriefView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingBriefView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/briefListing.php', $args);

        return apply_filters('wolfnet_listingBriefView', ob_get_clean());

    }


    public function listingResultsView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingResultsView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/resultsListing.php', $args);

        return apply_filters('wolfnet_listingResultsView', ob_get_clean());

    }    


    public function featuredListingView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_featuredListingView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/featuredListings.php', $args);

        return apply_filters('wolfnet_featuredListingView', ob_get_clean());

    }


    public function propertyListView(array $args=array())
    {
        $args['itemsPerPage'] = $this->getItemsPerPage();
        $args['sortOptions'] = $this->getSortOptions();

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_propertyListView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/propertyList.php', $args);

        return apply_filters('wolfnet_propertyListView', ob_get_clean());

    }


    public function resultsSummaryView(array $args=array())
    {
        $args['itemsPerPage'] = $this->getItemsPerPage();
        $args['sortOptions'] = $this->getSortOptions();

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_resultsSummaryView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/resultsSummary.php', $args);

        return apply_filters('wolfnet_resultsSummaryView', ob_get_clean());

    }    


    public function listingGridView(array $args=array())
    {
        $args['itemsPerPage'] = $this->getItemsPerPage();
        $args['sortOptions'] = $this->getSortOptions();

        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_listingGridView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/listingGrid.php', $args);

        return apply_filters('wolfnet_listingGridView', ob_get_clean());

    }


    public function quickSearchView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters( 'wolfnet_quickSearchView_' . $key, $item );
        }

        ob_start();
        echo $this->parseTemplate('template/quickSearch.php', $args);

        return apply_filters('wolfnet_quickSearchView', ob_get_clean());

    }


    public function mapView($listingsData)
    {
        ob_start();
        $args = $this->getMapParameters($listingsData);        
        echo $this->parseTemplate('template/map.php', $args);

        return apply_filters('wolfnet_mapView', ob_get_clean());

    }


    public function hideListingsToolsView($hideId,$showId,$collapseId,$instance_id)
    {
        ob_start(); 

        $args['hideId'] = $hideId;
        $args['showId'] = $showId;
        $args['collapseId'] = $collapseId;
        $args['instance_id'] = $instance_id;

        echo $this->parseTemplate('template/hideListingsTools.php', $args);

        return apply_filters('wolfnet_hideListingsTools', ob_get_clean());
    }


    public function toolbarView(array $args=array())
    {
        foreach ($args as $key => $item) {
            $args[$key] = apply_filters('wolfnet_toolbarView_' . $key, $item);
        }

        ob_start();
        echo $this->parseTemplate('template/toolbar.php', $args);

        return apply_filters('wolfnet_toolbarView', ob_get_clean());

    }


    /* PRIVATE METHODS ************************************************************************** */
    /*  ____       _            _         __  __      _   _               _                       */
    /* |  _ \ _ __(_)_   ____ _| |_ ___  |  \/  | ___| |_| |__   ___   __| |___                   */
    /* | |_) | '__| \ \ / / _` | __/ _ \ | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                  */
    /* |  __/| |  | |\ V / (_| | ||  __/ | |  | |  __/ |_| | | | (_) | (_| \__ \                  */
    /* |_|   |_|  |_| \_/ \__,_|\__\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                  */
    /*                                                                                            */
    /* ****************************************************************************************** */

    private function productKeyIsValid($key=null)
    {
        $valid = false;

        if ($key != null) {
            $productKey = $key;
        }
        else {
            $productKey = $this->getProductKey();
        }

        $url = 'http://services.mlsfinder.com/v1/validateKey/' . $productKey . '.json';

        $http = wp_remote_get($url, array('timeout'=>180));

        if (!is_wp_error($http) && $http['response']['code'] == '200') {
            $data = json_decode($http['body']);
            $errorExists = property_exists($data, 'error');
            $statusExists = ($errorExists) ? property_exists($data->error, 'status') : false;

            if ($errorExists && $statusExists && $data->error->status === false) {
                $valid = true;
            }

        }

        return $valid;

    }


    private function searchManagerHtml()
    {
        global $wp_version;
        $baseUrl = $this->getBaseUrl();
        $maptracksEnabled = $this->getMaptracksEnabled();

        if (!strstr($baseUrl, 'index.cfm')) {
            if (substr($baseUrl, strlen($baseUrl) - 1) != '/') {
                $baseUrl .= '/';
            }

            $baseUrl .= 'index.cfm';

        }

        if (!array_key_exists('search_mode', $_GET)) {
            $_GET['search_mode'] = ($maptracksEnabled) ? 'map' : 'form';
        }

        $url = $baseUrl
             . ((!strstr($baseUrl, '?')) ? '?' : '')
             . '&action=wpshortcodebuilder';

        $resParams = array(
            'page',
            'action',
            'market_guid',
            'reinit',
            'show_header_footer'
            );

        foreach ($_GET as $param => $paramValue) {
            if (!array_search($param, $resParams)) {
                $paramValue = urlencode($this->html_entity_decode_numeric($paramValue));
                $url .= "&{$param}={$paramValue}";
            }
        }

        $reqHeaders = array(
            'cookies'    => $this->searchManagerCookies(),
            'timeout'    => 180,
            'user-agent' => 'WordPress/' . $wp_version,
            );

        $http = wp_remote_get($url, $reqHeaders);

        if (!is_wp_error($http)) {

            $http['request'] = array(
                'url' => $url,
                'headers' => $reqHeaders,
                );

            if ($http['response']['code'] == '200') {
                $this->searchManagerCookies($http['cookies']);
                $http['body'] = $this->removeJqueryFromHTML($http['body']);

                return $http;

            }
            else {
                $http['body'] = '';
                return $http;
            }

        }
        else {
            return array('body' => '');

        }

    }


    private function searchManagerCookies($cookies=null)
    {
        if (is_array($cookies)) {

            foreach ($cookies as $name => $value) {
                if ($value instanceof WP_Http_Cookie) {
                    $cookieArgs = array(
                        $value->name,
                        $value->value,
                        ($value->expires !== null && is_numeric($value->expires)) ? $value->expires : 0,
                        );

                    if ($value->path !== null) {
                        array_push($cookieArgs, $value->path);

                        if ($value->domain !== null) {
                            array_push($cookieArgs, $value->domain);
                        }

                    }

                    call_user_func_array('setcookie', $cookieArgs);

                }
                else {
                    setcookie($name, $value);
                }
            }

        }

        $cookies = array();
        foreach ($_COOKIE as $name => $value) {
            $cookie = new WP_Http_Cookie($name);
            $cookie->name = $name;
            $cookie->value = $value;
            array_push($cookies, $cookie);
        }

        return $cookies;

    }


    private function removeJqueryFromHTML($string)
    {
        return preg_replace('/(<script)(.*)(jquery\.min\.js)(.*)(<\/script>)/i', '', $string);

    }


    private function buildUrl($url='', array $params=array())
    {
        if (!strstr($url, '?')) {
            $url .= '?';
        }

        $restrictedParams = array('criteria','toolbarTop','toolbarBottom','listingsHtml','prevLink',
            'nextLink','prevClass','nextClass','toolbarClass','instance_id','siteUrl','class','_');

        $restrictedSuffix = array('_wpid', '_wpname', '_wps', '_wpc');

        foreach ($params as $key => $value) {
            $valid = true;
            $valid = (array_search($key, $restrictedParams) !== false) ? false : $valid;
            $valid = (!is_string($value) && !is_numeric($value) && !is_bool($value)) ? false : $valid;

            foreach ($restrictedSuffix as $suffix) {
                $valid = (substr($key, strlen($suffix)*-1) == $suffix) ? false : $valid;
            }

            if ($valid) {
                $url .= '&' . $key . '=' . urlencode($this->html_entity_decode_numeric($value));
            }

        }

        return $url;

    }


    private function parseTemplate($template, array $vars=array())
    {
        extract($vars, EXTR_OVERWRITE);
        ob_start();

        include $template;

        return ob_get_clean();

    }


    private function getMarketDisclaimer()
    {
        $productKey = $this->getProductKey();
        $url = 'http://services.mlsfinder.com/v1/marketDisclaimer/' . $productKey . '.json';
        $url = $this->buildUrl($url, array('type'=>'search_results'));

        return $this->getApiData($url, 86400)->disclaimer;

    }


    private function getProductKey()
    {
        return get_option(trim($this->productKeyOptionKey));
    }


    private function getPublicCss() {
        return get_option(trim($this->publicCssOptionKey));
    }


    private function getAdminCss() {
        return get_option($this->adminCssOptionKey);
    }


    private function getApiData($url, $cacheFor=900)
    {
        global $wp_version;
        $key = 'wolfnet_' . md5($url);
        $index = $this->transientIndex();
        $time = time();
        $data = (array_key_exists($key, $index)) ? get_transient($key) : false;

        $url = $this->buildUrl($url, array(
            'pluginVersion' => $this->version,
            'phpVersion'    => phpversion(),
            'wpVersion'     => $wp_version,
            ));

        if ($data === false || $time > $index[$key]) {
            $http = wp_remote_get($url, array('timeout'=>180));

            if (!is_object($data)) {
                $data = new stdClass();
                $data->error = new stdClass();
                $data->error->status = true;
                $data->error->message = 'Unknown error.';
                $data->url = $url;
            }

            if (!is_wp_error($http) && $http['response']['code'] >= 500) {
                $data->error->message = 'A remote server error occurred!';
            }
            elseif (is_wp_error($http) || $http['response']['code'] >= 400) {
                $data->error->message = 'A connection error occurred!';
                $index[$key] = $time;
                set_transient($key, $data, $this->transientMaxExpiration);
            }
            else {
                $tmp = json_decode($http['body']);

                if ($tmp === false) {
                    $data->error->message = 'An error occurred while attempting '
                        . 'to decode the body as Json.';
                }
                else {
                    $data = $tmp;
                }

                if (is_object($data)) {
                    $data->url = $url;
                }

                $index[$key] = $time + $cacheFor;
                set_transient($key, $data, $this->transientMaxExpiration);

            }

        }

        $errorExists = property_exists($data, 'error');
        $statusExists = ($errorExists) ? property_exists($data->error, 'status') : false;

        if ($errorExists && $statusExists && $data->error->status) {
            print('<!-- WNT Plugin Error: ' . $data->error->message . ' -->');
        }

        $this->transientIndex($index);

        return $data;

    }


    private function transientIndex($data=null)
    {
        $key = $this->transientIndexKey;

        // Set transient index data.
        if ($data !== null && is_array($data)) {
            set_transient($key, $data, $this->transientMaxExpiration);
        }
        // Get transient index data.
        else {
            $data = get_transient($key);

            if ($data === false) {
                $data = $this->transientIndex(array());
            }

        }

        return $data;

    }


    private function deleteTransientIndex()
    {
        $this->clearTransients();
        delete_transient($this->transientIndexKey);
    }


    private function clearTransients()
    {
        $index = $this->transientIndex();

        foreach ($index as $key => $value) {
            delete_transient($key);
        }

        $this->transientIndex(array());

    }


    private function augmentListingData(&$listing)
    {

        if (is_numeric($listing->listing_price)) {
            $listing->listing_price = '$' . number_format($listing->listing_price);
        }

        $listing->location = $listing->city;

        if ( $listing->city != '' && $listing->state != '' ) {
            $listing->location .= ', ';
        }

        $listing->location .= $listing->state;
        $listing->location .= ' ' . $listing->zip_code;

        $listing->bedsbaths = '';

        if (is_numeric($listing->bedrooms)) {
            $listing->bedsbaths .= $listing->bedrooms . 'bd';
        }

        if (is_numeric($listing->bedrooms) && is_numeric($listing->bathroom)) {
            $listing->bedsbaths .= '/';
        }

        if (is_numeric($listing->bathroom)) {
            $listing->bedsbaths .= $listing->bathroom . 'ba';
        }

        $listing->bedsbaths_full = '';

        if ( is_numeric( $listing->bedrooms ) ) {
            $listing->bedsbaths_full .= $listing->bedrooms . ' Bed Rooms';
        }

        if ( is_numeric( $listing->bedrooms ) && is_numeric( $listing->bathroom ) ) {
            $listing->bedsbaths_full .= ' & ';
        }

        if ( is_numeric( $listing->bathroom ) ) {
            $listing->bedsbaths_full .= $listing->bathroom . ' Bath Rooms';
        }

        $listing->address = $listing->display_address;

        if ($listing->city != '' && $listing->address != '') {
            $listing->address .= ', ';
        }

        $listing->address .= $listing->city;

        if ($listing->state != '' && $listing->address != '') {
            $listing->address .= ', ';
        }

        $listing->address .= ' ' . $listing->state;
        $listing->address .= ' ' . $listing->zip_code;

    }


    private function getMap($listingsData) 
    {      
        return $this->mapView($listingsData);
    }


    private function getHideListingTools($hideId,$showId,$collapseId,$instance_id)
    {         
        return $this->hideListingsToolsView($hideId,$showId,$collapseId,$instance_id);
    }


    private function getMapParameters($listingsData)
    {
        $productKey = $this->getProductKey();

        $args['houseoverData'] = $this->getHouseoverData($listingsData);

        $url = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json'
             . '?setting=maptracks_map_provider';
        $data = $this->getApiData($url, 86400)->maptracks_map_provider;
        $args['maptracks_map_provider'] = $data;

        $url = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json'
             . '?setting=map_start_lat';
        $data = $this->getApiData($url, 86400)->map_start_lat;
        $args['map_start_lat'] = $data;

        $url = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json'
             . '?setting=map_start_lng';
        $data = $this->getApiData($url, 86400)->map_start_lng;
        $args['map_start_lng'] = $data;

        $url = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json'
             . '?setting=map_start_scale';
        $data = $this->getApiData($url, 86400)->map_start_scale;
        $args['map_start_scale'] = $data;

        $args['houseoverIcon'] = $this->url . 'img/houseover.png';

        return $args;
    }    


    private function getHouseoverData($listingsData)
    {

        $houseoverData = array();

        foreach ($listingsData as $listing) {

            $concatHouseover  = '<a style="display:block" rel="follow" href="';
            $concatHouseover .= $listing->property_url;
            $concatHouseover .= '">';
            $concatHouseover .= '<div class="wolfnet_wntHouseOverWrapper">';
            $concatHouseover .= '<div data-property-id="';
            $concatHouseover .= $listing->property_id;
            $concatHouseover .= '" class="wolfnet_wntHOItem">';
            $concatHouseover .= '<table class="wolfnet_wntHOTable">';
            $concatHouseover .= '<tbody>';
            $concatHouseover .= '<tr>';
            $concatHouseover .= '<td class="wntHOImgCol" valign="top" style="vertical-align:top;">';
            $concatHouseover .= '<div class="wolfnet_wntHOImg">';
            $concatHouseover .= '<img src="';
            $concatHouseover .= $listing->thumbnail_url;
            $concatHouseover .= '"">';
            $concatHouseover .= '</div>';
            $concatHouseover .= '<div class="wolfnet_wntHOBroker" style="text-align: center">';
            $concatHouseover .= '<img class="wolfnet_wntHOBrokerLogo" src="';
            $concatHouseover .= $listing->branding->brokerLogo;
            $concatHouseover .= '" alt="Broker Reciprocity">';
            $concatHouseover .= '</div>';
            $concatHouseover .= '</td>';            
            $concatHouseover .= '<td valign="top" style="vertical-align:top;">';
            $concatHouseover .= '<div class="wolfnet_wntHOContentContainer">';
            $concatHouseover .= '<div style="text-align:left;font-weight:bold">';
            $concatHouseover .= $listing->listing_price;
            $concatHouseover .= '</div>';
            $concatHouseover .= '<div style="text-align:left;">';
            $concatHouseover .= $listing->display_address;
            $concatHouseover .= '</div>';
            $concatHouseover .= '<div style="text-align:left;">';
            $concatHouseover .= $listing->city . ', ' . $listing->state;
            $concatHouseover .= '</div>';            
            $concatHouseover .= '<div style="text-align:left;">';
            $concatHouseover .= $listing->bedsbaths;
            $concatHouseover .= '</div>';  
            $concatHouseover .= '<div style="text-align:left;padding-top:20px;">';
            $concatHouseover .= $listing->branding->content;
            $concatHouseover .= '</div>'; 
            $concatHouseover .= '</div>';
            $concatHouseover .= '</td>';            
            $concatHouseover .= '</tr>';
            $concatHouseover .= '</tbody>';
            $concatHouseover .= '</table>';
            $concatHouseover .= '</div>';
            $concatHouseover .= '</div>';
            $concatHouseover .= '</a>';

            array_push($houseoverData, array(
                'lat' => $listing->lat,
                'lng' => $listing->lng,
                'content' => $concatHouseover,
                ));
        }  

        return $houseoverData;      

    }


    private function getToolbar($data, $class)
    {
        $args = array_merge(json_decode($data['criteria'], true), array(
            'toolbarClass' => $class . ' ',
            'maxresults'   => $data['maxresults'],
            'numrows'      => $data['numrows'],
            'prevClass'    => ($data['startrow']<=1) ? 'wolfnet_disabled' : '',
            'lastitem'     => $data['startrow'] + $data['numrows'] - 1,
            'action'       => 'wolfnet_listings'
            ));

        $args['nextClass'] = ($args['lastitem']>=$args['maxresults']) ? 'wolfnet_disabled' : '';

        if ($args['lastitem'] > $args['maxresults']) {
            $args['lastitem'] = $args['maxresults'];
        }

        $prev = $args['startrow'] - $args['numrows'];

        if ($prev < 1) {
            $prev = $prev - $args['numrows'] + 1;
        }

        if ($prev < 1) {
            $prev = $args['startrow'];
        }

        $args['prevLink'] = $this->buildUrl(
            admin_url('admin-ajax.php'),
            array_merge($args, array('startrow'=>$prev))
            );

        $next = $args['startrow'] + $args['numrows'];

        if ($next >= $args['maxresults']) {
            $next = 1;
        }

        $args['nextLink']  = $this->buildUrl(
            admin_url('admin-ajax.php'),
            array_merge($args, array('startrow'=>$next))
            );

        $args = $this->convertDataType($args);

        return $this->toolbarView($args);

    }


    private function getMaxResults()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json'
              . '?setting=site_text';
        $data = $this->getApiData($url, 86400)->site_text;
        $maxResults = (property_exists($data, 'Max Results')) ? $data->{'Max Results'} : '';

        return (is_numeric($maxResults)) ? $maxResults : 250;

    }


    private function getPricesFromApi()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json'
              . '?setting=site_text';
        $data = $this->getApiData($url, 86400);
        $data = (property_exists($data, 'site_text')) ? $data->site_text : new stdClass();
        $prcs = (property_exists($data, 'Price Range Values')) ? $data->{'Price Range Values'} : '';

        return explode(',', $prcs);

    }


    private function getMaptracksEnabled()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/setting/' . $productKey 
              . '?setting=maptracks_enabled';
        $data = $this->getApiData($url, 86400);
        $data = (property_exists($data, 'maptracks_enabled')) ? ($data->maptracks_enabled == 'Y') : false;

        return $data;

    }


    private function getWpHeader ()
    {
        $wntClass = 'wnt-wrapper';

        ob_start();

        get_header();
        $header = ob_get_clean();
        $htmlTags = array();
        $hasHtmlTags = preg_match_all( "(<html([^\>]*)>)", $header, $htmlTags, PREG_PATTERN_ORDER );

        if ( $hasHtmlTags > 0 ) {
            foreach ( $htmlTags[0] as $tag ) {
                $classRegex = "/(?<=class\=[\"|\'])([^\"|\']*)/";
                $currentClassArray=array();
                $hasClassAttr = preg_match( $classRegex, $tag, $currentClassArray );

                if ( $hasClassAttr > 0) {
                    $currentClasses = ( $hasClassAttr > 0 ) ? $currentClassArray[0] : "";
                    $newTag = preg_replace( $classRegex, $currentClasses . ' ' . $wntClass, $tag );
                }
                else {
                    $newTag = str_replace( '>', ' class="' . $wntClass . '">', $tag );
                }

                $header = str_replace( $tag, $newTag, $header );

            }
        }

        return $header;

    }


    private function getWpFooter ()
    {
        ob_start();
        get_footer();
        $footer = ob_get_clean();

        return $footer;

    }


    private function getSortOptions()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/sortOptions/' . $productKey . '.json';

        return $this->getApiData($url, 86400)->sort_options;

    }


    private function getItemsPerPage()
    {
        return array(5,10,15,20,25,30,35,40,45,50);

    }


    private function getOwnerTypes ()
    {
        return array(
            array('value'=>'agent_broker', 'label'=>'Agent Then Broker'),
            array('value'=>'agent',        'label'=>'Agent Only'),
            array('value'=>'broker',       'label'=>'Broker Only')
            );

    }


    private function getMapTypes ()
    {
        return array(
            array('value'=>'disabled', 'label'=>'No'),
            array('value'=>'above',    'label'=>'Above Listings'),
            array('value'=>'below',    'label'=>'Below Listings'),
            array('value'=>'map_only', 'label'=>'Map Only')
            );
    }


    private function getPrices()
    {
        $values = $this->getPricesFromApi();
        $data   = array();

        foreach ($values as $value) {
            $data[] = array(
                'value' => trim($value),
                'label' => (is_numeric($value)) ? '$' . number_format(trim($value)) : $value
                );
        }

        return $data;

    }


    private function getBeds ()
    {
        $values = array(1,2,3,4,5,6,7);
        $data   = array();

        foreach ($values as $value) {
            $data[] = array('value'=>$value, 'label'=>$value);
        }

        return $data;

    }


    private function getBaths ()
    {
        return $this->getBeds();

    }


    private function getBaseUrl()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json';
        $url .= '?setting=SITE_BASE_URL';

        return $this->getApiData($url, 86400)->site_base_url;

    }


    private function localizedScriptData()
    {
        global $wp_version;

        return array(
            'ajaxurl'        => admin_url('admin-ajax.php'),
            'loaderimg'      => admin_url('/images/wpspin_light.gif'),
            'buildericon'    => $this->url . 'img/wp_wolfnet_nav.png',
            'houseoverIcon'    => $this->url . 'img/houseover.png',
            'useDialogClass' => (version_compare($wp_version, '3.6')>0) ? "true" : "false",
            );
    }


    private function addAction($action, $callable=null, $priority=null)
    {
        if (is_array($action)) {
            foreach ($action as $act) {
                if(count($act) == 2) {
                    $this->addAction($act[0], $act[1]);
                } else {
                    $this->addAction($act[0], $act[1], $act[2]);
                }
            }
        }
        else {
            if (is_callable($callable)) {
                add_action($action, $callable, $priority);
            }
            else if (is_string($callable) && method_exists($this, $callable)) {
                do_action($this->preHookPrefix . $callable);
                add_action($action, array(&$this, $callable), $priority);
                do_action($this->postHookPrefix . $callable);
            }
        }

        return $this;

    }


    private function addFilter($filter, $callable=null)
    {
        if (is_array($filter)) {
            foreach ($filter as $flt) {
                $this->addFilter($flt[0], $flt[1]);
            }
        }
        else {
            if (is_callable($callable)) {
                add_filter($filter, $callable);
            }
            else if (is_string($callable) && method_exists($this, $callable)) {
                do_action($this->preHookPrefix . $callable);
                add_filter($filter, array(&$this, $callable));
                do_action($this->postHookPrefix . $callable);
            }
        }

        return $this;

    }


    private function registerCustomPostType()
    {
        do_action($this->preHookPrefix . 'registerCustomPostTypes'); // Legacy hook

        register_post_type($this->customPostTypeSearch, array(
            'public'    => false,
            'show_ui'   => false,
            'query_var' => 'wolfnet_search',
            'rewrite'   => array(
                'slug'       => 'wolfnet/search',
                'with_front' => false
                ),
            'supports'  => array('title'),
            'labels'    => array(
                'name'               => 'Saved Searches',
                'singular_name'      => 'Saved Search',
                'add_new'            => 'Add Search',
                'add_new_item'       => 'Add Search',
                'edit_item'          => 'View Saved Search',
                'new_item'           => 'New Saved Search',
                'view_item'          => 'View Saved Searches',
                'search_items'       => 'Find Saved Searches',
                'not_found'          => 'No Saved Searches',
                'not_found_in_trash' => 'No Saved Searches In Trash'
                ),
            'register_meta_box_cb' => array(&$this, 'cpSearchMetabox')
            ));

        do_action($this->postHookPrefix . 'registerCustomPostTypes'); // Legacy hook

    }


    private function registerShortCodes()
    {
        $shrtCodes = array(
            'WolfNetFeaturedListings'   => 'scFeaturedListings',
            'woflnetfeaturedlistings'   => 'scFeaturedListings',
            'WOFLNETFEATUREDLISTINGS'   => 'scFeaturedListings',
            'wnt_featured'              => 'scFeaturedListings',
            'WolfNetListingGrid'        => 'scListingGrid',
            'wolfnetlistinggrid'        => 'scListingGrid',
            'WOLFNETLISTINGGRID'        => 'scListingGrid',
            'wnt_grid'                  => 'scListingGrid',
            'WolfNetPropertyList'       => 'scPropertyList',
            'wolfnetpropertylist'       => 'scPropertyList',
            'WOLFNETPROPERTYLIST'       => 'scPropertyList',
            'wnt_list'                  => 'scPropertyList',
            'WolfNetResultsSummary'     => 'scResultsSummary',
            'wolfnetresultssummary'     => 'scResultsSummary',
            'WOLFNETRESULTSSUMMARY'     => 'scResultsSummary',
            'wnt_results'               => 'scResultsSummary',
            'WolfNetListingQuickSearch' => 'scQuickSearch',
            'wolfnetlistingquicksearch' => 'scQuickSearch',
            'WOLFNETLISTINGQUICKSEARCH' => 'scQuickSearch',
            'wnt_search'                => 'scQuickSearch',
            'WolfNetQuickSearch'        => 'scQuickSearch',
            'wolfnetquicksearch'        => 'scQuickSearch',
            'WOLFNETQUICKSEARCH'        => 'scQuickSearch',
            );

        foreach ($shrtCodes as $code => $method) {
            add_shortcode($code, array(&$this, $method));
        }

    }


    private function registerScripts()
    {
        $scripts = array(
            'tooltipjs' => array(
                $this->url . 'js/jquery.tooltip.src.js',
                array('jquery'),
                ),
            'imagesloadedjs' => array(
                $this->url . 'js/jquery.imagesloaded.src.js',
                array('jquery'),
                ),
            'mousewheeljs' => array(
                $this->url . 'js/jquery.mousewheel.src.js',
                array('jquery'),
                ),
            'smooth-div-scroll' => array(
                $this->url . 'js/jquery.smoothDivScroll-1.2.src.js',
                array('mousewheeljs', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-effects-core'),
                ),
            'wolfnet' => array(
                $this->url . 'js/wolfnet.src.js',
                array('jquery', 'tooltipjs'),
                ),
            'wolfnet-admin' => array(
                $this->url . 'js/wolfnetAdmin.src.js',
                array('jquery-ui-dialog', 'jquery-ui-tabs', 'jquery-ui-datepicker', 'wolfnet'),
                ),
            'wolfnet-scrolling-items' => array(
                $this->url . 'js/jquery.wolfnetScrollingItems.src.js',
                array('smooth-div-scroll', 'wolfnet'),
                ),
            'wolfnet-quick-search' => array(
                $this->url . 'js/jquery.wolfnetQuickSearch.src.js',
                array('jquery', 'wolfnet'),
                ),
            'wolfnet-listing-grid' => array(
                $this->url . 'js/jquery.wolfnetListingGrid.src.js',
                array('jquery', 'tooltipjs', 'imagesloadedjs', 'wolfnet'),
                ),
            'wolfnet-toolbar' => array(
                $this->url . 'js/jquery.wolfnetToolbar.src.js',
                array('jquery', 'wolfnet'),
                ),
            'wolfnet-property-list' => array(
                $this->url . 'js/jquery.wolfnetPropertyList.src.js',
                array('jquery', 'wolfnet'),
                ),          
            'wolfnet-shortcode-builder' => array(
                $this->url . 'js/jquery.wolfnetShortcodeBuilder.src.js',
                array('jquery-ui-widget', 'jquery-effects-core', 'wolfnet-admin'),
                ),
            'mapquest-api-config' => array(
                '//www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key=Gmjtd%7Clu6znua2n9%2C7l%3Do5-la70q'
                ),
            'mapquest-api' => array(
                '//www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key=Gmjtd%7Clu6znua2n9%2C7l%3Do5-la70q',
                array('mapquest-api-config'),
                ),
            'bing-mapcontrol' => array(
                'http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.2'
                ),
            'bing-atlascompat' => array(
                'http://ecn.dev.virtualearth.net/mapcontrol/v6.3/js/atlascompat.js'
                ),            
            'wolfnet-maptracks' => array(
                $this->url . 'js/jquery.wolfnetMaptracks.src.js',
                array('jquery',  'mapquest-api-config', 'mapquest-api','bing-mapcontrol','bing-atlascompat'),
                )
            );

        foreach ($scripts as $script => $data) {
            $params   = array($script);
            if (is_array($data) && count($data) > 0) {
                $params[] = $data[0];
                $params[] = (count($data) > 1) ? $data[1] : array();
                $params[] = (count($data) > 2) ? $data[2] : $this->version;
                $params[] = (count($data) > 3) ? $data[3] : false;

                call_user_func_array('wp_register_script', $params);

                if ($script == 'wolfnet') {
                    wp_localize_script('wolfnet', 'wolfnet_ajax', $this->localizedScriptData());
                }

            }

        }

    }


    private function registerStyles()
    {
        global $wp_scripts;
        $jquery_ui = $wp_scripts->query('jquery-ui-core');

        $styles = array(
            'wolfnet' => array(
                $this->url . 'css/wolfnet.src.css'
                ),
            'wolfnet-admin' => array(
                $this->url . 'css/wolfnetAdmin.src.css',
                ),
            'wolfnet-custom' => array(
                admin_url('admin-ajax.php') . '?action=wolfnet_css',
                ),
            'jquery-ui' => array(
                'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_ui->ver
                    . '/themes/smoothness/jquery-ui.css'
                ),
            );

        foreach ($styles as $style => $data) {
            $params   = array($style);
            $params[] = $data[0];
            $params[] = (count($data) > 1) ? $data[1] : array();
            $params[] = (count($data) > 2) ? $data[2] : $this->version;
            $params[] = (count($data) > 3) ? $data[3] : 'screen';

            call_user_func_array('wp_register_style', $params);

        }

    }


    private function registerAjaxActions()
    {
        $ajxActions = array(
            'wolfnet_content'           => 'remoteContent',
            'wolfnet_content_header'    => 'remoteContentHeader',
            'wolfnet_content_footer'    => 'remoteContentFooter',
            'wolfnet_listings'          => 'remoteListings',
            'wolfnet_get_listings'      => 'remoteListingsGet',
            'wolfnet_css'               => 'remotePublicCss',
            );

        foreach ($ajxActions as $action => $method) {
            $this->addAction('wp_ajax_nopriv_' . $action, array(&$this, $method));
        }

    }


    private function registerAdminAjaxActions()
    {
        $ajxActions = array(
            'wolfnet_validate_key'            => 'remoteValidateProductKey',
            'wolfnet_saved_searches'          => 'remoteGetSavedSearchs',
            'wolfnet_save_search'             => 'remoteSaveSearch',
            'wolfnet_delete_search'           => 'remoteDeleteSearch',
            'wolfnet_scb_options_featured'    => 'remoteShortcodeBuilderOptionsFeatured',
            'wolfnet_scb_options_grid'        => 'remoteShortcodeBuilderOptionsGrid',
            'wolfnet_scb_options_list'        => 'remoteShortcodeBuilderOptionsList',
            'wolfnet_scb_results_summary'     => 'remoteShortcodeBuilderOptionsResultsSummary',
            'wolfnet_scb_options_quicksearch' => 'remoteShortcodeBuilderOptionsQuickSearch',
            'wolfnet_scb_savedsearch'         => 'remoteShortcodeBuilderSavedSearch',
            'wolfnet_content'                 => 'remoteContent',
            'wolfnet_content_header'          => 'remoteContentHeader',
            'wolfnet_content_footer'          => 'remoteContentFooter',
            'wolfnet_listings'                => 'remoteListings',
            'wolfnet_get_listings'            => 'remoteListingsGet',
            'wolfnet_css'                     => 'remotePublicCss'
            );

        foreach ($ajxActions as $action => $method) {
            $this->addAction('wp_ajax_' . $action, array(&$this, $method));
        }

    }


    /**
    * Decodes all HTML entities, including numeric and hexadecimal ones.
    *
    * @param mixed $string
    * @return string decoded HTML
    */
    public function html_entity_decode_numeric($string, $quote_style=ENT_COMPAT, $charset='utf-8')
    {
        $hexCallback = array(&$this, 'chr_utf8_hex_callback');
        $nonHexCallback = array(&$this, 'chr_utf8_nonhex_callback');

        $string = html_entity_decode($string, $quote_style, $charset);
        $string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', $hexCallback, $string);
        $string = preg_replace_callback('~&#([0-9]+);~i', $nonHexCallback, $string);

        return $string;

    }


    /**
     * Callback helper
     */
    public function chr_utf8_hex_callback($matches)
    {
        return $this->chr_utf8(hexdec($matches[1]));

    }


    public function chr_utf8_nonhex_callback($matches)
    {
        return $this->chr_utf8($matches[1]);

    }


    /**
    * Multi-byte chr(): Will turn a numeric argument into a UTF-8 string.
    *
    * @param mixed $num
    * @return string
    */
    private function chr_utf8($num)
    {
        if ($num < 128) {
            return chr($num);
        }

        if ($num < 2048) {
            return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        }

        if ($num < 65536) {
            return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }

        if ($num < 2097152) {
            return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128)
                . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }

        return '';

    }


}


$GLOBALS['wolfnet'] = new wolfnet();
