<?php

/**
 * @title         Wolfnet_Plugin.php
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

class Wolfnet_Plugin
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
    public $customPostTypeSearch = 'wolfnet_search';

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

    public $url;

    public $smHttp = null;

    protected $pluginFile;

    private $cachingService;

    const CACHE_CRON_HOOK = 'wntCronCacheDaily';

    const SSL_WP_OPTION = 'wolfnet_sslEnabled';

    const VERIFYSSL_WP_OPTION = 'wolfnet_verifySSL';


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

        // Clear cache if url param exists.
        $cacheFlag = Wolfnet_Service_CachingService::CACHE_FLAG;
        $cacheParamExists = array_key_exists($cacheFlag, $_REQUEST);

        $this->ioc = new Wolfnet_Factory(array(
            'plugin' => &$this,
            'cacheRenew' => ($cacheParamExists) ? ($_REQUEST[$cacheFlag] == 'refresh') : false,
            'cacheClear' => ($cacheParamExists) ? ($_REQUEST[$cacheFlag] == 'clear') : false,
            'cacheReap' => ($cacheParamExists) ? ($_REQUEST[$cacheFlag] == 'reap') : false,
            'sslEnabled' => $this->getSslEnabled(),
            'verifySsl' => $this->getSslVerify(),
        ));

        $this->cachingService = $this->ioc->get('Wolfnet_Service_CachingService');
        $this->keyService = $this->ioc->get('Wolfnet_Service_ProductKeyService');

        $this->apin = $this->ioc->get('Wolfnet_Api_Client');

        $this->ajax = $this->ioc->get('Wolfnet_Ajax');

        $this->views = $this->ioc->get('Wolfnet_Views');

        // Modules
        $this->agentPages = $this->ioc->get('Wolfnet_Module_AgentPages');
        $this->featuredListings = $this->ioc->get('Wolfnet_Module_FeaturedListings');
        $this->listingGrid = $this->ioc->get('Wolfnet_Module_ListingGrid');
        $this->propertyList = $this->ioc->get('Wolfnet_Module_PropertyList');
        $this->quickSearch = $this->ioc->get('Wolfnet_Module_QuickSearch');
        $this->searchManager = $this->ioc->get('Wolfnet_Module_SearchManager');

        if(is_admin()) {
            $this->admin = $this->ioc->get('Wolfnet_Admin');
        }

        // Register actions.
        $this->addAction(array(
            array('init',                  'init'),
            array('wp_enqueue_scripts',    'scripts'),
            array('wp_enqueue_scripts',    'styles'),
            array('wp_footer',             'footer'),
            array('template_redirect',     'templateRedirect'),
            array('wp_enqueue_scripts',    'publicStyles',      1000),
            array(self::CACHE_CRON_HOOK, array($this->cachingService, 'clearExpired')),
            ));

        if($this->keyService->getDefault()) {
            $this->addAction(array(
                array('widgets_init',      'widgetInit'),
            ));
        }

        // Register filters.
        $this->addFilter(array(
            array('do_parse_request',     'doParseRequest'),
            ));

        // Register Cron Events
        // NOTE: We do this here instead of on activation because the activation does not fire for updates.
        $this->registerCronEvents();

        register_activation_hook($this->pluginFile, array($this, 'wolfnetActivation'));
        register_deactivation_hook($this->pluginFile, array($this, 'wolfnetDeactivation'));

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
    public function htmlEntityDecodeNumeric($string, $quote_style = ENT_COMPAT, $charset = 'utf-8')
    {
        $hexCallback = array(&$this, 'chrUtf8HexCallback');
        $nonHexCallback = array(&$this, 'chrUtf8NonhexCallback');

        $string = html_entity_decode($string, $quote_style, $charset);
        $string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', $hexCallback, $string);
        $string = preg_replace_callback('~&#([0-9]+);~i', $nonHexCallback, $string);

        return $string;

    }


    public function addAction($action, $callable = null, $priority = null)
    {
        if (is_array($action)) {
            foreach ($action as $act) {
                if (count($act) == 2) {
                    $this->addAction($act[0], $act[1]);
                } else {
                    $this->addAction($act[0], $act[1], $act[2]);
                }
            }
        } else {
            if (is_callable($callable) && is_array($callable)) {
                add_action($action, $callable, $priority);
            } elseif (is_string($callable) && method_exists($this, $callable)) {
                do_action($this->preHookPrefix . $callable);
                add_action($action, array(&$this, $callable), $priority);
                do_action($this->postHookPrefix . $callable);
            }
        }

        return $this;

    }


    /**
     * Callback helper
     */
    public function chrUtf8HexCallback($matches)
    {
        return $this->chr_utf8(hexdec($matches[1]));
    }


    public function chrUtf8NonhexCallback($matches)
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
        $this->ajax->registerAjaxActions();

        // Register Scripts
        $this->registerScripts();

        // Register CSS
        $this->registerStyles();

    }


    public function wolfnetActivation()
    {
        /*
         * Note - functionality here has been moved to AFTER the activation
         * redirect. In the unforunate event that the activation code fails,
         * we want the activation to at least have succeeded and not thrown 
         * a fatal error. Problems related to SSL and API connectivity should
         * not destroy the activation process.
         */
        if(get_option(self::VERIFYSSL_WP_OPTION) === false) {
            // See Wolfnet_Admin->adminInit for this usage.
            add_option('wolfnet_activatedPlugin181', '1.8.1');
        }
    }


    public function wolfnetDeactivation()
    {
        $this->removeCronEvents();
        $this->cachingService->clearAll();
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
            'wolfnet-scrolling-items',
            'wolfnet-quick-search',
            'wolfnet-listing-grid',
            'wolfnet-toolbar',
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
        if (strlen($this->views->getPublicCss())) {
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

        register_widget('Wolfnet_Widget_FeaturedListingsWidget');

        register_widget('Wolfnet_Widget_ListingGridWidget');

        register_widget('Wolfnet_Widget_PropertyListWidget');

        register_widget('Wolfnet_Widget_QuickSearchWidget');

        register_widget('Wolfnet_Widget_AgentPagesWidget');

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
            foreach ($_REQUEST['keyList'] as $key) {
                try {
                    $disclaimer = $this->apin->sendRequest($key, '/core/disclaimer', 'GET', array(
                        'type'=>'search_results', 'format'=>'html'
                        ));
                    echo $disclaimer['responseData']['data'];
                } catch (Wolfnet_Exception $e) {
                    echo $this->displayException($e);
                }

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
                    $this->ajax->remoteContent();
                    break;

                case 'wolfnet_content_header':
                    $this->ajax->remoteContentHeader();
                    break;

                case 'wolfnet_content_footer':
                    $this->ajax->remoteContentFooter();
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
     * This method returns an array of integer values to be used as possible pagination item counts.
     * @return array An array of integers.
     */
    public function getItemsPerPage()
    {
        return array(5,10,15,20,25,30,35,40,45,50);
    }


    public function getMarketName($apiKey)
    {
        $data = $this->apin->sendRequest($apiKey, '/settings');

        return $data['responseData']['data']['market']['datasource_name'];

    }


    public function buildUrl($url = '', array $params = array())
    {
        if (!strstr($url, '?')) {
            $url .= '?';
        }

        $restrictedParams = array('criteria','toolbarTop','toolbarBottom','listingsHtml','prevLink',
            'nextLink','prevClass','nextClass','toolbarClass','instance_id','siteUrl','class','_','key');

        $restrictedSuffix = array('_wpid', '_wpname', '_wps', '_wpc');

        foreach ($params as $key => $value) {
            $valid = true;
            $valid = (array_search($key, $restrictedParams) !== false) ? false : $valid;
            $valid = (!is_string($value) && !is_numeric($value) && !is_bool($value)) ? false : $valid;

            foreach ($restrictedSuffix as $suffix) {
                $valid = (substr($key, strlen($suffix)*-1) == $suffix) ? false : $valid;
            }

            if ($valid) {
                $url .= '&' . $key . '=' . urlencode($this->htmlEntityDecodeNumeric($value));
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

        foreach ($customFields as $field => $value) {
            if (substr($field, 0, 1) != '_') {
                echo "<div><label>{$field}:</label> {$value[0]}</div>";
            }
        }

    }




    /**
     * Prepare an array usable by the Wolfnet API for a /listing query
     * @param  array  $criteria mixed array containing query api parameter. this can also contain
     * other items not used by the API, these will be stripped out.
     * @return array            Return array containing only query parameters to be passed to the api
     */
    public function prepareListingQuery(array $criteria)
    {

        // Array of aliased criteria
        $criteriaAlias = array(
            'priceReduced' => 'pricereduced',
            'exactcity' => 'exact_city',
            'maxprice' => 'max_price',
            'minprice' => 'min_price',
            'zipcode' => 'zip_code',
            'ownertype' => 'owner_type',
            'maxresults' => 'maxrows',
        );

        // Translate aliases to their canonical version and then removed the alias from the array
        foreach ($criteriaAlias as $alias => $crit) {
            if (array_key_exists($alias, $criteria)) {
                if (!array_key_exists($crit, $criteria)) {
                    $criteria[$crit] = $criteria[$alias];
                }

                unset($criteria[$alias]);

            }

        }

        // Array of boolean criteria
        $boolCriteria = array(
            'agent_only',
            'office_only',
            'agent_office_only',
            'business_with_real_estate',
            'commercial',
            'commercial_lease',
            'condo',
            'condo_townhouse',
            'duplex',
            'exact_property_id',
            'farm_hobby',
            'foreclosure',
            'gated_community',
            'half_duplex',
            'has_basement',
            'has_family_room',
            'has_fireplace',
            'has_garage',
            'has_golf',
            'has_horse_property',
            'has_mountain_view',
            'has_pool',
            'has_waterfront',
            'waterfront',
            'has_waterview',
            'has_lakefront',
            'industrial',
            'investment',
            'loft',
            'lots_acreage',
            'mixed_use',
            'mobile_home',
            'model',
            'multi_family',
            'new_and_updated',
            'new_construction',
            'newlistings',
            'on_golf_course',
            'open_house',
            'pricereduced',
            'property_view',
            'redraw_map_bounds',
            'residential_lease',
            'residential_lease_detached',
            'retail_store',
            'shortsale',
            'similar_listings',
            'single_family',
            'single_family_detached',
            'sold',
            'townhouse',
            'one_story',
            'two_story',
            'three_plus_story',
        );

        // Translate pseudo boolean values to true boolean values
        foreach ($boolCriteria as $bool) {
            if (array_key_exists($bool, $criteria)) {
                $criteria[$bool] = $this->convertBool($criteria[$bool]);
            }

        }

        if (array_key_exists('exact_city', $criteria)) {
            $hasCity = array_key_exists('city', $criteria);

            // If multiple cities were selected we must set "exact_city" to false
            if ($hasCity && count(explode(',', trim($criteria['city']))) > 1) {
                $criteria['exact_city'] = 0;
            }

            if ($criteria['exact_city'] === null || trim($criteria['exact_city']) === '') {
                unset($criteria['exact_city']);
            }

        }

        // Translate legacy "primary search type" criteria to API criteria
        if (array_key_exists('primarysearchtype', $criteria)) {
            switch ($criteria['primarysearchtype']) {

                case 'sold':
                    $criteria['sold'] = 1;
                    break;

                case 'open':
                    $criteria['open_house'] = 1;
                    break;

                case 'foreclosure':
                    $criteria['foreclosure'] = 1;
                    break;

            }

            unset($criteria['primarysearchtype']);

        }

        // Translate legacy "owner type" criteria to API criteria
        // agent_only, office_only, and agent_office_only are legacy cases.
        if (array_key_exists('owner_type', $criteria)) {
            switch ($criteria['owner_type']) {

                case 'agent':
                    $criteria['agent_only'] = 1;
                    break;
                case 'agent_only':
                    $criteria['agent_only'] = 1;
                    break;

                case 'broker':
                    $criteria['office_only'] = 1;
                    break;
                case 'office_only':
                    $criteria['office_only'] = 1;
                    break;

                case 'agent_broker':
                    $criteria['agent_office_only'] = 1;
                    break;
                case 'agent_office_only':
                    $criteria['agent_office_only'] = 1;
                    break;

            }

            unset($criteria['owner_type']);

        }

        // Plugin specific criteria
        $pluginCriteria = array(
            'owner_type',
            'ownertypes',
            'paginated',
            'criteria',
            'mode',
            'savedsearch',
            'savedsearches',
            'wntSavedSearches',
            'key',
            'keyid',
            'title',
            'class',
            'maptype',
            'maptypes',
            'mapEnabled',
            'sortoptions',
            'maxresults',
            'autoplay',
            'direction',
            'speed',
            'prices',
        );

        $pluginCriteriaPattern = array(
            '/.*_wpid$/',
            '/.*_wpname$/',
            '/.*_wps$/',
            '/.*_wpc$/',
        );

        $criteriaKeys = array_keys($criteria);

        foreach ($pluginCriteriaPattern as $pattern) {
            $pluginCriteria = array_merge($pluginCriteria, preg_grep($pattern, $criteriaKeys));
        }

        // Remove Plugin specific values
        foreach ($pluginCriteria as $crit) {
            if (array_key_exists($crit, $criteria)) {
                unset($criteria[$crit]);
            }

        }

        // Remove non-scalar values
        foreach ($criteria as $crit => $value) {
            if (!is_scalar($value)) {
                unset($criteria[$crit]);
            }
        }

        return $criteria;

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
        $bool_true = array(true,'Y','y',1,'true','yes');

        return (in_array($to_bool, $bool_true)) ? 1 : 0 ;

    }


    public function getOptions(array $defaultOptions, $instance = null)
    {
        if (is_array($instance)) {
            $options = array_merge($defaultOptions, $instance);
        } else {
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
        } elseif (is_string($value) && ($value==='true' || $value==='false')) {
            return ($value==='true') ? true : false;
        } elseif (is_string($value) && ctype_digit($value)) {
            return (integer) $value;
        } elseif (is_string($value) && is_numeric($value)) {
            return (float) $value;
        }

        return $value;

    }


    public function soldListingsEnabled()
    {
        try {
            $data = $this->apin->sendRequest(
                $this->keyService->getDefault(),
                '/settings',
                'GET'
            );
        } catch(Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        $marketEnabled = $data['responseData']['data']['market']['has_sold_property'];
        $siteEnabled = $data['responseData']['data']['site']['sold_property_enabled'];

        return ($marketEnabled && $siteEnabled);
    }


    public function getOffices()
    {
        try {
            $data = $this->apin->sendRequest(
                $this->keyService->getDefault(), 
                '/office', 
                'GET'
            );
        } catch (Wolfnet_Exception $e) {
            return $this->displayException($e);
        }

        return $data;
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


    protected function addFilter($filter, $callable = null)
    {
        if (is_array($filter)) {
            foreach ($filter as $flt) {
                $this->addFilter($flt[0], $flt[1]);
            }
        } else {
            if (is_callable($callable)) {
                add_filter($filter, $callable);
            } elseif (is_string($callable) && method_exists($this, $callable)) {
                do_action($this->preHookPrefix . $callable);
                add_filter($filter, array(&$this, $callable));
                do_action($this->postHookPrefix . $callable);
            }
        }

        return $this;

    }


    /* PRIVATE METHODS ************************************************************************** */
    /*  ____       _            _         __  __      _   _               _                       */
    /* |  _ \ _ __(_)_   ____ _| |_ ___  |  \/  | ___| |_| |__   ___   __| |___                   */
    /* | |_) | '__| \ \ / / _` | __/ _ \ | |\/| |/ _ \ __| '_ \ / _ \ / _` / __|                  */
    /* |  __/| |  | |\ V / (_| | ||  __/ | |  | |  __/ |_| | | | (_) | (_| \__ \                  */
    /* |_|   |_|  |_| \_/ \__,_|\__\___| |_|  |_|\___|\__|_| |_|\___/ \__,_|___/                  */
    /*                                                                                            */
    /* ****************************************************************************************** */


    public function isJsonEncoded($str)
    {
        if (is_array(json_decode($str)) || is_object(json_decode($str))) {
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

        if (is_array($data['responseData']['data'])) {
            $listingsData = &$data['responseData']['data']['listing'];
        }

        $br_logo = $this->getBrLogo($key);

        if (array_key_exists('src', $br_logo)) {
            $br_logo_url =  $br_logo['src'];
        }

        $show_logo = $data['responseData']['metadata']['display_rules']['results']['display_broker_reciprocity_logo'];
        $wnt_base_url = $this->getBaseUrl($key);

        // loop over listings
        foreach ($listingsData as &$listing) {
            if (is_numeric($listing['listing_price'])) {
                $listing['listing_price'] = '$' . number_format($listing['listing_price']);
            }

            if ($show_logo && empty($listing['branding']['logo'])&& !empty($br_logo_url)) {
                $listing['branding']['logo'] = $br_logo_url;
            }

            if (empty($listing['property_url'])) {
                $listing['property_url'] = $wnt_base_url . '/?action=listing_detail&property_id='
                    . $listing['property_id'];
            }

            $listing['location'] = $listing['city'];

            if ($listing['city'] != '' && $listing['state'] != '') {
                $listing['location'] .= ', ';
            }

            $listing['location'] .= $listing['state'];
            $listing['location'] .= ' ' . $listing['zip_code'];

            $listing['bedsbaths'] = '';

            if (is_numeric($listing['total_bedrooms']) && ($listing['total_bedrooms'] > 0 )) {
                $listing['bedsbaths'] .= $listing['total_bedrooms'] . 'bd';
            }

            $listing['total_baths'] = 0;

            if (is_numeric($listing['total_partial_baths'])) {
                $listing['total_baths'] += $listing['total_partial_baths'];
            }

            if (is_numeric($listing['total_full_baths'])) {
                $listing['total_baths'] += $listing['total_full_baths'];
            }

            if (!empty($listing['bedsbaths']) && is_numeric($listing['total_baths']) && ($listing['total_baths'] > 0)) {
                $listing['bedsbaths'] .= '/';
            }

            if (is_numeric($listing['total_baths']) && ($listing['total_baths'] > 0)) {
                $listing['bedsbaths'] .= $listing['total_baths'] . 'ba';
            }

            $listing['bedsbaths_full'] = '';

            if (is_numeric($listing['total_bedrooms'])) {
                $listing['bedsbaths_full'] .= $listing['total_bedrooms'] . ' Bedrooms';
            }

            if (is_numeric($listing['total_bedrooms']) && is_numeric($listing['total_baths'])) {
                $listing['bedsbaths_full'] .= ' & ';
            }

            if (is_numeric($listing['total_baths'])) {
                $listing['bedsbaths_full'] .= $listing['total_baths'] . ' Bathrooms';
            }

            $listing['address'] = $listing['display_address'];

            if ($listing['city'] != '' && $listing['address'] != '') {
                $listing['address'] .= ', ';
            }

            $listing['address'] .= $listing['city'];

            if ($listing['state'] != '' && $listing['address'] != '') {
                $listing['address'] .= ', ';
            }

            $listing['address'] .= ' ' . $listing['state'];
            $listing['address'] .= ' ' . $listing['zip_code'];

        }

        return $data;

    }


    public function getMap($listingsData, $productKey = null)
    {
        return $this->views->mapView($listingsData, $productKey);
    }


    public function getHideListingTools($hideId, $showId, $collapseId, $instance_id)
    {
        return $this->views->hideListingsToolsView($hideId, $showId, $collapseId, $instance_id);
    }


    public function getToolbar($data, $class)
    {
        $args = array_merge($data['wpMeta'], array(
            'toolbarClass' => $class . ' ',
            'maxresults'   => $this->getMaxResults($data['wpMeta']['key']), // total results on all pages
            'numrows'      => $data['wpMeta']['maxresults'], // total results per page
            'prevClass'    => ($data['wpMeta']['startrow']<=1) ? 'wolfnet_disabled' : '',
            'lastitem'     => $data['wpMeta']['startrow'] + $data['wpMeta']['maxresults'] - 1,
            'action'       => 'wolfnet_listings'
            ));

        if ($args['total_rows'] < $args['maxresults']) {
            $args['maxresults'] = $args['total_rows'];
        }

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


    public function displayException(Wolfnet_Exception $exception)
    {
        return $this->views->exceptionView($exception);
    }


    /**
     * get the api display setting for "Max Results". If it is not set use 250
     * @param  string $productKey
     * @return int
     */
    private function getMaxResults($productKey = null)
    {
        if ($productKey == null) {
            $productKey = json_decode($this->keyService->getDefault());
        }

        $data = $this->apin->sendRequest($productKey, '/settings');

        $maxResults = $data['responseData']['data']['market']['display_rules']['Max Results'];

        return (is_numeric($maxResults) && $maxResults <= 250 ) ? $maxResults : 250;

    }


    /**
     * Get the Broker Reciprocity Logo. returns array containing url, height, width $alt text
     * @param  string $productKey
     * @return array               keys: "SRC", "ALT", "HEIGHT", "WIDTH"
     */
    private function getBrLogo($productKey = null)
    {

        if ($productKey == null) {
            $productKey = json_decode($this->keyService->getDefault());
        }

        $data = $this->apin->sendRequest($productKey, '/settings');

        return $data['responseData']['data']['market']['broker_reciprocity_logo'];

    }


    public function getMaptracksEnabled($productKey = null)
    {

        if ($productKey == null) {
            $productKey = json_decode($this->keyService->getDefault());
        }

        $data = $this->apin->sendRequest($productKey, '/settings');

        if (is_wp_error($data)) {
            return $data;
        }

        return ($data['responseData']['data']['site']['maptracks_enabled'] == 'Y');

    }


    public function getWpHeader()
    {
        $wntClass = 'wnt-wrapper';

        ob_start();

        get_header();
        $header = ob_get_clean();
        $htmlTags = array();
        $hasHtmlTags = preg_match_all("(<html([^\>]*)>)", $header, $htmlTags, PREG_PATTERN_ORDER);

        if ($hasHtmlTags > 0) {
            foreach ($htmlTags[0] as $tag) {
                $classRegex = "/(?<=class\=[\"|\'])([^\"|\']*)/";
                $currentClassArray=array();
                $hasClassAttr = preg_match($classRegex, $tag, $currentClassArray);

                if ($hasClassAttr > 0) {
                    $currentClasses = ($hasClassAttr > 0) ? $currentClassArray[0] : "";
                    $newTag = preg_replace($classRegex, $currentClasses . ' ' . $wntClass, $tag);
                } else {
                    $newTag = str_replace('>', ' class="' . $wntClass . '">', $tag);
                }

                $header = str_replace($tag, $newTag, $header);

            }
        }

        return $header;

    }


    public function getWpFooter()
    {
        ob_start();
        get_footer();
        $footer = ob_get_clean();

        return $footer;

    }


    public function getOwnerTypes()
    {
        return array(
            array('value'=>'agent_broker', 'label'=>'Agent Then Broker'),
            array('value'=>'agent', 'label'=>'Agent Only'),
            array('value'=>'broker', 'label'=>'Broker Only')
        );

    }


    public function getMapTypes()
    {
        return array(
            array('value'=>'disabled', 'label'=>'No'),
            array('value'=>'above',    'label'=>'Above Listings'),
            array('value'=>'below',    'label'=>'Below Listings'),
            array('value'=>'map_only', 'label'=>'Map Only')
        );

    }


    public function getMapParameters($listingsData, $productKey = null)
    {
        if ($productKey == null) {
            $productKey = $this->keyService->getDefault();
        }

        $data  = $this->apin->sendRequest($productKey, '/settings');

        if (is_wp_error($data)) {
            return $this->getWpError($data);
        }


        $args['mapParams'] = array(
    		'mapProvider'  => 'mapquest',
    		'centerLat'    => $data['responseData']['data']['market']['maptracks']['map_start_lat'],
			'centerLng'    => $data['responseData']['data']['market']['maptracks']['map_start_lng'],
			'zoomLevel'    => $data['responseData']['data']['market']['maptracks']['map_start_scale'],
			'houseoverIcon'=> $GLOBALS['wolfnet']->url . 'img/houseover.png',
			'mapId'        => uniqid('wntMapTrack'),
			'hideMapId'    => uniqid('hideMap'),
			'showMapId'    => uniqid('showMap'),
		);

        $args['houseoverData'] = $this->getHouseoverData(
            $listingsData,
            $data['responseData']['data']['resource']['searchResults']['allLayouts']['showBrokerReciprocityLogo']
        );

        return $args;

    }


    private function getHouseoverData($listingsData, $showBrokerImage)
    {

        $houseoverData = array();

        foreach ($listingsData as $listing) {
            $vars = array(
                'listing' => $listing,
                'showBrokerImage' => $showBrokerImage,
            );

            $concatHouseover = $this->views->houseOver($vars);

            array_push($houseoverData, array(
                'lat' => $listing['geo']['lat'],
                'lng' => $listing['geo']['lng'],
                'content' => $concatHouseover,
                'propertyId' => $listing['property_id'],
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
    public function getBaseUrl($productKey = null)
    {
        if ($productKey == null) {
            $productKey = $this->keyService->getDefault();
        }

        $data  = $this->apin->sendRequest($productKey, '/settings');

        if (is_wp_error($data)) {
            return $data;
        }

        return $data['responseData']['data']['site']['site_base_url'];

    }


    public function getPrices($productKey)
    {

        $data = $this->apin->sendRequest($productKey, '/search_criteria/property_feature');

        if (is_wp_error($data)) {
            return $data->get_error_message();
        }

        $prices = array();
        $prices['max_price'] = $data['responseData']['data']['max_price'];
        $prices['min_price'] = $data['responseData']['data']['min_price'];

        return $prices;

    }


    public function getBeds()
    {
        $values = array(1,2,3,4,5,6,7);
        $data   = array();

        foreach ($values as $value) {
            $data[] = array('value'=>$value, 'label'=>$value);
        }

        return $data;

    }


    public function getBaths()
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
            'show_ui'   => true,
            'show_in_nav_menus' => false,
            'show_in_menu' => false,
            'show_in_admin_bar' => false,
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


    /*
     * Shortcode helper functions for registering in registerShortCodes.
     */
    public function scAgentPages($attrs) 
    {
        return $this->agentPages->scAgentPages($attrs);
    }

    public function scFeaturedListings($attrs, $content = '')
    {
        return $this->featuredListings->scFeaturedListings($attrs, $content);
    }

    public function scListingGrid($attrs)
    {
        return $this->listingGrid->scListingGrid($attrs);
    }

    public function scPropertyList($attrs = array())
    {
        return $this->propertyList->scPropertyList($attrs);
    }

    public function scQuickSearch($attrs, $content = '') 
    {
        return $this->quickSearch->scQuickSearch($attrs, $content);
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
            'wnt_agent'                 => 'scAgentPages',
            'WolfNetAgentPages'         => 'scAgentPages',
            'wolfnetagentpages'         => 'scAgentPages',
            'WOLFNETAGENTPAGES'         => 'scAgentPages',
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
                array('wolfnet'),
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
            'wolfnet-shortcode-builder' => array(
                $this->url . 'js/jquery.wolfnetShortcodeBuilder.src.js',
                array('jquery-ui-widget', 'jquery-effects-core', 'wolfnet-admin'),
            ),
            'mapquest-api' => array(
                '//www.mapquestapi.com/sdk/js/v7.0.s/mqa.toolkit.js?key=Gmjtd%7Clu6znua2n9%2C7l%3Do5-la70q',
                array(),
                $this->version,
                true,
            ),
            'wolfnet-maptracks' => array(
                $this->url . 'js/jquery.wolfnetMaptracks.src.js',
                array('jquery', 'mapquest-api'),
                $this->version,
                true,
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


    public function decodeCriteria(array &$criteria)
    {

        // Decode req parameters vals so they can be cleanly encoded before api req
        foreach ($criteria as &$value) {
            $value = html_entity_decode($value);
        }

    }


    public function registerCronEvents()
    {
        // Schedule Cache Clearing event
        if (!wp_next_scheduled(self::CACHE_CRON_HOOK)) {
            wp_schedule_event(time(), 'daily', self::CACHE_CRON_HOOK);
        }

    }


    public function removeCronEvents()
    {
        // Remove Cache Clearing event
        wp_clear_scheduled_hook(self::CACHE_CRON_HOOK);

    }


    public function getSslEnabled()
    {
        // Attempt to read value from the options, but default to Client default
        return get_option(self::SSL_WP_OPTION, Wolfnet_Api_Client::DEFAULT_SSL);

    }


    public function getSslVerify()
    {
        return get_option(self::VERIFYSSL_WP_OPTION, Wolfnet_Api_Client::DEFAULT_VERIFYSSL);
    }


    public function setSslVerifyOption($key)
    {
        // Hit an API endpoint so we can verify SSL.
        try {
            $data = $this->apin->sendRequest($key, '/settings');
        } catch(Wolfnet_Api_ApiException $e) {
            // And exception at this point is PROBABLY due to SSL verification.
            // Set the verify SSL option to false if so.
            if(strpos($e->getDetails(), 'SSL certificate problem') >= 0) {
                $this->apin->setVerifySSL(0);
                update_option(self::VERIFYSSL_WP_OPTION, 0);
                return false;
            }
        }
        // If we made it to this point we can set SSL verification to true.
        if(get_option(self::VERIFYSSL_WP_OPTION) === false) {
            update_option(self::VERIFYSSL_WP_OPTION, 1);
            return true;
        } else {
            return false;
        }
    }


}
