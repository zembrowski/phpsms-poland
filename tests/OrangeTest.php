<?php

namespace zembrowski\SMS\Tests;

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

    public function testURL()
    {
        $this->assertEquals('https://www.orange.pl/', $this->orange->url);
    }

    // used when zembrowski\SMS\Orange::free private
    /**
     * @covers Orange::free
     */
    public function testFree()
    {
        $method = new \ReflectionMethod('zembrowski\SMS\Orange', 'free');

        $method->setAccessible(TRUE);

        $this->assertFalse($method->invoke(new zembrowski\SMS\Orange));
    }

    // used when zembrowski\SMS\Orange::free public
    /*
    public function testFree()
    {
        $orange = new zembrowski\SMS\Orange();

        $this->assertFalse($this->orange->free(null));
    }
    */
}
