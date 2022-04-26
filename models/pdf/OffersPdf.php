<?php

namespace app\models\pdf;

use app\models\oldDb\OfferMix;
use Exception;
use Yii;
use yii\base\Model;

class OffersPdf extends Model
{
    public $data;
    public $consultant;
    public function __construct($options)
    {
        $this->validateOptions($options);
        $this->consultant = $options['consultant'];

        $this->data = OfferMix::find()->where([
            'object_id' => $options['object_id'],
            'type_id' => $options['type_id'],
            'original_id' => $options['original_id'],
        ])->limit(1)->one();

        if (!$this->data) {
            throw new Exception("This offer not found");
        }

        $this->data = (object) $this->data->toArray();
        if ($this->data->deal_type == 3) {
            throw new Exception("Для ОТВЕТ-ХРАНЕНИЯ презентация не реализована!");
        }
    }
    private function validateOptions($options)
    {
        $_options = [
            'object_id' => null,
            'type_id' => null,
            'original_id' => null,
            'consultant' => null,
        ];

        $options = array_merge($_options, $options);
        foreach ($options as $key => $option) {
            if ($option === null) {
                throw new Exception("$key cannot be null!");
            }
        }
    }
    public function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getPhoto()
    {
        if (is_array($this->data->photos)) {
            return "https://pennylane.pro" . $this->data->photos[0];
        }
        return "http://" . $this->getHost() . "/images/1.jpg";
    }

    public function getAreaLabel()
    {
        if ($this->data->deal_type == 1) {
            return "ПЛОЩАДИ В АРЕНДУ";
        }
        if ($this->data->deal_type == 2) {
            return "ПЛОЩАДИ НА ПРОДАЖУ";
        }
    }
    public function getArea()
    {
        $min = $this->data->area_floor_min;
        $max = $this->data->area_mezzanine_max + $this->data->area_floor_max;
        $area = max($min, $max);
        return Yii::$app->formatter->format($area, 'decimal');
    }
    public function getPriceLabel()
    {
        if ($this->data->deal_type == 1) {
            return "СТАВКА ЗА М<sup>2</sup>/ГОД";
        }
        if ($this->data->deal_type == 2) {
            return "СТАВКА ЗА М<sup>2</sup>";
        }
    }
    public function getPrice()
    {
        if ($this->data->deal_type == 1) {
            $price = $this->calcPriceGeneralForRent($this->data);
            if ($price['min'] && $price['min'] < $price['max']) {
                return "от " . $price['min'];
            }
            return $this->data->calc_price_general;
        }
        if ($this->data->deal_type == 2) {
            return $this->data->calc_price_sale;
        }
    }

    public function calcPriceGeneralForRent($fields)
    {
        $array = [
            $fields->price_mezzanine_min,
            $fields->price_floor_min,
            $fields->price_mezzanine_max,
            $fields->price_floor_max,
            $fields->price_office_max,
            $fields->price_office_max,
        ];
        $min = min($array);
        $max = max($array);
        return [
            'min' => $min,
            'max' => $max
        ];
    }

    public function getPower()
    {
        return Yii::$app->formatter->format($this->data->power_value, 'decimal');
    }
    public function getGatesCount()
    {
        $gates = json_decode($this->data->gates);
        $count = 0;
        if (is_array($gates)) {
            foreach ($gates as $key => $gate) {
                if (($key + 1) % 2 == 0) {
                    $count += $gate;
                }
            }
        }
        return $count;
    }
}
