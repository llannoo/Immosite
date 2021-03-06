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

        $advertisements = $app['advertisements']->findAllByAgency($contact);
        return $app['twig']->render('Backend/Advertisements/overview.twig', array('advertisements' => $advertisements, 'session' => $contact));
    }

    /**
     * @param Application $app
     * @param             $idAdvertisement
     * @return mixed
     */
    public function detail(Application $app, $idAdvertisement){
        $contact = $app['session']->get('contact');

        $id['idAgency'] = $contact['idAgency'];
        $id['idAdvertisement'] = $idAdvertisement;
        $advertisement = $app['advertisements']->findAdByAgency($id);
        if (!$advertisement) {
            $app->abort(404, 'Advertisement does not exist');
        }

        return $app['twig']->render('Backend/advertisements/detail.twig', array('id' => $advertisement['idAdvertisement'], 'session' => $contact));
    }

    /**
     * @param Application $app
     * @param             $idAdvertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, $idAdvertisement){
        $contact = $app['session']->get('contact');
        $id['idAgency'] = $contact['idAgency'];
        $id['idAdvertisement'] = $idAdvertisement;
        $advertisement = $app['advertisements']->findAdByAgency($id);

        if (!$advertisement) {
            $app->abort(404, 'Advertisement does not exist');
        }
        $app['advertisement']->delete($id);

        return $app->redirect($app['url_generator']->generate('backend.advertisements.overview') . '?deleted');
    }

    /**
     * @param Application $app
     * @param             $idAdvertisement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Application $app, $idAdvertisement){
        $contact = $app['session']->get('contact');

        $id['idAgency'] = $contact['idAgency'];
        $id['idAdvertisement'] = $idAdvertisement;
        $advertisement = $app['advertisements']->findAdByAgency($id);
        if (!$advertisement) {
            $app->abort(404, 'Advertisement does not exist');
        }
        $propertytypes1 = $app['advertisements']->fetchPropertytype();
        $propertytypes = $this->reorderArray($propertytypes1);
        $cities2 = $app['cities']->findAll();
        $cities1 = $this->reorder2dArray($cities2, 'name');
        $cities = $this->reorderArray($cities1);

        $editform = $app['form.factory']->createNamed('editform')
            ->add('propertytype', 'choice', array(
                'choices'=>$propertytypes,
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => $propertytypes,
                            'message' => 'Kies een optie uit de lijst',
                            'strict' => true
                        )
                    )
                )
            ))
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
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een prijs mee te geven'
                    ))
                ),
                'data' => $advertisement['price']
            ))

            ->add('epc','integer', array(
                'constraints' => array(
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
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een straat in te geven'
                    )),
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
            ->add('city','choice', array(
                'empty_value' => $advertisement['name'],
                'empty_data' => $advertisement['name'],
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
            ));

        if ('POST' == $app['request']->getMethod()) {
            $editform->bind($app['request']);
            $data = $editform->getData();
            if ($data['city'] == null){
                $editform->get('city')->addError(new \Symfony\Component\Form\FormError('Gelieve een postcode of een gemeente in te vullen'));
            }

            if ($editform->isValid()) {
                $data = $editform->getData();
                $data['city'] = strtolower($data['city']);
                $resultCode = $app['cities']->findByCityName($data);
                if (!$resultCode){
                    $editform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                }
                $data['idCity'] = $resultCode['idCity'];

                $id['idAgency'] = $contact['idAgency'];
                $id['idAdvertisement'] = $idAdvertisement;
                $id['idLocation'] = $advertisement['idLocation'];

                $app['locations']->update($data, $id);

                $app['advertisements']->update($data, $id);
//@todo fotos uploaden


                //@todo send mail and redirect to auth.login
                return $app->redirect($app['url_generator']->generate('backend.advertisements.overview') . '?edited');
            }
        }
        return $app['twig']->render('Backend/Advertisements/edit.twig', array('id' => $advertisement['idAdvertisement'],'editform' => $editform->createView(), 'session' => $contact));
    }

    /**
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function add (Application $app){
        $contact = $app['session']->get('contact');

        $propertytypes1 = $app['advertisements']->fetchPropertytype();
        $propertytypes = $this->reorderArray($propertytypes1);
        $cities2 = $app['cities']->findAll();
        $cities1 = $this->reorder2dArray($cities2, 'name');
        $cities = $this->reorderArray($cities1);

        $newform = $app['form.factory']->createNamed('newform')
            ->add('propertytype', 'choice', array(
                'choices'=> $propertytypes,
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => $propertytypes,
                            'message' => 'Kies een optie uit de lijst',
                            'strict' => true
                        )
                    )
                )
            ))
            ->add('description','textarea', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een beschrijving na te laten'
                    ))
                )
            ))
            ->add('price','money', array(
                'constraints' => array(
                    new Assert\NotBlank(array(
                        'message' => 'Gelieve een prijs mee te geven'
                    ))
                ),
                'data' => 0.0
            ))
            ->add('epc','integer', array(
                'constraints' => array(
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
            ->add('city','choice', array(
                'choices' => $cities,
                'empty_value' => '',
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => $cities,
                            'message' => 'Kies een optie uit de lijst',
                            'strict' => true
                        )
                    ),
                )
            ));
        if ('POST' == $app['request']->getMethod()) {
            $newform->bind($app['request']);
            $data = $newform->getData();

            if ($data['city'] == null && $data['code'] == null){
                $newform->get('city')->addError(new \Symfony\Component\Form\FormError('Gelieve een postcode of een gemeente in te vullen'));
            }

            if ($newform->isValid()) {
                $data = $newform->getData();

                if(isset($data['city']) && $data['city'] != null ){
                    $data['city'] = strtolower($data['city']);
                    $resultCode = $app['cities']->findByCityName($data);
                    if (!$resultCode){
                        $newform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                    }
                    $data['idCity'] = $resultCode['idCity'];
                }

                $data['idAgency'] = $contact['idAgency'];

                $app['locations']->insert($data);
                $data['idLocation'] = $app['locations']->getLastInsertedId();

                $app['advertisements']->insert($data);

                //@todo fotos uploaden
                //@todo send mail and redirect to auth.login
                return $app->redirect($app['url_generator']->generate('backend.advertisements.overview') . '?added');
            }
        }

    return $app['twig']->render('Backend/Advertisements/new.twig', array('newform' => $newform->createView(), 'session' => $contact));

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