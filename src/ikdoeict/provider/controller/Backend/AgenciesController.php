<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 8/08/13
 * Time: 17:31
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Provider\Controller\Backend;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class AgenciesController implements ControllerProviderInterface{

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

// Bind sub-routes
        $controllers
            ->get('/', array($this, 'profile'))
            ->before(array($this, 'checkLogin'))
            ->bind('backend.agency.profile');

        $controllers
            ->get('/edit', array($this, 'edit'))
            ->before(array($this, 'checkLogin'))
            ->bind('backend.agency.edit');
        return $controllers;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function profile(Application $app){
        $contact = $app['session']->get('contact');
        $agency = $app['agencies']->find($contact['idAgency']);
        $contacts = $app['contacts']->findAllByAgency($contact['idAgency']);
        return $app['twig']->render('Backend/Agency/profile.twig', array('agency' => $agency, 'contacts' => $contacts));
    }

    /**
     * @param Application $app
     */
    public function edit(Application $app){

    }
    /**
     * @param Request     $request
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkLogin(Request $request, Application $app) {
        if (!$app['session']->get('contact')) {
            return $app->redirect($app['url_generator']->generate('auth.login'));
        }
    }
}