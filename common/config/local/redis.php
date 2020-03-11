<?php
/**
 * Created by PhpStorm.
 * User: donallin
 * Date: 2018/11/9
 * Time: 11:45
 */
return [
    'redis' => [ // 可拓展
        'class' => 'common\components\KsModel',
        'redisConfig' => [
            'main' => [
                'host' => '10.10.40.33',
                'port' => 6379,
                'password' => 'ks_2017',
                'dbId' => 0,
            ],
            'slave' => [
                'host' => '10.10.40.33',
                'port' => 6379,
                'password' => 'ks_2017',
                'dbId' => 0,
            ]
        ]
    ]
];