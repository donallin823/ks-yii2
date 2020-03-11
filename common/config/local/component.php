<?php
/**
 * Created by PhpStorm.
 * User: donallin
 * Date: 2018/11/9
 * Time: 11:44
 */
return [
    'request' => [
        'csrfParam' => '_csrf-backend',
        'cookieValidationKey' => 'G9kYTvVO4p7TYC-0TUjt1NvY9NdXgaKo',
        'enableCsrfValidation' => false,
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
            'text/json' => 'yii\web\JsonParser',
        ],
    ],
    'errorHandler' => [
        'errorAction' => 'site/error',
    ],
    'urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => true, // 是否隐藏入口脚本
        'rules' => [
        ],
    ],
    'wechatSdk' => [
        'class' => 'common\components\WechatSdk',
        'appId' => 'xxx',
        'appSecret' => 'xxx'
    ],
    'ksLogger' => [
        'class' => 'common\components\KsLogger'
    ],
    'jfSsoSdk' => [
        'class' => 'common\components\SsoSdk',
        'url' => 'http://sso.xxx.com.cn',
        'clientId' => 'xxx',
        'clientSecret' => 'xxx',
        'baseUrl' => 'xxx'
    ]
];