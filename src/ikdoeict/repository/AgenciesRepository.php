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

    public function delete($data){
        $this->db->delete(
            'agencies',array(
                'idAgency' => $data['idAgency']
            )
        );
    }
    /**
     * @param array $data
     * @return int|void
     */
    public function insert($data){
        $this->db->insert(
            'agencies',array(
                'idLocation' => $data['idLocation'],
                'name' => $data['agencyname'],
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
     * @return int|void
     */
    public function update($data){
        $this->db->update(
            'agencies',array(
                'idLocation' => $data['idLocation'],
                'name' => $data['agencyname'],
                'logo' =>       isset($data['logoName'])    ? $data['logoName'] : null,
                'website' =>    isset($data['website'])     ? $data['website']:null,
                'description' => isset($data['description'])? $data['description']: null,
                'tel' =>        isset($data['tel'])         ? $data['tel'] : null,
                'fax' =>        isset($data['fax'])         ? $data['fax'] : null
            ),
            array('idAgency' => $data['idAgency'])
        );
    }

    /**
     * @return string
*/
    public function getLastInsertedId(){
        return $this->db->lastInsertId();
    }
}