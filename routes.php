<?php

$router = new \CoffeeCode\Router\Router(url(), '@');

/* *
 * WEB
 * */
$router->namespace('Source\Controllers');
$router->get('/', 'Web@home');
$router->get('/sobre', 'Web@about');

//Blog
$router->group('/blog');
$router->get('/', 'Web@blog');
$router->get('/page/{page}', 'Web@blog');
$router->get('/{uri}', 'Web@blogPost');
$router->post('/buscar', 'Web@blogSearch');
$router->get('/buscar/{terms}/{page}', 'Web@blogSearch');
$router->get('/categoria/{category}/', 'Web@blogCategory');
$router->get('/categoria/{category}/{page}', 'Web@blogCategory');


//Auth
$router->group(null);
$router->get('/entrar', 'Web@login');
$router->post('/entrar', 'Web@login');

$router->get('/cadastrar', 'Web@register');
$router->post('/cadastrar', 'Web@register');

$router->get('/recuperar', 'Web@recover');
$router->post('/recuperar', 'Web@recover');

$router->get('/recuperar/{code}', 'Web@reset');
$router->post('/recuperar/resetar', 'Web@reset');


//Opt
$router->get('/confirma', 'Web@confirm');
$router->get('/obrigado/{email}', 'Web@success');

//Terms
$router->get('/termos', 'Web@terms');

/**
 * APP
 */
$router->group('/app');
$router->get('/', 'App@home');
$router->get('/sair', 'App@logout');

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