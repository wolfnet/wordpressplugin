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
        // to access private methods

        //  First we need to create a ReflectionClass object
        //  passing in the class name as a variable    
        $reflection_class = new ReflectionClass("wolfnet");
        
        // Then we need to get the method we wish to test and
        // make it accessible
        $method = $reflection_class->getMethod("getWpHeader");
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
        $reflection_class = new ReflectionClass("wolfnet");
        $method = $reflection_class->getMethod("getWpFooter");
        $method->setAccessible(true);
        $html = $method->invoke($this->wolfnet);

        // should have closing html tag
        $this->assertTrue(strpos($html,'</html') !== false);
        
        //should not have opening tag
        $this->assertFalse(strpos($html,'<html'));     

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

