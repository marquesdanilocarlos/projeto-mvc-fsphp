<?php

namespace Source\Models;

use PHPMailer\PHPMailer\PHPMailer;
use Source\Core\Email;
use Source\Core\Message;
use Source\Core\Model;
use Source\Core\View;

class Auth extends Model
{
    public function __construct()
    {
        parent::__construct('user', ['id'], ['email', 'password']);
    }

    public function register(User $user): bool
    {
        if (!$user->save()) {
            $this->message = $user->message;
            return false;
        }

        $view = new View(__DIR__ . '/../../views/email');
        $message = $view->render('confirm', [
            'first_name' => $user->first_name,
            'confirm_link' => url('/obrigado/' . base64_encode($user->email))
        ]);
        $email = new Email();
        $email->bootstrap(
            'Ative sua conta no ' . CONF_SITE_NAME,
            $message,
            $user->email,
            "{$user->first_name} {$user->last_name}"
        );
        $email->send();
        return true;
    }
}