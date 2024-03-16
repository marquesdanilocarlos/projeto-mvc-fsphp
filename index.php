<?php
ob_start();

include __DIR__ . '/vendor/autoload.php';

$session = new \Source\Core\Session();
$router = new \CoffeeCode\Router\Router(url(), '@');


ob_end_flush();