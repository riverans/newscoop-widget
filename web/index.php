<?php

use Newscoop\API\Client;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/Resources/views',
));

//debug
$app['debug'] = true;

$api = new Client();
$api->setApiEndpoint('http://newscoop.dev/api');

$app->get('/', function (Request $request) use ($app, $api){
    $articles = $api->getResource('/articles', array(
            'type' => 'news'
        ))
        ->setItemsPerPage(10)
        ->setOrder(array(
            'number' => 'desc'
        ))
        ->makeRequest()
        ->toArray();

    return $app['twig']->render('index.html.twig', array(
        'articles' => $articles,
    ));
});

$app->run();