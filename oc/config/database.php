<?php defined('SYSPATH') or die('No direct script access.');
return array
(
    'default' => array(
        'type'       => 'mysqli',
        'connection' => array(
            'hostname'   => 'localhost',
            'username'   => 'essce_qwe',
            'password'   => 'qweqwe',
            'persistent' => FALSE,
            'database'   => 'essce_qwe',
            ),
        'table_prefix' => 'oc2_',
        'charset'      => 'utf8',
        'profiling'    => (Kohana::$environment===Kohana::DEVELOPMENT)? TRUE:FALSE,
     ),
);