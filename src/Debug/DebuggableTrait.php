<?php

namespace Debug;

trait DebuggableTrait
{
    protected $debugger;
    // protected $dColor;
    // protected $dPort;
    // protected $dOutput;

    public function d()
    {
        call_user_func_array([$this->getDebugger(), 'debug'], func_get_args());
    }

    public function dColor($color)
    {
        $this->dColor = $color;
    }

    public function dPort($port)
    {
        $this->dPort = $port;
    }

    public function dOutput($output)
    {
        $this->dOutput = $output;
    }

    public function getDebugger(array $config = [])
    {
        if (null === $this->debugger) {
            $this->debugger = Debugger::get(get_class($this), $config);
        }

        return $this->debugger;
    }
}
