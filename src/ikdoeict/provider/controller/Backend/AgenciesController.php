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
            ->method('GET|POST')
            ->before(array($this, 'checkLogin'))
            ->bind('backend.agency.profile');

        $controllers
            ->get('/edit', array($this, 'edit'))
            ->method('GET|POST')
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
        $agency = $app['agencies']->find($contact);
        $contacts = $app['contacts']->findAllByAgency($contact['idAgency']);

        return $app['twig']->render('Backend/Agency/profile.twig', array('session' => $contact, 'agency' => $agency, 'contacts' => $contacts));
    }

    /**
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Application $app){
        $contact = $app['session']->get('contact');
        $agency = $app['agencies']->find($contact);

        if (!$agency) {
            $app->abort(404, 'Agency does not exist');
        }

        $cities2 = $app['cities']->findAll();
        $cities1 = $this->reorder2dArray($cities2, 'name');
        $cities = $this->reorderArray($cities1);

        $profileform = $app['form.factory']->createNamed('profileform')
            ->add('agencyname','text', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een naam in te vullen'
                    )),
                    new Assert\Length(array('min' => 2))
                ),
                'data' => $agency['name']
            ))
            ->add('street','text', array(
                 'data'=> $agency['street']
            ))
            ->add('housenumber','integer', array(
                'constraints' => array(
                    new Assert\Length(array('max'=>5))
                ),
                'data' => $agency['housenumber']
            ))
            ->add('bus','text', array(
                'constraints' => array(),
                'data' => $agency['bus']
            ))
            ->add('city','choice', array(
                'empty_value' => $agency['city'],
                'empty_data' => $agency['city'],
                'choices' => $cities,
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => $cities,
                            'message' => 'Kies een optie uit de lijst',
                            'strict' => true
                        )
                    )
                )
            ))
        //@todo controle nummers
            ->add('tel','text', array(
                'constraints' => array(
                ),
                'data' => $agency['tel']
            ))
            ->add('fax','text', array(
                'constraints' => array(
                ),
                'data' => $agency['fax']
            ))
            ->add('logo','file', array(
                'constraints' => array(
                    new Assert\Image(array(
                        'minWidth' => 200,
                        'maxWidth' => 400,
                        'minHeight' => 200,
                        'maxHeight' => 400,
                    ))
                ),
            ))
            ->add('website','url', array(
                'constraints' => array(
                    new Assert\Url(array(
                        'message' => 'Dit is geen geldige url'
                    ))
                ),
                'data' => $agency['website']
            ))
            ->add('description','textarea', array(
                'constraints' => array(),
                'data' => $agency['description']
            ));

        if ('POST' == $app['request']->getMethod()) {
            $profileform->bind($app['request']);
            $data = $profileform->getData();
            if ($data['city'] == null){
                $profileform->get('city')->addError(new \Symfony\Component\Form\FormError('Gelieve een postcode of een gemeente in te vullen'));
            }
            if ($profileform->isValid()) {
                $data = $profileform->getData();
                $data['city'] = strtolower($data['city']);
                $resultCode = $app['cities']->findByCityName($data);
                if (!$resultCode){
                    $profileform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                }

                //check logo
                if (isset($data['logo']) && (
                        ('.jpg' == substr($data['logo']-> getClientOriginalName(), -4)) ||
                        ('.png' == substr($data['logo']->getClientOriginalName(), -4))

                    )){
                    $data['logoName'] = sha1($data['logo']->getClientOriginalName() .  microtime()) . '.jpg';
                    // Move it to its new location
                    $data['logo']->move($app['logo.base_path'], $data['logoName']);
                }
                else {
                    $profileform->get('logo')->addError(new \Symfony\Component\Form\FormError('Only .jpg, .png allowed'));
                }
var_dump($data);
                $data['idCity'] = $resultCode['idCity'];

                $id['idAgency'] = $contact['idAgency'];
                $id['idLocation'] = $agency['idLocation'];

                $app['locations']->update($data, $id);

                $app['agencies']->update($data, $id);

                //@todo send mail and redirect to auth.login
                return $app->redirect($app['url_generator']->generate('backend.agency.profile') . '?edited');
            }
        }
        return $app['twig']->render('Backend/Agency/edit.twig', array('profileform' => $profileform->createView(), 'session' => $contact));
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

    /**
     * @param $data
     * @param $field
     * @return mixed
     */
    public function reorder2dArray($data , $field){
        foreach($data as $key=>$value) {
            $data2[$key] = $value[$field];
        }
        return $data2;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function reorderArray($data){
        foreach($data as $value) {
            $data2[$value] = $value;
        }
        return $data2;
    }
}