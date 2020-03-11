<?php
/**
 * Created by PhpStorm.
 * User: donallin
 * Date: 2018/11/9
 * Time: 11:57
 */
$configPath = trim(file_get_contents(__DIR__ . "/../../env"));
$configDir = __DIR__ . "/" . $configPath;
if (!is_dir($configDir)) {
    throw new Exception("配置文件目录 [{$configPath}] 不存在");
}

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');

$components = array_merge(
    require $configDir . '/component.php',
    require $configDir . '/db.php',
    require $configDir . '/redis.php'
);
$config = [
    'api' => [
        'id' => 'app-api',
        'basePath' => dirname(__DIR__) . '/../api/',
        'controllerNamespace' => 'api\controllers',
        'timeZone' => 'Asia/Shanghai',
        'language' => 'zh-CN',
        'components' => $components,
        'params' => $configDir . '/main.php',
        'modules' => [
            'test' => [
                'class' => 'api\modules\test\Module',
            ],
        ],
    ],
];

return $config;