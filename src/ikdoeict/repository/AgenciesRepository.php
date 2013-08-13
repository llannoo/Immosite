<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/07/13
 * Time: 20:24
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Repository;


class AgenciesRepository extends \Knp\Repository{

    /**
     * @return string
     */
    public function getTableName () {
        return 'Agencies';
    }

    /**
     * @param mixed $id
     * @return array
     */
    public function find($id){
        return $this->db->fetchAssoc('
        SELECT agencies.*, locations.*, cities.name as city from agencies
        INNER JOIN contacts ON contacts.idAgency = agencies.idAgency
        INNER JOIN locations ON locations.idLocatie = agencies.idLocation
        INNER JOIN cities ON cities.idCity = locations.idCity
        WHERE agencies.idAgency = ? AND contacts.idContact = ?', array($id['idAgency'], $id['idContact']));
    }

    public function findBestAgencies(){
        return $this->db->fetchAll(
            'SELECT count(advertisements.idAdvertisement) as aantal, agencies.*  FROM agencies
            INNER JOIN advertisements ON advertisements.idAgency = agencies.idAgency
            GROUP BY agencies.name
            ORDER BY aantal DESC
            LIMIT 0,3
            '
        );
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function delete(array $data){
        $this->db->delete(
            'agencies', array(
                'idAgency' => $data['idAgency']
            )
        );
    }
    /**
     * @param array $data
     * @return int|void
     */
    public function insert(array $data){
        $this->db->insert(
            'agencies',array(
                'idLocation' =>     $data['idLocation'],
                'name' =>           $data['agencyname'],
                'logo' =>       isset($data['logoName'])    ? $data['logoName'] : null,
                'website' =>    isset($data['website'])     ? $data['website']:null,
                'description' => isset($data['description'])? $data['description']: null,
                'tel' =>        isset($data['tel'])         ? $data['tel'] : null,
                'fax' =>        isset($data['fax'])         ? $data['fax'] : null
            )
        );
    }

    /**
     * @param array $data
     * @param array $id
     * @return int|void
     */
    public function update(array $data, array $id){
        $this->db->update(
            'agencies',array(
                'name' =>       $data['agencyname'],
                'logo' =>       isset($data['logoName'])    ? $data['logoName'] : null,
                'website' =>    isset($data['website'])     ? $data['website']:null,
                'description' => isset($data['description'])? $data['description']: null,
                'tel' =>        isset($data['tel'])         ? $data['tel'] : null,
                'fax' =>        isset($data['fax'])         ? $data['fax'] : null
            ),
            array('idAgency' => $id['idAgency'])
        );
    }

    /**
     * @return string
*/
    public function getLastInsertedId(){
        return $this->db->lastInsertId();
    }
}