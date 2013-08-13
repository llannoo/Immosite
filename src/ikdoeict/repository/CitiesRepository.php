<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/07/13
 * Time: 20:26
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Repository;


class CitiesRepository extends \Knp\Repository{

    /**
     * @return string
     */
    public function getTableName () {
        // TODO: Implement getTableName() method.
        return 'Cities';
    }

    public function findAllProvinces(){
        return $this->db->fetchAll('SELECT name FROM provinces');
    }
    /**
     * @return array
     */
    public function findAll(){
        return $this->db->fetchAll('SELECT DISTINCT cities.code, cities.name FROM cities ORDER BY code');
    }

    /**
     * @return array
     */
    public function findAllCodes(){
        return $this->db->fetchAll('SELECT DISTINCT cities.code FROM cities ORDER BY code');
    }
    /**
     * @param $data
     * @return array
     */
    public function findByCode($data){
        return $this->db->fetchAssoc(
            'SELECT cities.idCity as idCity, cities.code as code, cities.name as name
            FROM cities
            WHERE code = ?',
            array($data['code'])
        );
    }

    /**
     * @param $data
     * @return array
     */
    public function findByCityName($data){
        return $this->db->fetchAssoc(
            'SELECT cities.idCity as idCity, cities.code as code, cities.name as name
            FROM cities
            WHERE cities.alpha = ?',
            array($data['city'])
        );
    }

    /**
     * @param $data
     * @return array
     */
    public function findByCityAndCode($data){
        return $this->db->fetchAssoc(
            'SELECT cities.idCity as idCity, cities.code as code, cities.name as name
            FROM cities
            WHERE cities.alpha = ? AND cities.code = ?',
            array($data['city'], $data['code'])
        );
    }
    /*
    public function searchCode (){
        return $this->db->fetchAll('
        SELECT cities.code, cities.name
        FROM cities
        ORDER BY cities.code ASC
        ');
    }*/
}