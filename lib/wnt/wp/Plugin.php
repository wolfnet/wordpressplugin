<?php

/**
 * @title         Plugin.php
 * @contributors  AJ Michels (http://aj.michels@wolfnet.com)
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
 */
class WNT_WP_Plugin
{


    /* PROPERTIES ******************************************************************************* */

    /**
     * This property holds a static reference to the only instance of the wolfnet class.
     * @var wolfnet
     */
    private static $instance;


    private $config;


    /* CONSTRUCT PLUGIN ************************************************************************* */

    /**
     * This method is the primary constructor for the plugin.
     */
    private function __construct(WNT_WP_Config $config)
    {
        $this->config = $config;

        $this->fac = WNT_WP_Factory::getInstance(array(
            'PluginUrl' => $this->config->pluginUrl,
            'ApiUrl' => 'http://services.mlsfinder.com/v1'
            ));

        if (!session_id()) { session_start(); }

        add_action('init', array(&$this, 'registerCustomPostTypes'));
        add_action('init', array(&$this, 'registerRewriteRules'));
        //add_action('widgets_init', array(&$this, 'registerWidgets'));
        add_action('wp_enqueue_scripts', array(&$this, 'registerResources'));
        add_action('admin_init', array(&$this, 'registerOptions'));
        add_action('admin_init', array(&$this, 'registerShortcodeBuilderButton'));
        add_action('admin_menu', array(&$this, 'adminMenu'));
        add_action('admin_enqueue_scripts', array(&$this, 'registerAdminResources'));
        add_action('wp_footer', array(&$this, 'footerDisclaimer'));

    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This static method is the only means by which to instantiate the plugin class. Calling the
     * method will either return an existing instance or create and return an instance. (see
     * Singleton pattern)
     * @return wolfnet  A static instance of the wolfnet class.
     */
    public static function run(WNT_WP_Config $config=null)
    {
        return (!isset(self::$instance)) ? self::$instance = new self($config) : self::$instance;

    }


    public function registerCustomPostTypes()
    {
        if (!post_type_exists('wolfnet_search')) {
            register_post_type('wolfnet_search', array(
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
                'register_meta_box_cb' => array(&$this, 'searchPostMetabox')
            ));
        }

    }


    public function searchPostMetabox()
    {
        add_meta_box(
            'search_criteria',
            'Search Criteria',
            array(&$this, 'searchPostMetaboxOutput'),
            'wolfnet_search',
            'advanced',
            'core'
            );

    }


    public function searchPostMetaboxOutput($post)
    {
        $customFields = get_post_custom($post->ID);

        foreach ($customFields as $field=>$value) {
            if (substr($field, 0, 1) != '_') {
                echo "<div><label>{$field}:</label> {$value[0]}</div>";
            }
        }

    }


    public function registerRewriteRules()
    {
        $rule    = '^wolfnet/admin/shortcodebuilder/optionform/([^/]*)?';
        $rewrite = 'index.php?pagename=wolfnet-admin-shortcodebuilder-optionform&formpage=$matches[1]';
        add_rewrite_rule( $rule, $rewrite, 'top' );
        add_rewrite_tag( '%formpage%', '([^&]+)' );

        $rule    = '^wolfnet/admin/searchmanager/save?';
        $rewrite = 'index.php?pagename=wolfnet-admin-searchmanager-save';
        add_rewrite_rule( $rule, $rewrite, 'top' );

        $rule    = '^wolfnet/content?';
        $rewrite = 'index.php?pagename=wolfnet-content';
        add_rewrite_rule( $rule, $rewrite, 'top' );

        $rule    = '^wolfnet/content/header?';
        $rewrite = 'index.php?pagename=wolfnet-content-header';
        add_rewrite_rule( $rule, $rewrite, 'top' );

        $rule    = '^wolfnet/content/footer?';
        $rewrite = 'index.php?pagename=wolfnet-content-footer';
        add_rewrite_rule( $rule, $rewrite, 'top' );

    }


    public function registerWidgets()
    {
        register_widget('WNT_WP_Widget_FeaturedListingsWidget');
        register_widget('WNT_WP_Widget_ListingGridWidget');
        register_widget('WNT_WP_Widget_PropertyListWidget');
        register_widget('WNT_WP_Widget_QuickSearchWidget');

    }


    public function registerShortcodes()
    {

    }


    public function registerResources()
    {
        $url = $this->config->pluginUrl;

        wp_enqueue_script('jquery');
        wp_enqueue_script('tooltipjs',               $url . '/js/jquery.tooltip.src.js',               array('jquery'), null, true);
        wp_enqueue_script('imagesloadedjs',          $url . '/js/jquery.imagesloaded.src.js',          array('jquery'), null, true);
        wp_enqueue_script('mousewheeljs',            $url . '/js/jquery.mousewheel.src.js',            array('jquery'), null, true);
        wp_enqueue_script('smoothdivscrolljs',       $url . '/js/jquery.smoothDivScroll-1.2.src.js',   array('mousewheeljs','jquery-ui-core','jquery-ui-widget','jquery-effects-core'), null, true);
        wp_enqueue_script('wolfnetscrollingitemsjs', $url . '/js/jquery.wolfnetScrollingItems.src.js', array('smoothdivscrolljs'), null, true);
        wp_enqueue_script('wolfnetquicksearchjs',    $url . '/js/jquery.wolfnetQuickSearch.src.js',    array('jquery'), null, true);
        wp_enqueue_script('wolfnetlistinggridjs',    $url . '/js/jquery.wolfnetListingGrid.src.js',    array('jquery','tooltipjs','imagesloadedjs'), null, true);
        wp_enqueue_script('wolfnettoolbarjs',        $url . '/js/jquery.wolfnetToolbar.src.js',        array('jquery' ), null, true);
        wp_enqueue_script('wolfnetpropertylistjs',   $url . '/js/jquery.wolfnetPropertyList.src.js',   array('jquery'), null, true);
        wp_enqueue_script('wolfnetjs',               $url . '/js/wolfnet.src.js',                      array('jquery','tooltipjs'), null, true);

        wp_enqueue_style('wolfnetcss', $url . '/css/wolfnet.src.css', array(), false, 'screen');

    }


    public function registerOptions()
    {
        register_setting('wolfnet', 'wolfnet_productKey');

    }


    public function registerShortcodeBuilderButton()
    {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing') == 'true') {
            add_action('admin_enqueue_scripts', array(&$this, 'registerShortcodeBuilderResources'));
            add_filter('mce_external_plugins', array(&$this, 'applyShortcodeBuilderMcePlugin'));
            add_filter('mce_buttons', array(&$this, 'registerButton'));
        }

    }


