<?php

use Newscoop\Client;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Resources/views',
));

//debug
$app['debug'] = true;

$newscoopClient = new Client();
$newscoopClient->setApiEndpoint('http://newscoop.dev/api');

$app->get('/', function (Request $request) use ($app, $newscoopClient){
    $articles = $newscoopClient->api('/articles', array(
        'type' => 'news',
        'items_per_page' => 10
    ))->toArray();

    return $app['twig']->render('index.html.twig', array(
        'articles' => $articles,
    ));
});

$app->run();