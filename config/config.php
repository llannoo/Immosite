<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Lorenzo
 * Date: 9/07/13
 * Time: 15:11
 * To change this template use File | Settings | File Templates.
 */

return array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'immo_db',
        'user' => 'root',
        'password' => 'Azerty123'
    ),
    'PASSWORD_SALT' => '(rt§t,;:,HJFHJKZrèfh!çàkdhg^$ù`ù^$m,;:j)',

    'swiftmailer.options' => array(
        'host'       => 'smtp2.kahosl.be',
        'port'       => 25,
        'username'   => '',
        'password'   => '',
        'encryption' => null,
        'auth_mode'  => null
    )
);