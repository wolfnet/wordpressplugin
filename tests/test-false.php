<?php
// a very simple example test. Just to make sure the testing framework is working.

class Test_Test extends WP_UnitTestCase 
{
    /**
     * An example test to demonstrate a simple test.
     *
     * We just want to make sure that false is still false.
     */
    function test_false_is_false() {
        $msg = "false is not false. Wait, what?"; 
        $this->assertFalse( false, $msg );
    }


}