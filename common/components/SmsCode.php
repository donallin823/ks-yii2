<?php
/**
 * 手机短信验证码组件
 */

namespace common\components;

class SmsCode
{
    public $appId = "";
    public $appKey = "";
    public $signId = "";
    public $tplId = "";
    public $urlWithSign = "";
    /**
     * @var KsRedis|\Redis
     */
    public $redis;

    public function __construct()
    {
        $this->redis = KsComponent::redis();
    }

    protected static function phoneNumbersToArray($nationCode, $phoneNumbers)
    {
        $i = 0;
        $tel = array();
        do {
            $telElement = new \stdClass();
            $telElement->nationcode = $nationCode;
            $telElement->mobile = $phoneNumbers[$i];
            array_push($tel, $telElement);
        } while (++$i < count($phoneNumbers));
        return $tel;
    }

    protected static function calculateSigForTemplAndPhoneNumbers($appkey, $random, $curTime, $phoneNumbers)
    {
        $phoneNumbersString = implode(',', $phoneNumbers);
        return hash("sha256", "appkey={$appkey}&random={$random}&time={$curTime}&mobile={$phoneNumbersString}");
    }

    public function sendWithSign($phone, $code)
    {
        $random = mt_rand(10000, 99999);
        $phoneNumbers = [$phone];
        $curTime = time();
        $params = [
            "tel" => self::phoneNumbersToArray('86', $phoneNumbers),
            "sign" => $this->signId,
            "params" => [$code, 5],
            "random" => $random,
            "sig" => self::calculateSigForTemplAndPhoneNumbers($this->appKey, $random, $curTime, $phoneNumbers),
            "time" => $curTime,
            "tpl_id" => $this->tplId,
            "extend" => "",
            "ext" => ""
        ];
        $url = "{$this->urlWithSign}?sdkappid={$this->appId}&random={$random}";
        $response = HttpUtils::doRequest($url, json_encode($params), 'post'); // TODO:test
        HttpUtils::checkResponse($response);
        if (is_array($response) && $response['result'] == 0) {
            return true;
        } else {
            KsComponent::logger()->error("验证码发送失败：$response", "sms");
            return false;
        }
    }

    /**
     * 生成数字的验证码
     */
    public static function genSmsCode($maxLength = 4)
    {
        list($usec, $sec) = explode(' ', microtime());
        $randSeed = (float)$sec + ((float)$usec * 100000);
        mt_srand($randSeed);
        $code = '';
        for ($i = 0; $i < $maxLength; $i++) {
            $code .= mt_rand(0, 9);
        }
        return $code;
    }

    public function getSmsCode($phone, $type)
    {
        $key = RKey::smsCode($phone, $type);
        return $this->redis->get($key);
    }

    public function setSmsCode($phone, $type, $code)
    {
        $key = RKey::smsCode($phone, $type);
        return $this->redis->setex($key, 300, $code);
    }

    // 创建一个验证码。5分钟内，不重复生成验证码。
    public function createSmsCode($phone, $type)
    {
        $code = $this->getSmsCode($phone, $type);
        if (!$code) {
            $code = self::genSmsCode();
        }
        return $code;
    }

    // 检查验证码发送是否超过频率
    public function isOverSendLimit($phone, $type)
    {
        $key = RKey::smsCode($phone, $type);
        $ttl = $this->redis->ttl($key);
        return $ttl > 240;
    }

    // 检查验证码是否想等
    public function isRightCode($phone, $type, $code)
    {
        //判断验证码格式
        if (!KsUtils::isSmsCode($code)) {
            return false;
        }
        $sysCode = $this->getSmsCode($phone, $type);
        return $code == $sysCode;
    }

    // 删除验证码
    public function delSmsCode($phone, $type)
    {
        $key = RKey::smsCode($phone, $type);
        return $this->redis->del($key);
    }

}