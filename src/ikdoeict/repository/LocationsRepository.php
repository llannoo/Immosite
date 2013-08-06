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

    /**
     * @param array $data
     * @return int|void
     */
    public function insert($data){
        $this->db->insert(
            'locations',array(
               'idCity' =>      isset($data['idCity'])      ? $data['idCity'] : null,
               'street' =>      isset($data['street'])      ? $data['street'] : null,
               'housenumber' => isset($data['housenumber']) ? $data['housenumber'] : null,
               'bus' =>         isset($data['bus'])         ? $data['bus'] : null
        ));
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function update($data){
        $this->db->update(
            'locations',array(
                'idCity' =>      isset($data['idCity'])      ? $data['idCity'] : null,
                'street' =>      isset($data['street'])      ? $data['street'] : null,
                'housenumber' => isset($data['housenumber']) ? $data['housenumber'] : null,
                'bus' =>         isset($data['bus'])         ? $data['bus'] : null
            ),
            array(
                'idLocatie' => $data['idLocation']
            )
        );
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function delete($data){
        $this->db->delete(
            'locations', array(
                'idLocatie' => 'idLocation'
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