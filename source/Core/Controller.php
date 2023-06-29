<?php

namespace Source\Core;

use Source\Support\Seo;

class Controller
{    
    /** @var View */
    protected $view;

    /** @var Seo */
    protected $seo;
    
    /**
     * __construct
     *
     * @param  mixed $pathToView
     * @return void
     */
    public function __construct(string $pathToView = null)
    {
        $this->view = new View($pathToView);
        $this->seo = new Seo();
    }
}