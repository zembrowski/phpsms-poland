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
     * @covers ::free
     */
    public function testFree()
    {
        $method = new \ReflectionMethod('\zembrowski\SMS\Orange', 'free');

        $method->setAccessible(TRUE);

        $this->assertFalse($method->invokeArgs(new \zembrowski\SMS\Orange(), array(null)));
    }

}