    public function registerShortcodeBuilderResources()
    {
        $url = $this->config->pluginUrl;

        wp_enqueue_script(
            'wolfnetshortcodebuilder',
            $url . '/js/jquery.wolfnet_shortcode_builder.src.js',
            array('jquery-ui-core', 'jquery-ui-widget', 'jquery-effects-core')
        );

    }


    public function applyShortcodeBuilderMcePlugin(array $plugins)
    {
        $url = $this->config->pluginUrl;

        echo '<script type="text/javascript">var wordpressBaseUrl = "' . site_url() . '";</script>';

        $plugins['wolfnetShortcodeBuilder'] = $url . '/js/tinymce.wolfnet_shortcode_builder.src.js';

        return $plugins;

    }


    public function registerButton(array $buttons)
    {
        array_push($buttons, '|', 'wolfnetShortcodeBuilderButton');

        return $buttons;

    }


    /**
     * This method is called by the 'admin_menu' hook within WordPress. It is used to register admin
     * pages for the plugin itself.
     */
    public function adminMenu()
    {
        $url = $this->config->pluginUrl;
        $lvl = 'administrator';

        $setPag = array(
            'title' => 'General Settings',
            'key'   => 'wolfnet_plugin_settings',
            'cb'    => array(&$this, 'adminSettingsPage')
            );

        $schPag = array(
            'title' => 'Search Manager',
            'key'   => 'wolfnet_plugin_search_manager',
            'cb'    => array(&$this, 'adminSearchManagerPage')
            );

        $insPag = array(
            'title' => 'Support',
            'key'   => 'wolfnet_plugin_support',
            'cb'    => array(&$this, 'adminSupportPage')
            );

        $idxPag = array(
            'title' => 'WolfNet',
            'key'   => $setPag['key'],
            'icon'  => $url . '/img/wp_wolfnet_nav.png',
            );

        add_menu_page($idxPag['title'], $idxPag['title'], $lvl, $idxPag['key'], null, $idxPag['icon']);

        add_submenu_page($idxPag['key'], $setPag['title'], $setPag['title'], $lvl, $setPag['key'], $setPag['cb']);
        add_submenu_page($idxPag['key'], $schPag['title'], $schPag['title'], $lvl, $schPag['key'], $schPag['cb']);
        add_submenu_page($idxPag['key'], $insPag['title'], $insPag['title'], $lvl, $insPag['key'], $insPag['cb']);

    }


