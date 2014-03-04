<?php

putenv('DEBUG=1');

require_once __DIR__.'/../../vendor/autoload.php';

?>

<h1>Examples</h1>

<img src="terminal.png">

<?php
// d($_SERVER);

d('A');

dd('B');

d('C');

?>
