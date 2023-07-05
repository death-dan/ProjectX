<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Auth;
use Source\Support\Message;

class App extends Controller
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_APP);

        if (!Auth::user()) {
            $this->message->warning("Efetue o login para acessar o APP")->flash();
            redirect("/entrar");
        }
    }

    public function home(): void
    {
        echo flash();
        var_dump(Auth::user());
        echo "<a tittle='Sair' href='" . url("/app/sair") . "'>Sair</a>";
    }

    public function logout(): void
    {
        (new Message())->info("Você saiu com sucesso " . Auth::user()->first_name . ". Volte logo :)")->flash();

        Auth::logout();
        redirect("/entrar");
    }
}