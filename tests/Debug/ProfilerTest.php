<?php

namespace Debug;

use PHPUnit_Framework_TestCase as Base;

class ProfilerTest extends Base
{
    public function testCreate()
    {
        $process = new Profiler();

        $this->assertInstanceOf('Debug\Profiler', $process);
    }
}
