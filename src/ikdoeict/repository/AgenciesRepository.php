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
     * @param array $data
     * @return int|void
     */
    public function insert($data){
        $this->db->insert(
            'agencies',array(
                'idLocation' => $data['idLocation'],
                'name' => $data['agencyname'],
                'logo' => $data['logoName'],
                'website' => $data['website'],
                'description' => $data['description'],
                'tel' => $data['tel'],
                'fax' => $data['fax']
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