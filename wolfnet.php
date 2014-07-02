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

class Wolfnet
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
    public $version = '{X.X.X}';

    /**
     * This property is used to set the option group for the plugin which creates a namespaced
     * collection of variables which are used in saving widget settings.
     * @var string
     */
    public $optionGroup = 'wolfnet';

    /**
     * This property is used to set the option group for the Edit Css page. It creates a namespaced
     * collection of variables which are used in saving page settings.
     * @var string
     */
    public $CssOptionGroup = 'wolfnetCss';

    /**
     * This property is used to define the 'search' custom type which is how "Search Manager"
     * searches are saved.
     * @var string
     */
    protected $customPostTypeSearch = 'wolfnet_search';

   
    /**
     * This property is a unique idenitfier that is used to define a plugin option which saves the
     * product key used by the plugin to retreive data from the WolfNet API.
     * @var string
     */
    protected $productKeyOptionKey = 'wolfnet_productKey';

    /**
     * This property contains the public CSS as defined in the Edit CSS page.
     * @var string
     */
    public $publicCssOptionKey = "wolfnetCss_publicCss";

    /**
     * This property is used to prefix custom hooks which are defined in the plugin. Specifically
     * this prefix is used for hooks which are executed before a certain portion of code.
     * @var string
     */
    protected $preHookPrefix = 'wolfnet_pre_';

    /**
     * This property is used to prefix custom hooks which are defined in the plugin. Specifically
     * this prefix is used for hooks which are executed after a certain portion of code.
     * @var string
     */
    protected $postHookPrefix = 'wolfnet_post_';

    /**
     * This property is used to determine how long a WNT session should last.
     * @var integer
     */
    protected $sessionLength = 3600; // one hour

    public $url;

    protected $pluginFile = __FILE__;

    public $smHttp = null;

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
        //$this->url = plugin_dir_url(__FILE__);
        $this->setUrl();

        // Set the Autoloader Method
        spl_autoload_register(array( $this, 'autoload'));

        $this->api = new Wolfnet_Api($this);
        $this->views = new Wolfnet_Views($this);
        if (is_admin()) {
            $this->admin = new Wolfnet_Admin($this);
        }

        // Register actions.
        $this->addAction(array(
            array('init',                  'init'),
            array('wp_enqueue_scripts',    'scripts'),
            array('wp_enqueue_scripts',    'styles'),
            array('widgets_init',          'widgetInit'),
            array('wp_footer',             'footer'),
            array('template_redirect',     'templateRedirect'),
            array('wp_enqueue_scripts',    'publicStyles',      1000),
            ));

        // Register filters.
        $this->addFilter(array(
            array('do_parse_request',     'doParseRequest'),
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
    public function publicStyles()
    {
        if(strlen($this->views->getPublicCss())) {
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
     * This method is a callback for the 'wp_footer' hook. Currently this method is used to display
     * market disclaimer information if necessary for the request.
     * @return void
     */
    public function footer()
    {
        do_action($this->preHookPrefix . 'footerDisclaimer'); // Legacy hook

        /* If it has been established that we need to output the market disclaimer do so now in the
         * site footer, otherwise do nothing. */
        if (array_key_exists('wolfnet_includeDisclaimer', $_REQUEST) && 
            array_key_exists('keyList', $_REQUEST)) {
            echo '<div class="wolfnet_marketDisclaimer">';
            foreach($_REQUEST['keyList'] as $key) {
                echo $this->api->getMarketDisclaimer($key);
            }
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

    public function searchManagerHtml($productKey=null)
    {
        global $wp_version;
        $baseUrl = $this->api->getBaseUrl($productKey);
        $maptracksEnabled = $this->api->getMaptracksEnabled($productKey);

        if (!strstr($baseUrl, 'index.cfm')) {
            if (substr($baseUrl, strlen($baseUrl) - 1) != '/') {
                $baseUrl .= '/';
            }

            $baseUrl .= 'index.cfm';

        }

        /* commenting out map mode in search manager until we better figure out session constraints..
        if (!array_key_exists('search_mode', $_GET)) {
            $_GET['search_mode'] = ($maptracksEnabled) ? 'map' : 'form';
        } */

        $_GET['search_mode'] = 'form';

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
                $paramValue = urlencode($this->api->html_entity_decode_numeric($paramValue));
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


    public function getProductKeyById($id) {
        $keyList = json_decode($this->getProductKey());
        foreach($keyList as $key) {
            if($key->id == $id) {
                return $key->key;
            }
        }
    }


    public function getDefaultProductKey() {
        $productKey = json_decode($this->getProductKey());
        return $productKey[0]->key;
    }


    public function getProductKey()
    {
        $key = get_option(trim($this->productKeyOptionKey));
        if(!$this->isJsonEncoded($key)) {
            $key = $this->setJsonProductKey($key);
        }
        return $key;
    }


    public function getItemsPerPage()
    {
        return array(5,10,15,20,25,30,35,40,45,50);

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

        echo ($this->api->productKeyIsValid($productKey)) ? 'true' : 'false';

        die;

    }


    public function remoteGetSavedSearches($keyid=null)
    {
        if($keyid == null) {
            $keyid = (array_key_exists('keyid', $_REQUEST)) ? $_REQUEST['keyid'] : '1';
        }
        echo json_encode($this->getSavedSearches(-1, $keyid));

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

            $key = $_REQUEST['custom_fields']['keyid'];

        }

        $this->remoteGetSavedSearches($key);

    }


    public function remoteDeleteSearch()
    {
        if (array_key_exists('id', $_REQUEST)) {
            wp_delete_post($_REQUEST['id'], true);
        }

        $this->remoteGetSavedSearches();

    }


    public function remoteShortcodeBuilderOptionsFeatured ()
    {
        $args = $this->getFeaturedListingsOptions();

        echo $this->views->featuredListingsOptionsFormView($args);

        die;

    }


    public function remoteShortcodeBuilderOptionsGrid ()
    {
        $args = $this->getListingGridOptions();

        echo $this->views->listingGridOptionsFormView($args);

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

        echo $this->views->quickSearchOptionsFormView($args);

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
        echo json_encode($this->api->getListings($args));
        echo $callback ? ');' : '';

        die;

    }


    public function remotePublicCss() 
    {
        header('Content-type: text/css');
        $publicCss = $this->views->getPublicCss();

        if(strlen($publicCss) > 0) {
            echo $publicCss;
        }

        die;

    }


    public function remotePriceRange() 
    {
        $productKey = $this->getProductKeyById($_REQUEST["keyid"]);
        $prices = $this->getPrices($productKey);
        echo json_encode($prices);

        die;
    }


    public function remoteGetMarketName() 
    {
        $productKey = $_REQUEST["productkey"];
        echo json_encode(strtoupper($this->api->getMarketName($productKey)));

        die;
    }


    public function remoteMapEnabled()
    {
        $productKey = $this->getProductKeyById($_REQUEST["keyid"]);
        echo json_encode($this->api->getMaptracksEnabled($productKey));

        die;
    }


    public function remoteGetBaseUrl() {
        $productKey = $this->getProductKeyById($_REQUEST["keyid"]);
        echo json_encode($this->api->getBaseUrl($productKey));

        die;
    }


    /* Data ************************************************************************************* */
    /*  _                                                                                         */
    /* | \  _. _|_  _.                                                                            */
    /* |_/ (_|  |_ (_|                                                                            */
    /*                                                                                            */
    /* ****************************************************************************************** */


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
            'keyid' => '',
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
        // Maintain backwards compatibility if there is no keyid in the shortcode.
        if(!array_key_exists('keyid', $criteria) || $criteria['keyid'] == '') {
            $criteria['keyid'] = 1;
        }

        if(!$this->isSavedKey($this->getProductKeyById($criteria['keyid']))) {
            return false;
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->api->getFeaturedListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->views->listingView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;
        $_REQUEST['productkey'] = $this->getProductKeyById($criteria['keyid']);

        // Keep a running array of product keys so we can output all necessary disclaimers
        if(!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }
        if(!in_array($_REQUEST['productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST['productkey']);
        }

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'listingsHtml' => $listingsHtml,
            'siteUrl'      => site_url(),
            'criteria'     => json_encode($criteria)
            );

        $args = $this->convertDataType(array_merge($criteria, $vars));

        return $this->views->featuredListingView($args);

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
            'exactcity'   => 0,
            'minprice'    => '',
            'maxprice'    => '',
            'keyid'       => '',
            );

    }


    public function getListingGridOptions($instance=null)
    {
        $options = $this->getOptions($this->getListingGridDefaults(), $instance);

        if(array_key_exists('keyid', $options) && $options['keyid'] != '') {
            $keyid = $options['keyid'];
        } else {
            $keyid = 1;
        }

        $options['mode_basic_wpc']        = checked($options['mode'], 'basic', false);
        $options['mode_advanced_wpc']     = checked($options['mode'], 'advanced', false);
        $options['paginated_false_wps']   = selected($options['paginated'], 'false', false);
        $options['paginated_true_wps']    = selected($options['paginated'], 'true', false);
        $options['sortoptions_false_wps'] = selected($options['sortoptions'], 'false', false);
        $options['sortoptions_true_wps']  = selected($options['sortoptions'], 'true', false);
        $options['ownertypes']            = $this->getOwnerTypes();
        $options['prices']                = $this->getPrices($this->getProductKeyById($keyid));
        $options['savedsearches']         = $this->getSavedSearches(-1, $keyid);
        $options['mapEnabled']            = $this->api->getMaptracksEnabled($this->getProductKeyById($keyid));
        $options['maptypes']              = $this->getMapTypes();

        return $options;

    }


    public function listingGrid(array $criteria)
    {
        // Maintain backwards compatibility if there is no keyid in the shortcode.
        if(!array_key_exists('keyid', $criteria) || $criteria['keyid'] == '') {
            $criteria['keyid'] = 1;
        }

        if(!$this->isSavedKey($this->getProductKeyById($criteria['keyid']))) {
            return false;
        }

        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }
        
        $listingsData = $this->api->getListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->views->listingView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;
        $_REQUEST['productkey'] = $this->getProductKeyById($criteria['keyid']);

        // Keep a running array of product keys so we can output all necessary disclaimers
        if(!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }
        if(!in_array($_REQUEST['productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST['productkey']);
        }

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'criteria'           => json_encode($criteria),
            'class'              => 'wolfnet_listingGrid ',
            'mapEnabled'         => $this->api->getMaptracksEnabled($_REQUEST['productkey']),
            'map'                => '',
            'mapType'            => '',
            'hideListingsTools'  => '',
            'hideListingsId'     => uniqid('hideListings'),
            'showListingsId'     => uniqid('showListings'),
            'collapseListingsId' => uniqid('collapseListings'),
            'toolbarTop'         => '',
            'toolbarBottom'      => '',
            'maxresults'         => ((count($listingsData) > 0) ? $listingsData[0]->maxresults : 0)
            );


        $vars = $this->convertDataType(array_merge($criteria, $vars));

        if ($vars['maptype'] != "disabled") {
            $vars['map']     = $this->getMap($listingsData, $_REQUEST['productkey']);
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

        return $this->views->listingGridView($vars);

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
        // Maintain backwards compatibility if there is no keyid in the shortcode.
        if(!array_key_exists('keyid', $criteria) || $criteria['keyid'] == '') {
            $criteria['keyid'] = 1;
        }

        if(!$this->isSavedKey($this->getProductKeyById($criteria['keyid']))) {
            return false;
        }

        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->api->getListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->views->listingBriefView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;
        $_REQUEST['productkey'] = $this->getProductKeyById($criteria['keyid']);

        // Keep a running array of product keys so we can output all necessary disclaimers
        if(!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }
        if(!in_array($_REQUEST['productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST['productkey']);
        }

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_propertyList_')),
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'criteria'           => json_encode($criteria),
            'class'              => 'wolfnet_propertyList ',
            'mapEnabled'         => $this->api->getMaptracksEnabled($_REQUEST['productkey']),
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
            $vars['map']     = $this->getMap($listingsData, $_REQUEST['productkey']);
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

        return $this->views->propertyListView($vars);

    }


    /* Results Summary ************************************************************************** */

    public function getResultsSummaryDefaults()
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


    public function getResultsSummaryOptions($instance=null)
    {

        return $this->getListingGridOptions($instance);

    }


    public function resultsSummary(array $criteria) 
    {
        // Maintain backwards compatibility if there is no keyid in the shortcode.
        if(!array_key_exists('keyid', $criteria) || $criteria['keyid'] == '') {
            $criteria['keyid'] = 1;
        }
        
        // if(!$this->isSavedKey($criteria['productkey'])) {
        if(!$this->isSavedKey($criteria['keyid'])) {
            return false;
        }

        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $listingsData = $this->api->getListings($criteria);

        $listingsHtml = '';

        foreach ($listingsData as &$listing) {

            $this->augmentListingData($listing);

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->views->listingResultsView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;
        $_REQUEST['productkey'] = $criteria['productkey'];

        // Keep a running array of product keys so we can output all necessary disclaimers
        if(!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }
        if(!in_array($_REQUEST['productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST['productkey']);
        }

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_resultsSummary_')),
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'criteria'           => json_encode($criteria),
            'class'              => 'wolfnet_resultsSummary ',
            'mapEnabled'         => $this->api->getMaptracksEnabled($criteria["productkey"]),
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
            $vars['map']     = $this->getMap($listingsData, $criteria["productkey"]);
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

        return $this->views->resultsSummaryView($vars);        
    }    



    /* Quick Search ***************************************************************************** */

    public function getQuickSearchDefaults()
    {

        return array(
            'title' => 'QuickSearch',
            'keyid' => '',
            'keyids' => '',
            );

    }


    public function getQuickSearchOptions($instance=null)
    {
        $options = $this->getOptions($this->getQuickSearchDefaults(), $instance);

        return $options;

    }


    public function quickSearch(array $criteria)
    {
        $productKey = $this->getDefaultProductKey();

        if(array_key_exists("keyids", $criteria)) {
            $keyids = explode(",", $criteria["keyids"]);
        } else {
            $keyids[0] = 1;
        }

        if(count($keyids) == 1) {
            $productKey = $this->getProductKeyById($keyids[0]);
        }

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
            'siteUrl'      => site_url(),
            'keyids'       => $keyids,
            'markets'      => json_decode($this->getProductKey()),
            'prices'       => $this->getPrices($productKey),
            'beds'         => $this->getBeds(),
            'baths'        => $this->getBaths(),
            'formAction'   => $this->api->getBaseUrl($productKey)
            );

        $args = $this->convertDataType(array_merge($criteria, $vars));

        return $this->views->quickSearchView($args);

    }


    /* Misc. Data ******************************************************************************* */

    public function getSavedSearches($count=-1, $keyid=null)
    {
        // Cache the data in the request scope so that we only have to query for it once per request.
        $cacheKey = 'wntSavedSearches';
        $data = (array_key_exists($cacheKey, $_REQUEST)) ? $_REQUEST[$cacheKey] : null;
        if($keyid == null) {
            $keyid = "1";
        }

        if ($data==null) {

            $dataArgs = array(
                'numberposts' => $count,
                'post_type' => $this->customPostTypeSearch,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'keyid',
                        'value' => $keyid,
                    )
                )
            );

            $data = get_posts($dataArgs);

            if(count($data) == 0 && $keyid == 1) {
                /* 
                 * This is for backwards compatibility - get posts without keyid meta query.
                 * We will loop through these custom posts and add the keyid meta key.
                 * Only do this on a keyid of 1 since that would be the default key back when we only allowed one.
                 */
                $dataArgs = array(
                    'numberposts' => $count,
                    'post_type' => $this->customPostTypeSearch,
                    'post_status' => 'publish',
                );

                $data = get_posts($dataArgs);

                foreach($data as $post) {
                    add_post_meta($post->ID, 'keyid', 1);
                }
            }

            $_REQUEST[$cacheKey] = $data;

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



    /* PROTECTED METHODS ************************************************************************ */
    /*  ____            _            _           _   __  __      _   _               _            */
    /* |  _ \ _ __ ___ | |_ ___  ___| |_ ___  __| | |  \/  | ___| |_| |__   ___   __| |___        */
    /* | |_) | '__/ _ \| __/ _ \/ __| __/ _ \/ _` | | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|       */
    /* |  __/| | | (_) | ||  __/ (__| ||  __/ (_| | | |  | |  __/ |_| | | | (_) | (_| \__ \       */
    /* |_|   |_|  \___/ \__\___|\___|\__\___|\__,_| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/       */
    /*                                                                                            */
    /* ****************************************************************************************** */


    protected function setUrl()
    {
        $this->url = plugin_dir_url(__FILE__);
    }
    
    protected function addAction($action, $callable=null, $priority=null)
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
            if (is_callable($callable) && is_array($callable)) {
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


    protected function addFilter($filter, $callable=null)
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


    protected function registerAdminAjaxActions()
    {
        $ajxActions = array(
            'wolfnet_validate_key'            => 'remoteValidateProductKey',
            'wolfnet_saved_searches'          => 'remoteGetSavedSearches',
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
            'wolfnet_css'                     => 'remotePublicCss',
            'wolfnet_price_range'             => 'remotePriceRange',
            'wolfnet_market_name'             => 'remoteGetMarketName',
            'wolfnet_map_enabled'             => 'remoteMapEnabled',
            'wolfnet_base_url'                => 'remoteGetBaseUrl',
            );

        foreach ($ajxActions as $action => $method) {
            $this->addAction('wp_ajax_' . $action, array(&$this, $method));
        }

    }


    
    /* PRIVATE METHODS ************************************************************************** */
    /*  ____       _            _         __  __      _   _               _                       */
    /* |  _ \ _ __(_)_   ____ _| |_ ___  |  \/  | ___| |_| |__   ___   __| |___                   */
    /* | |_) | '__| \ \ / / _` | __/ _ \ | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                  */
    /* |  __/| |  | |\ V / (_| | ||  __/ | |  | |  __/ |_| | | | (_) | (_| \__ \                  */
    /* |_|   |_|  |_| \_/ \__,_|\__\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                  */
    /*                                                                                            */
    /* ****************************************************************************************** */

    /**
     * This method is used to load additional classes as needed. defined in the construct by 
     * spl_autoload_register().
     * @param  string $class The name of the class to load. same as the name of the file in the 
     * classes directory
     * @return bool success?
     */
    private function autoload($class)
    {
        $filename = $class . '.php';
        $file = $this->dir . '/wolfnet/' . $filename;
        if (!file_exists($file))
        {
            //  echo "could not fined $file.<br>";
            return false;
        }
        include $file;
        return true;
    }


    private function isSavedKey($find) {
        $keyList = json_decode($this->getProductKey());

        foreach($keyList as $key) {
            if($key->key == $find) {
                return true;
            }
        }

        return false;
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

   
    protected function setJsonProductKey($keyString) {
        // This takes the old style single key string and returns a JSON formatted key array
        $keyArray = array(
            array(
                "id" => "1",
                "key" => $keyString,
                "label" => ""
            )
        );
        return json_encode($keyArray);
    }


    private function isJsonEncoded($str) 
    {
        if(is_array(json_decode($str)) || is_object(json_decode($str))) {
            return true;
        } else {
            return false;
        }
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


    private function getMap($listingsData, $productKey=null) 
    {      
        return $this->views->mapView($listingsData, $productKey);
    }


    private function getHideListingTools($hideId,$showId,$collapseId,$instance_id)
    {
        return $this->views->hideListingsToolsView($hideId,$showId,$collapseId,$instance_id);

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

        return $this->views->toolbarView($args);

    }

    // remove this. for testing
    private function testMethod ()
    {
        return true;
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


    private function getPrices($productKey)
    {
        $values = $this->api->getPricesFromApi($productKey);
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
            'mapquest-api' => array(
                '//www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key=Gmjtd%7Clu6znua2n9%2C7l%3Do5-la70q',
                ),
            'wolfnet-maptracks' => array(
                $this->url . 'js/jquery.wolfnetMaptracks.src.js',
                array('jquery', 'mapquest-api'),
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

}


$GLOBALS['wolfnet'] = new Wolfnet();
