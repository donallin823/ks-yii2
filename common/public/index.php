<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/evn_config.php';
$app = new yii\web\Application($config['api']);
$app->run();
