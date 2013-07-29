<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/07/13
 * Time: 20:22
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Repository;


class LocationsRepository extends \Knp\Repository {

    /**
     * @return string
     */
    public function getTableName () {
        // TODO: Implement getTableName() method.
        return 'Locations';
    }

    public function insert($data){
        $this->db->insert('locations',array(
           'idCity' => $data['idCity'],
           'street' => $data['street'],
           'housenumber' => $data['housenumber'],
           'bus' => $data['bus']
        ));
    }

    public function find($id){
        return;
    }

    /**
     * @return string
     */
    public function getLastInsertedId(){
        return $this->db->lastInsertId();
    }

}