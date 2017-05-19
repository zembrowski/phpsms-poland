<?php

/**
 * @coversDefaultClass \zembrowski\SMS\Orange
 */

class OrangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var orange
     */
    private $orange;

    public function __construct()
    {
        $this->orange = new \zembrowski\SMS\Orange();
    }

    /**
     * @coversNothing
     */
    public function testURL()
    {
        $this->assertEquals('https://www.orange.pl', $this->orange->url);
    }

    /**
     * @covers ::checkRemaining
     */
    /*public function testCheckRemaining()
    {
        $method = new \ReflectionMethod('\zembrowski\SMS\Orange', 'checkRemaining');
        $method->setAccessible(TRUE);

        $this->assertArrayHasKey('found', $method->invokeArgs(new \zembrowski\SMS\Orange(), array(null)));
    }*/

    /**
     * @covers ::remaining
     */
    /*public function testRemaining()
    {
        $method = new \ReflectionMethod('\zembrowski\SMS\Orange', 'remaining');

        $method->setAccessible(TRUE);

        $this->assertFalse($method->invokeArgs(new \zembrowski\SMS\Orange(), array(null)));
    }*/

}
