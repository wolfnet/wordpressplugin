<?php
/**
 * Unit tests for the Wolfnet_Api functions WolfNet IDX for WordPress plugin
 */

class Test_Wolfnet_Api extends WP_UnitTestCase 
{
    function setUp() {
        parent::setUp();
        $this->wolfnet = $GLOBALS['wolfnet'];
    }


    function tearDown() 
    {
        parent::tearDown();
    }


    function testTransientIndex() {
        
        $data = $this->wolfnet->api->transientIndex();
        // an epty array at this point
        //print_r($data);
        $this->assertTrue( is_array( $data ) );


        // transient index is generated from an encoded url
    
        $url = 'http://just.atest.com/v1/?key=value&something=somethingelse';
        
        // set some transients
        for($i = 1; $i <= 10; $i++) {
            $key = 'wolfnet_' . md5($url . $i);
            $time = time();
            $data[$key] = $time;
            $data_new1 = $this->wolfnet->api->transientIndex($data);
            //$data_history[$i] = $this->wolfnet->api->transientIndex();
        }
        //print_r($data_history);
        // $data_new1 returned from method arrray with at least 10 items
        $this->assertTrue( count($data_new1) >= 10 );
        //echo "\n\n";
        // also see if we can get it when not adding to it.
        $data_new2 = $this->wolfnet->api->transientIndex();
        //print_r($data_new2);
        $this->assertTrue( count($data_new2) >= 10 );
    }


}