<?php
ob_start();

require __DIR__ . "/vendor/autoload.php";

/** 
 * BOOTSTRAP
 */
use CoffeeCode\Router\Router;
use Source\Core\Session;

$session = new Session();
$route  = new Router(url(), ":");
$route->namespace("Source\App");

/** 
 * WEB ROUTES
 */
$route->group(null);
$route->get("/", "web:home");
$route->get("/sobre", "web:about");

//blog
$route->group("/blog");
$route->get("/", "web:blog");
$route->get("/p/{page}", "web:blog");
$route->get("/{uri}", "web:blogPost");
$route->post("/buscar", "web:blogSearch");
$route->get("/buscar/{terms}/{page}", "web:blogSearch");
$route->get("/em/{category}", "web:blogCategory");
$route->get("/em/{category}/{page}", "web:blogCategory");

//auth
$route->group(null);
$route->get("/entrar", "web:login");
$route->post("/entrar", "web:login");
$route->get("/cadastrar", "web:register");
$route->post("/cadastrar", "web:register");
$route->get("/recuperar", "web:forget");
$route->post("/recuperar", "web:forget");
$route->get("/recuperar/{code}", "web:reset");
$route->post("/recuperar/resetar", "web:reset");


//optin
$route->group(null);
$route->get("/confirma", "web:confirm");
$route->get("/obrigado/{email}", "web:success");

//serviçes
$route->group(null);
$route->get("/termos", "web:terms");


/**
 * APP
 */
$route->group("/app");
$route->get("/", "App:home");
$route->get("/receber", "App:income");
$route->get("/pagar", "App:expense");
$route->get("/fatura/{invoice_id}", "App:invoice");

$route->get("/perfil", "App:profile");
$route->get("/sair", "App:logout");


/** 
 * ERROR ROUTES
 */
$route->group("/ops");
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