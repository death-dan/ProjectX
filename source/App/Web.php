<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Faq\Channel;
use Source\Models\Faq\Question;
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
            "video" => "jGQBmSsunT4",
            "faq" => (new Question())
                ->find("channel_id = :id", "id=1", "question, response")
                ->order("order_by")
                ->fetch(true)
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
     * login
     *
     * @return void
     */
    public function login(): void
    {
        $head = $this->seo->render(
            "Entrar - " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/entrar"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-login", [
            "head" => $head
        ]);
    }
    
    /**
     * forget
     *
     * @return void
     */
    public function forget(): void
    {
        $head = $this->seo->render(
            "Recuperar Senha - " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/recuperar"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-forget", [
            "head" => $head
        ]);
    }
    
    /**
     * register
     *
     * @return void
     */
    public function register(): void
    {
        $head = $this->seo->render(
            "Criar Conta - " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/cadastrar"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("auth-register", [
            "head" => $head
        ]);
    }

    public function confirm(): void
    {
        $head = $this->seo->render(
            "Confirme Seu Cadastro - " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/confirma"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("optin-confirm", [
            "head" => $head
        ]);
    }

    public function success(): void
    {
        $head = $this->seo->render(
            "Bem-Vindo(a) ao " . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url("/obrigado"),
            theme("/assets/images/share.jpg")
        );

        echo $this->view->render("optin-success", [
            "head" => $head
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
        
        switch ($data['errcode']) {
            case "problemas":
                $error->code = "OPS";
                $error->title = "Estamos enfrentando problemas";
                $error->message = "Parece que nossos serviço não está disponivel no momento. Já estamos vendo isso mas caso percise, envie um e-mail :)";
                $error->linkTitle = "ENVIAR E-MAIL";
                $error->link = "mailto:" . CONF_MAIL_SUPPORT;
                break;
            case "manutencao": 
                $error->code = "OPS";
                $error->title = "Desculpe, estamos em manutenção!";
                $error->message = "Voltamos logo! Por hora estamos trabalhando para melhorar nosso conteúdo para você controlar suas contas :p";
                $error->linkTitle = null;
                $error->link = null;
                break;
            default:
                $error->code = $data['errcode'];
                $error->title = "Opps. Conteúdo indisponível";
                $error->message = "Sentímos muito, mas o conteúdo que você tentou acessar não existe, esta indisponível no momento ou foi removido :/";
                $error->linkTitle = "Continue navegando";
                $error->link = url_back();
                break;
        }
        
        $head = $this->seo->render(
            "{$error->code} {$error->title}",
            $error->message,
            url("/ops/{$error->code}"),
            theme("/assets/images/share.jpg"),
            false
        );
        echo $this->view->render("error", [
            "head" => $head,
            "error" => $error
        ]);
    }
}