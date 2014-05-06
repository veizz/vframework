<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');

$app->add('index', function() use($app) {
    $c = new IndexController($app);
    $c->index();
});
$app->add('meta', function() use($app) {
    $c = new IndexController($app);
    $c->meta();
});
