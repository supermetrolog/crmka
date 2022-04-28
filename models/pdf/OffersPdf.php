<?php

namespace app\models\pdf;

use app\models\oldDb\Crane;
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
                $array[] = (object) array_merge($miniOffersMix->toArray(), ['block' => (object) array_merge($miniOffersMix->block->toArray(), ['craness' => Crane::find()->where(['deleted' => 0, 'id' => $miniOffersMix->block->toArray()['cranes']])->all()])]);
            }
        }
        $block = $this->data->block ? (object) array_merge($this->data->block->toArray(), ['craness' => Crane::find()->where(['deleted' => 0, 'id' => $this->data->block->toArray()['cranes']])->all()]) : null;
        $this->data = (object) array_merge($this->data->toArray(), [
            'miniOffersMix' => $array,
            'object' => (object) $this->data->object->toArray(),
            'block' => $block
        ]);
        $this->normalizeData();
        if ($this->data->deal_type == 3) {
            throw new Exception("Для ОТВЕТ-ХРАНЕНИЯ презентация не реализована!");
        }
    }
    private function normalizeData()
    {
        $this->data->cranes_gantry = 0;
        $this->data->cranes_overhead = 0;
        $this->data->cranes_cathead = 0;
        $this->data->telphers = 0;
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block || !$this->data->block->craness) return;
            $cranes = $this->data->block->craness;

            foreach ($cranes as $crane) {
                switch ($crane->crane_type) {
                    case 1:
                        $this->data->cranes_cathead = 1;
                        break;
                    case 2:
                        $this->data->cranes_overhead = 1;
                        break;
                    case 3:
                        $this->data->cranes_gantry = 1;
                        break;
                    case 4:
                        $this->data->telphers = 1;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as $miniOffer) {

                if (!$miniOffer->block->craness) return;
                $cranes = $miniOffer->block->craness;
                foreach ($cranes as $crane) {
                    switch ($crane->crane_type) {
                        case 1:
                            $this->data->cranes_cathead = 1;
                            break;
                        case 2:
                            $this->data->cranes_overhead = 1;
                            break;
                        case 3:
                            $this->data->cranes_gantry = 1;
                            break;
                        case 4:
                            $this->data->telphers = 1;
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
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
            return $text . 'КУ';
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
    public function isValidParameter($value)
    {
        $invalidParamsList = ["", null, 0, " ", "0", "  ", "0 "];

        // $val = is_callable($value['value']) ? $value['value']() : $value['value'];
        // var_dump($value);
        foreach ($invalidParamsList as $invalidParam) {
            if ($value === $invalidParam) {
                return false;
            }
        }
        return true;
    }
    public function normalizeValue($value)
    {
        if (ArrayHelper::keyExists('value_list', $value)) {
            if (ArrayHelper::keyExists($value['value'], $value['value_list'])) {
                if (is_callable($value['value_list'][$value['value']])) {
                    return $value['value_list'][$value['value']]();
                }
                return $value['value_list'][$value['value']];
            }
        }
        if (is_callable($value['value'])) {
            return $value['value']();
        }
        return $value['value'];
    }
    public function getParameterListOne()
    {
        $data = $this->data;
        return [
            'Площади к аренде' => [
                'Свободная площадь' => [
                    'label' => 'calc_area_general',
                    'value' => $data->calc_area_general,
                    'dimension' => '<small>м<sup>2</sup></small>',
                ],
                'Из них мезонина' => [
                    'label' => 'calc_area_mezzanine',
                    'value' => $data->calc_area_mezzanine,
                    'dimension' => '<small>м<sup>2</sup></small>',
                ],
                'Из них офисов' => [
                    'label' => 'calc_area_office',
                    'value' => $data->calc_area_office,
                    'dimension' => '<small>м<sup>2</sup></small>',
                ],
            ],
            'Характеристики' => [
                'Этажность' => [
                    'label' => 'calc_floors',
                    'value' => $data->calc_floors,
                    'dimension' => '',
                ],
                'Класс объекта' => [
                    'label' => 'class_name',
                    'value' => $data->class_name,
                    'dimension' => '',
                ],
                'Высота потолков' => [
                    'label' => 'calc_ceilingHeight',
                    'value' => $data->calc_ceilingHeight,
                    'dimension' => '<small>м</small>',
                ],
                'Тип ворот' => [
                    'label' => 'gate_type',
                    'value' => $data->gate_type,
                    'dimension' => '',
                ],
                'Количество ворот' => [
                    'label' => 'gate_num',
                    'value' => $data->gate_num,
                    'dimension' => '<small>шт</small>',
                ],
                'Стеллажи' => [
                    'label' => 'racks',
                    'value' => $data->racks,
                    'dimension' => '<small>шт</small>',
                ],
                'Нагрузка на пол' => [
                    'label' => 'calc_load_floor',
                    'value' => $data->calc_load_floor,
                    'dimension' => '<small>тонн</small>',
                ],
                'Нагрузка на мезонин' => [
                    'label' => 'racks',
                    'value' => $data->calc_load_mezzanine,
                    'dimension' => '<small>тонн</small>',
                ],
                'Температура' => [
                    'label' => 'calc_temperature',
                    'value' => $data->calc_temperature,
                    'dimension' => '<small><sup>°</sup>C</small>',
                ],
                'Шаг колонн' => [
                    'label' => 'column_grid',
                    'value' => $data->column_grid,
                    'dimension' => '',
                ],
                'Грузовые лифты' => [
                    'label' => 'elevators_num',
                    'value' => $data->elevators_num,
                    'dimension' => '<small>шт</small>',
                ],
            ],
            'Безопасность' => [
                'Охрана объекта' => [
                    'label' => 'guard',
                    'value' => $data->guard,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Пожаротушение' => [
                    'label' => 'firefighting',
                    'value' => $data->firefighting,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Видеонаблюдение' => [
                    'label' => 'video_control',
                    'value' => $data->video_control,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Контроль доступа' => [
                    'label' => 'access_control',
                    'value' => $data->access_control,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Охранная сигнализация' => [
                    'label' => 'security_alert',
                    'value' => $data->security_alert,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Пожарная сигнализация' => [
                    'label' => 'fire_alert',
                    'value' => $data->fire_alert,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
            ]
        ];
    }
    public function getParameterListTwo()
    {
        $data = $this->data;
        return [
            'Коммуникации' => [
                'Электричество' => [
                    'label' => 'power',
                    'value' => $data->power,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => function () use ($data) {
                            if ($data->power_value > 0) {
                                return $data->power_value . ' <small>кВт</small>';
                            }
                            return 'есть';
                        }
                    ]
                ],
                'Отопление' => [
                    'label' => 'heating',
                    'value' => $data->heating,
                    'dimension' => '',
                ],
                'Водоснабжение' => [
                    'label' => 'water',
                    'value' => $data->water,
                    'dimension' => '',
                ],
                'Канализация' => [
                    'label' => 'sewage_central',
                    'value' => $data->sewage_central,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Вентиляция' => [
                    'label' => 'ventilation',
                    'value' => $data->ventilation,
                    'dimension' => '',
                ],
                'Газ' => [
                    'label' => 'gas',
                    'value' => $data->gas,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Пар' => [
                    'label' => 'steam',
                    'value' => $data->steam,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Телефония' => [
                    'label' => 'phone',
                    'value' => $data->phone,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
                'Интернет' => [
                    'label' => 'internet',
                    'value' => $data->internet,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть'
                    ]
                ],
            ],
            'Ж/Д и крановые устр-ва' => [
                'Ж/Д ветка' => [
                    'label' => 'railway',
                    'value' => $data->railway,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет'
                    ]
                ],
                'Козловые краны' => [
                    'label' => 'cranes_gantry',
                    'value' => $data->cranes_gantry,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Мостовые краны' => [
                    'label' => 'cranes_overhead',
                    'value' => $data->cranes_overhead,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Кран-балки' => [
                    'label' => 'cranes_cathead',
                    'value' => $data->cranes_cathead,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Тельферы' => [
                    'label' => 'telphers',
                    'value' => $data->telphers,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
            ],
            'Инфраструктура' => [
                'Въезд на территорию' => [
                    'label' => 'entry_territory',
                    'value' => $data->entry_territory,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Парковка легковая' => [
                    'label' => 'parking_car',
                    'value' => $data->parking_car,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Парковка грузовая ' => [
                    'label' => 'parking_truck',
                    'value' => $data->parking_truck,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Столовая/кафе' => [
                    'label' => 'canteen',
                    'value' => $data->canteen,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
                'Общежитие' => [
                    'label' => 'hostel',
                    'value' => $data->hostel,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                    ]
                ],
            ]
        ];
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
