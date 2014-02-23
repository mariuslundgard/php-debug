<?php

require __DIR__.'/../vendor/autoload.php';

// $liveDebug = Debug\Debugger::get('live', [
//     'output' => false,
//     'color' => 'blue',
// ]);

// $liveDebug('This doesn\'t show in the browser');

echo '<h1>Example</h1>';

debug('test');



// debug(new Debug\Profiler, 'sss');

// echo '<pre>';
// echo dump($d);

// $message = new Debug\Message('test message');

// $message->send();

// $d($message);

//


//
// $startTime = time();

// do {
//     $packet = $client->receive();

//     print_r($packet) . PHP_EOL;
// }

// while ($packet);