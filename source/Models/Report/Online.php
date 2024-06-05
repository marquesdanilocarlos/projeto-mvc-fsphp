<?php

namespace Source\Models\Report;

use Source\Core\Model;
use Source\Core\Session;

class Online extends Model
{
    public function __construct(
        private int $sessionTime = 20
    ) {
        parent::__construct('report_online', ['id'], ['ip', 'url', 'agent']);
    }

    public function findByActive(bool $count = false)
    {
        $online = $this->find("updated_at >= NOW() - INTERVAL {$this->sessionTime} MINUTE");

        if ($count) {
            return $online->count();
        }

        return $online->fetch(true);
    }

    public function report(bool $clear = true): self
    {
        $session = new Session();

        if (!$session->has('online')) {
            $this->user = $session->authUser ?? null;
            $this->url = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_SPECIAL_CHARS) ?? '/';
            $this->ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
            $this->agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');

            $this->save();
            $session->set('online', $this->id);
            return $this;
        }

        $onlineUser = $this->findById($session->online);

        if (!$onlineUser) {
            $session->unset('online');
            return $this;
        }

        $onlineUser->user = $session->authUser ?? null;
        $onlineUser->url = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_SPECIAL_CHARS) ?? '/';
        $onlineUser->pages += 1;
        $onlineUser->save();

        if ($clear) {
            $this->clear();
        }

        return $this;
    }


    public function clear(): void
    {
        $this->delete("updated_at <= NOW() - INTERVAL {$this->sessionTime} MINUTE", null);
    }

    public function save(): bool
    {
        $onlineId = !empty($this->id)
            ? $this->updateAccess()
            : $this->createAccess();

        if (!$onlineId) {
            return false;
        }

        $this->data = $this->findById($onlineId)->getData();
        return true;
    }

    public function createAccess(): ?int
    {
        $onlineId = $this->create($this->safe());

        if ($this->getFail()) {
            $this->message->error('Não foi possível cadastrar os dados de acesso.');
            return null;
        }

        return $onlineId;
    }

    public function updateAccess(): ?int
    {
        $onlineId = $this->id;
        $this->update($this->safe(), 'id = :id', "id={$onlineId}");

        if ($this->getFail()) {
            $this->message->error('Não foi possível atualizar os dados de acesso.');
            return null;
        }

        return $onlineId;
    }
}