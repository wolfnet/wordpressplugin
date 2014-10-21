<?php
/**
 * Unit tests for the Wolfnet_Views functions WolfNet IDX for WordPress plugin
 */

class Test_Wolfnet_Views extends WP_UnitTestCase 
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

        // we need to be admin to test the admin views
        wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
        set_current_screen( 'index.php' );

        // a new instance with active admin user
        $this->wolfnet = new Wolfnet();

        // // protected reflection class needed
        // $key = $this->wolfnet->setJsonProductKey($GLOBALS['wnt_tests_options']['api_key_good1']);
        // $option = $wolfnet_productKey->wolfnet->productKeyOptionKey;
        // update_option($key);
         
        // set a product key in the wordpress setting. 
        // there is a test for this in test-wolfnet.php
        $this->wolfnet_reflection = new ReflectionClass("wolfnet");
        $method = $this->wolfnet_reflection->getMethod("setJsonProductKey");
        $method->setAccessible(true);
        $productKeyOptionKey = $this->wolfnet_reflection->getProperty('productKeyOptionKey');
        $productKeyOptionKey->setAccessible(true);
        $key = $productKeyOptionKey->getValue($this->wolfnet);
        $key_json = $method->invoke( $this->wolfnet, $GLOBALS['wnt_tests_options']['api_key_good1'] );
        update_option( $key, $key_json );

        // this will match any <tag>. 
        $this->wnt_html_regex = '/<[^>]*>/';
        $this->wnt_html_msg = "Method returned nothing that looks like an HTML tag";

        

    }

    
    /**
     * Cleans up after tests, removing any options, posts, etc. we may have added as part of a test.
     */
    function tearDown() 
    {
        parent::tearDown();
    }


    function testEmpty(){
        // an empty test to prevent "no tests" warning
        $this->assertTrue(true);
    }

    function  testAmStettingsPage(){

        ob_start();
        $this->wolfnet->views->amSettingsPage();
        $html = ob_get_clean();
        
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }



    function  testAmEditCssPage(){
            
        ob_start();
        $this->wolfnet->views->amEditCssPage();
        $html = ob_get_clean();
        
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


    function  testAmSearchManagerPage(){

        ob_start();
        $this->wolfnet->views->amSearchManagerPage();
        $html = ob_get_clean();
        
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


    function  testAmSupportPage(){

        ob_start();
        $this->wolfnet->views->amSearchManagerPage();
        $html = ob_get_clean();
        
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }

    function  testFeaturedListingsOptionsFormView(){

        $args = $this->wolfnet->getFeaturedListingsOptions();
        $html = $this->wolfnet->views->featuredListingsOptionsFormView($args);
        
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


    function  testListingGridOptionsFormView(){
        $args = $this->wolfnet->getListingGridOptions();
        $html = $this->wolfnet->views->listingGridOptionsFormView( $args );
        
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }

   function  testFeaturedListingView(){

        // This view is tested indirectly though because of all the setup that happens in 
        // wolfnet->featuredListings

        // $html = $this->wolfnet->views->featuredListingView();
        $defaultAttributes = $this->wolfnet->getFeaturedListingsDefaults();
        
        $html = $this->wolfnet->featuredListings($defaultAttributes);
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }

    
    function  testPropertyListView(){

        // This view is tested indirectly though because of all the setup that happens in 
        // wolfnet->propertyList

        $defaultAttributes = $this->wolfnet->getPropertyListDefaults();
        
        // $html = $this->wolfnet->propertyList($defaultAttributes);
        $html = $this->wolfnet->listingGrid($criteria, 'list');
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


    function  testListingGridView(){

        // This view is tested indirectly though because of all the setup that happens in 
        // wolfnet->listingGrid

        $defaultAttributes = $this->wolfnet->getListingGridDefaults();
        $criteria = $this->wolfnet->getOptions($defaultAttributes);

        $html = $this->wolfnet->listingGrid($criteria);

        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


    function  testQuickSearchView(){
        // called indirectly
        $defaultAttributes = $this->wolfnet->getQuickSearchDefaults();
        $html = $this->wolfnet->quickSearch($defaultAttributes);
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


    function  testMapView()
    {

        $method = $this->wolfnet_reflection->getMethod("augmentListingData");
        $method->setAccessible(true);

        $criteria = $this->wolfnet->getListingGridDefaults();
        $criteria['keyid'] = 1;
        $criteria['numrows'] = 5;
        $criteria['startrow'] = 1;
        $listingsData = $this->wolfnet->api->getListings($criteria);

        foreach ($listingsData as &$listing) {
            $method->invoke( $this->wolfnet, $listing );
        }

        $html = $this->wolfnet->views->mapView($listingsData, $GLOBALS['wnt_tests_options']['api_key_good1'] );
        $this->assertRegExp($this->wnt_html_regex, $html, $this->wnt_html_msg);

    }


}