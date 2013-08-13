<?php
namespace Ikdoeict\Provider\Controller\Frontend;

use Silex\Application;
use Silex\ControllerCollection;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/08/13
 * Time: 14:38
 * To change this template use File | Settings | File Templates.
 */

class HomeController implements \Silex\ControllerProviderInterface{
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
            ->match('/', array($this, 'home'))
            ->method('GET|POST')
            ->bind('frontend.home');

        $controllers
            ->match('/{idAdvertisement}/detail', array($this, 'detail'))
            ->method('GET|POST')
            ->bind('frontend.advertisement.detail');

        return $controllers;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function home (Application $app){
        $provinces2 = $app['cities']->findAllProvinces();
        $provinces1 = $this->reorder2dArray($provinces2, 'name');
        $provinces = $this->reorderArray($provinces1);

//var_dump($provinces);

        $propertytypes1 = $app['advertisements']->fetchPropertytype();
        $propertytypes = $this->reorderArray($propertytypes1);
//ar_dump($propertytypes);

        $homeform = $app['form.factory']->createNamed('profileform')
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
            $homeform->bind($app['request']);
            if ($homeform->isValid()) {
                $data = $homeform->getData();
                $result = $app['advertisements']->findAllByHomeFilter($data);
            }
        }

        $headliners = $app['advertisements']->findMostViews();
        foreach($headliners as $key=>$value) {
            $headliners[$key]['photopath'] = $app['photos.base_url'] . $headliners[$key]['URL'];
        }
  //      var_dump($headliners);
        $carousel = $app['advertisements']->findMostRecent();
        foreach($carousel as $key=>$value) {
            $carousel[$key]['photopath'] = $app['photos.base_url'] . $carousel[$key]['URL'];
        }
        $agencies = $app['agencies']->findBestAgencies();
       // var_dump($agencies);
        foreach($agencies as $key=>$value) {
            $agencies[$key]['logopath'] = $app['logo.base_url'] . $agencies[$key]['logo'];
        }

        return $app['twig']->render('Frontend/home/home.twig',
            array(
                'agencies' => $agencies,
                'carousel' => $carousel,
                'headliners' => $headliners,
                'homeform' => $homeform->createView()));
    }

    public function detail (Application $app, $idAdvertisement){
        $advertisement = $app['advertisement']->find($idAdvertisement);
        if (!$advertisement) {
            $app->abort(404, 'Advertisement does not exist');
        }
        $photos = $app['photos']->find($idAdvertisement);
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