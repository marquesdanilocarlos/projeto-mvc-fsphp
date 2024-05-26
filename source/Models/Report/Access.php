<?php

namespace Source\Models\Report;

use Source\Core\Model;
use Source\Core\Session;

class Access extends Model
{
    public function __construct()
    {
        parent::__construct('report_access', ['id'], ['users', 'views', 'pages']);
    }

    public function report(): self
    {
        $existentAccess = $this->find('DATE(created_at) = DATE(now())', '')->fetch();
        $session = new Session();

        if (!$existentAccess) {
            $this->users = 1;
            $this->views = 1;
            $this->pages = 1;

            $session->set('access', true);
            setcookie('access', true, time() + (86400), "/");

            $this->save();
            return $this;
        }

        if (!filter_input(INPUT_COOKIE, 'access')) {
            $existentAccess->users += 1;
            setcookie('access', true, time() + (86400), "/");
        }

        if (!$session->has('access')) {
            $existentAccess->views += 1;
            $session->set('access', true);
        }

        $existentAccess->pages += 1;
        $existentAccess->save();

        return $this;
    }

    public function save(): bool
    {
        $accessId = !empty($this->id)
            ? $this->updateAccess()
            : $this->createAccess();

        if (!$accessId) {
            return false;
        }

        $this->data = $this->findById($accessId)->getData();
        return true;
    }

    public function createAccess(): ?int
    {
        $accessId = $this->create($this->safe());

        if ($this->getFail()) {
            $this->message->error('Não foi possível cadastrar os dados de acesso.');
            return null;
        }

        return $accessId;
    }

    public function updateAccess(): ?int
    {
        $accessId = $this->id;
        $this->update($this->safe(), 'id = :id', "id={$accessId}");

        if ($this->getFail()) {
            $this->message->error('Não foi possível atualizar os dados de acesso.');
            return null;
        }

        return $accessId;
    }
}