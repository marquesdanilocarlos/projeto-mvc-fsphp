<?php

namespace Source\Models;

use Source\Core\Model;

class User extends Model
{
    public function __construct()
    {
        parent::__construct("users", ["id"], ["first_name", "last_name", "email", "password"]);
    }

    public function bootstrap(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $document = null
    ) {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->document = $document;

        return $this;
    }

    public function findByEmail(string $email, string $columns = "*"): ?self
    {
        $find = $this->find("email=:email", "email={$email}", $columns);
        return $find->fetch();
    }

    public function save(): bool
    {
        if (!empty($this->id)) {
            $userId = $this->atualize($this->id);
        }

        if (empty($this->id)) {
            $userId = $this->insert();
        }

        if (!$userId) {
            return false;
        }

        $this->data = ($this->findById($userId))->getData();
        return true;
    }

    private function atualize(int $userId): bool
    {
        $email = $this->find("email = :email AND id != :id", "email={$this->email}&id={$userId}", "id")->fetch();


        if ($email) {
            $this->message->warning("O e-mail informado já está cadastrado.");
            return false;
        }

        $this->update($this->safe(), "id = :id", "id={$userId}");

        if ($this->getFail()) {
            $this->message->error("Erro ao cadastrar, verifique os dados");
            return false;
        }

        $this->message->success("Dados atualizados com sucesso!");
        return $userId;
    }

    private function insert(): bool
    {
        if (!$this->required()) {
            $this->message->warning('Nome, sobrenome, email e senha são obrigatórios');
            return false;
        }

        if (!$this->validateEmail()) {
            $this->message->warning('E-mail inválido');
            return false;
        }

        if (!isPassword($this->password)) {
            $this->message->warning(
                'A senha deve ter entre ' . CONF_PASS_MIN_LENGTH . ' e ' . CONF_PASS_MAX_LENGTH . ' caracteres.'
            );
            return false;
        }

        if ($this->findByEmail($this->email, "id")) {
            $this->message->warning("O e-mail informado já está cadastrado.");
            return false;
        }

        $userId = $this->create($this->safe());
        if ($this->getFail()) {
            $this->message->error("Erro ao cadastrar, verifique os dados");
            return false;
        }
        $this->message->success("Cadastro realizado com sucesso!");

        return $userId;
    }

    private function validateEmail(): bool
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->message = message()->warning("O e-mail informado não é válido!");
            return false;
        }

        return true;
    }
}