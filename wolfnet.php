<?php

/**
 * Plugin Name:  WolfNet IDX for WordPress
 * Plugin URI:   http://wordpress.wolfnet.com
 * Description:  The WolfNet IDX for WordPress plugin provides IDX search solution integration with any WordPress website.
 * Version:      {X.X.X}
 * Author:       WolfNet Technologies, LLC.
 * Author URI:   http://www.wolfnet.com
 */

class WolfNetPlugin
{


    /* PROPERTIES ******************************************************************************* */

    private $optionGroup          = 'wolfnet';
    private $customPostTypeSearch = 'wolfnet_search';
    private $productKeyOptionKey  = 'wolfnet_productKey';
    private $transientIndexKey    = 'wolfnet_transients';


    /* CONSTRUCTOR METHOD *********************************************************************** */

    public function __construct()
    {

        $this->url = plugins_url(dirname(__FILE__));

        // Clear cache if url param exists.
        if (array_key_exists('-wolfnet-cache', $_REQUEST)) {
            $this->clearTransients();
        }

        add_action('init', array(&$this, 'init'));
        add_action('wp_enqueue_scripts', array(&$this, 'scripts'));
        add_action('widgets_init', array(&$this, 'widgetInit'));
        add_action('admin_init', array(&$this, 'adminInit'));
        add_action('admin_menu', array(&$this, 'adminMenu'));
        add_action('admin_enqueue_scripts', array(&$this, 'adminScripts'));
        add_action('wp_footer', array(&$this, 'footer'));

    }


    /* PUBLIC METHODS *************************************************************************** */

    public function init()
    {

        // Register Custom Post Types
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

        // Register Rewrite Rules
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

        // Register Shortcodes
        add_shortcode('WolfNetFeaturedListings', array(&$this, 'scFeaturedListings'));
        add_shortcode('woflnetfeaturedlistings', array(&$this, 'scFeaturedListings'));
        add_shortcode('WOFLNETFEATUREDLISTINGS', array(&$this, 'scFeaturedListings'));
        add_shortcode('wnt_featured', array(&$this, 'scFeaturedListings'));
        add_shortcode('WolfNetListingGrid', array(&$this, 'scListingGrid'));
        add_shortcode('wolfnetlistinggrid', array(&$this, 'scListingGrid'));
        add_shortcode('WOLFNETLISTINGGRID', array(&$this, 'scListingGrid'));
        add_shortcode('wnt_grid', array(&$this, 'scListingGrid'));
        add_shortcode('WolfNetPropertyList', array(&$this, 'scPropertyList'));
        add_shortcode('wolfnetpropertylist', array(&$this, 'scPropertyList'));
        add_shortcode('WOLFNETPROPERTYLIST', array(&$this, 'scPropertyList'));
        add_shortcode('wnt_list', array(&$this, 'scPropertyList'));
        add_shortcode('WolfNetListingQuickSearch', array(&$this, 'scQuickSearch'));
        add_shortcode('wolfnetlistingquicksearch', array(&$this, 'scQuickSearch'));
        add_shortcode('WOLFNETLISTINGQUICKSEARCH', array(&$this, 'scQuickSearch'));
        add_shortcode('wnt_search', array(&$this, 'scQuickSearch'));
        add_shortcode('WolfNetQuickSearch', array(&$this, 'scQuickSearch'));
        add_shortcode('wolfnetquicksearch', array(&$this, 'scQuickSearch'));
        add_shortcode('WOLFNETQUICKSEARCH', array(&$this, 'scQuickSearch'));

        // Register Ajax Actions
        add_action('wp_ajax_nopriv_wolfnet_content', array(&$this, 'remoteContent'));
        add_action('wp_ajax_nopriv_wolfnet_content_header', array(&$this, 'remoteContentHeader'));
        add_action('wp_ajax_nopriv_wolfnet_content_footer', array(&$this, 'remoteContentFooter'));
        add_action('wp_ajax_nopriv_wolfnet_listings', array(&$this, 'remoteListings'));
        add_action('wp_ajax_nopriv_wolfnet_sort_options', array(&$this, 'remoteSortOptions'));
        add_action('wp_ajax_nopriv_wolfnet_listings_per_page', array(&$this, 'remoteListingsPerPage'));
        add_action('wp_ajax_nopriv_wolfnet_get_listings', array(&$this, 'remoteListingsGet'));

    }


