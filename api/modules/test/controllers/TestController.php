<?php
/**
 * User: donallin
 */

namespace api\modules\test\controllers;

use api\controllers\ApiCoreController;
use common\components\KsComponent;
use common\components\KsUtils;
use Yii;

class TestController extends ApiCoreController
{
    public function actionIndex()
    {
        echo 'hello test';
    }
}
