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
     * This property is used to set the option group for the Appearance page. It creates a namespaced
     * collection of variables which are used in saving page settings.
     * @var string
     */
    public $WidgetThemeOptionGroup = 'wolfnetStyle';

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
     * This property is used to identify which widget theme to use.
     * @var string
     */
    public $widgetThemeOptionKey = 'wolfnet_widgetTheme';

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
    public $preHookPrefix = 'wolfnet_pre_';

    /**
     * This property is used to prefix custom hooks which are defined in the plugin. Specifically
     * this prefix is used for hooks which are executed after a certain portion of code.
     * @var string
     */
    public $postHookPrefix = 'wolfnet_post_';

    /**
     * This Property is use as a prefix to request scope variables to avoid conflicts with get,
     * post, and other global variables used by WordPress and other plugins.
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

        $this->api = $this->ioc->get('Wolfnet_Api_Client');
        $this->ajax = $this->ioc->get('Wolfnet_Ajax');
        $this->data = $this->ioc->get('Wolfnet_Data');
        $this->listings = $this->ioc->get('Wolfnet_Listings');
        $this->template = $this->ioc->get('Wolfnet_Template');
        $this->views = $this->ioc->get('Wolfnet_Views');

        // Modules
        $this->agentPages = $this->ioc->get('Wolfnet_Module_AgentPages');
        $this->featuredListings = $this->ioc->get('Wolfnet_Module_FeaturedListings');
        $this->listingGrid = $this->ioc->get('Wolfnet_Module_ListingGrid');
        $this->propertyList = $this->ioc->get('Wolfnet_Module_PropertyList');
        $this->quickSearch = $this->ioc->get('Wolfnet_Module_QuickSearch');
        $this->smartSearch = $this->ioc->get('Wolfnet_Module_SmartSearch');
        $this->searchManager = $this->ioc->get('Wolfnet_Module_SearchManager');
        $this->widgetTheme = $this->ioc->get('Wolfnet_Module_WidgetTheme');

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

        try {
			$productKey = $this->keyService->getDefault();
			$response = $this->api->sendRequest($productKey, '/status', 'GET');
			$successfulApiConnection = true;
		} catch (Exception $e) {
			$successfulApiConnection = false;
		}

        if ($successfulApiConnection) {
            $this->addAction(array(
                array('widgets_init','widgetInit'),
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


    /* Hooks ************************************************************************************ */
    /* |_|  _   _  |   _                                                                          */
    /* | | (_) (_) |< _>                                                                          */
    /*                                                                                            */
    /* ****************************************************************************************** */

    // Hook functions for addAction
    public function scripts()
    {
        return $this->template->scripts();
    }

    public function styles()
    {
        return $this->template->styles();
    }

    public function footer()
    {
        return $this->template->footer();
    }

    public function publicStyles()
    {
        return $this->template->publicStyles();
    }

    public function templateRedirect()
    {
        return $this->template->templateRedirect();
    }

    public function createUUID($withDashes = false)
    {
        // Source: http://www.php.net/manual/en/function.uniqid.php#94959

        $idSet = '%04x';
        $idFormat = $idSet . $idSet;

        for ($i=0; $i<4; $i++) {
            $idFormat .= ($withDashes ? '-' : '') . $idSet;
        }

        $idFormat .= $idSet . $idSet;

        return sprintf(

            $idFormat,

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)

        );

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
        $this->ajax->registerAjaxActions();

        // Register Scripts
        $this->template->registerScripts();

        // Register CSS
        $this->template->registerStyles();

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


    public function chrUtf8HexCallback($matches)
    {
        return $this->chr_utf8(hexdec($matches[1]));
    }


    public function chrUtf8NonhexCallback($matches)
    {
        return $this->chr_utf8($matches[1]);
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


    public function getWpError($error)
    {
        return $this->views->errorView($error);
    }


    public function displayException(Wolfnet_Exception $exception)
    {
        return $this->views->exceptionView($exception);
    }


    public function sbMcePlugin(array $plugins)
    {
        $plugins['wolfnetShortcodeBuilder'] = $this->url . 'js/tinymce.wolfnetShortcodeBuilder.min.js';

        return $plugins;

    }


    public function sbButton(array $buttons)
    {

        do_action($this->preHookPrefix . 'addShortcodeBuilderButton'); // Legacy hook

        array_push($buttons, '|', 'wolfnetShortcodeBuilderButton');

        do_action($this->postHookPrefix . 'addShortcodeBuilderButton'); // Legacy hook

        return $buttons;

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

    /*
     * Shortcode helper functions for registering in registerShortCodes above.
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
        // Route to smart search module, if user opted for smart functionality
        $isSmart = isset($attrs['smartsearch']) ? $attrs['smartsearch'] : false;

        if ($isSmart === 'true') {
            return $this->smartSearch->scSmartSearch($attrs, $content);
        } else {
            return $this->quickSearch->scQuickSearch($attrs, $content);
        }
    }

}
