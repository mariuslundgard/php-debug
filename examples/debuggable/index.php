<?php

putenv('DEBUG=1');

require_once __DIR__.'/../../vendor/autoload.php';

use Debug\DebuggableTrait;
use Event\Emitter;

class DebuggableExample extends Emitter
{
    use DebuggableTrait;

    public function __construct()
    {
        parent::__construct($this);
    }
}

$obj = new DebuggableExample;

$obj->getDebugger([
    'color' => 'green',
]);

$obj->d('testing');

$obj->on('foo', function() use ($obj) {
    $obj->d('`foo` was triggered');
});

$obj->trigger('foo');

var_dump($obj);
