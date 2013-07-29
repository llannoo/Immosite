<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 12/07/13
 * Time: 15:08
 * To change this template use File | Settings | File Templates.
 */
namespace Ikdoeict\lib;

use Silex\Application;
use Silex\ControllerCollection;

class TwigDataExtension extends \Twig_Extension{

    public function getFunctions (){
        return array(
            'codeFunction' => new \Twig_Function_Method($this, 'getCodes')
        );
    }

   public function getCodes(){/*
        return $this->db->fetchAll('
        SELECT cities.code, cities.name
        FROM cities
        ORDER BY cities.code ASC
        ',array());*/
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName () {
        // TODO: Implement getName() method.
        return 'DataExtension';
    }
}