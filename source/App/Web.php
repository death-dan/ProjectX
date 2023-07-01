<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Support\Pager;

class Web extends Controller
{    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");
    }
    
    /**
     * home
     *
     * @return void
     */
    public function home(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . " - " . CONF_SITE_TITLE,
            CONF_SITE_DESC,
            url(),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("home", [
            "head" => $head,
            "video" => "jGQBmSsunT4"
        ]);
    }
    
    /**
     * about
     *
     * @return void
     */
    public function about(): void
    {
        $head = $this->seo->render(
            "Descubra o " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
            CONF_SITE_DESC,
            url("/sobre"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("about", [
            "head" => $head,
            "video" => "jGQBmSsunT4"
        ]);
    }
    
    /**
     * blog
     *
     * @param  mixed $data
     * @return void
     */
    public function blog(?array $data): void
    {
        $head = $this->seo->render(
            "Blog - " . CONF_SITE_NAME,
            "Confira em nosso Blog dicas e sacadas de como controlar melhor suas contas. Vamos toma um Coffe",
            url("/blog"),
            theme("/assets/images/share.jpg")
        );

        $pager = new Pager(url("/blog/page/"));
        $pager->pager(100, 10, ($data['page'] ?? 1));

        echo $this->view->render("blog", [
            "head" => $head,
            "paginator" => $pager->render()
        ]);
    }
    
    /**
     * blogPost
     *
     * @param  mixed $data
     * @return void
     */
    public function blogPost(?array $data): void
    {
        $postName = $data['postName'];

        $head = $this->seo->render(
            "POST NAME - " . CONF_SITE_NAME,
            "POST HEADLINE",
            url("/blog/{$postName}"),
            theme("BLOG IMAGE")
        );

        $pager = new Pager(url("/blog/page/"));
        $pager->pager(100, 10, ($data['page'] ?? 1));

        echo $this->view->render("blog-post", [
            "head" => $head,
            "data" => $this->seo->data()
        ]);
    }


    
    /**
     * terms
     *
     * @return void
     */
    public function terms(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . " - Termos de uso",
            CONF_SITE_DESC,
            url("/termos"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("terms", [
            "head" => $head
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
            theme("/assets/images/share.jpg"),
            false
        );
        echo $this->view->render("error", [
            "head" => $head,
            "error" => $error
        ]);
    }
}