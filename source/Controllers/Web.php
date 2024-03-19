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
        echo $this->view->render('home', [
            'title' => 'CafeControl - Gerencie suas contas com o melhor café!'
        ]);
    }

    public function about()
    {
        echo '<h1>SOBRE</h1>';
    }

    public function error(array $data): void
    {
        echo $this->view->render('error', [
            'title' => "{$data['errcode']} | Ooops!"
        ]);
    }
}