<?php

namespace Source\App;

use Source\Core\Controller;

class Web extends Controller
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");
    }

    public function home()
    {
        echo "<h1>HELLO WORLD!</h1>";
    }

    public function error(array $data)
    {
        echo "<h1>ERROR !</h1>";
        var_dump($data);
    }
}