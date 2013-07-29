<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 21/07/13
 * Time: 12:35
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Provider\Controller\Backend;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdvertisementController implements ControllerProviderInterface{

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
            ->get('/', array($this, 'overview'))
            ->method('GET|POST')
            ->before(array($this, 'checkLogin'))
            ->bind('backend.advertisements.overview');

        $controllers
            ->get('/new', array($this, 'add'))
            ->method('GET|POST')
            ->before(array($this, 'checkLogin'))
            ->bind('backend.advertisements.new');

        $controllers
            ->get('/{idAdvertisement}/edit', array($this, 'edit'))
            ->assert('idAdvertisement', '\d+')
            ->method('GET|POST')
            ->before(array($this, 'checkLogin'))
            ->bind('backend.advertisements.edit');


        $controllers
            ->get('/{idAdvertisement}/delete', array($this, 'delete'))
            ->assert('idAdvertisement', '\d+')
            ->method('GET|POST')
            ->before(array($this, 'checkLogin'))
            ->bind('backend.advertisements.delete');
        $controllers
            ->get('/{idAdvertisement}/detail', array($this, 'detail'))
            ->assert('idAdvertisement', '\d+')
            ->method('GET|POST')
            ->before(array($this, 'checkLogin'))
            ->bind('backend.advertisements.detail');

        return $controllers;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function overview(Application $app){
        $contact = $app['session']->get('contact');
        $advertisements = $app['advertisements']->findAllByAgency($contact['idAgency']);

        return $app['twig']->render('Backend/Advertisements/overview.twig', array('advertisements' => $advertisements, 'session' => $app['session']->get('contact')));
    }

    public function detail(Application $app, $idAdvertisement){
        $contact = $app['session']->get('contact');
        $advertisement = $app['advertisements']->findAdByAgency($contact['idAgency'], $idAdvertisement);

        if (!$advertisement) {
            $app->abort(404, 'Internship does not exist');
        }

        return $app['twig']->render('Backend/advertisements/detail.twig', array('advertisement' => $advertisement, 'session' => $contact));
    }

    /**
     * @param Application $app
     * @param             $idAdvertisement
     */
    public function delete(Application $app, $idAdvertisement){
        $contact = $app['session']->get('contact');
    }

    /**
     * @param Application $app
     * @param             $idAdvertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Application $app, $idAdvertisement){
       $contact = $app['session']->get('contact');
        $propertytypes = $app['advertisements']->fetchPropertytype();

        $advertisement = $app['advertisements']->findAdByAgency($contact['idAgency'], $idAdvertisement);
var_dump($advertisement);
        if (!$advertisement) {
            $app->abort(404, 'Internship does not exist');
        }

        $newform = $app['form.factory']->createNamed('newform')

            ->add('description','textarea', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een beschrijving na te laten'
                    ))
                ),
                'data' => $advertisement['description']
            ))

            ->add('price','money', array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'data' => $advertisement['price']
            ))

            ->add('epc','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'max' => 2000,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'maxMessage'  => 'De waarde kan niet groter zijn dan 2000',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => $advertisement['EPC']
            ))

            ->add('ki','money', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'data' => $advertisement['ki']
            ))

            ->add('chambers','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => $advertisement['chambers']
            ))
            ->add('living_area','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde moet groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => $advertisement['living_area']
            ))

            ->add('total_area','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde moet groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => $advertisement['total_area']
            ))

            ->add('rent_sell','choice', array(
                'choices'   => array(
                    'verkoop'   => 'Verkoop',
                    'verhuur' => 'Verhuur',
                ),
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => ['verkoop', 'verhuur'],
                            'message' => 'Choose one option',
                            'strict' => true
                        )
                    )
                ),
                'data' => (boolean)$advertisement['rent_sell']
            ))

            ->add('sold_rented','checkbox', array(
                'constraints' => array(

                ),
                'data' => (boolean)$advertisement['sold_rented']
            ))


            ->add('street','text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                        'data' => $advertisement['street']
            ))

            ->add('housenumber','integer', array(
                'constraints' => array(
                    new Assert\Range(
                        array(
                            'min' => 1,
                            'max' => 2000,
                            'minMessage' => 'De waarde moet groter dan 1 zijn',
                            'maxMessage' => 'De waarde mag niet groter dan 2000 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => $advertisement['housenumber']
            ))

            ->add('bus','text', array(
                'constraints' => array(),
                'data' => $advertisement['bus']
            ))

            ->add('code','integer', array(
                'constraints' => array(
                    new Assert\Range(array(
                        'min' => 1000,
                        'max' => 9999,
                        'minMessage' => 'De waarde moet groter dan 1000 zijn',
                        'maxMessage' => 'De waarde mag niet groter dan 9999 zijn',
                        'invalidMessage'=> 'Dit is geen geldige waarde'
                    ))
                ),
                'data' => $advertisement['code']
            ))

            ->add('city','text', array(
                'constraints' => array(),
                'data' => $advertisement['name']
            ));

        if ('POST' == $app['request']->getMethod()) {
            $newform->bind($app['request']);

            if ($newform->isValid()) {
                $data = $newform->getData();
                var_dump($data);




                //check postcode met citynaam
                if(isset($data['code'])){
                    $resultCode = $app['cities']->findByCode($data);
                    if (!$resultCode){
                        $newform->get('code')->addError(new \Symfony\Component\Form\FormError('Postcode wordt niet herkend'));
                    }
                }
                elseif(isset($data['city'])){
                    $data['city'] = strtolower($data['city']);
                    $resultCode = $app['cities']->findByCityName($data);
                    if (!$resultCode){
                        $newform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                    }
                }
                elseif(isset($data['city']) && isset($data['code'])){
                    $data['city'] = strtolower($data['city']);
                    $resultCode = $app['cities']->findByCityAndCode($data);
                    if (!$resultCode){
                        $newform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente en postcode stemmen niet overeen'));
                    }
                }
                $data['idCity'] = $resultCode['idCity'];
                $app['locations']->update($data);
                $data['idLocation'] = $app['locations']->getLastInsertedId();



                //@todo send mail and redirect to auth.login
                return $app->redirect($app['url_generator']->generate('auth.login'));

            }
        }

        return $app['twig']->render('Backend/Advertisements/new.twig', array('registerform' => $newform->createView()));


    }

    /**
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add (Application $app){
        $contact = $app['session']->get('contact');
        $propertytypes = $app['advertisements']->fetchPropertytype();

        $newform = $app['form.factory']->createNamed('newform')
            ->add('description','textarea', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een beschrijving na te laten'
                    ))
                )
            ))

            ->add('price','money', array(
                'constraints' => array(
                    new Assert\NotBlank()
                ),
                'data' => 0.0
            ))

            ->add('epc','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'max' => 2000,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'maxMessage'  => 'De waarde kan niet groter zijn dan 2000',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => 0
            ))

            ->add('ki','money', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'data' => 0
            ))

            ->add('chambers','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => 0
            ))
            ->add('living_area','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde moet groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => 0
            ))

            ->add('total_area','integer', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde moet groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
                'data' => 0
            ))

            ->add('rent_sell','choice', array(
                'choices'   => array(
                    'verkoop'   => 'Verkoop',
                    'verhuur' => 'Verhuur',
                ),
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => ['verkoop', 'verhuur'],
                            'message' => 'Choose one option',
                            'strict' => true
                        )
                    )
                )
            ))

            ->add('sold_rented','checkbox', array(
                'constraints' => array(

                )
            ))


            ->add('street','text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                )
            ))

            ->add('housenumber','integer', array(
                'constraints' => array(
                    new Assert\Range(
                        array(
                            'min' => 1,
                            'max' => 2000,
                            'minMessage' => 'De waarde moet groter dan 1 zijn',
                            'maxMessage' => 'De waarde mag niet groter dan 2000 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                ),
            ))

            ->add('bus','text', array(
                'constraints' => array(),
            ))

            ->add('code','integer', array(
                'constraints' => array(
                    new Assert\Range(array(
                        'min' => 1000,
                        'max' => 9999,
                        'minMessage' => 'De waarde moet groter dan 1000 zijn',
                        'maxMessage' => 'De waarde mag niet groter dan 9999 zijn',
                        'invalidMessage'=> 'Dit is geen geldige waarde'
                    ))
                ),
            ))

            ->add('city','text', array(
                'constraints' => array()
            ));

        if ('POST' == $app['request']->getMethod()) {
            $newform->bind($app['request']);

            if ($newform->isValid()) {
                $data = $newform->getData();
                var_dump($data);




                //check postcode met citynaam
                if(isset($data['code'])){
                    $resultCode = $app['cities']->findByCode($data);
                    if (!$resultCode){
                        $newform->get('code')->addError(new \Symfony\Component\Form\FormError('Postcode wordt niet herkend'));
                    }
                }
                elseif(isset($data['city'])){
                    $data['city'] = strtolower($data['city']);
                    $resultCode = $app['cities']->findByCityName($data);
                    if (!$resultCode){
                        $newform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                    }
                }
                elseif(isset($data['city']) && isset($data['code'])){
                    $data['city'] = strtolower($data['city']);
                    $resultCode = $app['cities']->findByCityAndCode($data);
                    if (!$resultCode){
                        $newform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente en postcode stemmen niet overeen'));
                    }
                }
                $data['idCity'] = $resultCode['idCity'];
                $app['locations']->insert($data);
                $data['idLocation'] = $app['locations']->getLastInsertedId();



                //@todo send mail and redirect to auth.login
                return $app->redirect($app['url_generator']->generate('auth.login'));

            }
        }

    return $app['twig']->render('Backend/Advertisements/new.twig', array('registerform' => $newform->createView()));

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