<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/07/13
 * Time: 20:25
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Repository;


class PhotosRepository extends \Knp\Repository{

    /**
     * @return string
     */
    public function getTableName () {
        // TODO: Implement getTableName() method.
        return 'Photos';
    }

    /**
     * @param mixed $idAdvertisment
     * @return array
     */
    public function find($idAdvertisment){
        return $this->db->fetchAssoc(
            'SELECT URL, priority FROM Photos WHERE idAdvertisement = ?',
            array($idAdvertisment));
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function delete($data){
        $this->db->delete(
            'photos', array(
                'idAdvertisement' => $data['idAdvertisement']
            )
        );
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function insert($data){
        $this->db->insert('photos',
            array(
                'idAdvertisement' =>$data['idAdvertisement'],
                'url' => $data['url'],
                'priority' => $data['priority']
            )
        );
    }
}