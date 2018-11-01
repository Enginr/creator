<?php

use Enginr\Router;

$router = new Router();

$router->get('/', function($req, $res) {
    $res->render('welcome');
});

return $router;