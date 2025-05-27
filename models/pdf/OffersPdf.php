<?php

namespace app\models\pdf;

use app\helpers\StringHelper;
use app\models\oldDb\ObjectsBlock;
use app\models\oldDb\OfferMix;
use app\models\UserProfile;
use Exception;
use floor12\phone\PhoneFormatter;
use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class OffersPdf extends Model
{
    /** @var OfferMix $data */
    public $data;
    public $consultant;
    public $is_new = 0;
    public $formatter;
    public $host;
    /** @var UserProfile */
    public $userProfile;

    private const TITLE_SEPARATOR = " | ";

    public function __construct($options, $host = null)
    {
		parent::__construct([]);

        $this->host = $host ?? Yii::$app->params['url']['this_host'];
        $this->formatter = Yii::$app->formatter;
        $this->validateOptions($options);
        $this->consultant = $options['consultant'];
        $this->is_new = isset($options['is_new']) ? $options['is_new'] : 0;
        $this->normalizeConsultant();

        $this->data = OfferMix::find()
            ->with(['object', 'block', 'miniOffersMix.block'])
            ->with(['miniOffersMix' => function($query){
                /** @var ActiveQuery $query */
                return $query->andWhere(['status' => OfferMix::STATUS_ACTIVE]);
            }])
            ->where([
                'object_id' => $options['object_id'],
                'type_id' => $options['type_id'],
                'original_id' => $options['original_id'],
            ])
            ->limit(1)
            ->one();

        if (!$this->data) {
            throw new Exception("Такое предложение не найдено");
        }
        $this->data = $this->data->toArray([], ['object', 'block', 'miniOffersMix.block']);

        // array to object
        $this->data = json_decode(json_encode($this->data));

        if ($this->data->deal_type == OfferMix::DEAL_TYPE_RESPONSE_STORAGE) {
            throw new Exception("Для ОТВЕТ-ХРАНЕНИЯ презентация не реализована!");
        }
        if ($this->data->type_id == 3) {
            throw new Exception("Для объекта презентация не реализована!");
        }
        $this->normalizeData();
    }

    public function getPresentationTitle(): string
    {
        $objectId = $this->data->object_id;

        $parts = ["Лот #$objectId", "PDF",];

        $townName = $this->data->town_name;

        if ($townName) {
            $parts[] = StringHelper::ucFirst($townName);
        }

        $parts[] = OfferMix::DEAL_TYPES_RU_STRING[$this->data->deal_type];

        return StringHelper::join(self::TITLE_SEPARATOR, ...$parts);
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
    private function normalizeConsultant()
    {
        if (!is_numeric($this->consultant)) {
            throw new Exception("Пользователя с таким ID не существует");
        }
        $user_id = $this->consultant;

        if (!$this->is_new) {
            $user_id = OfferMix::USERS[$this->consultant];
        }

        $this->userProfile = UserProfile::find()->where(['user_id' => $user_id])->limit(1)->one();
        if (!$this->userProfile) {
            throw new Exception("Пользователя с таким ID не существует");
        }
        $this->consultant = $this->userProfile->mediumName;
    }
    public function getCompanyPhone()
    {
        return '+7 (495) 150-03-23';
    }
    public function getMainConsultantPhone()
    {
        $phones = $this->userProfile->phones;
        if (!count($phones)) {
            return $this->getCompanyPhone();
        }
        return PhoneFormatter::format($phones[0]->phone);
    }
    private function getTownNameWithoutSpecialSymbols()
    {
        if (!$this->data->town_name) return null;
        return str_replace("-", "_", $this->data->town_name);
    }
    public function getPresentationName()
    {
        $prefix = "presentation";
        if ($this->data->town_name) {
            $prefix = $this->getTownNameWithoutSpecialSymbols();
        }
        $ext = ".pdf";

        $name = "_" . $this->data->object_id . "_" . OfferMix::DEAL_TYPES_STRING[$this->data->deal_type];
        return str_replace(" ", "_", $prefix . $name . $ext);
    }
    private function normalizeData()
    {
        $this->normalizeCranes();
        $this->normalizeElevators();
        $this->normalizeColumnGrid();
        $this->normalizeFirefighting();
        $this->normalizeVentilation();
        $this->normalizePower();
        $this->normalizeSewage();
        $this->normalizeWater();
        $this->normalizeElevatorsCount();
        $this->normalizeDescription();
        $this->normalizeVideoControl();
        $this->normalizeInternet();
        $this->normalizeAccessControl();
        $this->normalizeSecurityAlert();
        $this->normalizeFireAlert();
        $this->normalizeGas();
        $this->normalizeSteam();
    }

    private function normalizeDescription()
    {

        try {
            if ($this->data->type_id != 1 || !$this->data->block || !$this->data->block->description_manual_use) {
                $url = Yii::$app->params['url']['objects'] . 'autodesc.php/' . $this->data->original_id . '/' . $this->data->type_id . '?api=1';
                $this->data->auto_desc = file_get_contents($url);
            } else {
                $this->data->auto_desc = $this->data->block->description;
            }
        } catch (\Throwable $th) {
            $this->data->auto_desc = null;
        }
    }


    private function getSteamForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->steam;
        }
        return $model->steam;
    }
    private function getSteamForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $steam = $this->getSteamForMiniModel($miniOffer);
                if ($steam) {
                    return $steam;
                }
            }
        } else {
            return $model->steam;
        }
        return 0;
    }
    private function normalizeSteam()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->steam = $this->getSteamForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->steam = $this->getSteamForGeneralModel($this->data);
    }


    private function getGasForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->gas;
        }
        return $model->gas;
    }
    private function getGasForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $gas = $this->getGasForMiniModel($miniOffer);
                if ($gas) {
                    return $gas;
                }
            }
        } else {
            return $model->gas;
        }
        return 0;
    }
    private function normalizeGas()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->gas = $this->getGasForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->gas = $this->getGasForGeneralModel($this->data);
    }


    private function getFireAlertForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->fire_alert;
        }
        return $model->fire_alert;
    }
    private function getFireAlertForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $fire_alert = $this->getFireAlertForMiniModel($miniOffer);
                if ($fire_alert) {
                    return $fire_alert;
                }
            }
        } else {
            return $model->fire_alert;
        }
        return 0;
    }
    private function normalizeFireAlert()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->fire_alert = $this->getFireAlertForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->fire_alert = $this->getFireAlertForGeneralModel($this->data);
    }

    private function getSecurityAlertForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->security_alert;
        }
        return $model->security_alert;
    }
    private function getSecurityAlertForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $security_alert = $this->getSecurityAlertForMiniModel($miniOffer);
                if ($security_alert) {
                    return $security_alert;
                }
            }
        } else {
            return $model->security_alert;
        }
        return 0;
    }
    private function normalizeSecurityAlert()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->security_alert = $this->getSecurityAlertForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->security_alert = $this->getSecurityAlertForGeneralModel($this->data);
    }
    private function getAccessControlForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->access_control;
        }
        return $model->access_control;
    }
    private function getAccessControlForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $access_control = $this->getAccessControlForMiniModel($miniOffer);
                if ($access_control) {
                    return $access_control;
                }
            }
        } else {
            return $model->access_control;
        }
        return 0;
    }
    private function normalizeAccessControl()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->access_control = $this->getAccessControlForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->access_control = $this->getAccessControlForGeneralModel($this->data);
    }
    private function getInternetForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->internet;
        }
        return $model->internet;
    }
    private function getInternetForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $internet = $this->getInternetForMiniModel($miniOffer);
                if ($internet) {
                    return $internet;
                }
            }
        } else {
            return $model->internet;
        }
        return 0;
    }
    private function normalizeInternet()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->internet = $this->getInternetForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->internet = $this->getInternetForGeneralModel($this->data);
    }

    private function getVideoControlForMiniModel($model)
    {
        if ($model->block) {
            return $model->block->video_control;
        }
        return $model->video_control;
    }
    private function getVideoControlForGeneralModel($model)
    {
        if ($model->miniOffersMix) {
            foreach ($model->miniOffersMix as $miniOffer) {
                $video_control = $this->getVideoControlForMiniModel($miniOffer);
                if ($video_control) {
                    return $video_control;
                }
            }
        } else {
            return $model->video_control;
        }
        return 0;
    }
    private function normalizeVideoControl()
    {
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID)
            $this->data->video_control = $this->getVideoControlForMiniModel($this->data);

        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID)
            $this->data->video_control = $this->getVideoControlForGeneralModel($this->data);
    }

    private function normalizeElevators()
    {
        $this->data->elevators_lift = [];
        $this->data->elevators_list_capacity = 0;
        $this->data->elevators_hydraulic_platform = [];
        $this->data->elevators_hydraulic_platform_capacity = 0;
        $this->data->elevators_service_lift = [];
        $this->data->elevators_service_lift_capacity = 0;

        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block || !$this->data->block->elevatorss) return $this->generateElevatorsInfo();
            $elevators = $this->data->block->elevatorss;

            foreach ($elevators as $elevator) {
                switch ($elevator->elevator_type) {
                    case 1:
                        $this->data->elevators_lift[] = (int) $elevator->elevator_capacity;
                        break;
                    case 2:
                        $this->data->elevators_service_lift[] = (int) $elevator->elevator_capacity;
                        break;
                    case 3:
                        $this->data->elevators_hydraulic_platform[] = (int) $elevator->elevator_capacity;
                        break;
                }
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            $elevatorsIds = [];
            foreach ($this->data->miniOffersMix as $miniOffer) {
                if (!$miniOffer->block->elevatorss) return $this->generateElevatorsInfo();
                $elevators = $miniOffer->block->elevatorss;
                foreach ($elevators as $elevator) {
                    switch ($elevator->elevator_type) {
                        case 1:
                            if (!in_array($elevator->id, $elevatorsIds)) {
                                $this->data->elevators_lift[] = (int) $elevator->elevator_capacity;
                                $elevatorsIds[] = $elevator->id;
                            }
                            break;
                        case 2:
                            if (!in_array($elevator->id, $elevatorsIds)) {
                                $this->data->elevators_service_lift[] = (int) $elevator->elevator_capacity;
                                $elevatorsIds[] = $elevator->id;
                            }
                            break;
                        case 3:
                            if (!in_array($elevator->id, $elevatorsIds)) {
                                $this->data->elevators_hydraulic_platform[] = (int) $elevator->elevator_capacity;
                                $elevatorsIds[] = $elevator->id;
                            }
                            break;
                    }
                }
            }
        }

        $this->generateElevatorsInfo();
    }
    private function generateElevatorsInfo()
    {
        if (count($this->data->elevators_lift)) {
            $this->data->elevators_lift_capacity = $this->calcMinMax(min($this->data->elevators_lift), max($this->data->elevators_lift));
        }
        $this->data->elevators_lift = count($this->data->elevators_lift);

        if (count($this->data->elevators_service_lift)) {
            $this->data->elevators_service_lift_capacity = $this->calcMinMax(min($this->data->elevators_service_lift), max($this->data->elevators_service_lift));
        }
        $this->data->elevators_service_lift = count($this->data->elevators_service_lift);

        if (count($this->data->elevators_hydraulic_platform)) {
            $this->data->elevators_hydraulic_platform_capacity = $this->calcMinMax(min($this->data->elevators_hydraulic_platform), max($this->data->elevators_hydraulic_platform));
        }

        $this->data->elevators_hydraulic_platform =  count($this->data->elevators_hydraulic_platform);
    }
    private function normalizeElevatorsCount()
    {
        $this->data->elevators_count = [];
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            $elevators = $this->data->block->elevators;
            foreach ($elevators as $elevator) {
                if (!in_array($elevator, $this->data->elevators_count)) {
                    $this->data->elevators_count[] = $elevator;
                }
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                $elevators = $miniOffer->block->elevators;

                foreach ($elevators as $elevator) {
                    if (!in_array($elevator, $this->data->elevators_count)) {
                        $this->data->elevators_count[] = $elevator;
                    }
                }
            }
        }

        $this->data->elevators_count = count($this->data->elevators_count);
    }
    private function normalizeWater()
    {
        $this->data->water = 0;
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            if ($this->data->block->water == 1) {
                $this->data->water = 1;
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                if ($miniOffer->block->water == 1) {
                    $this->data->water = 1;
                }
            }
        }
    }
    private function normalizeSewage()
    {
        $this->data->sewage = 0;
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            if ($this->data->block->sewage == 1) {
                $this->data->sewage = 1;
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                if ($miniOffer->block->sewage == 1) {
                    $this->data->sewage = 1;
                }
            }
        }
    }
    private function normalizePower()
    {
        $this->data->power = 0;
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            $this->data->power = (int) $this->data->block->power;
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                $this->data->power += (int)$miniOffer->block->power;
            }
        }
    }
    private function normalizeVentilation()
    {
        $this->data->ventilation = [];
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            $ventilations = $this->data->block->ventilation;

            foreach ($ventilations as $ventilation) {
                $this->data->ventilation[] = ObjectsBlock::VENTILATION_LIST[(int)$ventilation];
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                $ventilations = $miniOffer->block->ventilation;

                foreach ($ventilations as $ventilation) {
                    if (!in_array(ObjectsBlock::VENTILATION_LIST[(int)$ventilation], $this->data->ventilation)) {
                        $this->data->ventilation[] = ObjectsBlock::VENTILATION_LIST[(int)$ventilation];
                    }
                }
            }
        }
    }
    private function normalizeFirefighting()
    {
        $this->data->firefighting = [];
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            $firefightings = $this->data->block->firefighting_type;

            foreach ($firefightings as $firefighting) {
                $this->data->firefighting[] = ObjectsBlock::FIREFIGHTING_LIST[(int)$firefighting];
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                $firefightings = $miniOffer->block->firefighting_type;

                foreach ($firefightings as $firefighting) {
                    if (!in_array(ObjectsBlock::FIREFIGHTING_LIST[(int)$firefighting], $this->data->firefighting)) {
                        $this->data->firefighting[] = ObjectsBlock::FIREFIGHTING_LIST[(int)$firefighting];
                    }
                }
            }
        }
    }
    private function normalizeCranes()
    {
        $this->data->cranes_gantry = [];
        $this->data->cranes_gantry_capacity = 0;
        $this->data->cranes_overhead = [];
        $this->data->cranes_overhead_capacity = 0;
        $this->data->cranes_cathead = [];
        $this->data->cranes_cathead_capacity = 0;
        $this->data->telphers = [];
        $this->data->telphers_capacity = 0;

        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block || !$this->data->block->craness) return $this->generateCranesInfo();
            $cranes = $this->data->block->craness;

            foreach ($cranes as $crane) {
                switch ($crane->crane_type) {
                    case 1:
                        $this->data->cranes_cathead[] = (int) $crane->crane_capacity;
                        break;
                    case 2:
                        $this->data->cranes_overhead[] = (int) $crane->crane_capacity;
                        break;
                    case 3:
                        $this->data->cranes_gantry[] = (int) $crane->crane_capacity;
                        break;
                    case 4:
                        $this->data->telphers[] = (int) $crane->crane_capacity;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            $cranesIds = [];
            foreach ($this->data->miniOffersMix as $miniOffer) {
                if (!$miniOffer->block->craness) return $this->generateCranesInfo();
                $cranes = $miniOffer->block->craness;
                foreach ($cranes as $crane) {
                    switch ($crane->crane_type) {
                        case 1:
                            if (!in_array($crane->id, $cranesIds)) {
                                $this->data->cranes_cathead[] = (int) $crane->crane_capacity;
                                $cranesIds[] = $crane->id;
                            }
                            break;
                        case 2:
                            if (!in_array($crane->id, $cranesIds)) {
                                $this->data->cranes_overhead[] = (int) $crane->crane_capacity;
                                $cranesIds[] = $crane->id;
                            }
                            break;
                        case 3:
                            if (!in_array($crane->id, $cranesIds)) {
                                $this->data->cranes_gantry[] = (int) $crane->crane_capacity;
                                $cranesIds[] = $crane->id;
                            }
                            break;
                        case 4:
                            if (!in_array($crane->id, $cranesIds)) {
                                $this->data->telphers[] = (int) $crane->crane_capacity;
                                $cranesIds[] = $crane->id;
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
        }

        $this->generateCranesInfo();
    }
    private function generateCranesInfo()
    {
        if (count($this->data->cranes_gantry)) {
            $this->data->cranes_gantry_capacity = $this->calcMinMax(min($this->data->cranes_gantry), max($this->data->cranes_gantry));
        }

        $this->data->cranes_gantry = count($this->data->cranes_gantry);

        if (count($this->data->cranes_overhead)) {
            $this->data->cranes_overhead_capacity = $this->calcMinMax(min($this->data->cranes_overhead), max($this->data->cranes_overhead));
        }

        $this->data->cranes_overhead = count($this->data->cranes_overhead);
        if (count($this->data->cranes_cathead)) {
            $this->data->cranes_cathead_capacity = $this->calcMinMax(min($this->data->cranes_cathead), max($this->data->cranes_cathead));
        }

        $this->data->cranes_cathead =  count($this->data->cranes_cathead);

        if (count($this->data->telphers)) {
            $this->data->telphers_capacity = $this->calcMinMax(min($this->data->telphers), max($this->data->telphers));
        }

        $this->data->telphers = count($this->data->telphers);
    }
    private function calcMinMax($min, $max)
    {
        $min = (int)$min;
        $max = (int)$max;
        $result = 0;
        if ($min == $max) {
            return Yii::$app->formatter->format($min, 'decimal');
        }
        if ($min) {
            $result = Yii::$app->formatter->format($min, 'decimal');
        }
        if ($max) {
            if ($min) {
                $result .= " - " . Yii::$app->formatter->format($max, 'decimal');
            } else {
                $result = Yii::$app->formatter->format($max, 'decimal');
            }
        }
        return $result;
    }
    public function normalizeColumnGrid()
    {
        $this->data->column_grids = [];
        if ($this->data->type_id == OfferMix::MINI_TYPE_ID) {
            if (!$this->data->block) return;
            $column_grids = $this->data->block->column_grids;

            foreach ($column_grids as $grid) {
                if ($grid != 13) {
                    $this->data->column_grids[] = ObjectsBlock::COLUMN_GRID_LIST[$grid];
                }
            }
        }
        if ($this->data->type_id == OfferMix::GENERAL_TYPE_ID) {
            if (!$this->data->miniOffersMix) return;
            foreach ($this->data->miniOffersMix as  $miniOffer) {
                if (!$miniOffer->block) return;
                $column_grids = $miniOffer->block->column_grids;

                foreach ($column_grids as $grid) {
                    if ($grid != 13) {
                        if (!in_array(ObjectsBlock::COLUMN_GRID_LIST[$grid], $this->data->column_grids)) {
                            $this->data->column_grids[] = ObjectsBlock::COLUMN_GRID_LIST[$grid];
                        }
                    }
                }
            }
        }
    }

    public function getHost()
    {
        return $this->host;
    }
    public function getHeated($model)
    {
        if ($model->heating) {
            return $model->heating;
        }

		if ($model->heated == 1) {
			return "есть";
		}

        return "нет";
    }
    public function getPhoto()
    {
        $photos = $this->data->photos;
        $object_photos = $this->data->object->photo;
        $result_image = null;
        if (is_array($photos)) {
            foreach ($photos as $photo) {
                if ($result_image === null && is_string($photo) && mb_strlen($photo) > 2) {
                    $result_image = Yii::$app->params['url']['objects'] . $photo;
                }
            }
        }
        if ($result_image) {
            return $result_image;
        }
        if (is_array($object_photos) && is_string($object_photos[0]) && mb_strlen($object_photos[0]) > 2) {
            return Yii::$app->params['url']['objects'] . $object_photos[0];
        }
        return "http://www.tinybirdgames.com/wp-content/uploads/2017/04/tinybirdgames_telegram_background_02.jpg";
    }

    public function getAreaLabel()
    {
        if ($this->data->deal_type == OfferMix::DEAL_TYPE_SALE) {
            return "ПЛОЩАДИ НА ПРОДАЖУ";
        }
        return "ПЛОЩАДИ В АРЕНДУ";
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMaxOfficeArea($model)
    {
        return max($model->area_office_max, $model->area_office_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMaxFloorArea($model)
    {
        return max($model->area_max, $model->area_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMaxMezzanineArea($model)
    {
        return max($model->area_mezzanine_max, $model->area_mezzanine_min);
    }
    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMinOfficeArea($model)
    {
        return min($model->area_office_max, $model->area_office_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMinFloorArea($model)
    {
        return min($model->area_max, $model->area_min);
    }

    /**
     * @param OfferMix $model
     * @return mixed
     */
    public function getMinMezzanineArea($model)
    {
        return min($model->area_mezzanine_max, $model->area_mezzanine_min);
    }

    /**
     * @param OfferMix $model
     * @return int
     */
    public function getArea($model)
    {
//        $area = $this->getMaxMezzanineArea($model) +
//            $this->getMaxOfficeArea($model) +
//            $this->getMaxFloorArea($model);
//        if (!$area){
//            Sreturn $model->area_max;
//        }

        return  max($model->area_max, $model->area_min);

//        return $area;
    }
    public function getAreaMax($model)
    {
        if ($this->isPlot()) {
            return  $this->getAreaMaxForPlot($model);
        }
        $min = $model->area_floor_min;
        $max = $model->area_mezzanine_max + $model->area_floor_max;
        $area = max($min, $max);
        return $area;
    }
    public function getAreaMin($model)
    {
        if ($this->isPlot()) {
            return  $this->getAreaMinForPlot($model);
        }
        $min = $model->area_floor_min;
        $max = $model->area_mezzanine_max + $model->area_floor_max;
        $area = min($min, $max);
        return $area;
    }
    public function getAreaMaxForPlot($model)
    {
        $min = $model->area_min;
        $max = $model->area_max;
        $area = max($min, $max);
        return $area;
    }
    public function getAreaMinForPlot($model)
    {

        $min = $model->area_floor_min;
        $max = $model->area_mezzanine_max + $model->area_floor_max;
        $area = min($min, $max);
        return $area;
    }
    public function getAreaMinSplit($model)
    {
        if ($this->isPlot()) {
            return  $this->getAreaMinSplitForPlot($model);
        }

        $min = $this->getMinFloorArea($model);
        $max = $this->getMaxFloorArea($model);

        if ($min == $max) {
            return false;
        }

        return min($min, $max);
    }
    public function getAreaMinSplitForPlot($model)
    {
        $min = $model->area_min;
        $max = $model->area_max;
        if ($min == $max) {
            return false;
        }
        return min($min, $max);
    }
    public function getPriceLabel()
    {
        if ($this->data->deal_type == OfferMix::DEAL_TYPE_SALE) {
            return "СТАВКА ЗА М<sup>2</sup>";
        }
        return "СТАВКА ЗА М<sup>2</sup>/ГОД";
    }
    public function getPriceWarehouseForRent($fields)
    {
        $array = [
            $fields->price_floor_min,
            $fields->price_floor_max,
        ];
        $min = min($array);
        $max = max($array);
        return [
            'min' => $min,
            'max' => $max
        ];
    }
    public function getPrice()
    {
        if ($this->data->deal_type == OfferMix::DEAL_TYPE_RENT || $this->data->deal_type == OfferMix::DEAL_TYPE_SUBLEASE) {
            $price = $this->getPriceWarehouseForRent($this->data);
            if ($price['min'] && $price['min'] < $price['max']) {
                return "от " . $price['min'];
            }
            // return $this->data->calc_price_general;
            return $this->data->calc_price_warehouse;
        }
        if ($this->data->deal_type == OfferMix::DEAL_TYPE_SALE) {
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

    public function getMinPrice($model)
    {
        if ($model->deal_type == 1) {
            return $model->price_floor_min;
        }
        if ($model->deal_type == 2) {
            return min($this->calcPriceGeneralForSale($model));
        }
    }

    public function getTotalPrice($model)
    {
        $pricePerYear = (int) $this->getMaxPrice($model);
        $area = (int) $this->getAreaMax($model);
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
        /** @var OfferMix $offerMix */
        foreach ($this->data->miniOffersMix as $offerMix) {
            $min = $offerMix->area_floor_min;
            $max = $offerMix->area_mezzanine_max + $offerMix->area_floor_max;
            $area[] = min($min, $max);
        }
        return $this->formatter->format(min($area), 'decimal');
    }
    public function getTaxInfo($model)
    {
        if ($model->tax_form == 'triple net'){
            return 'БЕЗ НДС';
        }
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
        if ($model->tax_form === 0 || $model->tax_form === '0') {
            return null;
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
        $baseUrl = Yii::$app->params['url']['objects'];

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
                    // 'src' => "http://www.tinybirdgames.com/wp-content/uploads/2017/04/tinybirdgames_telegram_background_02.jpg",
                    'src' => Yii::$app->params['url']['empty_image'],
                ];
            }
            $index++;
        }

        return $array;
    }

    public function isPlot()
    {
        $object_types = $this->data->object_type;
        if (!$object_types || !is_array($object_types)) {
            return null;
        }
        foreach ($object_types  as $type) {
            if ($type == 3) {
                return true;
            }
        }

        return false;
    }
    public function isValidParameter($value)
    {
        $invalidParamsList = ["", null, 0, " ", "0", "  ", "0 "];

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
            if (ArrayHelper::keyExists(is_callable($value['value']) ? $value['value']() : $value['value'], $value['value_list'])) {
                if (is_callable($value['value_list'][is_callable($value['value']) ? $value['value']() : $value['value']])) {
                    return $value['value_list'][is_callable($value['value']) ? $value['value']() : $value['value']]();
                }
                return $value['value_list'][is_callable($value['value']) ? $value['value']() : $value['value']];
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
        $array =  [
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
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
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
                    'value' => function () {
                        return implode(', ', $this->data->column_grids);
                    },
                    'dimension' => '',
                ],
                'Внешняя отделка' => [
                    'label' => 'facing',
                    'value' => $data->facing,
                    'dimension' => '',
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
                    'value' => function () {
                        return implode(', ', $this->data->firefighting);
                    },
                    'dimension' => '',
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
                        1 => 'есть',
                        2 => 'нет'
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
        if ($this->isPlot()) {
            return $this->getParameterListOneForPlot($array);
        }
        return $array;
    }
    public function getParameterListTwo()
    {
        $data = $this->data;
        $array =  [
            'Коммуникации' => [
                'Электричество' => [
                    'label' => 'power',
                    'value' => $data->power,
                    'dimension' => 'кВт',
                ],
                'Отопление' => [
                    'label' => 'heating',
                    'value' => function () {
                        if ($this->data->heating) {
                            return $this->data->heating;
                        }

						if ($this->data->heated == 1) {
							return "есть";
						}

                        return "нет";
                    },
                    'dimension' => '',
                ],
                'Водоснабжение' => [
                    'label' => 'water',
                    'value' => $data->water,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Канализация' => [
                    'label' => 'sewage_central',
                    'value' => $data->sewage,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Вентиляция' => [
                    'label' => 'ventilation',
                    'value' => function () {
                        return implode(', ', $this->data->ventilation);
                    },
                    'dimension' => '',
                ],
                'Газ' => [
                    'label' => 'gas',
                    'value' => $data->gas,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Пар' => [
                    'label' => 'steam',
                    'value' => $data->steam,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет'
                    ]
                ],
                'Телефония' => [
                    'label' => 'phone',
                    'value' => $data->phone,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Интернет' => [
                    'label' => 'internet',
                    'value' => $data->internet,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
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
                    'value' => function () use ($data) {
                        if (!$data->cranes_gantry) return 0;
                        $text = $data->cranes_gantry . ' <small>шт</small>';
                        if ($data->cranes_gantry_capacity) {
                            $text .= ', ' . $data->cranes_gantry_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                    ]
                ],
                'Мостовые краны' => [
                    'label' => 'cranes_overhead',
                    'value' => function () use ($data) {
                        if (!$data->cranes_overhead) return 0;
                        $text = $data->cranes_overhead . ' <small>шт</small>';
                        if ($data->cranes_overhead_capacity) {
                            $text .= ', ' . $data->cranes_overhead_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                    ]
                ],
                'Кран-балки' => [
                    'label' => 'cranes_cathead',
                    'value' => function () use ($data) {
                        if (!$data->cranes_cathead) return 0;
                        $text = $data->cranes_cathead . ' <small>шт</small>';
                        if ($data->cranes_cathead_capacity) {
                            $text .= ', ' . $data->cranes_cathead_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                    ]
                ],
                'Тельферы' => [
                    'label' => 'telphers',
                    'value' => function () use ($data) {
                        if (!$data->telphers) return 0;
                        $text = $data->telphers . ' <small>шт</small>';
                        if ($data->telphers_capacity) {
                            $text .= ', ' . $data->telphers_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                    ]
                ],
            ],
            'Подъемные устройства' => [
                'Подъемники' => [
                    'label' => 'elevators_lift',
                    'value' => function () use ($data) {
                        if (!$data->elevators_lift) return 0;
                        $text = $data->elevators_lift . ' <small>шт</small>';
                        if ($data->elevators_lift_capacity) {
                            $text .= ', ' . $data->elevators_lift_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                    ]
                ],
                'Грузовые лифты' => [
                    'label' => 'elevators_service_lift',
                    'value' => function () use ($data) {
                        if (!$data->elevators_service_lift) return 0;
                        $text = $data->elevators_service_lift . ' <small>шт</small>';
                        if ($data->elevators_service_lift_capacity) {
                            $text .= ', ' . $data->elevators_service_lift_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                    ]
                ],
                'Гидроплатформа' => [
                    'label' => 'elevators_hydraulic_platform',
                    'value' => function () use ($data) {
                        if (!$data->elevators_hydraulic_platform) return 0;
                        $text = $data->elevators_hydraulic_platform . ' <small>шт</small>';
                        if ($data->elevators_hydraulic_platform_capacity) {
                            $text .= ', ' . $data->elevators_hydraulic_platform_capacity . ' <small>тонн</small>';
                        }
                        return $text;
                    },
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
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
                        2 => 'нет',
                    ]
                ],
                'Парковка легковая' => [
                    'label' => 'parking_car',
                    'value' => $data->parking_car,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Парковка грузовая ' => [
                    'label' => 'parking_truck',
                    'value' => $data->parking_truck,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Столовая/кафе' => [
                    'label' => 'canteen',
                    'value' => $data->canteen,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
                'Общежитие' => [
                    'label' => 'hostel',
                    'value' => $data->hostel,
                    'dimension' => '',
                    'value_list' => [
                        0 => 'нет',
                        1 => 'есть',
                        2 => 'нет',
                    ]
                ],
            ]
        ];
        if ($this->isPlot()) {
            return $this->getParameterListTwoForPlot($array);
        }
        return $array;
    }
    public function getParameterListOneForPlot(array $array): array
    {
        $data = $this->data;
        $security = $array['Безопасность'];

        unset($array['Характеристики']);
        unset($array['Безопасность']);
        $array['Характеристики'] = [
            'Правовой статус земли' => [
                'label' => 'own_type_land',
                'value' => $data->own_type_land,
                'dimension' => '',
            ],
            'Категория земли' => [
                'label' => 'land_category',
                'value' => $data->land_category,
                'dimension' => '',
            ],
            'Рельеф' => [
                'label' => 'landscape_type',
                'value' => $data->landscape_type,
                'dimension' => '',
            ],
            'Строения на участке' => [
                'label' => 'buildings_on_territory',
                'value' => $data->object->buildings_on_territory,
                'dimension' => '',
                'value_list' => [
                    0 => 'нет',
                    1 => 'есть',
                    2 => 'нет',
                ]
            ],
        ];
        $array['Безопасность'] = $security;
        return $array;
    }
    public function getParameterListTwoForPlot(array $array): array
    {
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