    public function adminSettingsPage()
    {
        $tpl = $this->fac->get('TemplateEngine');

        $content = array(
            'productKey' => get_option('wolfnet_productKey'),
            'siteUrl'    => site_url()
            );

        ob_start(); settings_fields('wolfnet'); $content['formHeader'] = ob_get_clean();

        echo $tpl->render('plugin-settings.html', $content);

    }


    public function adminSearchManagerPage()
    {
        $tpl = $this->fac->get('TemplateEngine');
        $settingsService = $this->fac->get('SettingsService');
        $searchService = $this->fac->get('SearchService');

        if ( !$settingsService->isKeyValid() ) {
            echo $tpl->render('invalid-product-key.html');
        }
        else {
            $content = array(
                'searchForm' => '<script type="text/javascript">'
                    . 'var wntcfid = "' . $searchService->getCfId() . '";'
                    . 'var wntcftoken = "' . $searchService->getCfToken() . '";'
                    . '</script>'
                    . $searchService->getSearchManagerHtml(),
                'pluginUrl' => $this->config->pluginUrl,
                'siteUrl' => site_url()
            );

            echo $tpl->render('search-manager.html', $content);

        }

    }


    public function adminSupportPage()
    {

        $tpl = $this->fac->get('TemplateEngine');
        $tpl->addFilter('thumbnailLink', new Twig_Filter_Function(array(&$this, 'thumbnailLinkHtml')));

        echo $tpl->render('plugin-instructions.html');

    }


    public function thumbnailLinkHtml ($img)
    {
        $imgdir = $this->config->pluginUrl . '/img/';
        $url = $imgdir . $img;

        echo '<a href="' . $url . '" target="_blank"><img src="' . $url . '" class="wolfnet_thumbnail" /></a>';

    }


    public function registerAdminResources()
    {
        global $wp_scripts;

        $url = $this->config->pluginUrl;
        $jquery_ui = $wp_scripts->query('jquery-ui-core');

        wp_enqueue_script('tooltipjs',      $url . '/js/jquery.tooltip.src.js', array('jquery'));
        wp_enqueue_script('wolfnetjs',      $url . '/js/wolfnet.src.js',        array('jquery','tooltipjs'));
        wp_enqueue_script('wolfnetadminjs', $url . '/js/wolfnetAdmin.src.js',   array('jquery','jquery-ui-dialog','jquery-ui-tabs'));
        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_style('jquery-ui-css',  'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_ui->ver . '/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('wolfnetadmincss', $url . '/css/wolfnetAdmin.src.css', array(), false, 'screen');

    }


    public function footerDisclaimer()
    {
        $marketDisclaimerService = $this->fac->get('MarketDisclaimerService');

        /* If it has been established that we need to output the market disclaimer do so now in the
         * site footer, otherwise do nothing. */
        if (array_key_exists('wolfnet_includeDisclaimer', $_REQUEST)) {
            echo '<div class="wolfnet_marketDisclaimer">';
            echo $marketDisclaimerService->getDisclaimerByType()->getContent();
            echo '</div>';
        }

    }


}
