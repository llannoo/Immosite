<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 9/07/13
 * Time: 15:09
 * To change this template use File | Settings | File Templates.
 */
// Bootstrap


require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Use Request from Symfony Namespace
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->error(function (\Exception $e, $code) use ($app) {
    if ($code == 404) {
        return $app['twig']->render('Errors/404.twig', array('error' => $e->getMessage()));
    } else {
        return 'Shenanigans! Something went horribly wrong // ' . $e->getMessage();
    }
});

$app->get('/', function(Silex\Application $app) {
    return $app->redirect($app['request']->getBaseUrl() . '/');
});

// Mount our controllers (dynamic routes)
$app->mount('/profile', new Ikdoeict\Provider\Controller\Backend\AgenciesController());
$app->mount('/auth', new Ikdoeict\Provider\Controller\Backend\AuthController());
$app->mount('/advertisements', new Ikdoeict\Provider\Controller\Backend\AdvertisementController());