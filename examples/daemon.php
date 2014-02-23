<?php

error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';

$process = Core\Process::get();


//
$server = Debug\Debugger::getServer();


// throws exception
// $process->on('kill', function () use ($server) {
//     $server->send('kill server');
//     $server->close();
// });

// $process->on('hangup', function () use ($process) {
//     echo 'HANGUP (pid #'.$process->getPid().')' . PHP_EOL;
// });

$process->on('shutdown', function () use ($server) {
    $server->send('shutdown server');
    $server->close();
});

// foreach ($process->getGroups() as $group) {
//     print_r($group->getInfo());
// }

$lastTime = float_microtime();
$num = 1;

echo 'Listening for debug messages...' . PHP_EOL;

do {
    if ($packet = $server->receive()) {
        $msDuration = number_format(float_microtime() - $lastTime, 3);

        $packet = explode(':', $packet);
        $domain = array_shift($packet);
        $color = array_shift($packet);
        $message = implode(':', $packet);

        echo '  ';

        // debug domain
        echo str_cli_color($domain, $color);
        echo ' ';

        // num
        // echo ($num++) . ':';
        // echo ' ';

        // duration
        echo str_cli_color('+' . $msDuration . 's', $color);
        echo ' ';

        // message
        echo str_cli_color($message, 'light_gray');
        echo ' ';
        echo '';

        echo PHP_EOL;

        $lastTime = float_microtime();
    }
}

while ($packet);

function str_cli_color($str, $fgColor = null, $bgColor = null)
{
    $cliFgColors = [
        'black' => '0;30',
        'dark_gray' => '1;30',

        'red' => '0;31',
        'light_red' => '1;31',

        'green' => '0;32',
        'light_green' => '1;32',

        'blue' => '0;34',
        'light_blue' => '1;34',

        'purple' => '0;35',
        'light_purple' => '1;35',

        'cyan' => '0;36',
        'light_cyan' => '1;36',

        'brown' => '0;33',
        'yellow' => '1;33',

        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    $cliBgColors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    $colorStr = '';

    if (isset($cliFgColors[$fgColor])) {
        $colorStr .= "\033[" . $cliFgColors[$fgColor] . "m";
    }

    if (isset($cliBgColors[$bgColor])) {
        $colorStr .= "\033[" . $cliBgColors[$bgColor] . "m";
    }

    if (strlen($colorStr)) {
        return $colorStr . $str . "\033[0m";
    }

    return $str;
}
