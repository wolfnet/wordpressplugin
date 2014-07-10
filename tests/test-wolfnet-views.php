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
        $this->wolfnet = $GLOBALS['wolfnet'];

        // // protected reflection class needed
        // $key = $this->wolfnet->setJsonProductKey($GLOBALS['wnt_tests_options']['api_key_good1']);
        // $option = $wolfnet_productKey->wolfnet->productKeyOptionKey;
        // update_option($key);
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
        // $html = $this->wolfnet->views->amSettingsPage();
        // echo $html;
    }

}