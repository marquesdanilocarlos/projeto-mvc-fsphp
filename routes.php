<?php

$router = new \CoffeeCode\Router\Router(url(), '@');

/* *
 * WEB
 * */
$router->namespace('Source\Controllers');
$router->get('/', 'Web@home');
$router->get('/sobre', 'Web@about');

//Blog
$router->get('/blog', 'Web@blog');
$router->get('/blog/{postName}', 'Web@blogPost');

//Auth
$router->get('/entrar', 'Web@login');
$router->get('/recuperar', 'Web@recover');
$router->get('/cadastrar', 'Web@register');

//opt
$router->get('/confirma', 'Web@confirm');
$router->get('/sucesso', 'Web@success');

//Terms

$router->get('/termos', 'Web@terms');

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