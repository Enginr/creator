<?php

require __DIR__ . '/vendor/autoload.php';

use Enginr\Enginr;
use Enginr\System\System;

$env = json_decode(file_get_contents(__DIR__ . '/env.json'));

$app = new Enginr();

$app->set('view', __DIR__ . '/views');
$app->set('template', 'pug');

$app->use(function($req, $res, $next) {
    System::log("$req->host:$req->port $req->method $req->uri");
    $next();
});

$app->use('/', require __DIR__ . '/routes/index.php');

$app->listen($env->host, $env->port, function() use ($env) {
    System::log("Server started and listening on $env->host:$env->port ...");
});