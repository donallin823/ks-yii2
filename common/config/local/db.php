<?php
/**
 * Created by PhpStorm.
 * User: donallin
 * Date: 2018/11/9
 * Time: 11:45
 */
return [
    'db' => [
        'class' => 'common\components\KsModel',
        'dbConfig' => [
            'main' => [
                'dsn' => 'mysql:host=xxx;dbname=kaiser_xxx',
                'username' => 'root',
                'password' => 'ks@2017',
                'charset' => 'utf8',
            ],
            'slave' => [
                'dsn' => 'mysql:host=xxx;dbname=kaiser_xxx',
                'username' => 'root',
                'password' => 'ks@2017',
                'charset' => 'utf8',
            ]
        ]
    ]
];