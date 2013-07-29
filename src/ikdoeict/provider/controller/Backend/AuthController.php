<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 9/07/13
 * Time: 15:55
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Provider\Controller\Backend;

use Silex\Application;
use Silex\ControllerCollection;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AuthController implements ControllerProviderInterface {

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect (Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function(Application $app) {
            return $app->redirect($app['url_generator']->generate('auth.login'));
        });

        $controllers
            ->match('/login', array($this, 'login'))
            ->method('GET|POST')
            ->bind('auth.login');

        $controllers
            ->get('/logout', array($this, 'logout'))
            ->assert('id', '\d+')
            ->bind('auth.logout');

        $controllers
            ->get('/register', array($this, 'register'))
            ->method('GET|POST')
            ->bind('auth.register');

        return $controllers;
    }

    public function login(Application $app) {

        // Already logged in
        if ($app['session']->get('contact')) {
           // return $app->redirect($app['url_generator']->generate('home'));
        }
//login
        $loginform = $app['form.factory']->createNamed('loginform')
            ->add('email','email', array(
                'constraints' => array(
                    new Assert\Email(array(
                        'message' => 'Dit is geen geldig emailadres'
                    ))
                )
            ))

            ->add('password','password', array(
                'constraints' => array(
                    new Assert\Length(array('min' => 6)),
                    new Assert\NotBlank()
                )
            ));

        // Form was submitted: process it
        if ('POST' == $app['request']->getMethod()) {
            if ($app['request']->request->has('loginform') ) {
                // handle the first form
                $loginform->bind($app['request']);

                if ($loginform->isValid()) {
                    $data = $loginform->getData();

                    $data['password'] = sha1($data['password'] . $app['PASSWORD_SALT']);

                    $userData = $app['contacts']->findContact($data);

                    //@todo get user and password + check
                    if ($userData['email']      == $data['email'] &&
                        $userData['password']   == $userData['password']) {

                        $app['session']->set('contact', array(
                            'idAgency' => $userData['idAgency'],
                            'username' => $userData['email']
                        ));

                        return $app->redirect($app['url_generator']->generate('backend.advertisements.overview'));

                    } else {
                        $loginform->get('password')->addError(new \Symfony\Component\Form\FormError('Invalid password'));
                    }
                }
            }
        }
        return $app['twig']->render('Backend/auth/login.twig', array('loginform' => $loginform->createView()));
    }

    public function logout(Application $app) {
        $app['session']->remove('user');
        return $app->redirect($app['url_generator']->generate('auth.login') . '?loggedout');
    }

    public function register(Application $app) {
        $registerform = $app['form.factory']->createNamed('registerForm')
            ->add('email','email', array(
                'constraints' => array(
                    new Assert\Email(array(
                        'message' => 'Dit is geen geldig emailadres'
                    ))
                )
            ))

            ->add('password','password', array(
                'constraints' => array(
                    new Assert\Length(array('min' => 6)),
                    new Assert\NotBlank()
                )
            ))

            ->add('confirmpass','password', array(
                'constraints' => array(
                    new Assert\Length(array('min' => 6)),
                    new Assert\NotBlank()
                )
            ))

            ->add('agencyname','text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 2))
                )
            ))

            ->add('street','text', array(
                'constraints' => array(
                    new Assert\NotBlank()
                )
            ))
            ->add('housenumber','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max'=>5))
                )
            ))

            ->add('bus','text', array(
                'constraints' => array(),
            ))

            ->add('code','integer', array(
                'constraints' => array(
                    new Assert\Range(array(
                        'min' => 1000,
                        'max' => 9999
                    ))
                )
            ))

            ->add('city','text', array(
                'constraints' => array()
            ))


            ->add('tel','text', array(
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => array(
                            '/^(((0)[1-9]{2}[0-9][-]?[1-9][0-9]{5})|((\\+32|0|0032)[1-9][0-9][-]?[1-9][0-9]{6}))$/'),
                            'message' => 'Formaat 12-34567890',
                            'match' => true
                        )
                    )
                )
            ))

            ->add('fax','text', array(
                'constraints' => array(
                    new Assert\Regex(array(
                            'pattern' => array(
                                '/^(((0)[1-9]{2}[0-9][-]?[1-9][0-9]{5})|((\\+32|0|0032)[1-9][0-9][-]?[1-9][0-9]{6}))$/'),
                            'message' => 'Formaat 12-34567890',
                            'match' => true
                        )
                    )
                )
            ))

            ->add('logo','file', array(
                'constraints' => array(
                    new Assert\Image(array(
                        'minWidth' => 200,
                        'maxWidth' => 400,
                        'minHeight' => 200,
                        'maxHeight' => 400,
                    ))
                )
            ))

            ->add('website','url', array(
                'constraints' => array(
                    new Assert\Url(array(
                        'message' => 'Dit is geen geldige url'
                    ))
                )
            ))

            ->add('description','textarea', array(
                'constraints' => array()
            ));

        if ('POST' == $app['request']->getMethod()) {
            $registerform->bind($app['request']);

            if ($registerform->isValid()) {
                $data = $registerform->getData();
                var_dump($data);
                //@todo get user and password + check
                if ($data['password'] == $data['confirmpass']) {
                    //check password
                    $data['password'] = sha1($data['password'] . $app['PASSWORD_SALT']);

                    //check logo
                    if (isset($data['logo']) && (
                        ('.jpg' == substr($data['logo']-> getClientOriginalName(), -4)) ||
                        ('.png' == substr($data['logo']->getClientOriginalName(), -4))

                        )){

                        $data['logoName'] = sha1($data['logo']->getClientOriginalName() .  microtime()) . '.jpg';
                        // Move it to its new location
                        $data['logo']->move($app['logo.base_path'], $data['logoName']);

                        // Redirect to the overview
//                        return $app->redirect($app['url_generator']->generate('backend.overview'));
                    }
                    else {
                        $registerform->get('logo')->addError(new \Symfony\Component\Form\FormError('Only .jpg, .bmp, .png allowed'));
                    }

                    //check postcode met citynaam
                    if(isset($data['code'])){
                        $resultCode = $app['cities']->findByCode($data);
                        if (!$resultCode){
                            $registerform->get('code')->addError(new \Symfony\Component\Form\FormError('Postcode wordt niet herkend'));
                        }
                    }
                    elseif(isset($data['city'])){
                        $data['city'] = strtolower($data['city']);
                        $resultCode = $app['cities']->findByCityName($data);
                        if (!$resultCode){
                            $registerform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                        }
                    }
                    elseif(isset($data['city']) && isset($data['code'])){
                        $data['city'] = strtolower($data['city']);
                        $resultCode = $app['cities']->findByCityAndCode($data);
                        if (!$resultCode){
                            $registerform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente en postcode stemmen niet overeen'));
                        }
                    }


                    $data['idCity'] = $resultCode['idCity'];

                    $app['locations']->insert($data);
                    $data['idLocation'] = $app['locations']->getLastInsertedId();

                    $app['agencies']->insert($data);
                    $data['idAgency'] = $app['agencies']->getLastInsertedId();

                    $app['contacts']->insert($data);


                    //@todo send mail and redirect to auth.login
                    return $app->redirect($app['url_generator']->generate('auth.login'));

                } else {
                    $registerform->get('password')->addError(new \Symfony\Component\Form\FormError('Password incorrect'));
                }
            }
        }

        return $app['twig']->render('Backend/auth/register.twig', array('registerform' => $registerform->createView()));
    }
 /*   public function getCode ($term){
        $max = 10;
        $suggests = $app['cities']->searchCode($term, $max);

        foreach ($suggests as $row)
        {
            $data[] = array(
                'label' => $row['code'] .' '. $row['city'],
                'value' => $row['code']
            );
        }
        var_dump($data);
        return $app['twig']->render('Backend/auth/register.twig',
            array('suggests' => json_encode($suggests)));

    }*/


}