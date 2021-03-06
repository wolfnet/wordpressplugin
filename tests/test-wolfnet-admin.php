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
     * Did our setup work?
     */
    function testIsAdmin()
    {
        // is_admin true only if admin screen is being displayed.
        $this->assertTrue( is_admin(), "We should be on an admin screen and logged in. We are not." ) ;
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
            array('mce_external_plugins', 'sbMcePlugin'),
            array('mce_buttons',          'sbButton'),
            );
        $this->wolfnet->admin->__construct($this->wolfnet);


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
            $msg = "The filter '$filter[0]' with callable '$filter[1]' does not appear to be in place.";
            $this->assertTrue($found, $msg);
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
            array('admin_menu',            'adminMenu'),
            array('admin_init',            'adminInit'),
            array('admin_enqueue_scripts', 'adminScripts'),
            array('admin_enqueue_scripts', 'adminStyles'),
            array('admin_print_styles',    'adminPrintStyles'),
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
            // uncoment to see which one failed
            // echo "\n" . $action[0] . " " . $found; ;
            $msg = "The action '$action[0]' with callable '$action[1]' does not appear to be in place.";
            $this->assertTrue($found, $msg);
        }


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
            $msg = "The script '$script' is not enqueued.";
            $this->assertTrue( wp_script_is( $script, 'enqueued' ), $msg );
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
            $msg = "The style '$style' is not enqueued";
            $this->assertTrue( wp_style_is( $style, 'enqueued' ), $msg );
        }
    }


    /**
     * Does the transient index get removed on deactivation?
     */
    function testDeactivate() {

        // preform a query to set some transients

        $data = $this->wolfnet->apin->sendRequest($GLOBALS['wnt_tests_options']['api_key_good1'], '/listing');

        global $wpdb;
        $sql = 'SELECT option_name FROM wptests_options WHERE option_name LIKE "_transient_timeout_wnt_tran_%" OR option_name LIKE "_transient_wnt_tran_%"';

        $listings = $wpdb->get_col( $sql );

        $msg = 'There should be Transients after activation';
        $this->assertTrue( (count($listings) > 0), $msg );


        $this->wolfnet->wolfnetDeactivation();

        $listings = $wpdb->get_col( $sql );

        $msg = 'There should be no transients after deactivation';
        $this->assertTrue( (count($listings) == 0), $msg );


    }



}
