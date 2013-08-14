<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 13/08/13
 * Time: 22:33
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Provider\Controller\Frontend;


use Silex\Application;
use Silex\ControllerCollection;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AdvertisementController  implements  ControllerProviderInterface{


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

        $controllers
            ->match('/', array($this, 'overview'))
            ->method('GET|POST')
            ->bind('frontend.advertisements.overview');

        $controllers
            ->match('/{idAdvertisement}/detail', array($this, 'detail'))
            ->assert('idAdvertisement', '\d+')
            ->method('GET|POST')
            ->bind('frontend.advertisement.detail');

        return $controllers;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function overview (Application $app){
        $advertisements = $app['advertisements']->findAll();
        foreach($advertisements as $key=>$value) {
            $advertisements[$key]['photopath'] = $app['photos.base_url'] . $advertisements[$key]['URL'];
        }

        $provinces2 = $app['cities']->findAllProvinces();
        $provinces1 = $this->reorder2dArray($provinces2, 'name');
        $provinces = $this->reorderArray($provinces1);

//var_dump($provinces);

        $propertytypes1 = $app['advertisements']->fetchPropertytype();
        $propertytypes = $this->reorderArray($propertytypes1);
//ar_dump($propertytypes);

        $filterform = $app['form.factory']->createNamed('filterform')
            ->add('from_price','text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                )
            ))
            ->add('to_price', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                )
            ))
            ->add('province', 'choice',array(
                'empty_value' => " ",
                'empty_data' => " ",
                'choices' => $provinces,
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => $provinces,
                            'message' => 'Kies een optie uit de lijst',
                            'strict' => true
                        )

                    )
                )

            ))
            ->add('chambers', 'integer', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Range(
                        array(
                            'min' => 0,
                            'minMessage' => 'De waarde groter dan 0 zijn',
                            'invalidMessage'=> 'Dit is geen geldige waarde'
                        )
                    )
                )
            ))
            ->add('propertytype', 'choice', array(
                'choices' => $propertytypes,
                'constraints' => array(
                    new Assert\Choice(
                        array(
                            'choices' => $propertytypes,
                            'message' => 'Kies een optie uit de lijst',
                            'strict' => true
                        )
                    )
                )

            ));

        if ('POST' == $app['request']->getMethod()) {
            $filterform->bind($app['request']);
            if ($filterform->isValid()) {
                $data = $filterform->getData();
                $data['city'] = strtolower($data['city']);
                $resultCode = $app['cities']->findByCityName($data);
                if (!$resultCode){
                    $filterform->get('city')->addError(new \Symfony\Component\Form\FormError('Naam van de gemeente wordt niet herkend'));
                }
                if(isset($data['to_price']) && ($data['from_price'])){
                    if ($data['to_price'] <= $data['from_price']){
                        $filterform->get('to_price')->addError(new \Symfony\Component\Form\FormError('Het bereik van de prijs is foutief'));
                    }
                }


                $result = $app['advertisements']->findAllByFilter($data);

                return $app['twig']->render('Frontend/Advertisements/overview.twig', array('advertisements' => $result));
            }
        }

        return $app['twig']->render('Frontend/Advertisements/overview.twig', array('filterform' => $filterform->createView(), 'advertisements' => $advertisements));
    }

    /**
     * @param Application $app
     * @param             $idAdvertisement
     * @return mixed
     */
    public function detail (Application $app, $idAdvertisement){
        $advertisement = $app['advertisements']->find($idAdvertisement);
        if (!$advertisement) {
            $app->abort(404, 'Advertisement does not exist');
        }
        $photos = $app['photos']->find($idAdvertisement);
        foreach($photos as $key=>$value) {
            $photos[$key]['photopath'] = $app['photos.base_url'] . $photos[$key]['URL'];
        }

        $app['advertisements']->updateView($advertisement, $idAdvertisement);
        return $app['twig']->render('Frontend/Advertisements/detail.twig', array('photos' => $photos, 'advertisement' => $advertisement));
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