    public function scripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('tooltipjs',               $this->url . '/js/jquery.tooltip.src.js',               array('jquery'), null, true);
        wp_enqueue_script('imagesloadedjs',          $this->url . '/js/jquery.imagesloaded.src.js',          array('jquery'), null, true);
        wp_enqueue_script('mousewheeljs',            $this->url . '/js/jquery.mousewheel.src.js',            array('jquery'), null, true);
        wp_enqueue_script('smoothdivscrolljs',       $this->url . '/js/jquery.smoothDivScroll-1.2.src.js',   array('mousewheeljs','jquery-ui-core','jquery-ui-widget','jquery-effects-core'), null, true);
        wp_enqueue_script('wolfnetscrollingitemsjs', $this->url . '/js/jquery.wolfnetScrollingItems.src.js', array('smoothdivscrolljs'), null, true);
        wp_enqueue_script('wolfnetquicksearchjs',    $this->url . '/js/jquery.wolfnetQuickSearch.src.js',    array('jquery'), null, true);
        wp_enqueue_script('wolfnetlistinggridjs',    $this->url . '/js/jquery.wolfnetListingGrid.src.js',    array('jquery','tooltipjs','imagesloadedjs'), null, true);
        wp_enqueue_script('wolfnettoolbarjs',        $this->url . '/js/jquery.wolfnetToolbar.src.js',        array('jquery' ), null, true);
        wp_enqueue_script('wolfnetpropertylistjs',   $this->url . '/js/jquery.wolfnetPropertyList.src.js',   array('jquery'), null, true);
        wp_enqueue_script('wolfnetjs',               $this->url . '/js/wolfnet.src.js',                      array('jquery','tooltipjs'), null, true);

