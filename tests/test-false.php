<?php
// a very simple example test.
// 
class Test_Test extends WP_UnitTestCase 
{
    /**
     * Performs wordpres setup and pre cleanup. add to this method as needed. 
     */
    function setUp() {
        parent::setUp();
    }

    /**
     * Cleans up after you, removeing any options, posts, etc. you may have added. add to as needed.
     */
    function tearDown() {
        parent::tearDown();
    }

    /**
     * An example test.
     *
     * We just want to make sure that false is still false.
     */
    function test_false_is_false() {
 
        $this->assertFalse( false );
    }


}