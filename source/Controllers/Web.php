<?php

namespace Source\Controllers;

use Source\Core\Controller;

class Web extends Controller
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");
    }

    public function home()
    {
        echo '<h1>HOME</h1>';
    }

    public function about()
    {
        echo '<h1>SOBRE</h1>';
    }

    public function error(array $data):void
    {
        echo "<h1>ERROR {$data['errcode']}</h1>";
    }
}