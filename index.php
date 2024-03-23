<?php

use Source\Support\Minify;

ob_start();


include __DIR__ . '/vendor/autoload.php';

if (strpos(url(), ".local")) {
    $minify = new Minify();
    $minify->minifyCss();
    $minify->minifyJs();
}

include __DIR__ . '/routes.php';

$session = new \Source\Core\Session();

ob_end_flush();