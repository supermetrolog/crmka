<?php

namespace app\models\pdf;

use Exception;
use Yii;
use yii\base\Model;

class Presentation extends Model
{
    private const URL = 'https://pennylane.pro/api/v1/get/index/?';
    private $response;
    public $devisionCount = 6;
    public $consultant;
    public function __construct($consultant)
    {
        if (is_null($consultant)) {
            throw new Exception('Consultant cannot be null!');
        }
        $this->consultant = $consultant;
    }
    public function fetchData($id, $type_id)
    {
        if (is_null($id) || is_null($type_id)) {
            throw new Exception('Original Id or Type id is null');
        }
        $url = self::URL . "id=$id&type_id=$type_id";
        $this->response = json_decode($this->Request($url), false);
        $this->devisionCount = count($this->response->blocks);
        // echo "<pre>";
        // print_r($this->response->description);
        // die;
    }
    public function getPower($object)
    {
        if (property_exists($object, 'power')) {
            return $object->power;
        }
        return '0 кВт';
    }
    public function getBlockArea($block)
    {
        if ($block->area_max != $block->area_min) {
            return $this->normalizeNumber($block->area_min) . '-' . $this->normalizeNumber($block->area_max);
        }
        return $this->normalizeNumber($block->area_max);
    }
    public function getHeating($block)
    {
        return $block->heating . ' ' . $block->temperature_min . '-' . $block->temperature_max;
    }
    public function getPrice($block)
    {
        if ($block->price_floor_min != $block->price_floor_max) {
            return $this->normalizeNumber($block->price_floor_min) . '-' . $this->normalizeNumber($block->price_floor_max);
        }
        return $this->normalizeNumber($block->price_floor_max);
    }
    public function getTotal($block)
    {
        if ($block->price_min_month_all != $block->price_max_month_all) {
            return $this->normalizeNumber($block->price_min_month_all) . '-' . $this->normalizeNumber($block->price_max_month_all);
        }
        return $this->normalizeNumber($block->price_max_month_all);
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
