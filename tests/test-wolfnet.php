<?php
/**
 * Unit tests for the WolfNet IDX for WordPress plugin
 */

class Test_Wolfnet extends WP_UnitTestCase 
{
    /**
     * Performs WordPress setup and pre test cleanup. add to this method as needed. 
     */
    function setUp() 
    {
        parent::setUp();
        $GLOBALS['wp_tests_options'] = array(
            'active_plugins' => array('wolfnet-idx-for-wordpress/wolfnet.php')
            );
        $this->wolfnet = $GLOBALS['wolfnet'];

        // to access private & protected methods & properties we need to create 
        // a ReflectionClass object passing in the class name.   
        $this->wolfnet_reflection = new ReflectionClass("wolfnet");

    }

    /**
     * Cleans up after tests, removing any options, posts, etc. we may have added as part of a test.
     */
    function tearDown() 
    {
        parent::tearDown();
    }

    /**
     * Check if plugin is active
     */
    function testIsPluginActive()
    {
        $this->assertTrue( is_plugin_active('wolfnet-idx-for-wordpress/wolfnet.php') );
    }

    /**
     * Are the public scripts being enqueued?
     */
    function testScripts() 
    {
        //$GLOBALS['wolfnet']->scripts();
        $this->wolfnet->scripts(); 
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
            $this->assertTrue( wp_script_is( $script, 'enqueued' ) );
        }
    
    }

    /**
     * Are the public styles being enqueued?
     */
    function testStyles() 
    {
        $this->wolfnet->styles();
        $this->assertTrue( wp_style_is( 'wolfnet', 'enqueued' ) );

    }


    /**
     * Are our filters registered?
     */
    function testFilters() 
    {
        // the wordpress array that holds all registered filters
        global $wp_filter; 
        // filters to check
        $filters = array(
            array('do_parse_request',     'doParseRequest'),
            );

        $this->wolfnet->__construct(); 
        global $wp_filter;


        foreach ($filters as $filter) {
            // we cant use somthing like this because has filter doesn't work with objects:
            // $this->assertTrue( has_filter( 'mce_buttons', 'Wolfnet_Admin->sbButton' ) !== false );
            // The registerd "function" (2nd parameter) contains a hash when it is an object method

            // so we have to loop though the filters and find a key with our method as part of the name
            // loop though each priority level
            $found = false ;
            foreach ($wp_filter[ $filter[0] ] as $priority) {
                if ($found) break;
                // loop though each registerd filter in the current piority to find our function name in the key
                foreach ($priority as $fnkey => $fninfo) {
                    // echo "$fnkey\n";
                    $regex = '/'. $filter[1] .'$/';
                    if (preg_match($regex, $fnkey)) {
                        $found = true;
                        break;
                    }
                }
            }
            $this->assertTrue($found);
        }

    }

    /**
     * Are our actions registered?
     */
    function testActions() {
        // the wordpress array that holds all registered actions
        global $wp_filter;
        
        // actions to check
        $actions = array(
            array('init',                  'init'),
            array('wp_enqueue_scripts',    'scripts'),
            array('wp_enqueue_scripts',    'styles'),
            array('widgets_init',          'widgetInit'),
            array('wp_footer',             'footer'),
            array('template_redirect',     'templateRedirect'),
            array('wp_enqueue_scripts',    'publicStyles',      1000),
            );

        foreach ($actions as $action) {
            // see the comments in the testFilters() test for a better idea of what is going on here
            
            // loop though each priority level
            $found = false ;
            foreach ($wp_filter[ $action[0] ] as $priority) {
                if ($found) break;
                // loop though each registerd filter in the current piority to find our function name in the key
                foreach ($priority as $fnkey => $fninfo) {
                    // echo "$fnkey\n";
                    $regex = '/'. $action[1] .'$/';
                    if (preg_match($regex, $fnkey)) {
                        $found = true;
                        break;
                    }
                }
            }
            $this->assertTrue($found);
        }

    }



    /**
     * are the widgets being initialized?
     */
    function testWidgetInit() 
    {
        global $wp_widget_factory;

        $this->wolfnet->widgetInit();

        $widgets = array(
            'Wolfnet_FeaturedListingsWidget',
            'Wolfnet_ListingGridWidget',
            'Wolfnet_PropertyListWidget',
            'Wolfnet_ResultsSummaryWidget',
            'Wolfnet_QuickSearchWidget',
            );

        foreach ($widgets as $widget) {
            $this->assertTrue( isset( $wp_widget_factory->widgets[$widget] ) );
        }

    }

    



    /**
     * test if we are generating the header for remote used by the Wolfnet back office
     */
    function testGetWpHeader()
    {
        
        // We need to get the method we wish to test from 
        // the reflection class object and make it accessible
        $method = $this->wolfnet_reflection->getMethod("getWpHeader");
        $method->setAccessible(true);

        // our private method getWpHeader can now be invoked using the reflection class
        $html = $method->invoke($this->wolfnet);

        // Does the header have an opening <html> tag? it should
        $this->assertTrue(strpos($html,'<html') !== false);

        // and it should not have a closing html tag
        $this->assertFalse(strpos($html,'</html'));

    }


    /**
     * Test if we are generating the footer.
     */
    function testGetWpFooter()
    {
        $method = $this->wolfnet_reflection->getMethod("getWpFooter");
        $method->setAccessible(true);
        $html = $method->invoke($this->wolfnet);

        // should have closing html tag
        $this->assertTrue(strpos($html,'</html') !== false);
        
        //should not have opening tag
        $this->assertFalse(strpos($html,'<html'));     

    }


    function testProductKey()
    {
        $method = $this->wolfnet_reflection->getMethod("setJsonProductKey");
        $method->setAccessible(true);

        $productKeyOptionKey = $this->wolfnet_reflection->getProperty('productKeyOptionKey');
        $productKeyOptionKey->setAccessible(true);
        $key = $productKeyOptionKey->getValue($this->wolfnet);
        //$wolfnet_productKey->wolfnet->productKeyOptionKey;

        $key_json = $method->invoke( $this->wolfnet, $GLOBALS['wnt_tests_options']['api_key_good1'] );
        
        // echo "\n\nAs returned from setJsonProductKey: \n";
        // print_r($keyJson);
        // echo "\n\ndecoded\n";
        // print_r(json_decode($keyJson));

        // test setJsonProductKey. does it return valid Json?
        $this->assertNotNull( json_decode($key_json) );

        // save our wordpress option
        update_option( $key, $key_json );
        
        //  get it from wordpress this time
        $default_key = $this->wolfnet->getDefaultProductKey();

        // Does getDefaultProductKey() return the key we just set?
        $this->assertTrue( $default_key ==  $GLOBALS['wnt_tests_options']['api_key_good1'] );


        // now add a second valid key
        unset($key_json);

        // get the json from getProductKey() this time
        $key_json = $this->wolfnet->getProductKey();
        $key_info = json_decode($key_json);

        // set up the second key
        $key2_json = $method->invoke( $this->wolfnet, $GLOBALS['wnt_tests_options']['api_key_good2'] );
        $key2_info = json_decode($key2_json);

        // the second key should have an "id" of 2
        $key2_info[0]->id = 2;

        $key_info = array_merge($key_info, $key2_info);
        $key_json = json_encode($key_info);
        
        ////  this works
        ////  update_option( $key, '[{"id":"1","key":"wp_d1bf90d8bf9046d1d1c43f1fad34ec7d","label":"Test Key 1"},{"id":"2","key":"wp_c14aae17b230979d4f05ef82f26fdff9","label":"Test Key 2"}]');

        // save our wordpress option with 2 keys
        update_option( $key, $key_json );

        unset($key_json, $key_info, $key2_json, $key2_info);
        // now see if they are there:
        $key_info = json_decode($this->wolfnet->getProductKey());

        $this->assertTrue( $key_info[0]->key ==  $GLOBALS['wnt_tests_options']['api_key_good1'] );
        $this->assertTrue( $key_info[1]->key ==  $GLOBALS['wnt_tests_options']['api_key_good2'] );


        // and make sure getDefaultProductKey() still works with multiple keys
        unset($key_json);
        $key_json = $this->wolfnet->getDefaultProductKey();
        $this->assertTrue( $key_json ==  $GLOBALS['wnt_tests_options']['api_key_good1'] );


        // Can we getProductKeyById()?
        
        $this->assertTrue( $this->wolfnet->getProductKeyById(2) ==  $GLOBALS['wnt_tests_options']['api_key_good2'] );


    }


    /**
     * Test to see if we are getting the search manager form
     */
    function testSearchManagerHtml() {
        global $GLOBALS;

        // see if we get the seach form back. The form id: "wntsearchForm"
        $find_in_form = "wntsearchForm";

        $http = $this->wolfnet->searchManagerHtml($GLOBALS['wnt_tests_options']['api_key_good1']);
                
        $this->assertTrue(strpos($http['body'], $find_in_form) !== false);

    }
}

