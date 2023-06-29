<?php

namespace Source\Core;

use \League\Plates\Engine;

class View
{
    /** @var #engine */
    private $engine;
    
    /**
     * __construct
     *
     * @param  mixed $path
     * @param  mixed $ext
     * @return void
     */
    public function __construct(string $path = CONF_VIEW_PATH, string $ext = CONF_VIEW_EXT)
    {
        $this->engine = new Engine($path, $ext);
    }
    
    /**
     * path
     *
     * @param  mixed $name
     * @param  mixed $path
     * @return View
     */
    public function path(string $name, string $path): View
    {
        $this->engine->addFolder($name, CONF_VIEW_PATH . "$path");
        return $this;
    }
    
    /**
     * render
     *
     * @param  mixed $templateName
     * @param  mixed $data
     * @return string
     */
    public function render(string $templateName, array $data): string
    {
        return $this->engine->render($templateName, $data);
    }
    
    /**
     * engine
     *
     * @return Engine
     */
    public function engine(): Engine
    {
        return $this->engine();
    }
}