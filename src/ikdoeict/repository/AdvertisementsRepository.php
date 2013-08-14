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

    public function find($id){
        return $this->db->fetchAssoc(
            'SELECT advertisements.*, cities.*, locations.*, provinces.name as province, agencies.*
FROM advertisements
            INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            INNER JOIN provinces ON provinces.idProvince = cities.idProvince
            INNER JOIN agencies ON agencies.idAgency = advertisements.idAgency
            WHERE advertisements.idAdvertisement = ? ',
            array($id)
        );
    }

    /**
     * returns all full adv
     * @return array
     */
    public function findAll(){
        return $this->db->fetchAll(
            'SELECT
            advertisements.*,
            photos.*,
            locations.street,
            locations.housenumber,
            locations.bus,
            cities.name as city
            FROM advertisements
            INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            INNER JOIN photos    ON advertisements.idAdvertisement = photos.idAdvertisement
            WHERE photos.front = true AND advertisements.sold_rented = false'
        );
    }

    /**
     * @param $id
     * @return array
     */
    public function findAllByAgency(array $id){
        return $this->db->fetchAll(
            'SELECT advertisements.*, cities.name
            FROM advertisements
            INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            WHERE advertisements.idAgency = ?
            ORDER BY advertisements.updated_on DESC',
            array($id['idAgency'])
        );
    }

    /**
     * @param array $id
     * @return array
     */
    public function findAdByAgency( array $id){
        return $this->db->fetchAssoc(
            'SELECT advertisements.*, cities.*, locations.*, provinces.name as province
FROM advertisements
            INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
            INNER JOIN cities    ON locations.idCity = cities.idCity
            INNER JOIN provinces ON provinces.idProvince = cities.idProvince
            WHERE idAgency = ? AND advertisements.idAdvertisement = ? ',
            array($id['idAgency'], $id['idAdvertisement'])
        );
    }


    /**
     * @return mixed
     */
    public function fetchPropertytype(){
        $result = $this->db->fetchAssoc('SHOW COLUMNS FROM advertisements WHERE Field="propertytype"');
        preg_match_all("/'(.*?)'/",$result['Type'], $enumArr);
        $enum_fields = $enumArr[1];
        return $enum_fields;
    }

    /**
     * @return array
     */
    public function findMostViews(){
        return $this->db->fetchAll('
        SELECT advertisements.*, locations.*, cities.name as city, photos.*
        FROM advertisements
        INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
        INNER JOIN photos ON photos.idAdvertisement = advertisements.idAdvertisement
        INNER JOIN cities ON cities.idCity = locations.idLocatie
        WHERE photos.front = true  AND advertisements.sold_rented = false
        ORDER BY advertisements.views DESC
        LIMIT 0,5
        ');
    }

    /**
     * @return array
     */
    public function findMostRecent(){
        return $this->db->fetchAll('
        SELECT advertisements.*, locations.*, cities.name as city, photos.*
        FROM advertisements
        INNER JOIN locations ON locations.idLocatie = advertisements.idLocation
        INNER JOIN photos ON photos.idAdvertisement = advertisements.idAdvertisement
        INNER JOIN cities ON cities.idCity = locations.idLocatie
        WHERE photos.front = true  AND advertisement.sold_rented = false
        ORDER BY advertisements.updated_on DESC
        LIMIT 0,10
        ');
    }


    /**
     * @param $data
     * @return array
     */
    public function findAllByFilter($data){
        if($data['from_price'] != null){
            $where[] = sprintf(" advertisements.price <= %u", $data['from_price'] );
        }
        else if($data['to_price'] != null && $data['from_price'] != null){
            $where[] = sprintf(" price >= %u", $data['to_price'] );
        }
        else if($data['from_price']){
            $where[] = sprintf(" advertisements.price BETWEEN %u AND  %u", $data['from_price'], $data['to_price'] );
        }
        if($data['province'] != null){
            $where[] = sprintf(" provinces.name = \"%s\"",$data['province']);
        }
        if($data['propertytype'] != null){
            $where[] = sprintf(" advertisements.propertytype = \"%s\"", $data['propertytype']);
        }
        if($data['chambers'] != null){
            $where[] = sprintf(" advertisements.chambers = %u", $data['chambers']);
        }
        $where2 = implode(' AND ',$where);
//var_dump($where2);
        return $this->db->fetchAll(sprintf('
        SELECT * FROM advertisements
        INNER JOIN locations  ON locations.idLocatie = advertisements.idLocation
        INNER JOIN cities     ON cities.idCity = locations.idCity
        INNER JOIN provinces  ON provinces.idProvince = cities.idProvince
        INNER JOIN agencies ON agencies.idAgency = advertisements.idAgency
        INNER JOIN photos ON photos.idAdvertisement = advertisements.idAdvertisement
        WHERE photos.front = true AND %s', $where2));
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function insert(array $data){
        $this->db->insert(
            'advertisements', array(
                'idAgency'      => $data['idAgency'],
                'idLocation'    => $data['idLocation'],
                'rent_sell'     => $data['rent_sell'],
                'sold_rented'   => $data['sold_rented'],
                'propertytype'  => $data['propertytype'],
                'price'         => $data['price'],
                'EPC'           => $data['epc'],
                'ki'            => $data['ki'],
                'chambers'      => $data['chambers'],
                'living_area'   => $data['living_area'],
                'total_area'    => $data['total_area'],
                'description'   => $data['description'],
                'views'         => 0,
                'updated_on'    => date("Y-m-d H:i:s")
            )
        );
    }

    /**
     * @param array $data
     * @param array $id
     * @return int|void
     */
    public function update(array $data, array $id){
        date_default_timezone_set('Europe/Brussels');
        $this->db->update(
            'advertisements', array(
                'rent_sell'     => $data['rent_sell'],
                'sold_rented'   => $data['sold_rented'],
                'propertytype'  => $data['propertytype'],
                'price'         => $data['price'],
                'EPC'           => $data['epc'],
                'ki'            => $data['ki'],
                'chambers'      => $data['chambers'],
                'living_area'   => $data['living_area'],
                'total_area'    => $data['total_area'],
                'description'   => $data['description'],
                'updated_on'    => date("Y-m-d H:i:s")
            ),
            array('idAdvertisement' => $id['idAdvertisement'], 'idAgency' => $id['idAgency'])
        );
    }

    public function updateViews(array $data, $id){
        date_default_timezone_set('Europe/Brussels');
        $this->db->update(
            'advertisements', array(
                'views' => $data['views'] + 1
            ),
            array('idAdvertisement' => $id)
        );
    }

    /**
     * @param array $id
     * @internal param array $data
     * @return int|void
     */
    public function delete(array $id){
        $this->db->delete(
            'advertisements',array(
            'idAdvertisement'   => $id['idAdvertisement'],
                'idAgency'      => $id['idAgency']
            )
        );
    }
    /**
     * @return string
     */
    public function getLastInsertedId(){
        return $this->db->lastInsertId();
    }

}