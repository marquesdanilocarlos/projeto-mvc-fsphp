<?php

namespace Source\Core;

use PDO;
use PDOException;
use PDOStatement;
use \stdClass;

abstract class Model
{
    protected ?stdClass $data;
    protected ?PDOException $fail = null;
    protected ?Message $message;

    protected string $query = '';
    protected array $params = [];
    protected string $order = '';
    protected int $limit = 0;
    protected int $offset = 0;


    public function __construct()
    {
        $this->message = new Message();
    }


    public function __set(string $name, $value): void
    {
        if (empty($this->data)) {
            $this->data = new stdClass();
        }

        $this->data->{$name} = $value;
    }

    public function __get(string $name)
    {
        return $this->data->{$name} ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data->{$name});
    }


    public function getData(): ?stdClass
    {
        return $this->data;
    }

    public function getFail(): ?PDOException
    {
        return $this->fail;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function find(?string $terms = null, ?string $params = null, string $columns = '*'): self
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM " . static::$entity . " WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM " . static::$entity;
        return $this;
    }

    public function order(string $columnOrder): self
    {
        $this->order = "ORDER BY {$columnOrder}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    public function fetch(bool $all = false)
    {
        try {
            $query = $this->query;

            if ($this->order) {
                $query .= $this->order;
            }

            if ($this->limit) {
                $query .= $this->limit;
            }

            if ($this->offset) {
                $query .= $this->offset;
            }

            $stmt = Connection::getInstance()->prepare($query);
            $stmt->execute($this->params);



            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    public function count(string $key = 'id'): int
    {
        $stmt = Connection::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    protected function create(array $data): ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));
            $stmt = Connection::getInstance()->prepare(
                "INSERT INTO " . static::$entity . " ({$columns}) VALUES ({$values})"
            );
            $stmt->execute($this->filter($data));

            return Connection::getInstance()->lastInsertId();
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    protected function read(string $select, string $params = null): ?PDOStatement
    {
        try {
            $stmt = Connection::getInstance()->prepare($select);

            if ($params) {
                parse_str($params, $arrParams);
                foreach ($arrParams as $key => $value) {
                    if ($key === 'limit' || $key === 'offset') {
                        $stmt->bindValue(":{$key}", $value, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue(":{$key}", $value, PDO::PARAM_STR);
                    }
                }
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    protected function update(array $data, string $terms, string $params): ?int
    {
        try {
            $dataSet = [];

            foreach ($data as $bind => $value) {
                $dataSet[] = "{$bind} = :{$bind}";
            }

            $dataSet = implode(", ", $dataSet);
            parse_str($params, $arrParams);

            $stmt = Connection::getInstance()->prepare("UPDATE " . static::$entity . " SET {$dataSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $arrParams)));

            return $stmt->rowCount() ?? 1;
        } catch (PDOException $e) {
            $this->fail = $e;
            return null;
        }
    }

    public function delete(string $key, string $value): bool
    {
        try {
            $stmt = Connection::getInstance()->prepare("DELETE from " . static::$entity . " where {$key} = :key");
            $stmt->bindValue("key", $value, PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            $this->fail = $e;
            return false;
        }
    }

    protected function safe(): ?array
    {
        $safe = (array)$this->data;
        foreach (static::$safe as $unset) {
            unset($safe[$unset]);
        }

        return $safe;
    }

    private function filter(array $data): ?array
    {
        $filtered = [];
        foreach ($data as $key => $value) {
            $filtered[$key] = $value ? filter_var($value, FILTER_DEFAULT) : null;
        }

        return $filtered;
    }

    protected function required(): bool
    {
        $data = (array)$this->getData();
        foreach (static::$required as $required) {
            if (empty($data[$required])) {
                return false;
            }
        }
        return true;
    }
}