<?php

namespace Source\Core;

use Source\Support\Message;

abstract class Model
{
    /** @var object|null */
    protected $data;

    /** @var \PDOException|null */
    protected $fail;

    /** @var Message|null */
    protected $message;
    
    /**
     * Model constructor
     */
    public function __construct()
    {
        $this->message = new Message();
    }
    
    /**
     * __set
     *
     * @param  mixed $name
     * @param  mixed $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @return null|object
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return null|\PDOException
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }
    
    /**
     * create
     *
     * @param  mixed $entity
     * @param  mixed $data
     * @return int
     */
    protected function create(string $entity, array $data): ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO {$entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }
    
    /**
     * read
     *
     * @param  mixed $select
     * @param  mixed $params
     * @return PDOStatement|null
     */
    protected function read(string $select, string $params = null): ?\PDOStatement
    {
        try {
            $stmt = Connect::getInstance()->prepare($select);
            if ($params) {
                parse_str($params, $params);
                foreach($params as $key => $value) {
                    if ($key == 'limit' || $key == 'offset') {
                        $stmt->bindValue(":{$key}", $value, \PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue(":{$key}", $value, \PDO::PARAM_STR);
                    }
                }
            }
            $stmt->execute();
            return $stmt;

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }
    
    /**
     * update
     *
     * @param  mixed $entity
     * @param  mixed $data
     * @param  mixed $terms
     * @param  mixed $params
     * @return int
     */
    protected function update(string $entity, array $data, string $terms, string $params): ?int
    {
        try {
            $dataSet = [];
            foreach($data as $bind => $value) {
                $dataSet[] = "{$bind} = :{$bind}";
            }

            $dataSet = implode(", ", $dataSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE {$entity} SET {$dataSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));

            return ($stmt->rowCount() ?? 1);

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }

        var_dump($entity, $data, $terms, $params);
    }
    
    /**
     * delete
     *
     * @param  mixed $entity
     * @param  mixed $terms
     * @param  mixed $params
     * @return int
     */
    protected function delete(string $entity, string $terms, string $params): ?int
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM {$entity} WHERE {$terms}");
            parse_str($params, $params);
            $stmt->execute($params);
            return ($stmt->rowCount() ?? 1);

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }
    
    /**
     * safe
     *
     * @return array
     */
    protected function safe(): ?array
    {
        $safe = (array)$this->data;
        foreach(static::$safe as $unset) {
            unset($safe[$unset]);
        }

        return $safe;
    }
    
    /**
     * filter
     *
     * @param  mixed $data
     * @return array
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
        }

        return $filter;
    }

    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach(static::$required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}