        wp_enqueue_style('wolfnetcss', $this->url . '/css/wolfnet.src.css', array(), false, 'screen');

    }


    public function widgetInit()
    {
        require_once dirname(__FILE__) . '/widget/FeaturedListingsWidget.php';
        register_widget('Wolfnet_FeaturedListingsWidget');

        require_once dirname(__FILE__) . '/widget/ListingGridWidget.php';
        register_widget('Wolfnet_ListingGridWidget');

        require_once dirname(__FILE__) . '/widget/PropertyListWidget.php';
        register_widget('Wolfnet_PropertyListWidget');

        require_once dirname(__FILE__) . '/widget/QuickSearchWidget.php';
        register_widget('Wolfnet_QuickSearchWidget');

    }


    public function adminInit()
    {

        // Register Options
        register_setting($this->optionGroup, $this->productKeyOptionKey);

        // Register Shortcode Builder Button
        $canEditPosts = current_user_can('edit_posts');
        $canEditPages = current_user_can('edit_pages');
        $richEditing = get_user_option('rich_editing');

        if (($canEditPosts || $canEditPages) && $richEditing == 'true') {
            add_action('admin_enqueue_scripts', array(&$this, 'sbScripts'));
            add_filter('mce_external_plugins', array(&$this, 'sbMcePlugin'));
            add_filter('mce_buttons', array(&$this, 'sbButton'));
        }

        // Register Ajax Actions
        add_action('wp_ajax_wolfnet_validate_key', array(&$this, 'remoteValidateProductKey'));
        add_action('wp_ajax_wolfnet_saved_searches', array(&$this, 'remoteGetSavedSearchs'));
        add_action('wp_ajax_wolfnet_save_search', array(&$this, 'remoteSaveSearch'));
        add_action('wp_ajax_wolfnet_delete_search', array(&$this, 'remoteDeleteSearch'));
        add_action('wp_ajax_wolfnet_scb_options_featured', array(&$this, 'remoteShortcodeBuilderOptionsFeatured'));
        add_action('wp_ajax_wolfnet_scb_options_grid', array(&$this, 'remoteShortcodeBuilderOptionsGrid'));
        add_action('wp_ajax_wolfnet_scb_options_list', array(&$this, 'remoteShortcodeBuilderOptionsList'));
        add_action('wp_ajax_wolfnet_scb_options_quicksearch', array(&$this, 'remoteShortcodeBuilderOptionsQuickSearch'));
        add_action('wp_ajax_wolfnet_scb_options_savedsearch', array(&$this, 'remoteShortcodeBuilderSavedSearch'));
        add_action('wp_ajax_wolfnet_content', array(&$this, 'remoteContent'));
        add_action('wp_ajax_wolfnet_content_header', array(&$this, 'remoteContentHeader'));
        add_action('wp_ajax_wolfnet_content_footer', array(&$this, 'remoteContentFooter'));
        add_action('wp_ajax_wolfnet_listings', array(&$this, 'remoteListings'));
        add_action('wp_ajax_wolfnet_sort_options', array(&$this, 'remoteSortOptions'));
        add_action('wp_ajax_wolfnet_listings_per_page', array(&$this, 'remoteListingsPerPage'));
        add_action('wp_ajax_wolfnet_get_listings', array(&$this, 'remoteListingsGet'));

    }


    public function adminMenu()
    {
        $lvl = 'administrator';

        $setPag = array(
            'title' => 'General Settings',
            'key'   => 'wolfnet_plugin_settings',
            'cb'    => array(&$this, 'amSettingsPage')
            );

        $schPag = array(
            'title' => 'Search Manager',
            'key'   => 'wolfnet_plugin_search_manager',
            'cb'    => array(&$this, 'amSearchManagerPage')
            );

        $insPag = array(
            'title' => 'Support',
            'key'   => 'wolfnet_plugin_support',
            'cb'    => array(&$this, 'amSupportPage')
            );

        $idxPag = array(
            'title' => 'WolfNet',
            'key'   => $setPag['key'],
            'icon'  => $this->url . '/img/wp_wolfnet_nav.png',
            );

        add_menu_page($idxPag['title'], $idxPag['title'], $lvl, $idxPag['key'], null, $idxPag['icon']);

        add_submenu_page($idxPag['key'], $setPag['title'], $setPag['title'], $lvl, $setPag['key'], $setPag['cb']);
        add_submenu_page($idxPag['key'], $schPag['title'], $schPag['title'], $lvl, $schPag['key'], $schPag['cb']);
        add_submenu_page($idxPag['key'], $insPag['title'], $insPag['title'], $lvl, $insPag['key'], $insPag['cb']);

    }


    public function adminScripts()
    {
        global $wp_scripts;

        $jquery_ui = $wp_scripts->query('jquery-ui-core');

        wp_enqueue_script('tooltipjs',      $this->url . '/js/jquery.tooltip.src.js', array('jquery'));
        wp_enqueue_script('wolfnetjs',      $this->url . '/js/wolfnet.src.js',        array('jquery','tooltipjs'));
        wp_enqueue_script('wolfnetadminjs', $this->url . '/js/wolfnetAdmin.src.js',   array('jquery','jquery-ui-dialog','jquery-ui-tabs'));
        wp_enqueue_script('jquery-ui-datepicker');

        wp_localize_script('wolfnetjs', 'wolfnet_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));

        wp_enqueue_style('jquery-ui-css',  'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_ui->ver . '/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('wolfnetadmincss', $this->url . '/css/wolfnetAdmin.src.css', array(), false, 'screen');

    }


    public function footer()
    {
        /* If it has been established that we need to output the market disclaimer do so now in the
         * site footer, otherwise do nothing. */
        if (array_key_exists('wolfnet_includeDisclaimer', $_REQUEST)) {
            echo '<div class="wolfnet_marketDisclaimer">';
            echo $this->getMarketDisclaimer();
            echo '</div>';
        }

    }


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


    public function sbScripts()
    {

        wp_enqueue_script(
            'wolfnetshortcodebuilder',
            $this->url . '/js/jquery.wolfnet_shortcode_builder.src.js',
            array('jquery-ui-core', 'jquery-ui-widget', 'jquery-effects-core')
        );

    }


    public function sbMcePlugin(array $plugins)
    {

        echo '<script type="text/javascript">var wordpressBaseUrl = "' . site_url() . '";</script>';
        $plugins['wolfnetShortcodeBuilder'] = $this->url . '/js/tinymce.wolfnet_shortcode_builder.src.js';

        return $plugins;

    }


    public function sbButton(array $buttons)
    {
        array_push($buttons, '|', 'wolfnetShortcodeBuilderButton');

        return $buttons;

    }


    public function amSettingsPage()
    {
        ob_start(); settings_fields($this->optionGroup); $formHeader = ob_get_clean();
        $productKey = $this->getProductKey();
        include 'template/adminSettings.php';

    }


    public function amSearchManagerPage()
    {
        if (!$this->productKeyIsValid()) {
            include 'template/invalidProductKey.php';
            return;
        }
        else {
            ob_start();
            echo '<script type="text/javascript">';
            echo 'var wntcfid = "' . $this->searchManagerCfId() . '";';
            echo 'var wntcftoken = "' . $this->searchManagerCfToken() . '";';
            echo '</script>';
            echo $this->searchManagerHtml();
            $searchForm = ob_get_clean();
            include 'template/adminSearchManager.php';

        }

    }


    public function amSupportPage()
    {
        $imgdir = $this->url . '/img/';
        include 'template/adminSupport.php';

    }


    public function getFeaturedListingsDefaults()
    {

        return array(
            'title'      => '',
            'direction'  => 'left',
            'autoplay'   => true,
            'speed'      => 5,
            'ownertype'  => 'agent_broker',
            'maxresults' => 50,
            'numrows'    => 50,
            'startrow'   => 1,
            );

    }


    public function scFeaturedListings($attrs, $content='')
    {
        $defaultAttributes = $this->getFeaturedListingsDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->featuredListings($criteria);

    }


    public function featuredListings(array $criteria)
    {
        if (!array_key_exists('numrows', $criteria)) {
            $criteria['numrows'] = $criteria['maxresults'];
        }

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

            $listingsHtml .= $this->parseTemplate('template/listing.php', $vars);

        }

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'listingsHtml' => $listingsHtml,
            'siteUrl'      => site_url(),
            'criteria'     => json_encode($criteria)
            );

        return $this->parseTemplate('template/featuredListings.php', array_merge($criteria, $vars));

    }


    public function getListingGridDefaults()
    {

        return array(
            'title'       => '',
            'criteria'    => '',
            'ownertype'   => 'all',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'numrows'     => 50,
            'startrow'    => 1,
            'mode'        => 'advanced',
            'savedsearch' => '',
            'zipcode'     => '',
            'city'        => '',
            'minprice'    => '',
            'maxprice'    => '',
            );

    }


    public function scListingGrid($attrs, $content='')
    {
        $defaultAttributes = $this->getListingGridDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->listingGrid($criteria);

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

            $listingsHtml .= $this->parseTemplate('template/listing.php', $vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'listings'     => $listingsData,
            'listingsHtml' => $listingsHtml,
            'siteUrl'      => site_url(),
            'criteria'     => json_encode($criteria),
            'class'        => 'wolfnet_listingGrid',
            'maxresults'   => $this->getMaxResults()
            );

        $vars = array_merge($criteria, $vars);

        $vars['toolbarTop'] = $this->getToolbar($vars, 'wolfnet_toolbarTop');
        $vars['toolbarBottom'] = $this->getToolbar($vars, 'wolfnet_toolbarBottom');

        return $this->parseTemplate('template/listingGrid.php', $vars);

    }


    public function getPropertyListDefaults()
    {

        return array(
            'title'       => '',
            'ownertype'   => 'all',
            'paginated'   => false,
            'sortoptions' => false,
            'maxresults'  => 50,
            'numrows'    => 50,
            'startrow'   => 1,
            );

    }


    public function scPropertyList($attrs, $content='')
    {
        $defaultAttributes = $this->getPropertyListDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->propertyList($criteria);

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

            $listingsHtml .= $this->parseTemplate('template/briefListing.php', $vars);

        }

        $_REQUEST['wolfnet_includeDisclaimer'] = true;

        $vars = array(
            'instance_id'  => str_replace('.', '', uniqid('wolfnet_propertyList_')),
            'listings'     => $listingsData,
            'listingsHtml' => $listingsHtml,
            'siteUrl'      => site_url(),
            'criteria'     => json_encode($criteria),
            'class'        => 'wolfnet_propertyList',
            'maxresults'   => $this->getMaxResults()
            );

        $vars = array_merge($criteria, $vars);

        $vars['toolbarTop'] = $this->getToolbar($vars, 'wolfnet_toolbarTop');
        $vars['toolbarBottom'] = $this->getToolbar($vars, 'wolfnet_toolbarBottom');

        return $this->parseTemplate('template/propertyList.php', $vars);

    }


    public function getQuickSearchDefaults()
    {

        return array(
            'title' => 'QuickSearch'
            );

    }


    public function scQuickSearch($attrs, $content='')
    {
        $defaultAttributes = $this->getQuickSearchDefaults();

        $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

        return $this->quickSearch($criteria);

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

        $args = array_merge($vars, $criteria);

        /* Register WordPress filters for each variable being used in the view. */
        foreach ($args as $key => $item) {
            $data[$key] = apply_filters('wolfnet_quickSearchView_' . $key, $item);
        }

        return $this->parseTemplate('template/quickSearch.php', $args);

    }


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
        echo $this->getFeaturedListingsOptionsForm();

        die;

    }


    public function remoteShortcodeBuilderOptionsGrid ()
    {
        echo $this->getListingGridOptionsForm();

        die;

    }


    public function remoteShortcodeBuilderOptionsList ()
    {
        $this->remoteShortcodeBuilderOptionsGrid();

        die;

    }


    public function remoteShortcodeBuilderOptionsQuickSearch ()
    {
        echo $this->getQuickSearchOptionsForm();

        die;

    }


    public function remoteShortcodeBuilderSavedSearch ()
    {
        $id = (array_key_exists('ID', $_REQUEST)) ? $_REQUEST['ID'] : 0;
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

    }


    public function remoteSortOptions()
    {
        $sortOptions = $this->getSortOptions();

        echo json_encode($sortOptions);

        die;

    }


    public function remoteListingsPerPage()
    {
        $itemsPerPage = $this->getItemsPerPage();

        echo json_encode($itemsPerPage);

        die;

    }


    public function remoteListingsGet()
    {
        echo json_encode($this->getListings($_REQUEST));

        die;

    }


    public function getFeaturedListingsOptionsForm(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'     => str_replace('.', '', uniqid('wolfnet_featuredListing_')),
            'autoplay_id'     => '',
            'autoplay_name'   => '',
            'autoplay'        => '',
            'direction_id'    => '',
            'direction_name'  => '',
            'direction'       => '',
            'maxresults_id'   => '',
            'maxresults_name' => '',
            'maxresults'      => '',
            'ownertype_id'    => '',
            'ownertype_name'  => '',
            'ownertype'       => '',
            'speed_id'        => '',
            'speed_name'      => '',
            'speed'           => '',
            'title_id'        => '',
            'title_name'      => '',
            'title'           => '',
            'direction_left'  => '',
            'direction_right' => '',
            'autoplay_true'   => '',
            'autoplay_false'  => '',
            'ownertypes'      => $this->getOwnerTypes(),
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/featuredListingsOptions.php', $args);

    }


    public function getListingGridOptionsForm(array $args=array())
    {
        $defaultArgs = array(
            'instance_id'      => str_replace('.', '', uniqid('wolfnet_listingGrid_')),
            'city_id'           => '',
            'city_name'         => '',
            'city_value'        => '',
            'criteria_id'       => '',
            'criteria_name'     => '',
            'criteria_value'    => '',
            'maxprice_id'       => '',
            'maxprice_name'     => '',
            'maxprice_value'    => '',
            'maxresults_id'     => '',
            'maxresults_name'   => '',
            'maxresults_value'  => '',
            'minprice_id'       => '',
            'minprice_name'     => '',
            'minprice_value'    => '',
            'mode_id'           => '',
            'mode_name'         => '',
            'mode_value'        => '',
            'ownertype_id'      => '',
            'ownertype_name'    => '',
            'ownertype_value'   => '',
            'paginated_id'      => '',
            'paginated_name'    => '',
            'paginated_value'   => '',
            'savedsearch_id'    => '',
            'savedsearch_name'  => '',
            'savedsearch_value' => '',
            'sortoptions_id'    => '',
            'sortoptions_name'  => '',
            'sortoptions_value' => '',
            'title_id'          => '',
            'title_name'        => '',
            'title_value'       => '',
            'zipcode_id'        => '',
            'zipcode_name'      => '',
            'zipcode_value'     => '',
            'mode_basic'        => '',
            'mode_advanced'     => '',
            'ownertypes'        => $this->getOwnerTypes(),
            'paginated_false'   => '',
            'paginated_true'    => '',
            'prices'            => $this->getPrices(),
            'savedsearches'     => $this->getSavedSearches(),
            'sortoptions_false' => '',
            'sortoptions_true'  => '',
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/listingGridOptions.php', $args);

    }


    public function getPropertyListOptionsForm(array $args=array())
    {
        $args = array_merge($args, array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_propertyList_'))
            ));

        return $this->getListingGridOptions($args);

    }


    public function getQuickSearchOptionsForm(array $args=array())
    {
        $defaultArgs = array(
            'instance_id' => str_replace('.', '', uniqid('wolfnet_quickSearch_')),
            'title_id'    => '',
            'title_name'  => '',
            'title'       => '',
            );

        $args = array_merge($defaultArgs, $args);

        return $this->parseTemplate('template/quickSearchOptions.php', $args);

    }


    public function getSavedSearches($count=-1)
    {
        $dataArgs = array(
            'numberposts' => $count,
            'post_type' => $this->customPostTypeSearch
            );

        return get_posts($dataArgs);

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


    public function getFeaturedListings(array $criteria=array())
    {
        $productKey = $this->getProductKey();
        $url = 'http://services.mlsfinder.com/v1/propertyBar/' . $productKey . '.json';
        $url = $this->buildUrl($url, $criteria);

        return $this->getApiData($url, 900)->listings;

    }


    public function getListings(array $criteria=array())
    {
        $productKey = $this->getProductKey();
        $url = 'http://services.mlsfinder.com/v1/propertyGrid/' . $productKey . '.json';
        $url = $this->buildUrl($url, $criteria);

        $data = $this->getApiData($url, 900);

        foreach ($data->listings as &$listing) {
            $listing->numrows    = $criteria['numrows'];
            $listing->startrow   = $criteria['startrow'];
            $listing->maxresults = ($data->total_rows < $criteria['maxresults']) ? $data->total_rows : $criteria['maxresults'];
        }

        return $data->listings;

    }


    public function getOptions(array $defaultOptions, $instance=null, $idCallback=null, $nameCallback=null)
    {
        $iExists = ($instance !== null);

        if ($idCallback === null) {
            $idCallback = array(&$this, 'emptyCallback');
        }

        if ($nameCallback === null) {
            $nameCallback = array(&$this, 'emptyCallback');
        }

        $options = array();

        foreach ($defaultOptions as $opt => $defaultValue) {
            $valExists = ($iExists && array_key_exists($opt, $instance));
            $options[$opt . '_wpid'] = esc_attr(call_user_method_array($idCallback[1], $idCallback[0], array($opt)));
            $options[$opt . '_wpname'] = esc_attr(call_user_method_array($nameCallback[1], $nameCallback[0], array($opt)));
            $options[$opt] = ($valExists) ? $instance[$opt] : $defaultValue;
        }

        return $options;

    }


    public function emptyCallback($arg)
    {
        return $arg;
    }


    /* PRIVATE METHODS ************************************************************************** */

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

            if (property_exists($data, 'error') && property_exists($data->error, 'status') && $data->error->status === false) {
                $valid = true;
            }
        }

        return $valid;

    }


    private function searchManagerHtml()
    {
        global $wp_version;
        $baseUrl = $this->getBaseUrl();

        if (!strstr($baseUrl, 'index.cfm')) {
            if (substr($baseUrl, strlen($baseUrl) - 1) != '/') {
                $baseUrl .= '/';
            }
            $baseUrl .= 'index.cfm';
        }

        $url = $baseUrl
             . ((!strstr($baseUrl, '?')) ? '?' : '')
             . '&action=wpshortcodebuilder&search_mode=form'
             . '&cfid=' . $this->searchManagerCfId()
             . '&cftoken=' . $this->searchManagerCfToken()
             . '&jsessionid=' . $this->searchManagerJSessionId();

        $resParams = array(
            'page',
            'action',
            'market_guid',
            'reinit',
            'show_header_footer',
            'search_mode'
            );

        foreach ($_GET as $param => $paramValue) {
            if (!array_search($param, $resParams)) {
                $paramValue = urlencode($paramValue);
                $url .= "&{$param}={$paramValue}";
            }
        }

        $reqHeaders = array(
            'cookies'    => array(
                'WntCfId' => new WP_Http_Cookie($this->searchManagerCfId()),
                'WntCfToken' => new WP_Http_Cookie($this->searchManagerCfToken()),
                'WntJSessionId' => new WP_Http_Cookie($this->searchManagerJSessionId())
                ),
            'timeout'    => 180,
            'user-agent' => 'WordPress/' . $wp_version
            );

        $http = wp_remote_get($url, $reqHeaders);

        if (!is_wp_error($http) && $http['response']['code'] == '200') {

            if (array_key_exists('WntCfId', $http['cookies'])) {
                $this->searchManagerCfId($http['cookies']['WntCfId']['value']);
            }

            if (array_key_exists('WntCfToken', $http['cookies'])) {
                $this->searchManagerCfToken($http['cookies']['WntCfToken']['value']);
            }

            if (array_key_exists('WntJSessionId', $http['cookies'])) {
                $this->searchManagerJSessionId($http['cookies']['WntJSessionId']['value']);
            }

            return $this->removeJqueryFromHTML($http['body']);

        }
        else {
            return '';
        }

    }


    private function searchManagerCfId($value=null)
    {
        return $this->cookie('WntCfId', $value);

    }


    private function searchManagerCfToken($value=null)
    {
        return $this->cookie('WntCfToken', $value);

    }


    private function searchManagerJSessionId($value=null)
    {
        return $this->cookie('WntJSessionId', $value);

    }


    private function cookie($key, $value=null)
    {
        if ($value != null) {
            $_COOKIE[$key] = $value;
        }

        return (array_key_exists($key, $_COOKIE)) ? $_COOKIE[$key] : '';

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
            'nextLink','prevClass','nextClass','toolbarClass','instance_id','siteUrl','class');

        $restrictedSuffix = array('_wpid', '_wpname', '_wps', '_wpc');

        foreach ($params as $key => $value) {
            $valid = true;
            $valid = (array_search($key, $restrictedParams) !== false) ? false : $valid;
            $valid = (!is_string($value) && !is_numeric($value) && !is_bool($value)) ? false : $valid;
            foreach ($restrictedSuffix as $suffix) {
                $valid = (substr($key, strlen($suffix)*-1) == $suffix) ? false : $valid;
            }
            if ($valid) {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }

        return $url;

    }


    private function parseTemplate($template, array $vars=array())
    {
        ob_start();

        foreach ($vars as $key => $value) {
            ${$key} = $value;
        }

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
        return get_option($this->productKeyOptionKey);

    }


    private function getApiData($url, $cacheFor=900)
    {
        $key = 'wolfnet_' . md5($url);
        $index = $this->transientIndex();
        $time = time();
        $data = (array_key_exists($key, $index)) ? get_transient($key) : false;

        if ($data === false || $time > $index[$key]) {
            $http = wp_remote_get($url);

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
                set_transient($key, $data, 0);
            }
            else {
                $tmp = json_decode($http['body']);

                if ($tmp === false) {
                    $data->error->message = 'An error occurred while attempting to decode the body as Json.';
                }
                else {
                    $data = $tmp;
                }

                if (is_object($data)) {
                    $data->url = $url;
                }

                $index[$key] = $time + $cacheFor;
                set_transient($key, $data, 0);

            }

        }

        if (property_exists($data, 'error') && property_exists($data->error, 'status') && $data->error->status) {
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
            set_transient($key, $data, 0);
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
        setlocale(LC_MONETARY, 'en_US');
        if (is_numeric($listing->listing_price)) {
            $listing->listing_price = money_format('%.0n', $listing->listing_price);
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


    private function getToolbar($data, $class)
    {
        $args = array_merge(json_decode($data['criteria'], true), array(
            'toolbarClass' => $class,
            'maxresults'   => $data['maxresults'],
            'numrows'      => $data['numrows'],
            'prevClass'    => ($data['startrow']<=1) ? 'wolfnet_disabled' : '',
            'lastitem'     => $data['startrow'] + $data['numrows'] - 1,
            'action'       => 'wolfnet_get_listings'
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

        $args['prevLink'] = $this->buildUrl(site_url(), array_merge($args, array('startrow'=>$prev)));

        $next = $args['startrow'] + $args['numrows'];

        if ($next >= $args['maxresults']) {
            $next = 1;
        }

        $args['nextLink']  = $this->buildUrl(admin_url('admin-ajax.php'), array_merge($args, array('startrow'=>$next)));

        return $this->parseTemplate('template/toolbar.php', $args);

    }


    private function getMaxResults()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json';
        $url .= '?setting=site_text';

        $data = $this->getApiData($url, 86400)->site_text;
        $maxResults = (property_exists($data, 'Max Results')) ? $data->{'Max Results'} : '';

        return (is_numeric($maxResults)) ? $maxResults : 250;

    }


    private function getPricesFromApi()
    {
        $productKey = $this->getProductKey();
        $url  = 'http://services.mlsfinder.com/v1/setting/' . $productKey . '.json';
        $url .= '?setting=site_text';

        $data = $this->getApiData($url, 86400)->site_text;
        $prices = (property_exists($data, 'Price Range Values')) ? $data->{'Price Range Values'} : '';

        return explode(',', $prices);

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


    private function getPrices()
    {
        $values = $this->getPricesFromApi();

        $data = array();

        setlocale(LC_MONETARY, 'en_US');

        foreach ($values as $value) {
            $data[] = array('value'=>trim($value), 'label'=>money_format('%.0n', trim($value)));
        }

        return $data;

    }


    private function getBeds ()
    {
        $values = array(1,2,3,4,5,6,7);

        $data = array();

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


}


$GLOBALS['wolfnet'] = new WolfNetPlugin();
