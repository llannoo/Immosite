<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 10/07/13
 * Time: 19:53
 * To change this template use File | Settings | File Templates.
 */

namespace Ikdoeict\Repository;


class ContactsRepository extends \Knp\Repository{

    /**
     * @return string
     */
    public function getTableName () {
        return 'contacts';
    }

    /**
     * @param $id
     * @return array
     */
    public function findAllByAgency($id){
        return $this->db->fetchAll('SELECT * FROM Contact WHERE idAgency = ?', array($id));
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function findContact($data){
        var_dump($data);
        return $this->db->fetchAssoc(
            'SELECT * FROM contacts
            WHERE email = ? AND password = ?',
            array($data['email'], $data['password'])
        );
    }

    /**
     * @param array $data
     * @return int|void
     */
    public function insert($data){
        $this->db->insert(
            'contacts',array(
                'idAgency' => $data['idAgency'],
                'email' => $data['email'],
                'password' => $data['password'],
                'tel' => isset($data['tel']) ? $data['tel'] : null
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