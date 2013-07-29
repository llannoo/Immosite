<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/07/13
 * Time: 20:24
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Repository;


class AdvertisementsRepository extends \Knp\Repository{

    /**
     * @return string
     */
    public function getTableName () {
        // TODO: Implement getTableName() method.
        return 'Advertisements';
    }

    /**
     * returns all full adv
     * @return array
     */
    public function findAll(){
        return $this->db->fetchAll(
            'SELECT
            advertisements.*,
            photos.URL,
            locations.street,
            locations.housenumber,
            locations.bus,
            cities.name
            FROM advertisements
            INNER JOIN locations ON locations.idLocation = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            INNER JOIN photos    ON advertisments.idAdvertisement = photos.idAdvertisement'
        );
    }

    /**
     * @param $id
     * @return array
     */
    public function findAllByAgency($id){
        return $this->db->fetchAll(
            'SELECT advertisements.*, cities.name
            FROM advertisements
            INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            WHERE idAgency = ?',
            array($id)
        );
    }
    /**
 * @param $idAgency
 * @param $idAd
 * @return array
 */
    public function findAdByAgency($idAgency,$idAd ){
        return $this->db->fetchAssoc(
            'SELECT advertisements.*, cities.*, locations.*
FROM advertisements
            INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            WHERE idAgency = ? AND advertisements.idAdvertisement = ?',
            array($idAgency, $idAd)
        );
    }
    public function fetchPropertytype(){
        $result = $this->db->fetchAssoc('SHOW COLUMNS FROM advertisements WHERE Field="propertytype"');
        preg_match_all("/'(.*?)'/",$result['Type'], $enumArr);
        $enum_fields = $enumArr[1];
        return $enum_fields;
    }


    /**
     * @return string
     */
    public function getLastInsertedId(){
        return $this->db->lastInsertId();
    }
}