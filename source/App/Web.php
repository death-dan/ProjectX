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
        echo $this->view->render("home", [
            "title" => "CoffeControl - Gerencie suas contas com o melhor coffe"
        ]);
    }

    public function error(array $data)
    {
        echo $this->view->render("error", [
            "title" => "{$data['errcode']} | Ops!"
        ]);
    }
}