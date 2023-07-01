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
        $head = $this->seo->render(
            CONF_SITE_NAME . " - " . CONF_SITE_TITLE,
            CONF_SITE_DESC,
            url(),
            url("/assets/images/share.jpg")
        );

        echo $this->view->render("home", [
            "head" => $head,
            "video" => "jGQBmSsunT4"
        ]);
    }

    public function error(array $data)
    {
        $error = new \stdClass();
        $error->code = $data['errcode'];
        $error->title = "Opps. Conteúdo indisponível";
        $error->message = "Sentímos muito, mas o conteúdo que você tentou acessar não existe, esta indisponível no momento ou foi removido :/";
        $error->linkTitle = "Continue navegando";
        $error->link = url_back();
        
        $head = $this->seo->render(
            "{$error->code} {$error->title}",
            $error->message,
            url_back("/ops/{$error->code}"),
            url("/assets/images/share.jpg"),
            false
        );
        echo $this->view->render("error", [
            "head" => $head,
            "error" => $error
        ]);
    }
}