<?php

namespace app\models\pdf;

use app\models\oldDb\OfferMix;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class OffersPdf extends Model
{
    public $data;
    public $consultant;
    public $formatter;
    public function __construct($options)
    {
        $this->formatter = Yii::$app->formatter;
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
        $array = [];
        $miniOffersMixModels = $this->data->miniOffersMix;
        if ($miniOffersMixModels) {
            foreach ($miniOffersMixModels as $miniOffersMix) {
                $array[] = (object) $miniOffersMix->toArray();
            }
        }
        $this->data = (object) array_merge($this->data->toArray(), [
            'miniOffersMix' => $array,
            'object' => (object) $this->data->object->toArray(),
        ]);
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
    public function getArea($model)
    {
        $min = $model->area_floor_min;
        $max = $model->area_mezzanine_max + $model->area_floor_max;
        $area = max($min, $max);
        return $area;
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
    public function getMaxPrice($model)
    {
        if ($model->deal_type == 1) {
            return max($this->calcPriceGeneralForRent($model));
        }
        if ($model->deal_type == 2) {
            return max($this->calcPriceGeneralForSale($model));
        }
    }
    public function getTotalPrice($model)
    {
        $pricePerYear = (int) $this->getMaxPrice($model);
        $area = (int) $this->getArea($model);
        $totalPricePerMonth = round(floor($pricePerYear * $area / 12), 0);
        return $this->formatter->format($totalPricePerMonth, 'decimal');
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
    public function calcPriceGeneralForSale($fields)
    {
        $min = $fields->price_sale_min * $fields->area_min;
        $max = $fields->price_sale_max * $fields->area_max;
        return [
            'min' => $min,
            'max' => $max
        ];
    }
    public function numberFormat($value)
    {
        return Yii::$app->formatter->format($value, 'decimal');
    }
    public function getBlocksMinArea()
    {
        if (!$this->data->miniOffersMix || $this->data->type_id == OfferMix::MINI_TYPE_ID) {
            return null;
        }
        $area = [];
        foreach ($this->data->miniOffersMix as $offerMix) {
            $min = $offerMix->area_floor_min;
            $max = $offerMix->area_mezzanine_max + $offerMix->area_floor_max;
            $area[] = max($min, $max);
        }
        return $this->formatter->format(min($area), 'decimal');
    }
    public function getTaxInfo($model)
    {
        $opex = $this->getOpex($model);
        $public_services_exist = $model->public_services == 1 || $model->public_services == 2;
        if ($opex == 1) {
            if ($public_services_exist) {
                return $model->tax_form . ", " . 'OPEX, КУ';
            }
            return $model->tax_form . ", " . 'OPEX';
        }
        if ($public_services_exist) {
            return $model->tax_form . ", " . 'КУ';
        }
        return $model->tax_form;
    }
    public function getOpex($model)
    {
        if ($model->type_id == OfferMix::GENERAL_TYPE_ID && $model->miniOffersMix && count($model->miniOffersMix)) {
            return $model->miniOffersMix[0]->price_opex;
        }
        return $model->price_opex;
    }
    public function getExtraTax($model)
    {
        $text = 'Дополнительно ';
        $opex = $this->getOpex($model);
        if ($opex == 3 && $model->public_services == 3) {
            return $text . 'OPEX и КУ';
        }
        if ($opex == 3) {
            return $text . 'OPEX';
        }
        if ($model->public_services == 3) {
            return $text . 'OPEX';
        }
    }
    public function getBlocksCount()
    {
        if ($this->data->type_id != OfferMix::GENERAL_TYPE_ID) {
            return 0;
        }

        if ($this->data->miniOffersMix) {
            return count($this->data->miniOffersMix);
        }
        return 0;
    }
    public function getPhotosForBlock($block_index = 1, $photo_count = 3)
    {
        $photos = $this->data->photos;
        $array = [];
        $classList = [
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
        ];
        if (!$photos || !is_array($photos) || $photo_count > 3) {
            return $array;
        }
        $baseUrl = "https://pennylane.pro";

        unset($photos[0]);
        $start = ($block_index - 1) * $photo_count + 1;
        if (!$start) {
            $start = 1;
        }
        $index = 1;
        for ($i = $start; $i <= $photo_count * $block_index; $i++) {
            if (ArrayHelper::keyExists($i, $photos)) {
                $array[] = [
                    'class' => $classList[$index],
                    'src' => $baseUrl . $photos[$i],
                ];
            } else {
                $array[] = [
                    'class' => $classList[$i],
                    'src' => "http://" . $this->getHost() . "/images/1.jpg",
                ];
            }
            $index++;
        }

        return $array;
    }
    // public function getGatesCount()
    // {
    //     $gates = json_decode($this->data->gates);
    //     $count = 0;
    //     if (is_array($gates)) {
    //         foreach ($gates as $key => $gate) {
    //             if (($key + 1) % 2 == 0) {
    //                 $count += $gate;
    //             }
    //         }
    //     }
    //     return $count;
    // }
}
