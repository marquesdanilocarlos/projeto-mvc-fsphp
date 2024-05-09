<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Core\Message;
use Source\Models\Auth;

class App extends Controller
{
    public function __construct()
    {
        $pathToView = __DIR__ . '/../../themes/' . CONF_VIEW_THEME;
        parent::__construct($pathToView);

        if (!Auth::user()) {
            $this->message->warning('Efetue login para acessar o App.')->flash();
            redirect('/entrar');
        }
    }

    public function home(): void
    {
        echo flash();
        var_dump(Auth::user());
        echo "<a title='Sair' href='" . url('/app/sair') . "'>Sair</a>";
    }

    public function logout(): void
    {
        (new Message())->info('Logout realizado com sucesso!')->flash();
        Auth::logout();
        redirect('/entrar');
    }
}