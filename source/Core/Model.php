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

    /** @var string */
    protected $query;

    /** @var string */
    protected $params;

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var string $entity database table */
    protected static $entity;

    /** @var array $protected no update or create */
    protected static $protected;

    /** @var array $entity database table */
    protected static $required;
    
    /**
     * Model constructor
     */
    public function __construct(string $entity, array $protected, array $required)
    {
        self::$entity = $entity;
        self::$protected = array_merge($protected, ['created_at', "updated_at"]);
        self::$required = $required;
        
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
     * find
     *
     * @param  mixed $terms
     * @param  mixed $params
     * @param  mixed $columns
     * @return Model|mixed
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*")
    {
        if ($terms) {
            $this->query = "SELECT {$columns} FROM " . static::$entity . " WHERE {$terms}";
            parse_str($params, $this->params);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM " . static::$entity;
        return $this;
    }

     /**
     * findById
     *
     * @param  mixed $id
     * @param  mixed $columns
     * @return null|mixed|Model
     */
    public function findById(int $id, string $columns = "*"): ?Model
    {
        $find = $this->find("id = :id", "id={$id}", $columns);
        return $find->fetch();
    }
    
    /**
     * order
     *
     * @param  mixed $columnOrder
     * @return Model
     */
    public function order(string $columnOrder): Model
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }
    
    /**
     * limit
     *
     * @param  mixed $limit
     * @return Model
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }
    
    /**
     * offset
     *
     * @param  mixed $offset
     * @return Model
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }
    
    /**
     * fetch
     *
     * @param  bool $all
     * @return null|array|mixed|Model
     */
    public function fetch(bool $all = false)
    {
        try {
            $stmt = Connect::getInstance()->prepare($this->query . $this->order . $this->limit . $this->offset);
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }

    public function count(string $key): int
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();   
    }
    
    /**
     * create
     *
     * @param  mixed $data
     * @return int
     */
    protected function create(array $data): ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO " . static::$entity . " ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }
    
    
    /**
     * update
     *
     * @param  mixed $data
     * @param  mixed $terms
     * @param  mixed $params
     * @return int
     */
    protected function update(array $data, string $terms, string $params): ?int
    {
        try {
            $dataSet = [];
            foreach($data as $bind => $value) {
                $dataSet[] = "{$bind} = :{$bind}";
            }

            $dataSet = implode(", ", $dataSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE " . static::$entity . " SET {$dataSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));

            return ($stmt->rowCount() ?? 1);

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return null;
        }
    }
            
    /**
     * delete
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return bool
     */
    public function delete(string $key, string $value): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM " . static::$entity . " WHERE {$key} = :key");
            $stmt->bindValue("key", $value, \PDO::PARAM_STR);
            $stmt->execute();

            return true;

        } catch (\PDOException $execpetion) {
            $this->fail = $execpetion;
            return false;
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
        foreach(static::$protected as $unset) {
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
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
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