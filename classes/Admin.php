<?php

/**
 * This class us used when the user is logged in as the admin user
 */
class Admin extends Wolfnet
{
    public function __construct()
    {
        // sets url
        $this->setUrl(); 


        register_activation_hook( $this->pluginFile, array($this, 'activate' ));
        register_deactivation_hook( $this->pluginFile, array($this, 'deactivate' ));
       
        //error_log($this->pluginFile);
        
        // Clear cache if url param exists.
        $cacheParamExists = array_key_exists($this->cacheFlag, $_REQUEST);
        $cacheParamClear = ($cacheParamExists) ? ($_REQUEST[$this->cacheFlag] == 'clear') : false;
        if ($cacheParamExists && $cacheParamClear) {
            $this->clearTransients();
        }

        // Register admin only actions.
        $this->addAction(array(
            array('admin_menu',            'adminMenu'),
            array('admin_init',            'adminInit'),
            array('admin_enqueue_scripts', 'adminScripts'),
            array('admin_enqueue_scripts', 'adminStyles'),
            array('admin_print_styles',    'adminPrintStyles',  1000),
            ));

            // Register admin only filters.
            $this->addFilter(array(
            array('mce_external_plugins', 'sbMcePlugin'),
            array('mce_buttons',          'sbButton'),
            ));


    }


    public function activate()
    {
        error_log("activating");
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
        error_log("deactivating");
        // Clear out all transient data as it is purely for caching and performance.
        $this->deleteTransientIndex();

    }

    private function deleteTransientIndex()
    {
        error_log("deleteTransientIndexing");
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

     /**
     * This method is a callback for the 'admin_enqueue_scripts' hook. Any JavaScript files (and
     * their dependacies) which are needed by the plugin for admin interfaces are registered in this
     * method.
     * @return void
     */
    public function adminScripts()
    {
        do_action( $this->preHookPrefix. 'enqueueAdminResources');

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
        $key = (array_key_exists("keyid", $_REQUEST)) ? $_REQUEST["keyid"] : "1";
        $productKey = $this->getProductKeyById($key);
        if(!$this->productKeyIsValid($productKey)) {
            $productKey = null;
        }
        if ($pageKeyExists && $pageIsSM) {
            $this->smHttp = $this->searchManagerHtml($productKey);
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

        // echo " URL: ". $this->url;
        //error_log ("Admin  adminMenu tom: ". $this->tom );
        
        // error_log ("Admin  adminMenu testUrl(): ". $this->testUrl() );
        
        // error_log ("Admin  adminMenu url: ". $this->url );
        
       
        $pgs = array(
            array(
                'title' => 'WolfNet <span class="wolfnet_sup">&reg;</span>',
                'key'   => 'wolfnet_plugin_settings',
                'icon'  => $this->url . '/img/wp_wolfnet_nav.png',
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
     * This method is used in the context of admin_print_styles to output custom CSS.
     * @return void
     */
    public function adminPrintStyles()
    {
        $adminCss = $this->getAdminCss();
        echo '<style>' . $adminCss . '</style>';

    }


    private function getAdminCss() 
    {
        return get_option($this->adminCssOptionKey);

    }


}