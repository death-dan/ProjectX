<?php

namespace Source\Models;

use Source\Core\Model;

class Post extends Model
{
    /** @var bool */
    private $all;
    
    /**
     * __construct
     *
     * @param  bool $all = ignore status and post_at
     * @return void
     */
    public function __construct(bool $all = false)
    {
        $this->all = $all;
        parent::__construct("posts", ["id"], ["title", "subtitle", "content"]);
    }
    
    /**
     * find
     *
     * @param  mixed $terms
     * @param  mixed $params
     * @param  mixed $columns
     * @return Model
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*"): Model
    {
        if (!$this->all) {
            $terms = "status = :status AND post_at <= NOW() " . ($terms ? " AND {$terms}" : "");
            $params = "status=post" . ($params ? "&{$params}" : "");
        }
        return parent::find($terms, $params, $columns);
    }
    
    /**
     * findByUri
     *
     * @param  mixed $uri
     * @param  mixed $columns
     * @return Post
     */
    public function findByUri(string $uri, string $columns = "*"): ?Post
    {
        $find = $this->find("uri = :uri", "uri={$uri}", $columns);
        return $find->fetch();
    }
    
    /**
     * author
     *
     * @return User
     */
    public function author(): ?User
    {
        if ($this->author) {
            return (new User())->findById($this->author);
        }
        return null;
    }
    
    /**
     * category
     *
     * @return Category
     */
    public function category(): ?Category
    {
        if ($this->category) {
            return (new Category())->findById($this->category);
        }
        return null;
    }
}