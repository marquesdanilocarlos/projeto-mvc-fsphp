<?php
ob_start();

include __DIR__ . '/vendor/autoload.php';

$session = new \Source\Core\Session();
$router = new \CoffeeCode\Router\Router(url(), '@');


/* *
 * WEB
 * */
$router->namespace('Source\Controllers');
$router->get('/', 'Web@home');
$router->get('/sobre', 'Web@about');

/* *
 * ERROR
 * */
$router
    ->namespace('Source\Controllers')
    ->group('/ooops');

$router->get('/{errcode}', 'Web@error');

$router->dispatch();

if ($router->error()) {
    $router->redirect("/ooops/{$router->error()}");
}


ob_end_flush();