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
                'EPC'           => $data['EPC'],
                'ki'            => $data['ki'],
                'chambers'      => $data['chambers'],
                'living_area'   => $data['living_area'],
                'total_area'    => $data['total_area'],
                'description'   => $data['description'],
                'views'         => $data['views'],
                'updated_on'    => $data['updated_on']
            )
        );
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function update(array $data){
        $this->db->update(
            'advertisements', array(
                'idAgency'      => $data['idAgency'],
                'idLocation'    => $data['idLocation'],
                'rent_sell'     => $data['rent_sell'],
                'sold_rented'   => $data['sold_rented'],
                'propertytype'  => $data['propertytype'],
                'price'         => $data['price'],
                'EPC'           => $data['EPC'],
                'ki'            => $data['ki'],
                'chambers'      => $data['chambers'],
                'living_area'   => $data['living_area'],
                'total_area'    => $data['total_area'],
                'description'   => $data['description'],
                'views'         => $data['views'],
                'updated_on'    => $data['updated_on']
            ),
            array('idAdvertisement' => $data['idAdvertisement'], 'idAgency' => $data['idAgency'])
        );
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function delete($data){
        $this->db->delete(
            'advertisements',array(
            'idAdvertisement'   => $data['idAdvertisement'],
                'idAgency'      => $data['idAgency']
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