<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/08/13
 * Time: 16:11
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Provider\Controller\Backend;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class AdminController implements ControllerProviderInterface{

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect (Application $app) {
        // TODO: Implement connect() method.

        $controllers = $app['controllers_factory'];

        $controllers->get('/', function(Application $app) {
            return $app->redirect($app['url_generator']->generate('auth.login'));
        });

        return $controllers;
    }
}