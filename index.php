<?php
ob_start();

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/routes.php';

$session = new \Source\Core\Session();

ob_end_flush();