<?php
/**
 * Unit tests for the Wolfnet_Admin functions WolfNet IDX for WordPress plugin
 */

class Test_Wolfnet_Admin extends WP_UnitTestCase 
{
    function setUp() 
    {
        parent::setUp();
        wp_set_current_user( $this->factory->user->create( array( 'role' => 'administrator' ) ) );
        set_current_screen( 'index.php' );
                
        // a new instance with active admin user
        $this->wolfnet = new Wolfnet();
    }


    function tearDown() 
    {
        parent::tearDown();
    }


    /**
     * Did out setup work?
     */
    function testIsAdmin()
    { 
        // is_admin true only if admin screen is being displayed.        
        $this->assertTrue( is_admin() ) ;
    }

    function testFilters() {
        // filters to check
        $filters = array(
            array('mce_external_plugins', 'sbMcePlugin'),
            array('mce_buttons',          'sbButton'),
            );
        $this->wolfnet->admin->__construct($this->wolfnet); 
        // global $wp_filters;
        // echo "Hello";
        // print_r($wp_filters);

        // foreach ($filters as $filter) {
        //     $this->assertTrue( has_filter( $filter[0], array( &$this, $filter[1] ) !== false ) );
        // }

    }

    /**
     * Are the Admin scripts being enqueued?
     */
    function testScripts() 
    {
        //$GLOBALS['wolfnet']->scripts();
        $this->wolfnet->admin->adminScripts(); 
        $scripts = array(
            'wolfnet-admin',
            'wolfnet-shortcode-builder',
            );

        foreach ($scripts as $script) {
            $this->assertTrue( wp_script_is( $script, 'enqueued' ) );
        }
    
    }

    /**
     * Are the Admin styles being enqueued?
     */
    function testStyles() 
    {
        $this->wolfnet->admin->adminStyles();
        $styles = array(
            'jquery-ui',
            'wolfnet-admin',
            );
        foreach ($styles as $style) {
            $this->assertTrue( wp_style_is( $style, 'enqueued' ) );
        }
    }


    /**
     * Does the transient index get removed on deactivation?
     */
    function testDeactivate() {

        // first set some index then make sure they are removed on deactivate
        $url = 'http://just.atest.com/v1/?key=value&something=somethingelse';
        
        // set some transients
        for($i = 1; $i <= 10; $i++) {
            $key = 'wolfnet_' . md5($url . $i);
            $time = time();
            $data[$key] = $time;
            $data_new1 = $this->wolfnet->api->transientIndex($data);
        }

        
        $data = $this->wolfnet->api->transientIndex();

        // we should have ten indexes in $data array
        $this->assertTrue( count($data) >= 10 );
       
        // remove these on deactivate
        $this->wolfnet->admin->deactivate();

        // there should be no index now
        $data = $this->wolfnet->api->transientIndex();
        $this->assertTrue( (count($data) == 0) );

    }


    
}