<?php
ob_start();

require __DIR__ . "/vendor/autoload.php";

/** 
 * BOOTSTRAP
 */
use Source\Core\Session;
use CoffeeCode\Router\Router;

$session = new Session();
$route  = new Router(url(), ":");

/** 
 * WEB ROUTES
 */
$route->namespace("Source\App");
$route->get("/", "web:home");
$route->get("/sobre", "web:about");
$route->get("/termos", "web:terms");

//blog
$route->get("/blog", "web:blog");
$route->get("/blog/page/{page}", "web:blog");
$route->get("/blog/{postName}", "web:blogPost");

//auth
$route->get("/entrar", "web:login");
$route->get("/recuperar", "web:forget");
$route->get("/cadastrar", "web:register");

/** 
 * ERROR ROUTES
 */
$route->namespace("Source\App")->group("/ops");
$route->get("/{errcode}", "web:error");

/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if ($route->error()) {
    $route->redirect("/ops/{$route->error()}");
}


ob_end_flush();