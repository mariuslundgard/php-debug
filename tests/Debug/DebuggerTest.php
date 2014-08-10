<?php

namespace Debug;

use PHPUnit_Framework_TestCase as Base;

class DebuggerTest extends Base
{
    public function testCreate()
    {
        // $debugger = new Debugger('test');
        $debugger = Debugger::get('test');

        $this->assertInstanceOf('Debug\Debugger', $debugger);
    }

    public function testGetServer()
    {
        // $debugger = new Debugger('test');
        $debugger = Debugger::get('test');

        $this->assertInstanceOf('Socket\Server', $debugger->getServer());
    }

    public function testGetClient()
    {
        // $debugger = new Debugger('test');
        $debugger = Debugger::get('test');

        $this->assertInstanceOf('Socket\Client', $debugger->getClient());
    }
}
