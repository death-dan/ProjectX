<?php

namespace Source\Core;

use Source\Support\Message;

/**
 * Session
 */
class Session
{
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (!empty($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->has($name);
    }
    
    /**
     * all
     *
     * @return object
     */
    public function all(): ?object
    {
        return  (object)$_SESSION;
    }
    
    /**
     * set
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return Session
     */
    public function set(string $key, $value): Session
    {
        $_SESSION[$key] = (is_array($value) ? (object)$value : $value);
        return $this;
    }
    
    /**
     * unset
     *
     * @param  mixed $key
     * @return Session
     */
    public function unset(string $key): Session
    {
        unset($_SESSION[$key]);
        return $this;
    }
    
    /**
     * has
     *
     * @param  mixed $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * regenerate
     *
     * @return Session
     */
    public function regenerate(): Session
    {
        session_regenerate_id(true);
        return $this;
    }
    
    /**
     * destroy
     *
     * @return Session
     */
    public function destroy(): Session
    {
        session_destroy();
        return $this;
    }
    
    /**
     * @return null|Message
     */
    public function flash(): ?Message
    {
        if ($this->has("flash")) {
            $flash = $this->flash;
            $this->unset("flash");
            return $flash;
        }
        return null;
    }
    
    /**
     * CSRF Token
     */
    public function csrf(): void
    {
        $_SESSION['csrf_token'] = base64_encode(random_bytes(20));
    }
}