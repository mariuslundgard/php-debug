<?php

namespace Debug;

trait DebuggableTrait
{
    protected $debugger;

    public function getDebugger(array $config = array())
    {
        if (null === $this->debugger) {
            $this->debugger = Debugger::get(get_class($this).'#'.spl_object_hash($this), $config);
        }

        return $this->debugger;
    }

    public function d()
    {
        call_user_func_array([$this->getDebugger(), 'debug'], func_get_args());
    }
}
