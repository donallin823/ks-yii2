<?php
/**
 * User: donallin
 */

namespace api\controllers;

use yii\base\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        echo 'Hello Api!';
    }
}