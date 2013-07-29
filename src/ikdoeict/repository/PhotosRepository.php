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
    public function find($idAdvertisment){
        return $this->db->fetchAssoc(
            'SELECT URL, priority FROM Photos WHERE idAdvertisement = ?',
            array($idAdvertisment));
    }
    public function insert($data){
        $this->db->insert('photos',
            array(
                '' => ''
            )
        );
    }
}