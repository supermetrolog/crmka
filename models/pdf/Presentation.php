<?php

namespace app\models\pdf;

use Exception;
use Yii;
use yii\base\Model;

class Presentation extends Model
{
    private const URL = 'https://pennylane.pro/api/v1/get/index/?';
    private $response;

    public function fetchData($id, $type_id)
    {
        if (is_null($id) || is_null($type_id)) {
            throw new Exception('Original Id or Type id is null');
        }
        $url = self::URL . "id=$id&type_id=$type_id";
        $this->response = json_decode($this->Request($url), false);
    }
    public function getResponse()
    {
        return $this->response;
    }
    public function normalizeText($text)
    {
        if ($text == '---') {
            return '-';
        }
        return $text;
    }
    public function normalizeNumber($number)
    {
        return Yii::$app->formatter->asInteger($number);
    }
    public function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }
    public function Request($url, $postdata = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0');

        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);

        //curl_setopt($ch, CURLOPT_PROXY, '85.10.219.102:1080');
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_MAXCONNECTS, 1);

        if ($postdata) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }

        $html = curl_exec($ch);
        echo curl_error($ch);
        if (!$html) {
            return false;
        }
        curl_close($ch);
        return $html;
    }
}
