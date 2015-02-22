<?php

/**
 * @title         Wolfnet_Plugin.php
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
     * This Property is use as a prefix to request scope variables to avoid conflicts with get,
     * post, and other global variables used by wordpress and other plugins.
     * @var string
     */
    public $requestPrefix = 'wolfnet_';

    /**
     * This property is used to determine how long a WNT session should last.
     * @var integer
     */
    protected $sessionLength = 3600; // one hour


    /**
     * This property defines a the request parameter which is used to determine if the values which
     * are cached in the Transient API should be cleared.
     * @var string
     */
    protected $cacheFlag = 'wolfnet-cache';

    public $url;

    protected $pluginFile;

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
        $this->pluginFile = dirname(dirname(__FILE__)) . '/wolfnet.php';
        $this->setUrl();

        $this->apin = new Wolfnet_Api_Wp_Client();
        $this->views = new Wolfnet_Views();
        if (is_admin()) {
            $this->admin = new Wolfnet_Admin($this);
        }


        // Clear cache if url param exists.
        $cacheParamExists = array_key_exists($this->cacheFlag, $_REQUEST);
        $cacheParamClear = ($cacheParamExists) ? ($_REQUEST[$this->cacheFlag] == 'clear') : false;
        if ($cacheParamExists && $cacheParamClear) {
            $this->apin->clearTransients('all');
        }

        // Register actions.
        $this->addAction(array(
            array('init',                  'init'),
            array('wp_enqueue_scripts',    'scripts'),
            array('wp_enqueue_scripts',    'styles'),

            array('wp_footer',             'footer'),
            array('template_redirect',     'templateRedirect'),
            array('wp_enqueue_scripts',    'publicStyles',      1000),
            ));

        if ($this->getDefaultProductKey()){
            $this->addAction(array(
                array('widgets_init',      'widgetInit'),
            ));
        }

        // Register filters.
        $this->addFilter(array(
            array('do_parse_request',     'doParseRequest'),
            ));

        register_activation_hook( $this->pluginFile, array( $this, 'wolfnet_activation' ));
        register_deactivation_hook( $this->pluginFile, array( $this, 'wolfnet_deactivation' ));

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

    public function wolfnet_activation()
    {
        $this->apin->startWpDailyCron();
    }

    public function wolfnet_deactivation()
    {
        $this->apin->stopWpDailyCron();
        $this->apin->clearTransients('all');
    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. Any JavaScript files (and their
     * dependencies) which are needed by the plugin for public interfaces are registered in this
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
     * which are needed for the plugin after all the other CSS includes in the event that we
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

        register_widget('Wolfnet_FeaturedListingsWidget');

        register_widget('Wolfnet_ListingGridWidget');

        register_widget('Wolfnet_PropertyListWidget');

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
                $disclaimer = $this->apin->sendRequest( $key, '/core/disclaimer', 'GET', array('type'=>'search_results', 'format'=>'html') );
                if (!is_wp_error($disclaimer)) echo $disclaimer['responseData']['data'];
            }
            echo '</div>';
        }
        // TODO: Add a filter point here. Allow developers to filter the disclaimer content for formatting purposes.

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
     * specific pagename prefix and if it is present then WordPress should not parse the request.
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
     * This method is used to retrieve search solution HTML from an MLSFinder 2.5 search solution
     * for use as a 'search manager' interface in the WordPress admin.
     * @param  string $productKey The product key for the solution to be retrieved.
     * @return string             The HTML retrieved from the MLSFinder server.
     */
    public function searchManagerHtml($productKey=null)
    {
        global $wp_version;
        $http = array();

        $baseUrl = $this->getBaseUrl($productKey);
        //$maptracksEnabled = $this->getMaptracksEnabled($productKey);

        if (is_wp_error($baseUrl)) {
            $http['body'] = $this->getWpError($baseUrl);
            return $http;
        }

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
                //null returned on non-200; wperrors returned in all other error handling in this fctn
                return array('body' => '');
            }
        }
        else {
            $http['body'] = $this->getWpError($http);
            return $http;
        }

    }


    /**
     * This method retrieves a specific product key from the WordPress options table based on a
     * provided unique ID value.
     * @param  integer $id The ID of the key to be retrieved.
     * @return string      The key that was retrieved from the WP options table.
     */
    public function getProductKeyById($id) {
        $keyList = json_decode($this->getProductKey());

        foreach($keyList as $key) {
            if($key->id == $id) {
                return $key->key;
            }
            // TODO: Add some sort of error throwing if no key is found for the given ID.
        }
    }


    /**
     * This method retrieved the 'default' key (or first key on the stack) from the WP options table.
     * @return string The key that was retrieved from the WP options table.
     */
    public function getDefaultProductKey() {

        $productKey = json_decode($this->getProductKey());
        // TODO: Add some sort of error throwing for if there are no keys.

        if (is_array($productKey) && array_key_exists(0, $productKey)) {
            return $productKey[0]->key;
        } else {
            return false;
        }
    }


    /**
     * This method retrieves a JSON representation of stored product keys from the WP options table.
     * @return string JSON representation of the stored product keys.
     */
    public function getProductKey()
    {
        $key = get_option(trim($this->productKeyOptionKey));
        // If the value stored in the options table is a legacy, single key value convert it to the
        // newer JSON format.
        if(!$this->isJsonEncoded($key)) {
            $key = $this->setJsonProductKey($key);
        }
        // TODO: perhaps it would be better to decode the JSON here instead of multiple other places.
        return $key;
    }


    /**
     * This method returns an array of integer values to be used as possible pagination item counts.
     * @return array An array of integers.
     */
    public function getItemsPerPage()
    {
        return array(5,10,15,20,25,30,35,40,45,50);
    }


    public function getMarketName($apiKey)
    {
        $data = $this->apin->sendRequest( $apiKey, '/settings');
        if (is_wp_error($data)) return $data;

        return $data['responseData']['data']['market']['datasource_name'];
    }



    public function buildUrl($url='', array $params=array())
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


    public function sclistingGrid($attrs)
    {
        $default_maxrows = '50';
        $criteria = array_merge($this->getListingGridDefaults(), (is_array($attrs)) ? $attrs : array());

        if ($criteria['maxrows'] == $default_maxrows && $criteria['maxresults'] != $default_maxrows ) {
           $criteria['maxrows'] = $criteria['maxresults'];
        }

        return $this->listingGrid( $criteria );

    }


    public function scPropertyList($attrs = array())
    {
        $criteria = array_merge($this->getPropertyListDefaults(), (is_array($attrs)) ? $attrs : array());

        return $this->listingGrid($criteria, 'list');

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


    public function remoteShortcodeBuilderOptionsFeatured()
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

        // used by pagination dropdown "per page"
        if (!empty($_REQUEST['numrows'])) {
            $_REQUEST['maxrows'] = $_REQUEST['numrows'];
        }

        // Convert 'zipcode' to 'zip_code' for new API
        if (array_key_exists('zipcode', $criteria)) {
            $criteria['zip_code'] = $criteria['zipcode'];
            unset($criteria['zipcode']);
        }

        $qdata = $this->prepareListingQuery($_REQUEST);

        $data = $this->apin->sendRequest($_REQUEST['key'], '/listing', 'GET', $qdata);
        if (is_wp_error($data)) return $this->getWpError($data);

        $this->augmentListingsData($data, $_REQUEST['key']);

        echo $callback ? $callback . '(' : '';

        echo json_encode($data);
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
        $marketName = $this->getMarketName($productKey);

        if (is_wp_error($marketName)) {
            echo '';
        } else {
            echo json_encode(strtoupper($marketName));
        }

        die;
    }


    public function remoteMapEnabled()
    {
        $productKey = $this->getProductKeyById($_REQUEST["keyid"]);
        echo json_encode($this->getMaptracksEnabled($productKey));

        die;
    }


    public function remoteGetBaseUrl() {
        $productKey = $this->getProductKeyById($_REQUEST["keyid"]);
        echo json_encode($this->getBaseUrl($productKey));

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

        if(!array_key_exists('key', $criteria) || $criteria['key'] == '') {
            $criteria['key'] = $this->getDefaultProductKey();
        }

        if(!$this->isSavedKey($this->getProductKeyById($criteria['keyid']))) {
            return false;
        }

        if (!array_key_exists('startrow', $criteria)) {
            $criteria['startrow'] = 1;
        }

        $qdata = array(
            'maxrows' => $criteria['maxresults'],
            'startrow' => $criteria['startrow'],
            );

        if($criteria['ownertype'] == 'agent_broker' )
            $qdata['agent_office_only'] = true;

        if($criteria['ownertype'] == 'agent' )
            $qdata['agent_only'] = true;

        if($criteria['ownertype'] == 'broker' )
            $qdata['office_only'] = true;

        $data = $this->apin->sendRequest($criteria['key'], '/listing', 'GET', $qdata);

        if (is_wp_error($data)) return $this->getWpError($data);

        $this->augmentListingsData($data, $criteria['key']);



        $listingsData = array();

        if (is_array($data['responseData']['data']))
            $listingsData = $data['responseData']['data']['listing'];

        $listingsHtml = '';


        foreach ($listingsData as &$listing) {

            $vars = array(
                'listing' => $listing
                );

            $listingsHtml .= $this->views->listingView($vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;
        $_REQUEST[$this->requestPrefix.'productkey'] = $this->getProductKeyById($criteria['keyid']);

        // Keep a running array of product keys so we can output all necessary disclaimers
        if(!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }
        if(!in_array($_REQUEST[$this->requestPrefix.'productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST[$this->requestPrefix.'productkey']);
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
        $options['mapEnabled']            = $this->getMaptracksEnabled($this->getProductKeyById($keyid));
        $options['maptypes']              = $this->getMapTypes();

        return $options;

    }


    /**
     * Returns the markup for listings. generates both the listingGrid layout as well as the property list layout
     * @param  array  $criteria the search criteria
     * @param  string $layout   'grid' or 'list'
     * @return string           listings markup
     */
    public function listingGrid(array $criteria, $layout='grid')
    {

        // Maintain backwards compatibility if there is no keyid in the shortcode.
        if(!array_key_exists('keyid', $criteria) || $criteria['keyid'] == '') {
            $criteria['keyid'] = 1;
        }

        if(!array_key_exists('key', $criteria) || $criteria['key'] == '') {
            $criteria['key'] = $this->getDefaultProductKey();
        }

        if(!$this->isSavedKey($this->getProductKeyById($criteria['keyid']))) {
            return false;
        }

        if (!array_key_exists('numrows', $criteria)) {
            $criteria['maxrows'] = $criteria['maxresults'];
        }

        // Convert 'zipcode' to 'zip_code' for new API
        if (array_key_exists('zipcode', $criteria)) {
            $criteria['zip_code'] = $criteria['zipcode'];
            unset($criteria['zipcode']);
        }

        $qdata = $this->prepareListingQuery($criteria);

        $data = $this->apin->sendRequest($criteria['key'], '/listing', 'GET', $qdata);
        if (is_wp_error($data)) return $this->getWpError($data);

        // add some elements to the array returned by the API
        // wpMeta should contain any criteria or other setting which do not come from the API
        $data['wpMeta']   = $criteria;

        $data['wpMeta']['total_rows'] = $data['responseData']['data']['total_rows'];

        $this->augmentListingsData($data, $criteria['key']);

        $listingsData = array();

        if (is_array($data['responseData']['data']))
            $listingsData = $data['responseData']['data']['listing'];

        $listingsHtml = '';

        if (!count($listingsData)){
            $listingsHtml = 'No Listings Found.';
        } else {
            foreach ($listingsData as &$listing) {

                // do we need this ??
                $vars = array(
                   'listing' => $listing
                   );

                if($layout=='list') {
                    $listingsHtml .= $this->views->listingBriefView($vars);

                }
                elseif($layout=='grid') {
                    $listingsHtml .= $this->views->listingView($vars);
                }


            }

            $_REQUEST['wolfnet_includeDisclaimer'] = true;
        }

        $_REQUEST[$this->requestPrefix.'productkey'] = $this->getProductKeyById($criteria['keyid']);

        // Keep a running array of product keys so we can output all necessary disclaimers
        if(!array_key_exists('keyList', $_REQUEST)) {
            $_REQUEST['keyList'] = array();
        }
        if(!in_array($_REQUEST[$this->requestPrefix.'productkey'], $_REQUEST['keyList'])) {
            array_push($_REQUEST['keyList'], $_REQUEST[$this->requestPrefix.'productkey']);
        }

        $vars = array(
            'instance_id'        => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            // ??? TODO not needed?? we are merging $vars and listing data below.
            'listings'           => $listingsData,
            'listingsHtml'       => $listingsHtml,
            'siteUrl'            => site_url(),
            'wpMeta'             => $data['wpMeta'],
            'title'              => $data['wpMeta']['title'],
            'class'              => $criteria['class'],
            'mapEnabled'         => $this->getMaptracksEnabled($data['wpMeta']['key']),
            'map'                => '',
            'maptype'            => $data['wpMeta']['maptype'],
            'hideListingsTools'  => '',
            'hideListingsId'     => uniqid('hideListings'),
            'showListingsId'     => uniqid('showListings'),
            'collapseListingsId' => uniqid('collapseListings'),
            'toolbarTop'         => '',
            'toolbarBottom'      => '',
            'maxrows'            => ((count($listingsData) > 0) ? $data['requestData']['maxrows'] : 0),

            );

        if (count($listingsData) && is_array($listingsData))
            $vars = $this->convertDataType(array_merge($vars, $listingsData));

        if ($vars['wpMeta']['maptype'] != "disabled") {
            $vars['map']                            = $this->getMap($listingsData, $_REQUEST[$this->requestPrefix.'productkey']);
            $vars['wpMeta']['maptype']              = $vars['maptype'];
            $vars['hideListingsTools']              = $this->getHideListingTools($vars['hideListingsId']
                                                                   ,$vars['showListingsId']
                                                                   ,$vars['collapseListingsId']
                                                                   ,$vars['instance_id']);
        }

        if (!array_key_exists('startrow', $vars['wpMeta'])) {
            $vars['wpMeta']['startrow'] = 1;
        }

        if ($vars['wpMeta']['paginated'] || $vars['wpMeta']['sortoptions']) {
            $vars['toolbarTop']    = $this->getToolbar($vars, 'wolfnet_toolbarTop ');
            $vars['toolbarBottom'] = $this->getToolbar($vars, 'wolfnet_toolbarBottom ');
        }

        if ($vars['wpMeta']['paginated']) {
            $vars['class'] .= 'wolfnet_withPagination ';
        }

        if ($vars['wpMeta']['sortoptions']) {
            $vars['class'] .= 'wolfnet_withSortOptions ';
        }

        // $layout='grid'
        if($layout=='list') {
            // echo "propertyListView<br>";
            return $this->views->propertyListView($vars);
        } else {
            return $this->views->listingGridView($vars);
        }


    }


    /* listings **************************************************************************** */

    /**
     * returns array containing all fields supported by the /listing queries to the API
     * plus some legacy fields
     * @return array all sported /listing query fields
     */
    public function getListingFields()
    {
        return array (

            );
    }

    public function getListingGridDefaults()
    {

        return array(
            'title'       => '',
            'class'       => 'wolfnet_listingGrid ',
            'criteria'    => '',
            'ownertype'   => 'all',
            'maptype'     => 'disabled',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'maxrows'     => 50,
            'mode'        => 'advanced',
            'savedsearch' => '',
            'zipcode'     => '',
            'city'        => '',
            'exactcity'   => 0,
            'minprice'    => '',
            'maxprice'    => '',
            'keyid'       => 1,
            'key'         => $this->getDefaultProductKey(),
            'startrow'    => 1,
            );

    }


    public function getPropertyListDefaults()
    {
        return array(
            'title'       => '',
            'class'       => 'wolfnet_propertyList ',
            'criteria'    => '',
            'ownertype'   => 'all',
            'maptype'     => 'disabled',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50, // needed??
            'maxrows'     => 50,
            'mode'        => 'advanced',
            'savedsearch' => '',
            'zipcode'     => '',
            'city'        => '',
            'exactcity'   => 0,
            'minprice'    => '',
            'maxprice'    => '',
            'keyid'       => 1,
            'key'         => $this->getDefaultProductKey(),
            'startrow'    => 1,
            );

    }


    public function getPropertyListOptions($instance=null)
    {
        return $this->getListingGridOptions($instance);

    }


    /* Quick Search ***************************************************************************** */

    public function getQuickSearchDefaults()
    {

        return array(
            'title'     => 'QuickSearch',
            'keyid'     => '',
            'keyids'    => '',
            'view'      => '',
            );

    }


    public function getQuickSearchOptions($instance=null)
    {
        $options = $this->getOptions($this->getQuickSearchDefaults(), $instance);

        return $options;

    }

    /**
     * Get markup for Quick Search form
     * @param  array  $criteria
     * @return string           form markup
     */
    public function quickSearch(array $criteria)
    {

        if (array_key_exists("keyids", $criteria) && !empty($criteria['keyids'])) {
            $keyids = explode(",", $criteria["keyids"]);
        } else {
            $keyids[0] = 1;
        }

        if(count($keyids) == 1) {
            $productKey = $this->getProductKeyById($keyids[0]);
        } else {
            $productKey = $this->getDefaultProductKey();
        }

        if (is_wp_error($productKey)) {
            return $this->getWpError($productKey);
        }

        // Get data
        $prices = $this->getPrices($productKey);
        $beds = $this->getBeds();
        $baths = $this->getBaths();
        $formAction = $this->getBaseUrl($productKey);
        $markets = $this->getProductKey();

        if (is_wp_error($prices)) {
            return $this->getWpError($prices);
        }

        if (is_wp_error($beds)) {
            return $this->getWpError($beds);
        }

        if (is_wp_error($baths)) {
            return $this->getWpError($baths);
        }

        if (is_wp_error($formAction)) {
            return $this->getWpError($formAction);
        }

        if (is_wp_error($markets)) {
            return $this->getWpError($markets);
        }

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
            'siteUrl'      => site_url(),
            'keyids'       => $keyids,
            'markets'      => json_decode($markets),
            'prices'       => $prices,
            'beds'         => $beds,
            'baths'        => $baths,
            'formAction'   => $formAction,
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


    /**
     * Prepare an array usable by the Wolfnet API for a /listing query
     * @param  array  $criteria mixed array containing query api parameter. this can also contain
     * other items not used by the API, these will be stripped out.
     * @return array            Return array containing only query parameters to be passed to the api
     */
    public function prepareListingQuery(array $criteria)
    {

        $qdata = array(); // will hold only valid api criteria

        if ( !empty( $criteria['exactcity'] ))  $qdata['exact_city'] = $criteria['exactcity']; // old
        if ( !empty( $criteria['exact_city'] ))  $qdata['exact_city'] = $criteria['exact_city']; // new

        // additional checks for city to handle multiple cities
        if ( !empty( $criteria['city'] )) {
            $qdata['city'] = $criteria['city'];
            // in order to parse a list of cities separated by commas ',' exact_city must be false
            // Because 'exact_city' in the past was set to true by default even when a list is used
            // we look for a comma in the string and change exact city to false if one exists
            if ( strpos($criteria['city'], ',') !== false) $qdata['exact_city'] = 0;
        }

        if ( !empty( $criteria['primarysearchtype'] )) {
            if ( $criteria['primarysearchtype'] == 'sold' ) $qdata['sold'] = 1;
            if ( $criteria['primarysearchtype'] == 'open' ) $qdata['open_house'] = 1;
            if ( $criteria['primarysearchtype'] == 'foreclosure' ) $qdata['foreclosure'] = 1;
        }

        // 'owner_type' was replaced by bools 'agent_only', 'office_only' and 'agent_office_only'
        // if owner_type is set then add the correct bool
        // if the bool is set directly the value set will overwrite what has been set by owner_type below
        //
        // The field 'ownertype' is being used by a client but I can't find where the plugin is generating that
        if ( !empty( $criteria['ownertype'] )) {
            if ($criteria['ownertype'] == 'agent') $qdata['agent_only'] = 1;
            if ($criteria['ownertype'] == 'broker') $qdata['office_only'] = 1;
            if ($criteria['ownertype'] == 'agent_broker') $qdata['agent_office_only'] = 1;
        }
        if ( !empty( $criteria['owner_type'] )) {
            if ($criteria['owner_type'] == 'agent') $qdata['agent_only'] = 1;
            if ($criteria['owner_type'] == 'broker') $qdata['office_only'] = 1;
            if ($criteria['owner_type'] == 'agent_broker') $qdata['agent_office_only'] = 1;
        }

        if (isset( $criteria['agent_only'] ))
            $qdata['agent_only'] = $this->convertBool($criteria['agent_only']);
        if (isset( $criteria['office_only'] ))
            $qdata['office_only'] = $this->convertBool($criteria['office_only']);
        if (isset( $criteria['agent_office_only'] ))
            $qdata['agent_office_only'] = $this->convertBool($criteria['agent_office_only']);

        if (isset($criteria['address'])) $qdata['address'] = $criteria['address'];
        if (isset($criteria['agent_id'])) $qdata['agent_id'] = $criteria['agent_id'];

        if (isset($criteria['area_name'])) $qdata['area_name'] = $criteria['area_name'];
        if (isset($criteria['area_int'])) $qdata['area_int'] = $criteria['area_int'];
        if (isset($criteria['building_name'])) $qdata['building_name'] = $criteria['building_name'];
        if (isset($criteria['built_after'])) $qdata['built_after'] = $criteria['built_after'];
        if (isset($criteria['built_before'])) $qdata['built_before'] = $criteria['built_before'];
        if (isset( $criteria['business_with_real_estate'] ))
            $qdata['business_with_real_estate'] = $this->convertBool($criteria['business_with_real_estate']);
        if (isset( $criteria['commercial'] ))
            $qdata['commercial'] = $this->convertBool($criteria['commercial']);
        if (isset( $criteria['commercial_lease'] ))
            $qdata['commercial_lease'] = $this->convertBool($criteria['commercial_lease']);
        if (isset($criteria['community'])) $qdata['community'] = $criteria['community'];
        if (isset($criteria['community_text'])) $qdata['community_text'] = $criteria['community_text'];
        if (isset($criteria['commute_time'])) $qdata['commute_time'] = $criteria['commute_time'];
        if (isset( $criteria['condo'] ))
            $qdata['condo'] = $this->convertBool($criteria['condo']);
        if (isset( $criteria['condo_townhouse'] ))
            $qdata['condo_townhouse'] = $this->convertBool($criteria['condo_townhouse']);
        if (isset($criteria['cost_of_living'])) $qdata['cost_of_living'] = $criteria['cost_of_living'];
        if (isset($criteria['county'])) $qdata['county'] = $criteria['county'];
        if (isset($criteria['crime_rating'])) $qdata['crime_rating'] = $criteria['crime_rating'];
        if (isset($criteria['date_from'])) $qdata['date_from'] = $criteria['date_from'];
        if (isset($criteria['date_to'])) $qdata['date_to'] = $criteria['date_to'];
        if (isset($criteria['detaillevel'])) $qdata['detaillevel'] = $criteria['detaillevel'];
        if (isset( $criteria['duplex'] ))
            $qdata['duplex'] = $this->convertBool($criteria['duplex']);
        if (isset($criteria['elementary_school'])) $qdata['elementary_school'] = $criteria['elementary_school'];
        if (isset( $criteria['exact_property_id'] ))
            $qdata['exact_property_id'] = $this->convertBool($criteria['exact_property_id']);
        if (isset( $criteria['farm_hobby'] ))
            $qdata['farm_hobby'] = $this->convertBool($criteria['farm_hobby']);
        if (isset($criteria['favorites_id'])) $qdata['favorites_id'] = $criteria['favorites_id'];
        if (isset( $criteria['foreclosure'] ))
            $qdata['foreclosure'] = $this->convertBool($criteria['foreclosure']);
        if (isset($criteria['garage_spaces'])) $qdata['garage_spaces'] = $criteria['garage_spaces'];
        if (isset( $criteria['gated_community'] ))
            $qdata['gated_community'] = $this->convertBool($criteria['gated_community']);
        if (isset( $criteria['half_duplex'] ))
            $qdata['half_duplex'] = $this->convertBool($criteria['half_duplex']);
        if (isset( $criteria['has_basement'] ))
            $qdata['has_basement'] = $this->convertBool($criteria['has_basement']);
        if (isset( $criteria['has_family_room'] ))
            $qdata['has_family_room'] = $this->convertBool($criteria['has_family_room']);
        if (isset( $criteria['has_fireplace'] ))
            $qdata['has_fireplace'] = $this->convertBool($criteria['has_fireplace']);
        if (isset( $criteria['has_garage'] ))
            $qdata['has_garage'] = $this->convertBool($criteria['has_garage']);
        if (isset( $criteria['has_golf'] ))
            $qdata['has_golf'] = $this->convertBool($criteria['has_golf']);
        if (isset( $criteria['has_horse_property'] ))
            $qdata['has_horse_property'] = $this->convertBool($criteria['has_horse_property']);
        if (isset( $criteria['has_mountain_view'] ))
            $qdata['has_mountain_view'] = $this->convertBool($criteria['has_mountain_view']);
        if (isset( $criteria['has_pool'] ))
            $qdata['has_pool'] = $this->convertBool($criteria['has_pool']);
        if (isset( $criteria['has_waterfront'] ))
            $qdata['has_waterfront'] = $this->convertBool($criteria['has_waterfront']);
        if (isset( $criteria['has_waterview'] ))
            $qdata['has_waterview'] = $this->convertBool($criteria['has_waterview']);
        if (isset($criteria['high_school'])) $qdata['high_school'] = $criteria['high_school'];
        if (isset( $criteria['industrial'] ))
            $qdata['industrial'] = $this->convertBool($criteria['industrial']);
        if (isset( $criteria['investment'] ))
            $qdata['investment'] = $this->convertBool($criteria['investment']);
        if (isset($criteria['jr_high_school'])) $qdata['jr_high_school'] = $criteria['jr_high_school'];
        if (isset($criteria['lake_name'])) $qdata['lake_name'] = $criteria['lake_name'];
        if (isset($criteria['last_update_date'])) $qdata['last_update_date'] = $criteria['last_update_date'];
        if (isset($criteria['list_date'])) $qdata['list_date'] = $criteria['list_date'];
        if (isset($criteria['listing_status'])) $qdata['listing_status'] = $criteria['listing_status'];
        if (isset( $criteria['loft'] ))
            $qdata['loft'] = $this->convertBool($criteria['loft']);
        if (isset( $criteria['lots_acreage'] ))
            $qdata['lots_acreage'] = $this->convertBool($criteria['lots_acreage']);
        if (isset($criteria['map_br_lat'])) $qdata['map_br_lat'] = $criteria['map_br_lat'];
        if (isset($criteria['map_br_lng'])) $qdata['map_br_lng'] = $criteria['map_br_lng'];
        if (isset($criteria['map_tl_lat'])) $qdata['map_tl_lat'] = $criteria['map_tl_lat'];
        if (isset($criteria['map_tl_lng'])) $qdata['map_tl_lng'] = $criteria['map_tl_lng'];
        if (isset($criteria['max_bathrooms'])) $qdata['max_bathrooms'] = $criteria['max_bathrooms'];
        if (isset($criteria['max_bedrooms'])) $qdata['max_bedrooms'] = $criteria['max_bedrooms'];
        if (isset($criteria['max_price'])) $qdata['max_price'] = $criteria['max_price'];
        if (isset($criteria['maxprice'])) $qdata['max_price'] = $criteria['maxprice']; // legacy
        if (isset($criteria['max_price'])) $qdata['max_price'] = $criteria['max_price'];
        if (isset($criteria['maxrows'])) $qdata['maxrows'] = $criteria['maxrows'];
        if (isset($criteria['median_household_income'])) $qdata['median_household_income'] = $criteria['median_household_income'];
        if (isset($criteria['middle_school'])) $qdata['middle_school'] = $criteria['middle_school'];
        if (isset($criteria['min_acres'])) $qdata['min_acres'] = $criteria['min_acres'];
        if (isset($criteria['min_bathrooms'])) $qdata['min_bathrooms'] = $criteria['min_bathrooms'];
        if (isset($criteria['min_bedrooms'])) $qdata['min_bedrooms'] = $criteria['min_bedrooms'];
        if (isset($criteria['min_price'])) $qdata['min_price'] = $criteria['min_price'];
        if (isset($criteria['min_square_feet'])) $qdata['min_square_feet'] = $criteria['min_square_feet'];
        if (isset($criteria['minprice'])) $qdata['min_price'] = $criteria['minprice']; // legacy
        if (isset($criteria['min_price'])) $qdata['min_price'] = $criteria['min_price'];
        if (isset( $criteria['mixed_use'] ))
            $qdata['mixed_use'] = $this->convertBool($criteria['mixed_use']);
        if (isset( $criteria['mobile_home'] ))
            $qdata['mobile_home'] = $this->convertBool($criteria['mobile_home']);
        if (isset( $criteria['model'] ))
            $qdata['model'] = $this->convertBool($criteria['model']);
        if (isset( $criteria['multi_family'] ))
            $qdata['multi_family'] = $this->convertBool($criteria['multi_family']);
        if (isset( $criteria['new_and_updated'] ))
            $qdata['new_and_updated'] = $this->convertBool($criteria['new_and_updated']);
        if (isset( $criteria['new_construction'] ))
            $qdata['new_construction'] = $this->convertBool($criteria['new_construction']);
        if (isset( $criteria['newlistings'] ))
            $qdata['newlistings'] = $this->convertBool($criteria['newlistings']);
        if (isset($criteria['office_id'])) $qdata['office_id'] = $criteria['office_id'];

        if (isset( $criteria['on_golf_course'] ))
            $qdata['on_golf_course'] = $this->convertBool($criteria['on_golf_course']);
        if (isset( $criteria['open_house'] ))
            $qdata['open_house'] = $this->convertBool($criteria['open_house']);
        if (isset($criteria['open_text'])) $qdata['open_text'] = $criteria['open_text'];
        if (isset($criteria['price_weight'])) $qdata['price_weight'] = $criteria['price_weight'];
        if (isset( $criteria['priceReduced'] ))
            $qdata['pricereduced'] = $this->convertBool($criteria['priceReduced']); // legacy
        if (isset( $criteria['pricereduced'] ))
            $qdata['pricereduced'] = $this->convertBool($criteria['pricereduced']);
        if (isset($criteria['property_id'])) $qdata['property_id'] = $criteria['property_id'];
        if (isset($criteria['property_type'])) $qdata['property_type'] = $criteria['property_type'];
        if (isset($criteria['property_url'])) $qdata['property_url'] = $criteria['property_url'];
        if (isset( $criteria['property_view'] ))
            $qdata['property_view'] = $this->convertBool($criteria['property_view']);
        if (isset($criteria['r_lat'])) $qdata['r_lat'] = $criteria['r_lat'];
        if (isset($criteria['r_lng'])) $qdata['r_lng'] = $criteria['r_lng'];
        if (isset($criteria['radius'])) $qdata['radius'] = $criteria['radius'];
        if (isset( $criteria['redraw_map_bounds'] ))
            $qdata['redraw_map_bounds'] = $this->convertBool($criteria['redraw_map_bounds']);
        if (isset( $criteria['residential_lease'] ))
            $qdata['residential_lease'] = $this->convertBool($criteria['residential_lease']);
        if (isset( $criteria['residential_lease_detached'] ))
            $qdata['residential_lease_detached'] = $this->convertBool($criteria['residential_lease_detached']);
        if (isset( $criteria['retail_store'] ))
            $qdata['retail_store'] = $this->convertBool($criteria['retail_store']);
        if (isset($criteria['school'])) $qdata['school'] = $criteria['school'];
        if (isset($criteria['school_district_name'])) $qdata['school_district_name'] = $criteria['school_district_name'];
        if (isset($criteria['school_rating'])) $qdata['school_rating'] = $criteria['school_rating'];
        if (isset($criteria['selling_agent_id'])) $qdata['selling_agent_id'] = $criteria['selling_agent_id'];
        if (isset($criteria['selling_office_id'])) $qdata['selling_office_id'] = $criteria['selling_office_id'];
        if (isset( $criteria['shortsale'] ))
            $qdata['shortsale'] = $this->convertBool($criteria['shortsale']);
        if (isset( $criteria['similar_listings'] ))
            $qdata['similar_listings'] = $this->convertBool($criteria['similar_listings']);
        if (isset( $criteria['single_family'] ))
            $qdata['single_family'] = $this->convertBool($criteria['single_family']);
        if (isset( $criteria['single_family_detached'] ))
            $qdata['single_family_detached'] = $this->convertBool($criteria['single_family_detached']);
        if (isset( $criteria['sold'] ))
            $qdata['sold'] = $this->convertBool($criteria['sold']);
        if (isset($criteria['sold_age'])) $qdata['sold_age'] = $criteria['sold_age'];
        if (isset($criteria['sort'])) $qdata['sort'] = $criteria['sort'];
        if (isset($criteria['startrow'])) $qdata['startrow'] = $criteria['startrow'];
        if (isset($criteria['state'])) $qdata['state'] = $criteria['state'];
        if (isset($criteria['street_name'])) $qdata['street_name'] = $criteria['street_name'];
        if (isset($criteria['style'])) $qdata['style'] = $criteria['style'];
        if (isset($criteria['subdivision'])) $qdata['subdivision'] = $criteria['subdivision'];
        if (isset($criteria['subdivision_text'])) $qdata['subdivision_text'] = $criteria['subdivision_text'];
        if (isset( $criteria['townhouse'] ))
            $qdata['townhouse'] = $this->convertBool($criteria['townhouse']);
        if (isset($criteria['township'])) $qdata['township'] = $criteria['township'];
        if (isset($criteria['type_of_neighborhood'])) $qdata['type_of_neighborhood'] = $criteria['type_of_neighborhood'];
        if (isset($criteria['virtual_tour'])) $qdata['virtual_tour'] = $criteria['virtual_tour'];
        if (isset($criteria['zipcode'])) $qdata['zip_code'] = $criteria['zipcode']; // legacy
        if (isset($criteria['zip_code'])) $qdata['zip_code'] = $criteria['zip_code'];

        if (isset( $criteria['one_story'] ))
            $qdata['one_story'] = $this->convertBool($criteria['one_story']);
        if (isset( $criteria['two_story'] ))
            $qdata['two_story'] = $this->convertBool($criteria['two_story']);
        if (isset( $criteria['three_plus_story'] ))
            $qdata['three_plus_story'] = $this->convertBool($criteria['three_plus_story']);

        for ($i = 1; $i <= 25; $i++) {
            $check = 'custom' . $i;
            if (isset( $criteria[ $check ] )) $qdata[ $check ] = $criteria[ $check ];
        }

        return $qdata;

    }

    /**
     * The API expects boolean values to be passed as 0 or 1.
     * shortcodes arguments from saved searches save boolean args in many non constant ways
     * Y/N, y/n, true/false, 0/1. This method converts these to API friendly 0/1
     * @param  string   to be converted to 1 or 0
     * @return int      API friendly 1 or 0
     */
    private function convertBool($to_bool)
    {
        $bool_true = array(true,'Y','y',1);

        return (in_array($to_bool, $bool_true)) ? 1 : 0 ;

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

        else if (is_string($value) && ctype_digit($value)) {
            return (integer) $value;
        }
        else if (is_string($value) && is_numeric($value) ) {
            return (float) $value;
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
        $this->url = plugin_dir_url($this->pluginFile) . 'public/';
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


    /**
     * Prepare the listings for display. Pass in the array returned from the api /listing method.
     * Format fields & add missing data items needed for displays
     * @param  array $data   the array as returned from the api /listing method
     * @param  string        the api key
     * @return array         returns the same array structure with additional info
     */
    public function augmentListingsData(&$data, $key)
    {

        if (is_array($data['responseData']['data']))
            $listingsData = &$data['responseData']['data']['listing'];

        $br_logo = $this->getBrLogo($key);
        if (array_key_exists('src', $br_logo))
            $br_logo_url =  $br_logo['src'];
        $show_logo = $data['responseData']['metadata']['display_rules']['results']['display_broker_reciprocity_logo'];
        $wnt_base_url = $this->getBaseUrl($key);

        // loop over listings
        foreach ($listingsData as &$listing) {

            if (is_numeric($listing['listing_price']))
                $listing['listing_price'] = '$' . number_format($listing['listing_price']);

            if ($show_logo && empty($listing['branding']['logo'])&& !empty($br_logo_url))
                $listing['branding']['logo'] = $br_logo_url;

            if (empty($listing['property_url']))
                $listing['property_url'] = $wnt_base_url . '/?action=listing_detail&property_id=' . $listing['property_id'];

            $listing['location'] = $listing['city'];

            if ( $listing['city'] != '' && $listing['state'] != '' )
                $listing['location'] .= ', ';

            $listing['location'] .= $listing['state'];
            $listing['location'] .= ' ' . $listing['zip_code'];

            $listing['bedsbaths'] = '';

            if (is_numeric($listing['total_bedrooms']) && ($listing['total_bedrooms'] > 0 ))
                $listing['bedsbaths'] .= $listing['total_bedrooms'] . 'bd';


            $listing['total_baths'] = 0;

            if (is_numeric($listing['total_partial_baths']))
                $listing['total_baths'] += $listing['total_partial_baths'];

            if (is_numeric($listing['total_full_baths']) )
                $listing['total_baths'] += $listing['total_full_baths'];

            if ( !empty($listing['bedsbaths']) && is_numeric($listing['total_baths']) && ( $listing['total_baths'] > 0 ))
                $listing['bedsbaths'] .= '/';

            if (is_numeric($listing['total_baths']) && ($listing['total_baths'] > 0))
                $listing['bedsbaths'] .= $listing['total_baths'] . 'ba';

            $listing['bedsbaths_full'] = '';

            if ( is_numeric( $listing['total_bedrooms'] ) )
                $listing['bedsbaths_full'] .= $listing['total_bedrooms'] . ' Bed Rooms';

            if ( is_numeric( $listing['total_bedrooms'] ) && is_numeric( $listing['total_baths'] ) )
                $listing['bedsbaths_full'] .= ' & ';

            if ( is_numeric( $listing['total_baths'] ) )
                $listing['bedsbaths_full'] .= $listing['total_baths'] . ' Bath Rooms';

            $listing['address'] = $listing['display_address'];

            if ($listing['city'] != '' && $listing['address'] != '')
                $listing['address'] .= ', ';

            $listing['address'] .= $listing['city'];

            if ($listing['state'] != '' && $listing['address'] != '')
                $listing['address'] .= ', ';

            $listing['address'] .= ' ' . $listing['state'];
            $listing['address'] .= ' ' . $listing['zip_code'];

        }
        return $data;

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
        $args = array_merge($data['wpMeta'], array(
            'toolbarClass' => $class . ' ',
            'maxresults'   => $this->getMaxResults($data['wpMeta']['key']), // total results on all pages
            'numrows'      => $data['wpMeta']['maxresults'], // total results per page
            'prevClass'    => ($data['wpMeta']['startrow']<=1) ? 'wolfnet_disabled' : '',
            'lastitem'     => $data['wpMeta']['startrow'] + $data['wpMeta']['maxresults'] - 1,
            'action'       => 'wolfnet_listings'
            ));

        if ($args['total_rows'] < $args['maxresults'] )  $args['maxresults'] = $args['total_rows'];

        $args['nextClass'] = ($args['lastitem']>=$args['maxresults']) ? 'wolfnet_disabled' : '';

        if ($args['lastitem'] > $args['total_rows']) {
            $args['lastitem'] = $args['total_rows'];
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

    public function getWpError($error)
    {
        return $this->views->errorView($error);
    }

    /**
     * get the api display setting for "Max Results". If it is not set use 250
     * @param  string $productKey
     * @return int
     */
    private function getMaxResults($productKey=null)
    {
        if($productKey == null) {
            $productKey = json_decode($this->getDefaultProductKey());
        }
        $data = $this->apin->sendRequest($productKey, '/settings');
        if (is_wp_error($data)) return $data;
        $maxResults = $data['responseData']['data']['market']['display_rules']['Max Results'];
        return (is_numeric($maxResults) && $maxResults <= 250 ) ? $maxResults : 250;
    }

    /**
     * Get the Broker Reciprocity Logo. returns array containing url, height, width $alt text
     * @param  string $productKey
     * @return array               keys: "SRC", "ALT", "HEIGHT", "WIDTH"
     */
    private function getBrLogo($productKey=null) {
        if($productKey == null) {
            $productKey = json_decode($this->getDefaultProductKey());
        }

        $data = $this->apin->sendRequest($productKey, '/settings');
        if (is_wp_error($data)) return $data;

        return $data['responseData']['data']['market']['broker_reciprocity_logo'];
    }

    public function getMaptracksEnabled($productKey=null)
    {
        if($productKey == null) {
            $productKey = json_decode($this->getDefaultProductKey());
        }

        $data = $this->apin->sendRequest($productKey, '/settings');
        if (is_wp_error($data)) return $data;

        return ($data['responseData']['data']['site']['maptracks_enabled'] == 'Y');

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

    public function getMapParameters($listingsData, $productKey=null)
    {
        if($productKey == null) {
            $productKey = $this->getDefaultProductKey();
        }

        $data  = $this->apin->sendRequest( $productKey, '/settings' );
        if (is_wp_error($data)) return $this->getWpError($data);

        $args['map_start_lat'] = $data['responseData']['data']['market']['maptracks']['map_start_lat'];
        $args['map_start_lng'] = $data['responseData']['data']['market']['maptracks']['map_start_lng'];
        $args['map_start_scale'] = $data['responseData']['data']['market']['maptracks']['map_start_scale'];
        $args['houseoverIcon'] = $GLOBALS['wolfnet']->url . 'img/houseover.png';
        $args['houseoverData'] = $this->getHouseoverData($listingsData,$data['responseData']['data']['resource']['searchResults']['allLayouts']['showBrokerReciprocityLogo']);

        return $args;

    }

    private function getHouseoverData($listingsData, $showBrokerImage)
    {

        $houseoverData = array();

        foreach ($listingsData as $listing) {
            $vars = array(
                'listing'         => $listing,
                'showBrokerImage' => $showBrokerImage,
            );

            $concatHouseover = $this->views->houseOver($vars);

            array_push($houseoverData, array(
                'lat'         => $listing['geo']['lat'],
                'lng'         => $listing['geo']['lng'],
                'content'     => $concatHouseover,
                'propertyId'  => $listing['property_id'],
                'propertyUrl' => $listing['property_url'],
                ));
        }

        return $houseoverData;

    }

    /**
     * Get the wolfnet search url qssociated eith given procuct key
     * @param  string $productKey
     * @return string             base URL of the Wolfnet search solution
     */
    // private function getWntSiteBaseUrl($productKey=null )
    private function getBaseUrl($productKey=null )
    {
        if($productKey == null)
            $productKey = $this->getDefaultProductKey();

        $data  = $this->apin->sendRequest( $productKey, '/settings' );
        if (is_wp_error($data)) return $data;

        return $data['responseData']['data']['site']['site_base_url'] ;
    }


    /**
     * check if key is valid
     * @param  string $key
     * @return bool         true?
     */
    public function productKeyIsValid($key=null)
    {
        $valid = false;

        if ($key != null) {
            $productKey = $key;
        }
        else {
            $productKey = json_decode($GLOBALS['wolfnet']->getDefaultProductKey());
        }

        $http = $this->apin->sendRequest( $productKey, '/status');


        if (!is_wp_error($http) && $http['responseStatusCode'] == '200') {
            $valid = true;
        }

        return $valid;

    }

    private function getPrices($productKey)
    {

        $data = $this->apin->sendRequest($productKey, '/search_criteria/property_feature');
        if (is_wp_error($data)) return $data->get_error_message();

        $prices = array();
        $prices['max_price'] = $data['responseData']['data']['max_price'];
        $prices['min_price'] = $data['responseData']['data']['min_price'];

        return $prices;

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
            'migrate' => array(
                $this->url . 'js/jquery.migrate.src.js',
                array('jquery'),
                ),
            'tooltipjs' => array(
                $this->url . 'js/jquery.tooltip.src.js',
                array('jquery', 'migrate'),
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
                array('jquery', 'migrate', 'mapquest-api'),
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
