<?php

use Newscoop\API\Client;
use Newscoop\API\Exception\NewscoopApiException;
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
        ->setItemsPerPage(15)
        ->setOrder(array(
            'number' => 'desc'
        ))
        ->makeRequest()
        ->toArray();

    return $app['twig']->render('index.html.twig', array(
        'articles' => $articles,
    ));
});

$app->get('/getSingleArticle/{number}/{language}', function (Request $request, $number, $language) use ($app, $api){
    
    $article = $api->getResource('/articles/'.$number)
        ->makeRequest()
        ->toArray();

    try {
        $comments = $api->getResource('/articles/'.$number.'/'.$language.'/comments')
            ->makeRequest()
            ->toArray();
    } catch(NewscoopApiException $e) {
        $comments = null;
    }

    return $app['twig']->render('getSingleArticle.html.twig', array(
        'article' => $article,
        'comments' => $comments
    ));
});

$app->get('/js', function (Request $request) use ($app){
    return $app['twig']->render('jsSdk.html.twig');
});

$app->run();