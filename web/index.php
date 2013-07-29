<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 9/07/13
 * Time: 14:26
 * To change this template use File | Settings | File Templates.
 */

// Make it so that PHP 5.4's built-in server can server site files
// @see http://silex.sensiolabs.org/doc/web_servers.html#php-5-4
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// Require the app and run it
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'app.php';

// Inject the current path onto the app
$app['logo.base_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'logos';
$app['logo.base_url'] = '/files/logos';

$app['photos.base_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'fotos';
$app['photos.base_url'] = '/files/photos';

$app->run();
