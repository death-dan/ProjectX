<?php

namespace Source\Support;

use CoffeeCode\Optimizer\Optimizer;

class Seo
{
    /** @var Optimizer */
    private $optimizer;
    
    /**
     * __construct
     *
     * @param  mixed $schema
     * @return void
     */
    public function __construct(string $schema = "article")
    {
        $this->optimizer = new Optimizer();
        $this->optimizer->openGraph(
            CONF_SITE_NAME,
            CONF_SITE_LANG,
            $schema
        )->twitterCard(
            CONF_SOCIAL_TWITER_CREATOR,
            CONF_SOCIAL_TWITER_PUBLISHER,
            CONF_SITE_DOMAIN
        )->publisher(
            "localHost",
            "HostLocal"
            // CONF_SOCIAL_FACEBOOK_PAGE,
            // CONF_SOCIAL_FACEBOOK_AUTHOR
        )->facebook(
            // CONF_SOCIAL_FACEBOOK_APP
            "HostLocal"
        );
    }
    
    /**
     * __get
     *
     * @param  mixed $name
     * @return void
     */
    public function __get($name)
    {
        return $this->optimizer->data()->$name;
    }
    
    /**
     * render
     *
     * @param  mixed $title
     * @param  mixed $description
     * @param  mixed $url
     * @param  mixed $image
     * @param  mixed $follow
     * @return string
     */
    public function render(string $title, string $description, string $url, string $image, bool $follow = true): string
    {
        return $this->optimizer->optimize($title, $description, $url, $image, $follow)->render();
    }
    
    /**
     * optmizer
     *
     * @return Optimizer
     */
    public function optimizer(): Optimizer
    {
        return $this->optimizer;
    }
    
    /**
     * data
     *
     * @param  mixed $title
     * @param  mixed $desc
     * @param  mixed $url
     * @param  mixed $image
     * @return void
     */
    public function data(string $title = null, string $desc = null, string $url = null, string $image = null)
    {
        return $this->optimizer->data($title, $desc, $url, $image);
    }
}