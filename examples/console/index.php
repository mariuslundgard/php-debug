<?php

putenv('DEBUG=1');

require_once __DIR__.'/../../vendor/autoload.php';

?>

<h1>Console</h1>

<p>Open the Terminal and start the debug monitor:</p>

<pre>$ vendor/bin/pdebug</pre>

<img src="terminal.png">

<?php
// d($_SERVER);

d('A');

dd('B');

d('C');

d($_SERVER);

d(new Debug\Debugger('foo'));

?>
