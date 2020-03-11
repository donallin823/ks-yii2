<?php
/**
 * Created by PhpStorm.
 * User: jimxu
 * Date: 2018/11/9
 * Time: 15:03
 * 凯撒文件上传组件
 */
namespace common\components;

use yii\base\Component;

class KsCdn extends Component {
    public $apiUrl = "http://fileup.ksgame.com/upload-file.php";
    public $cdnUrl = "https://upcdn.ksgame.com/";
    public $appKey = "~#kjsofjwojr&*()";

    /**
     * 执行文件上传
     * @param $file
     * @param string $appId
     * @param array $options curl参数选项
     * @return mixed
     * @throws \Exception
     */
    public function upload($file, $appId = "ks", $options = []) {
        if (!is_file($file)) {
            throw new \Exception("$file 文件不存在");
        }

        if (!is_readable($file)) {
            throw new \Exception("$file 文件不可读");
        }

        $time = time();
        $data = [
            "time" => $time,
            "app_id" => $appId,
            "sign" => md5($appId . $time . $this->appKey),
            "upload" => new \CURLFile($file)
        ];

        $ch = curl_init($this->apiUrl);
        $options = $options + [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => 1
        ];

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if ($this->getCurlErrno($ch) != 0) {
            throw new \Exception('curl请求失败: ' . $this->getCurlErrno($ch));
        }

        $arr = json_decode($result, true);
        if (!is_array($arr) || $arr['code'] != 10000) {
            throw new \Exception("图片上传失败：$result");
        }

        return $arr['name'];
    }

    /**
     * 得到错误码。
     * @return int
     */
    public function getCurlErrno($ch)
    {
        return curl_errno($ch);
    }

    /**
     * 得到执行curl的相关情况
     * @return mixed
     */
    public function getCurlInfo($ch)
    {
        return curl_getinfo($ch);
    }

    /**
     * 获取ks-cdn组件
     * 如果定义的ks-cdn组件，加载配置的组件。
     * 如果没有，创建一个新对象。
     *
     * @return null|object|static
     */
    public static function getInstance() {
        if (\Yii::$app->has("ks-cdn")) {
            return \Yii::$app->get("ks-cdn");
        }
        return new static();
    }

    public static function simpleUpload($file, $appId = "ks", $options = []) {
        self::getInstance()->upload($file, $appId, $options);
    }

    /**
     * 兼容以前FastDfs的上传接口。
     * @param $file
     * @return bool|string
     */
    public static function v1Upload($file) {
        $s = self::getInstance();
        try {
            $name = $s->upload($file);
            return rtrim($s->cdnUrl) . "/" . $name;
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return false;
        }
    }
}