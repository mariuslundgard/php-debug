<?php

putenv('DEBUG=1');

require_once __DIR__.'/../../vendor/autoload.php';

use Debug\DebuggableTrait;

class DebuggableExample
{
    use DebuggableTrait;
}

$obj = new DebuggableExample;

$obj->getDebugger([
    'color' => 'green',
]);

$obj->d('testing 1', 'testing 2');

$obj->d($_SERVER);

$obj->d($obj);

$obj->d(
    'A REALLY LONG DEBUG MESSAGE: '
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
    .print_r($_SERVER, 1)
);

d("YEPPP!!!");
d("YEPPP!!!");
