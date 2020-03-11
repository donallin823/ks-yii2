<?php
/**
 * User: donallin
 */

namespace api\controllers;

use common\components\ErrorCode;
use common\components\KsComponent;
use common\components\KsUtils;
use common\components\RKey;
use common\core\CoreController;
use common\services\SsoService;
use common\services\WexinService;
use Yii;

define('STATUS_ENABLED', 1);
define('STATUS_DISABLED', 2);
define('APP_KEY', 'OJfjomZZSvL$uF36');

class ApiCoreController extends CoreController
{
    public $is_super;
    public $token_white_list = [ // 权限白名单
    ];
    public $sign_white_list = [ // sign白名单
    ];

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        try {
            $this->checkLogin();   // 检测登录态
            $this->checkSign();    // 检查sign
            $this->checkCommonParams(); // 检测公共参数
        } catch (\Exception $e) {
            $errcode = $e->getCode();
            $errmsg = $e->getMessage();
            return $this->response($errcode, [], $errmsg);
        }
        return true;
    }

    private function checkCommonParams()
    {
        // 1、version
        $this->version = $this->parse('version', '', 'string');
    }

    /**
     * check login
     * @throws \Exception
     */
    private function checkLogin()
    {
        $redis = KsComponent::redis();
        $request = Yii::$app->getRequest();
        $headers = $request->getHeaders();

        $login_key = RKey::getApiSession(); // 登录态

        // TOKEN验证
        $token = $headers->get('X-Token'); //token有效时间
        $expire_time = 3 * 24 * 3600;
        $user_info = $redis->hGetAll("{$login_key}{$token}");
        $path = strtolower($request->getPathInfo());
        if (KsUtils::validatePath($path, $this->token_white_list) && empty($user_info)) { // 白名单；如果用户信息正确,不走白名单
            return true;
        }
        if (empty($token)) {
            throw new \Exception('param token no exists', ErrorCode::ERR_USER_NOT_LOGIN);
        }
        if (empty($user_info)) {
            throw new \Exception('user not login', ErrorCode::ERR_USER_NOT_LOGIN);
        }
        $redis->expire("{$login_key}{$token}", $expire_time); // 每次取，设置有效时间
        $this->user_id = $user_info['user_id'];
        $this->user_info = $user_info;
        $this->token = $token;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function checkSign()
    {
        $sign = $this->parse('sign', '', 'string');
        $timestamp = $this->parse('timestamp', '', 'string');
        $nonce = $this->parse('nonce', '', 'string'); // 10位的随机数
        $now_time = time();
        $request = Yii::$app->getRequest();
        $path = strtolower($request->getPathInfo());

        if (KsUtils::validatePath($path, $this->sign_white_list)) { // 验证名单；
            return true;
        }
        if (empty($sign) || empty($timestamp) || empty($nonce)) {
            throw new \Exception('param sign|timestamp|nonce no exists', ErrorCode::ERR_PARAM_CODE);
        }
        if (strlen($nonce) != 10) {
            throw new \Exception('param invalid!', ErrorCode::ERR_PARAM_CODE);
        }

        // 验签
        if ($timestamp <= $now_time - 10 * 60) { // sign10分钟有效
            throw new \Exception('sign timeout', ErrorCode::ERR_SIGN_CODE);
        }
        $request_params = $this->getRequestParams();
        $expect_sign = KsUtils::getSign($request_params, APP_KEY);
        if ($sign !== $expect_sign) {
            throw new \Exception('sign invalid', ErrorCode::ERR_SIGN_CODE);
        }
    }
}