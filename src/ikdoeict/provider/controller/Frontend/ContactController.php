<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 13/08/13
 * Time: 22:29
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Provider\Controller\Frontend;



use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ContactController implements ControllerProviderInterface {

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect (Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers
            ->get('/', array($this, 'overview'))
            ->bind('frontend.agency.overview')
            ->method('GET|POST');

        $controllers
            ->get('/{idAgency}', array($this, 'contact'))
            ->assert('idAgency', '\d+')
            ->bind('frontend.agency.contact')
            ->method('GET|POST');

        return $controllers;
    }

    public function overview(Application $app){
        $agencies = $app['agencies']->findAll();
        // var_dump($agencies);
        foreach($agencies as $key=>$value) {
            $agencies[$key]['logopath'] = $app['logo.base_url'] . $agencies[$key]['logo'];
        }
        return $app['twig']->render('Frontend/Contact/overview.twig', array('agencies' => $agencies ));
    }

    /**
     * @param Application $app
     * @param             $idAgency
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function contact(Application $app, $idAgency){
        $agency = $app['agencies']->findAgency($idAgency);

        if (!$agency) {
            $app->abort(404, 'Company does not exist');
        }

        $mailform = $app['form.factory']->createNamed('mailform')
            ->add('name', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
            ))
            ->add('email', 'text', array(
                'constraints' => array(
                    new Assert\Email(),
                    new Assert\NotBlank()
                ),
            ))
            ->add('message', 'textarea', array(
                'constraints' => array(
                    new Assert\NotBlank()),
            ));

        if ('POST' == $app['request']->getMethod()) {
            $mailform->bind($app['request']);

            if ($mailform->isValid()) {
                $data = $mailform->getData();

                $messagebody = $data['message'];
                $name        = $data['name'];

                $subject = "Message from ".$name;

                $app['mailer']->send(\Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array($data['email'])) // replace with your own
                    ->setTo(array($agency['email']))   // replace with email recipient
                    ->setBody($app['twig']->render('email.html.twig',   // email template
                        array('name'      => $name,
                            'message'   => $messagebody,
                        )),'text/html'));



                return $app->redirect($app['url_generator']->generate('contact.agency.contact') . '?sended');
            }
        }
        return $app['twig']->render('Frontend/Contact/detail.twig', array('mailform' => $mailform->createView(), 'agency' => $agency ));
    }
}