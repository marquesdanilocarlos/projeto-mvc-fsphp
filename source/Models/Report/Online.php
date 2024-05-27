<?php

namespace Source\Models\Report;

use Source\Core\Model;
use Source\Core\Session;

class Online extends Model
{
    public function __construct(
        private int $sessionTime = 20
    )
    {
        parent::__construct('report_online', ['id'], ['ip', 'url', 'agent']);
    }

    public function findByActive(bool $count = false)
    {

    }

    public function report(): self
    {

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