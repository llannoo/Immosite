<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 9/07/13
 * Time: 15:05
 * To change this template use File | Settings | File Templates.
 */


// Require Composer Autoloader
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Create new Silex App
$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'config.php' ));

// Use Twig — @note: Be sure to install Twig via Composer first!
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views'
));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addExtension(new \Ikdoeict\lib\TwigDataExtension($app));
    return $twig;
}));

// Use Doctrine — @note: Be sure to install Doctrine via Composer first!
$app->register(new Silex\Provider\DoctrineServiceProvider(), $app['db.options']);


// Use Repository Service Provider — @note: Be sure to install RSP via Composer first!
$app->register(new Knp\Provider\RepositoryServiceProvider(), array(
    'repository.repositories' => array(
        'advertisements' => 'Ikdoeict\\Repository\\AdvertisementsRepository',
        'agencies'  => 'Ikdoeict\\Repository\\AgenciesRepository',
        'cities' => 'Ikdoeict\\Repository\\CitiesRepository',
        'contacts' => 'Ikdoeict\\Repository\\ContactsRepository',
        'locations' => 'Ikdoeict\\Repository\\LocationsRepository',
        'photos' => 'Ikdoeict\\Repository\\PhotosRepository'
    )
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), $app['swiftmailer.options']);

// Use UrlGenerator Service Provider - @note: Be sure to install "symfony/twig-bridge" via Composer if you want to use the `url` & `path` functions in Twig
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Use Validator Service Provider - @note: Be sure to install "symfony/validator" via Composer first!
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Use Form Service Provider - @note: Be sure to install "symfony/form" & "symfony/twig-bridge" via Composer first!
$app->register(new Silex\Provider\FormServiceProvider());

// Use Translation Service Provider because without it our form won't work
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array()
));
