<?php

namespace Source\Models;

use Source\Core\Email;
use Source\Core\Model;
use Source\Core\Session;
use Source\Core\View;

class Auth extends Model
{
    public function __construct()
    {
        parent::__construct('user', ['id'], ['email', 'password']);
    }

    public static function user(): ?User
    {
        $session = new Session();

        if (!$session->has('authUser')) {
            return null;
        }

        return (new User())->findById($session->authUser);
    }

    public static function logout(): void
    {
        $session = new Session();
        $session->unset('authUser');
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

    public function login(string $email, string $password, bool $save = false): bool
    {
        if (!isEmail($email)) {
            $this->message->warning('O e-mail informado não é válido.');
            return false;
        }

        setcookie('authEmail', '', time() - 3600, '/');

        if ($save) {
            setcookie('authEmail', $email, time() + 604800, '/');
        }

        if (!isPassword($password)) {
            $this->message->warning('A senha informada não é válida.');
            return false;
        }

        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->error('O usuário informado não está cadastrado.');
            return false;
        }

        if (!passwdVerify($password, $user->password)) {
            $this->message->error('A senha informada não confere.');
            return false;
        }

        if (passwdRehash($user->password)) {
            $user->password = $password;
            $user->save();
        }

        (new Session())->set('authUser', $user->id);
        $this->message->success('Login efetuado com sucesso!')->flash();
        return true;
    }

    public function recover(string $email): bool
    {
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->warning('O e-mail informado não está cadastrado.');
            return false;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();

        $view = new View(__DIR__ . '/../../views/email');
        $message = $view->render('forget', [
            'first_name' => $user->first_name,
            'forget_link' => url("/recuperar/{$user->email}|{$user->forget}")
        ]);

        (new Email())->bootstrap(
            'Recupere sua senha no ' . CONF_SITE_NAME,
            $message,
            $user->email,
            "{$user->first_name} {$user->last_name}"
        )->send();

        return true;
    }

    public function reset(string $email, string $code, string $password, string $passwordRepeat): bool
    {
        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->warning('A conta para recuperação não foi encontrada.');
            return false;
        }

        if ($user->forget != $code) {
            $this->message->error('Desculpe, mas o código de verificação não é valido');
            return false;
        }

        if (!isPassword($password)) {
            $min = CONF_PASS_MIN_LENGTH;
            $max = CONF_PASS_MAX_LENGTH;
            $this->message->warning("Sua senha deve possuir {$min} e {$max} caracteres.");
            return false;
        }

        if ($password != $passwordRepeat) {
            $this->message->warning('As senhas informadas não conferem.');
            return false;
        }

        $user->password = $password;
        $user->forget = null;
        $user->save();
        return true;
    }
}