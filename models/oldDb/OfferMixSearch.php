<?php

namespace app\models\oldDb;

use app\components\ExpressionBuilder;
use app\exceptions\ValidationErrorHttpException;
use app\helpers\DbHelper;
use app\models\Company;
use app\models\oldDb\location\Region;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\oldDb\OfferMix;
use app\models\Search;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * OfferMixSearch represents the model behind the search form of `app\models\oldDb\OfferMix`.
 */
class OfferMixSearch extends Search
{
    private const MIN_RECOMMENDED_WEIGHT_SUM = 400;
    private const APPROXIMATE_PERCENT_FOR_DISTANCE_FROM_MKAD =  30;
    private const APPROXIMATE_PERCENT_FOR_PRICE_PER_FLOOR =  30;
    public $all;
    public $rangeMinArea;
    public $rangeMaxArea;
    public $rangeMinCeilingHeight;
    public $rangeMaxCeilingHeight;
    public $pricePerFloor;
    public $approximateDistanceFromMKAD;
    public $rangeMaxDistanceFromMKAD;
    public $rangeMinElectricity;
    public $rangeMaxElectricity;
    public $rangeMinPricePerFloor;
    public $rangeMaxPricePerFloor;
    public $approximateMaxPricePerFloor;
    public $uniqueOffer;
    public $recommended_sort;
    public $firstFloorOnly;

    public $objectsOnly;
    public $noWith;
    public $expand = [];
    public $sort_original_id;
    public $timeline_id;

    public $noDuplicate;
    public $region_neardy;
    // original attributes
    public $id;
    public $original_id;
    public $visual_id;
    public $type_id;
    public $deal_type;
    public $deal_type_name;
    public $title;
    public $status;
    public $object_id;
    public $complex_id;
    public $parent_id;
    public $company_id;
    public $contact_id;
    public $object_type;
    public $purposes;
    public $purposes_furl;
    public $object_type_name;
    public $year_built;
    public $agent_id;
    public $agent_visited;
    public $agent_name;
    public $is_land;
    public $land_width;
    public $land_length;
    public $landscape_type;
    public $land_use_restrictions;
    public $address;
    public $latitude;
    public $class;
    public $class_name;
    public $longitude;
    public $from_mkad;
    public $region;
    public $region_name;
    public $cian_region;
    public $outside_mkad;
    public $near_mo;
    public $town;
    public $town_name;
    public $district;
    public $district_name;
    public $district_moscow;
    public $district_moscow_name;
    public $direction;
    public $direction_name;
    public $highway;
    public $highway_name;
    public $highway_moscow;
    public $highway_moscow_name;
    public $metro;
    public $metro_name;
    public $from_metro_value;
    public $from_metro;
    public $railway_station;
    public $from_station_value;
    public $from_station;
    public $blocks;
    public $blocks_amount;
    public $photos;
    public $videos;
    public $thumbs;
    public $last_update;
    public $commission_client;
    public $commission_owner;
    public $deposit;
    public $pledge;
    public $area_building;
    public $area_floor_full;
    public $area_mezzanine_full;
    public $area_office_full;
    public $area_min;
    public $area_max;
    public $area_floor_min;
    public $area_floor_max;
    public $area_mezzanine_min;
    public $area_mezzanine_max;
    public $area_mezzanine_add;
    public $area_office_min;
    public $area_office_max;
    public $area_office_add;
    public $area_tech_min;
    public $area_tech_max;
    public $area_field_min;
    public $area_field_max;
    public $pallet_place_min;
    public $pallet_place_max;
    public $cells_place_min;
    public $cells_place_max;
    public $tax_form;
    public $inc_electricity;
    public $inc_heating;
    public $inc_water;
    public $price_opex_inc;
    public $price_opex;
    public $price_opex_min;
    public $price_opex_max;
    public $price_public_services_inc;
    public $price_public_services;
    public $public_services;
    public $price_public_services_min;
    public $price_public_services_max;
    public $price_floor_min;
    public $price_floor_max;
    public $price_floor_min_month;
    public $price_floor_max_month;
    public $price_min_month_all;
    public $price_max_month_all;
    public $price_floor_100_min;
    public $price_floor_100_max;
    public $price_mezzanine_min;
    public $price_mezzanine_max;
    public $price_office_min;
    public $price_office_max;
    public $price_sale_min;
    public $price_sale_max;
    public $price_safe_pallet_min;
    public $price_safe_pallet_max;
    public $price_safe_volume_min;
    public $price_safe_volume_max;
    public $price_safe_floor_min;
    public $price_safe_floor_max;
    public $price_safe_calc_min;
    public $price_safe_calc_max;
    public $price_safe_calc_month_min;
    public $price_safe_calc_month_max;
    public $price_sale_min_all;
    public $price_sale_max_all;
    public $ceiling_height_min;
    public $ceiling_height_max;
    public $temperature_min;
    public $temperature_max;
    public $load_floor_min;
    public $load_floor_max;
    public $load_mezzanine_min;
    public $load_mezzanine_max;
    public $safe_type;
    public $safe_type_furl;
    public $prepay;
    public $floor_min;
    public $floor_max;
    public $floor_type;
    public $floor_types;
    public $self_leveling;
    public $heated;
    public $gates;
    public $gate_type;
    public $gate_num;
    public $column_grid;
    public $elevators_min;
    public $elevators_max;
    public $elevators_num;
    public $has_cranes;
    public $cranes_min;
    public $cranes_max;
    public $cranes_num;
    public $cranes_railway_min;
    public $cranes_railway_max;
    public $cranes_railway_num;
    public $cranes_gantry_min;
    public $cranes_gantry_max;
    public $cranes_gantry_num;
    public $cranes_overhead_min;
    public $cranes_overhead_max;
    public $cranes_overhead_num;
    public $cranes_cathead_min;
    public $cranes_cathead_max;
    public $cranes_cathead_num;
    public $telphers_min;
    public $telphers_max;
    public $telphers_num;
    public $railway;
    public $railway_value;
    public $power;
    public $power_value;
    public $steam;
    public $steam_value;
    public $gas;
    public $gas_value;
    public $phone;
    public $internet;
    public $heating;
    public $facing;
    public $ventilation;
    public $water;
    public $water_value;
    public $sewage_central;
    public $sewage_central_value;
    public $sewage_rain;
    public $guard;
    public $firefighting;
    public $firefighting_name;
    public $video_control;
    public $access_control;
    public $security_alert;
    public $fire_alert;
    public $smoke_exhaust;
    public $canteen;
    public $hostel;
    public $racks;
    public $warehouse_equipment;
    public $charging_room;
    public $cross_docking;
    public $cranes_runways;
    public $cadastral_number;
    public $cadastral_number_land;
    public $field_allow_usage;
    public $available_from;
    public $own_type;
    public $own_type_land;
    public $land_category;
    public $entry_territory;
    public $parking_car;
    public $parking_car_value;
    public $parking_lorry;
    public $parking_lorry_value;
    public $parking_truck;
    public $parking_truck_value;
    public $built_to_suit;
    public $built_to_suit_time;
    public $built_to_suit_plan;
    public $rent_business;
    public $rent_business_fill;
    public $rent_business_price;
    public $rent_business_long_contracts;
    public $rent_business_last_repair;
    public $rent_business_payback;
    public $rent_business_income;
    public $rent_business_profit;
    public $sale_company;
    public $holidays;
    public $ad_realtor;
    public $ad_cian;
    public $ad_cian_top3;
    public $ad_cian_premium;
    public $ad_cian_hl;
    public $ad_yandex;
    public $ad_yandex_raise;
    public $ad_yandex_promotion;
    public $ad_yandex_premium;
    public $ad_arendator;
    public $ad_free;
    public $ad_special;
    public $description;
    public $deleted;
    public $test_only;
    public $is_exclusive;
    public $deal_id;
    public $hide_from_market;

    //Исключить офферы из этого запроса
    public $withoutOffersFromQuery;

    /**
     *  @var string|array
     */
    public $polygon;

    public $ad_avito;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['approximateMaxPricePerFloor', 'region_neardy', 'noDuplicate', 'timeline_id', 'noWith', 'objectsOnly', 'firstFloorOnly', 'recommended_sort', 'rangeMinPricePerFloor', 'rangeMaxPricePerFloor', 'rangeMaxElectricity', 'rangeMinElectricity', 'rangeMaxDistanceFromMKAD', 'approximateDistanceFromMKAD', 'rangeMaxCeilingHeight', 'rangeMinCeilingHeight', 'pricePerFloor', 'rangeMinArea', 'rangeMaxArea', 'id', 'status',  'parent_id', 'company_id', 'contact_id', 'year_built', 'agent_id', 'agent_visited', 'is_land', 'land_width', 'land_length', 'land_use_restrictions', 'from_mkad', 'cian_region', 'outside_mkad', 'near_mo', 'from_metro_value', 'from_metro', 'from_station_value', 'from_station', 'blocks_amount', 'last_update', 'commission_client', 'commission_owner', 'deposit', 'pledge', 'area_building', 'area_floor_full', 'area_mezzanine_full', 'area_office_full', 'area_min', 'area_max', 'area_floor_min', 'area_floor_max', 'area_mezzanine_min', 'area_mezzanine_max', 'area_mezzanine_add', 'area_office_min', 'area_office_max', 'area_office_add', 'area_tech_min', 'area_tech_max', 'area_field_min', 'area_field_max', 'pallet_place_min', 'pallet_place_max', 'cells_place_min', 'cells_place_max', 'inc_electricity', 'inc_heating', 'inc_water', 'price_opex_inc', 'price_opex', 'price_opex_min', 'price_opex_max', 'price_public_services_inc', 'price_public_services', 'public_services', 'price_public_services_min', 'price_public_services_max', 'price_floor_min', 'price_floor_max', 'price_floor_min_month', 'price_floor_max_month', 'price_min_month_all', 'price_max_month_all', 'price_floor_100_min', 'price_floor_100_max', 'price_mezzanine_min', 'price_mezzanine_max', 'price_office_min', 'price_office_max', 'price_sale_min', 'price_sale_max', 'price_safe_pallet_min', 'price_safe_pallet_max', 'price_safe_volume_min', 'price_safe_volume_max', 'price_safe_floor_min', 'price_safe_floor_max', 'price_safe_calc_min', 'price_safe_calc_max', 'price_safe_calc_month_min', 'price_safe_calc_month_max', 'price_sale_min_all', 'price_sale_max_all', 'temperature_min', 'temperature_max', 'prepay', 'floor_min', 'floor_max', 'self_leveling', 'elevators_min', 'elevators_max', 'elevators_num', 'cranes_num', 'cranes_railway_num', 'cranes_gantry_num', 'cranes_overhead_num', 'cranes_cathead_num', 'telphers_min', 'telphers_max', 'telphers_num', 'railway_value', 'power', 'power_value', 'steam_value', 'gas_value', 'phone', 'water_value', 'sewage_central_value', 'sewage_rain', 'firefighting', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'warehouse_equipment', 'charging_room', 'cross_docking', 'cranes_runways', 'parking_car', 'parking_lorry', 'parking_truck', 'built_to_suit', 'built_to_suit_time', 'built_to_suit_plan', 'rent_business', 'rent_business_fill', 'rent_business_price', 'rent_business_long_contracts', 'rent_business_last_repair', 'rent_business_payback', 'rent_business_income', 'rent_business_profit', 'sale_company', 'holidays', 'ad_realtor', 'ad_cian', 'ad_avito', 'ad_cian_top3', 'ad_cian_premium', 'ad_cian_hl', 'ad_yandex', 'ad_yandex_raise', 'ad_yandex_promotion', 'ad_yandex_premium', 'ad_arendator', 'ad_free', 'ad_special', 'deleted', 'test_only', 'is_exclusive', 'deal_id', 'hide_from_market'], 'integer'],
            [['polygon', 'withoutOffersFromQuery', 'sort_original_id', 'object_id', 'complex_id', 'original_id', 'expand', 'heated', 'uniqueOffer', 'has_cranes', 'railway', 'racks', 'sewage_central', 'steam', 'gas', 'deal_type', 'all', 'type_id', 'visual_id', 'deal_type_name', 'title', 'object_type', 'purposes', 'purposes_furl', 'object_type_name', 'agent_name', 'landscape_type', 'address', 'class', 'class_name', 'region', 'region_name', 'town', 'town_name', 'district', 'district_name', 'district_moscow', 'district_moscow_name', 'direction', 'direction_name', 'highway', 'highway_name', 'highway_moscow', 'highway_moscow_name', 'metro', 'metro_name', 'railway_station', 'blocks', 'photos', 'videos', 'thumbs', 'tax_form', 'safe_type', 'safe_type_furl', 'floor_type', 'floor_types', 'gates', 'gate_type', 'gate_num', 'column_grid', 'internet', 'heating', 'facing', 'ventilation', 'water', 'guard', 'firefighting_name', 'cadastral_number', 'cadastral_number_land', 'field_allow_usage', 'available_from', 'own_type', 'own_type_land', 'land_category', 'entry_territory', 'parking_car_value', 'parking_lorry_value', 'parking_truck_value', 'description'], 'safe'],
            [['latitude', 'longitude', 'ceiling_height_min', 'ceiling_height_max', 'load_floor_min', 'load_floor_max', 'load_mezzanine_min', 'load_mezzanine_max', 'cranes_min', 'cranes_max', 'cranes_railway_min', 'cranes_railway_max', 'cranes_gantry_min', 'cranes_gantry_max', 'cranes_overhead_min', 'cranes_overhead_max', 'cranes_cathead_min', 'cranes_cathead_max'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    protected function getTableName(): string
    {
        return OfferMix::tableName();
    }

    public function stringToArray($value)
    {
        if (is_string($value)) {
            return explode(",", $value);
        }
        return $value;
    }
    /**
     * @return void
     */
    public function normalizePolygon(): void
    {
        $polygon = $this->stringToArray($this->polygon);
        if (!$polygon || !is_array($polygon) || count($polygon) % 2 !== 0) {
            return;
        }
        $coordinates = [];
        for ($i = 0; $i < count($polygon); $i = $i + 2) {
            $coordinates[] = $polygon[$i] . ' ' . $polygon[$i + 1];
        }
        $coordinates[] = $coordinates[0];
        $this->polygon = $coordinates;
    }

    public function normalizeDirection()
    {
        $directions = $this->stringToArray($this->direction);
        $array = [];
        if (is_array($directions)) {
            foreach ($directions as $direction) {
                $array[] = OfferMix::normalizeDirections($direction);
            }

            $this->direction = $array;
        }
    }
    public function normalizeDistrict()
    {
        $districts = $this->stringToArray($this->district_moscow);
        $array = [];
        if (is_array($districts)) {
            foreach ($districts as $district) {
                $array[] = OfferMix::normalizeDistricts($district);
            }

            $this->district_moscow = $array;
        }
    }
    public function normalizeClass()
    {
        $objectClasses = $this->stringToArray($this->class);
        $array = [];
        if (is_array($objectClasses)) {
            foreach ($objectClasses as $objectClass) {
                $array[] = OfferMix::normalizeObjectClasses($objectClass);
            }

            $this->class = $array;
        }
    }
    public function normalizeGates()
    {
        $gates = $this->stringToArray($this->gates);
        $array = [];
        if (is_array($gates)) {
            foreach ($gates as $gate) {
                $array[] = '"' . OfferMix::normalizeGateTypes($gate) . '"';
            }

            $this->gates = $array;
        }
    }
    public function normalizePurposes()
    {
        $purposes = $this->stringToArray($this->purposes);
        $array = [];
        if (is_array($purposes)) {
            foreach ($purposes as $purpose) {
                $array[] = '"' . OfferMix::normalizeObjectTypes($purpose) . '"';
            }

            $this->purposes = $array;
        }
    }
    public function normalizeGas()
    {
        if ($this->gas == 2) {
            $this->gas = [0, 2];
        }
        // if ($this->gas == 1) {
        //     $this->gas = [0, 1];
        // }
    }
    public function normalizeSteam()
    {
        if ($this->steam == 2) {
            $this->steam = [0, 2];
        }
        // if ($this->steam == 1) {
        //     $this->steam = [0, 1];
        // }
    }
    public function normalizeSewageCentral()
    {
        if ($this->sewage_central == 2) {
            $this->sewage_central = [0, 2];
        }
    }
    public function normalizeRacks()
    {
        if ($this->racks == 2) {
            $this->racks = [0, 2];
        }
    }
    public function normalizeRailway()
    {
        if ($this->railway == 2) {
            $this->railway = [0, 2];
        }
    }
    public function normalizeHasCranes()
    {
        if ($this->has_cranes == 2) {
            $this->has_cranes = [0, 2];
        }
    }
    public function normalizeHeated()
    {
        if ($this->heated === null) return;

        $this->heated = [$this->heated, 0];
    }
    public function normalizeApproximateDistanceFromMKAD()
    {
        if ($this->approximateDistanceFromMKAD) {
            $this->approximateDistanceFromMKAD = floor(($this->approximateDistanceFromMKAD * self::APPROXIMATE_PERCENT_FOR_DISTANCE_FROM_MKAD / 100) + $this->approximateDistanceFromMKAD);
        }
    }
    public function normalizeApproximateMaxPricePerFloor()
    {
        if ($this->approximateMaxPricePerFloor) {
            $this->approximateMaxPricePerFloor = floor(($this->approximateMaxPricePerFloor * self::APPROXIMATE_PERCENT_FOR_PRICE_PER_FLOOR / 100) + $this->approximateMaxPricePerFloor);
            $this->rangeMaxPricePerFloor = $this->approximateMaxPricePerFloor;
        }
    }
    public function normalizeProps()
    {
        $this->deal_type = OfferMix::normalizeDealType($this->deal_type);
        $this->agent_id = OfferMix::normalizeAgentId($this->agent_id);
        $this->normalizeClass();
        $this->normalizeDirection();
        $this->normalizeDistrict();
        $this->normalizeGates();
        $this->normalizeGas();
        $this->normalizeSteam();
        $this->normalizeSewageCentral();
        $this->normalizeRacks();
        $this->normalizeRailway();
        $this->normalizeHasCranes();
        $this->normalizePurposes();
        $this->normalizeHeated();
        $this->normalizeApproximateDistanceFromMKAD();
        $this->normalizePolygon();
        $this->region = $this->stringToArray($this->region);
        $this->floor_types = $this->stringToArray($this->floor_types);
        $this->type_id = $this->stringToArray($this->type_id);
        $this->object_type = $this->stringToArray($this->object_type);
        $this->expand = $this->stringToArray($this->expand);
        $this->original_id = $this->stringToArray($this->original_id);
        $this->complex_id = $this->stringToArray($this->complex_id);
        $this->object_id = $this->stringToArray($this->object_id);
        $this->uniqueOffer = json_decode($this->uniqueOffer);
    }
    public function getRecommendedCondition()
    {
        $eb = new ExpressionBuilder();
        $eb->addCondition(['>=', 'power_value', $this->rangeMinElectricity], 70, 0)
            ->addCondition(['<=', 'from_mkad', $this->approximateDistanceFromMKAD], 40, 0)
            ->addCondition(['IN', 'heated', $this->heated], 70, 0)
            ->addCondition(['IN', 'has_cranes', $this->has_cranes], 70, 0)
            ->addCondition(['IN', 'deal_type', $this->deal_type], 20, 0)
            ->addCondition(['IN', 'floor_types', $this->floor_types], 25, 0)
            ->addCondition(['IN', 'region', $this->region], 80, 0)
            ->addCondition(['IN', 'status', $this->status], 80, 0)
            ->addCondition(['IN', 'direction', $this->direction], 60, 0)
            ->addCondition(['IN', 'district_moscow', $this->district_moscow], 60, 0)
            ->addCondition(['<=', 'GREATEST(c_industry_offers_mix.ceiling_height_min, c_industry_offers_mix.ceiling_height_max)', $this->rangeMaxCeilingHeight, false], 40, 0)
            ->addCondition(['>=', 'LEAST(c_industry_offers_mix.ceiling_height_min, c_industry_offers_mix.ceiling_height_max)', $this->rangeMinCeilingHeight, false], 40, 0)
            ->addCondition(['>=', 'area_max', $this->rangeMinArea], 75, 0)
            ->addCondition(['<=', 'area_min', $this->rangeMaxArea], 65, 0);


        if ($this->gates && is_array($this->gates)) {
            foreach ($this->gates as $gate) {
                $eb->addCondition(['like', 'gates', new Expression("'%\"{$gate}\"%'")], 35, 0);
            }
        }
        if ($this->deal_type == OfferMix::DEAL_TYPE_RENT || $this->deal_type == OfferMix::DEAL_TYPE_SUBLEASE) {
            $eb->addCondition(['<=', 'GREATEST(c_industry_offers_mix.price_mezzanine_min, c_industry_offers_mix.price_mezzanine_max, c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max )', $this->pricePerFloor, false], 50, 0);
        } elseif ($this->deal_type == OfferMix::DEAL_TYPE_SALE) {
            $eb->addCondition(['<=', 'price_sale_max', $this->pricePerFloor], 50, 0);
        }
        $eb->addTablePrefix(OfferMix::tableName());
        return $eb;
    }
    public function getRecommendedOrderExpression($sort)
    {
        $eb = $this->getRecommendedCondition();
        $eb->prepareToEnd($sort);
        return $eb->getConditionExpression();
    }

    public function setFilters(ActiveQuery $query): void
    {
        $joinedDbName = DbHelper::getDsnAttribute('dbname', Company::getDb()->dsn);

        if ($this->withoutOffersFromQuery) {
            $withoutQuery = json_decode($this->withoutOffersFromQuery, true);
            if (array_key_exists('withoutOffersFromQuery', $withoutQuery)) {
                throw new ValidationErrorHttpException("infinite cycle");
            }
            $searchModel = new self();
            $dp = $searchModel->search($withoutQuery);
            $dp->pagination->pageSize = 0;
            $models = $dp->getModels();
            $ids = array_map(function ($elem) {
                return $elem->id;
            }, $models);
            $query->andFilterWhere(['not in', 'c_industry_offers_mix.id', $ids]);
        }

        if (!$this->noWith) {
            $query->with(['object', 'miniOffersMix', 'generalOffersMix.offer', 'offer', 'company.mainContact.emails', 'company.mainContact.phones', 'comments', 'contact.emails', 'contact.phones']);
            if ($this->timeline_id) {
                $query->with(['comments' => function ($query) use ($joinedDbName) {
                    return $query->from("$joinedDbName.timeline_step_object_comment")->where(["$joinedDbName.timeline_step_object_comment.timeline_id" => $this->timeline_id]);
                }]);
            } else {
                $query->with(['comments']);
            }
        }

        if ($this->region_neardy) {
            $near_mo = Region::find()->distinct()->joinWith(['locations'])->where(['l_locations.near_mo' => $this->region_neardy])->andWhere(['!=', 'l_locations.region', 6])->asArray()->all();
            $region_ids = array_map(function ($elem) {
                return $elem['id'];
            }, $near_mo);
            if ($region_ids && $this->region) {
                if (is_array($this->region)) {
                    $this->region = array_merge($this->region, $region_ids);
                } else {
                    $this->region = array_merge([$this->region], $region_ids);
                }
            }
        }

        if ($this->all) {
            $searchArray = ['and'];
            $words = explode(" ", $this->all);
            foreach ($words as $word) {
                $searchArray[] = [
                    'or',
                    ['=', 'c_industry_offers_mix.object_id', $word],
                    ['like', 'c_industry_offers_mix.title', $word],
                    ['like', 'company.nameEng', $word],
                    ['like', 'company.nameRu', $word],
                    ['like', 'contact.first_name', $word],
                    ['like', 'contact.middle_name', $word],
                    ['like', 'contact.last_name', $word],
                    ['like', 'phone.phone', $word],
                    ['like', 'c_industry_offers_mix.address', $word]
                ];
            }
            $query->orFilterWhere($searchArray);
        }

        if ($this->recommended_sort) {
            if ($expression = $this->getRecommendedOrderExpression('DESC')) {
                $query->orderBy($expression);
                $query->andFilterWhere(['>=', $this->getRecommendedCondition()->getConditionExpression(), self::MIN_RECOMMENDED_WEIGHT_SUM]);
                $query->andFilterWhere([
                    'c_industry_offers_mix.deleted' => 0,
                    'c_industry_offers_mix.type_id' => [1, 2],
                ]);
                return;
            }
        }
        // для релевантности
        if ($this->all) {
            $rangingQueryExp = "";
            $words = explode(" ", $this->all);
            $addressWeight = 30;
            foreach ($words as $key => $word) {
                if ($key > 0) {
                    $rangingQueryExp .= "+";
                    $addressWeight += 20;
                }
                $rangingQueryExp .= "(
                    IF (`c_industry_offers_mix`.`object_id` LIKE '%{$word}%', 90, 0) 
                    + IF (`c_industry_offers_mix`.`object_id` = '{$word}', 420, 0) 
                    + IF (`phone`.`phone` LIKE '%{$word}%', 40, 0) 
                    + IF (`company`.`nameRu` LIKE '%{$word}%', 50, 0) 
                    + IF (`company`.`nameEng` LIKE '%{$word}%', 50, 0) 
                    + IF (`contact`.`first_name` LIKE '%{$word}%', 30, 0) 
                    + IF (`contact`.`middle_name` LIKE '%{$word}%', 30, 0) 
                    + IF (`contact`.`last_name` LIKE '%{$word}%', 30, 0) 
                    + IF (`c_industry_offers_mix`.`title` LIKE '%{$word}%', 30, 0) 
                    + IF (`c_industry_offers_mix`.`address` LIKE '%{$word}%', $addressWeight, 0) 
                )";
            }
            $rangingQueryExp .= " DESC";
            $query->orderBy(new Expression($rangingQueryExp));
        }


        // grid filtering conditions
        $query->andFilterWhere([
//            ObjectsBlock::tableName() . '.ad_avito' => $this->ad_avito,
            'c_industry_offers_mix.id' => $this->id,
            'c_industry_offers_mix.original_id' => $this->original_id,
            'c_industry_offers_mix.type_id' => $this->type_id,
            // 'c_industry_offers_mix.deal_type' => $this->deal_type,
            // 'c_industry_offers_mix.status' => $this->status,
            'c_industry_offers_mix.object_id' => $this->object_id,
            'c_industry_offers_mix.complex_id' => $this->complex_id,
            'c_industry_offers_mix.parent_id' => $this->parent_id,
            'c_industry_offers_mix.company_id' => $this->company_id,
            'c_industry_offers_mix.contact_id' => $this->contact_id,
            'c_industry_offers_mix.year_built' => $this->year_built,
            'c_industry_offers_mix.agent_id' => $this->agent_id,
            'c_industry_offers_mix.agent_visited' => $this->agent_visited,
            'c_industry_offers_mix.is_land' => $this->is_land,
            'c_industry_offers_mix.land_width' => $this->land_width,
            'c_industry_offers_mix.land_length' => $this->land_length,
            'c_industry_offers_mix.land_use_restrictions' => $this->land_use_restrictions,
            'c_industry_offers_mix.latitude' => $this->latitude,
            'c_industry_offers_mix.longitude' => $this->longitude,
            'c_industry_offers_mix.from_mkad' => $this->from_mkad,
            'c_industry_offers_mix.cian_region' => $this->cian_region,
            'c_industry_offers_mix.outside_mkad' => $this->outside_mkad,
            'c_industry_offers_mix.near_mo' => $this->near_mo,
            'c_industry_offers_mix.from_metro_value' => $this->from_metro_value,
            'c_industry_offers_mix.from_metro' => $this->from_metro,
            'c_industry_offers_mix.from_station_value' => $this->from_station_value,
            'c_industry_offers_mix.from_station' => $this->from_station,
            'c_industry_offers_mix.blocks_amount' => $this->blocks_amount,
            'c_industry_offers_mix.last_update' => $this->last_update,
            'c_industry_offers_mix.commission_client' => $this->commission_client,
            'c_industry_offers_mix.commission_owner' => $this->commission_owner,
            'c_industry_offers_mix.deposit' => $this->deposit,
            'c_industry_offers_mix.pledge' => $this->pledge,
            'c_industry_offers_mix.area_building' => $this->area_building,
            'c_industry_offers_mix.area_floor_full' => $this->area_floor_full,
            'c_industry_offers_mix.area_mezzanine_full' => $this->area_mezzanine_full,
            'c_industry_offers_mix.area_office_full' => $this->area_office_full,
            'c_industry_offers_mix.area_min' => $this->area_min,
            'c_industry_offers_mix.area_max' => $this->area_max,
            'c_industry_offers_mix.area_floor_min' => $this->area_floor_min,
            'c_industry_offers_mix.area_floor_max' => $this->area_floor_max,
            'c_industry_offers_mix.area_mezzanine_min' => $this->area_mezzanine_min,
            'c_industry_offers_mix.area_mezzanine_max' => $this->area_mezzanine_max,
            'c_industry_offers_mix.area_mezzanine_add' => $this->area_mezzanine_add,
            'c_industry_offers_mix.area_office_min' => $this->area_office_min,
            'c_industry_offers_mix.area_office_max' => $this->area_office_max,
            'c_industry_offers_mix.area_office_add' => $this->area_office_add,
            'c_industry_offers_mix.area_tech_min' => $this->area_tech_min,
            'c_industry_offers_mix.area_tech_max' => $this->area_tech_max,
            'c_industry_offers_mix.area_field_min' => $this->area_field_min,
            'c_industry_offers_mix.area_field_max' => $this->area_field_max,
            'c_industry_offers_mix.pallet_place_min' => $this->pallet_place_min,
            'c_industry_offers_mix.pallet_place_max' => $this->pallet_place_max,
            'c_industry_offers_mix.cells_place_min' => $this->cells_place_min,
            'c_industry_offers_mix.cells_place_max' => $this->cells_place_max,
            'c_industry_offers_mix.inc_electricity' => $this->inc_electricity,
            'c_industry_offers_mix.inc_heating' => $this->inc_heating,
            'c_industry_offers_mix.inc_water' => $this->inc_water,
            'c_industry_offers_mix.price_opex_inc' => $this->price_opex_inc,
            'c_industry_offers_mix.price_opex' => $this->price_opex,
            'c_industry_offers_mix.price_opex_min' => $this->price_opex_min,
            'c_industry_offers_mix.price_opex_max' => $this->price_opex_max,
            'c_industry_offers_mix.price_public_services_inc' => $this->price_public_services_inc,
            'c_industry_offers_mix.price_public_services' => $this->price_public_services,
            'c_industry_offers_mix.public_services' => $this->public_services,
            'c_industry_offers_mix.price_public_services_min' => $this->price_public_services_min,
            'c_industry_offers_mix.price_public_services_max' => $this->price_public_services_max,
            'c_industry_offers_mix.price_floor_min' => $this->price_floor_min,
            'c_industry_offers_mix.price_floor_max' => $this->price_floor_max,
            'c_industry_offers_mix.price_floor_min_month' => $this->price_floor_min_month,
            'c_industry_offers_mix.price_floor_max_month' => $this->price_floor_max_month,
            'c_industry_offers_mix.price_min_month_all' => $this->price_min_month_all,
            'c_industry_offers_mix.price_max_month_all' => $this->price_max_month_all,
            'c_industry_offers_mix.price_floor_100_min' => $this->price_floor_100_min,
            'c_industry_offers_mix.price_floor_100_max' => $this->price_floor_100_max,
            'c_industry_offers_mix.price_mezzanine_min' => $this->price_mezzanine_min,
            'c_industry_offers_mix.price_mezzanine_max' => $this->price_mezzanine_max,
            'c_industry_offers_mix.price_office_min' => $this->price_office_min,
            'c_industry_offers_mix.price_office_max' => $this->price_office_max,
            'c_industry_offers_mix.price_sale_min' => $this->price_sale_min,
            'c_industry_offers_mix.price_sale_max' => $this->price_sale_max,
            'c_industry_offers_mix.price_safe_pallet_min' => $this->price_safe_pallet_min,
            'c_industry_offers_mix.price_safe_pallet_max' => $this->price_safe_pallet_max,
            'c_industry_offers_mix.price_safe_volume_min' => $this->price_safe_volume_min,
            'c_industry_offers_mix.price_safe_volume_max' => $this->price_safe_volume_max,
            'c_industry_offers_mix.price_safe_floor_min' => $this->price_safe_floor_min,
            'c_industry_offers_mix.price_safe_floor_max' => $this->price_safe_floor_max,
            'c_industry_offers_mix.price_safe_calc_min' => $this->price_safe_calc_min,
            'c_industry_offers_mix.price_safe_calc_max' => $this->price_safe_calc_max,
            'c_industry_offers_mix.price_safe_calc_month_min' => $this->price_safe_calc_month_min,
            'c_industry_offers_mix.price_safe_calc_month_max' => $this->price_safe_calc_month_max,
            'c_industry_offers_mix.price_sale_min_all' => $this->price_sale_min_all,
            'c_industry_offers_mix.price_sale_max_all' => $this->price_sale_max_all,
            'c_industry_offers_mix.ceiling_height_min' => $this->ceiling_height_min,
            'c_industry_offers_mix.ceiling_height_max' => $this->ceiling_height_max,
            'c_industry_offers_mix.temperature_min' => $this->temperature_min,
            'c_industry_offers_mix.temperature_max' => $this->temperature_max,
            'c_industry_offers_mix.load_floor_min' => $this->load_floor_min,
            'c_industry_offers_mix.load_floor_max' => $this->load_floor_max,
            'c_industry_offers_mix.load_mezzanine_min' => $this->load_mezzanine_min,
            'c_industry_offers_mix.load_mezzanine_max' => $this->load_mezzanine_max,
            'c_industry_offers_mix.prepay' => $this->prepay,
            'c_industry_offers_mix.floor_min' => $this->floor_min,
            'c_industry_offers_mix.floor_max' => $this->floor_max,
            'c_industry_offers_mix.self_leveling' => $this->self_leveling,
            'c_industry_offers_mix.heated' => $this->heated,
            'c_industry_offers_mix.elevators_min' => $this->elevators_min,
            'c_industry_offers_mix.elevators_max' => $this->elevators_max,
            'c_industry_offers_mix.elevators_num' => $this->elevators_num,
            'c_industry_offers_mix.has_cranes' => $this->has_cranes,
            'c_industry_offers_mix.cranes_min' => $this->cranes_min,
            'c_industry_offers_mix.cranes_max' => $this->cranes_max,
            'c_industry_offers_mix.cranes_num' => $this->cranes_num,
            'c_industry_offers_mix.cranes_railway_min' => $this->cranes_railway_min,
            'c_industry_offers_mix.cranes_railway_max' => $this->cranes_railway_max,
            'c_industry_offers_mix.cranes_railway_num' => $this->cranes_railway_num,
            'c_industry_offers_mix.cranes_gantry_min' => $this->cranes_gantry_min,
            'c_industry_offers_mix.cranes_gantry_max' => $this->cranes_gantry_max,
            'c_industry_offers_mix.cranes_gantry_num' => $this->cranes_gantry_num,
            'c_industry_offers_mix.cranes_overhead_min' => $this->cranes_overhead_min,
            'c_industry_offers_mix.cranes_overhead_max' => $this->cranes_overhead_max,
            'c_industry_offers_mix.cranes_overhead_num' => $this->cranes_overhead_num,
            'c_industry_offers_mix.cranes_cathead_min' => $this->cranes_cathead_min,
            'c_industry_offers_mix.cranes_cathead_max' => $this->cranes_cathead_max,
            'c_industry_offers_mix.cranes_cathead_num' => $this->cranes_cathead_num,
            'c_industry_offers_mix.telphers_min' => $this->telphers_min,
            'c_industry_offers_mix.telphers_max' => $this->telphers_max,
            'c_industry_offers_mix.telphers_num' => $this->telphers_num,
            'c_industry_offers_mix.railway' => $this->railway,
            'c_industry_offers_mix.railway_value' => $this->railway_value,
            'c_industry_offers_mix.power' => $this->power,
            'c_industry_offers_mix.power_value' => $this->power_value,
            'c_industry_offers_mix.steam' => $this->steam,
            'c_industry_offers_mix.steam_value' => $this->steam_value,
            'c_industry_offers_mix.gas' => $this->gas,
            'c_industry_offers_mix.gas_value' => $this->gas_value,
            'c_industry_offers_mix.phone' => $this->phone,
            'c_industry_offers_mix.water_value' => $this->water_value,
            // 'c_industry_offers_mix.sewage_central' => $this->sewage_central,
            'c_industry_offers_mix.sewage_central_value' => $this->sewage_central_value,
            'c_industry_offers_mix.sewage_rain' => $this->sewage_rain,
            'c_industry_offers_mix.firefighting' => $this->firefighting,
            'c_industry_offers_mix.video_control' => $this->video_control,
            'c_industry_offers_mix.access_control' => $this->access_control,
            'c_industry_offers_mix.security_alert' => $this->security_alert,
            'c_industry_offers_mix.fire_alert' => $this->fire_alert,
            'c_industry_offers_mix.smoke_exhaust' => $this->smoke_exhaust,
            'c_industry_offers_mix.canteen' => $this->canteen,
            'c_industry_offers_mix.hostel' => $this->hostel,
            'c_industry_offers_mix.racks' => $this->racks,
            'c_industry_offers_mix.warehouse_equipment' => $this->warehouse_equipment,
            'c_industry_offers_mix.charging_room' => $this->charging_room,
            'c_industry_offers_mix.cross_docking' => $this->cross_docking,
            'c_industry_offers_mix.cranes_runways' => $this->cranes_runways,
            'c_industry_offers_mix.parking_car' => $this->parking_car,
            'c_industry_offers_mix.parking_lorry' => $this->parking_lorry,
            'c_industry_offers_mix.parking_truck' => $this->parking_truck,
            'c_industry_offers_mix.built_to_suit' => $this->built_to_suit,
            'c_industry_offers_mix.built_to_suit_time' => $this->built_to_suit_time,
            'c_industry_offers_mix.built_to_suit_plan' => $this->built_to_suit_plan,
            'c_industry_offers_mix.rent_business' => $this->rent_business,
            'c_industry_offers_mix.rent_business_fill' => $this->rent_business_fill,
            'c_industry_offers_mix.rent_business_price' => $this->rent_business_price,
            'c_industry_offers_mix.rent_business_long_contracts' => $this->rent_business_long_contracts,
            'c_industry_offers_mix.rent_business_last_repair' => $this->rent_business_last_repair,
            'c_industry_offers_mix.rent_business_payback' => $this->rent_business_payback,
            'c_industry_offers_mix.rent_business_income' => $this->rent_business_income,
            'c_industry_offers_mix.rent_business_profit' => $this->rent_business_profit,
            'c_industry_offers_mix.sale_company' => $this->sale_company,
            'c_industry_offers_mix.holidays' => $this->holidays,
            'c_industry_offers_mix.ad_realtor' => $this->ad_realtor,
            'c_industry_offers_mix.ad_cian' => $this->ad_cian,
            'c_industry_offers_mix.ad_cian_top3' => $this->ad_cian_top3,
            'c_industry_offers_mix.ad_cian_premium' => $this->ad_cian_premium,
            'c_industry_offers_mix.ad_cian_hl' => $this->ad_cian_hl,
            'c_industry_offers_mix.ad_yandex' => $this->ad_yandex,
            'c_industry_offers_mix.ad_avito' => $this->ad_avito,
            'c_industry_offers_mix.ad_yandex_raise' => $this->ad_yandex_raise,
            'c_industry_offers_mix.ad_yandex_promotion' => $this->ad_yandex_promotion,
            'c_industry_offers_mix.ad_yandex_premium' => $this->ad_yandex_premium,
            'c_industry_offers_mix.ad_arendator' => $this->ad_arendator,
            'c_industry_offers_mix.ad_free' => $this->ad_free,
            'c_industry_offers_mix.ad_special' => $this->ad_special,
            'c_industry_offers_mix.deleted' => 0,
            'c_industry_offers_mix.test_only' => $this->test_only,
            'c_industry_offers_mix.is_exclusive' => $this->is_exclusive,
            'c_industry_offers_mix.deal_id' => $this->deal_id,
            'c_industry_offers_mix.hide_from_market' => $this->hide_from_market,
            'c_industry_offers_mix.region' => $this->region,
            // 'c_industry_offers_mix.direction' => $this->direction,
            'c_industry_offers_mix.district' => $this->district,
            // 'c_industry_offers_mix.district_moscow' => $this->district_moscow,
            'c_industry_offers_mix.class' => $this->class
        ]);

        $query->andFilterWhere(['like', 'visual_id', $this->visual_id])
            ->andFilterWhere(['like', 'deal_type_name', $this->deal_type_name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'purposes_furl', $this->purposes_furl])
            ->andFilterWhere(['like', 'object_type_name', $this->object_type_name])
            ->andFilterWhere(['like', 'agent_name', $this->agent_name])
            ->andFilterWhere(['like', 'landscape_type', $this->landscape_type])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'class_name', $this->class_name])
            ->andFilterWhere(['like', 'region_name', $this->region_name])
            ->andFilterWhere(['like', 'town', $this->town])
            ->andFilterWhere(['like', 'town_name', $this->town_name])
            ->andFilterWhere(['like', 'district_name', $this->district_name])
            ->andFilterWhere(['like', 'district_moscow_name', $this->district_moscow_name])
            ->andFilterWhere(['like', 'direction_name', $this->direction_name])
            ->andFilterWhere(['like', 'highway', $this->highway])
            ->andFilterWhere(['like', 'highway_name', $this->highway_name])
            ->andFilterWhere(['like', 'highway_moscow', $this->highway_moscow])
            ->andFilterWhere(['like', 'highway_moscow_name', $this->highway_moscow_name])
            ->andFilterWhere(['like', 'metro', $this->metro])
            ->andFilterWhere(['like', 'metro_name', $this->metro_name])
            ->andFilterWhere(['like', 'railway_station', $this->railway_station])
            ->andFilterWhere(['like', 'blocks', $this->blocks])
            ->andFilterWhere(['like', 'photos', $this->photos])
            ->andFilterWhere(['like', 'videos', $this->videos])
            ->andFilterWhere(['like', 'thumbs', $this->thumbs])
            ->andFilterWhere(['like', 'tax_form', $this->tax_form])
            ->andFilterWhere(['like', 'safe_type', $this->safe_type])
            ->andFilterWhere(['like', 'safe_type_furl', $this->safe_type_furl])
            ->andFilterWhere(['like', 'floor_type', $this->floor_type])
            ->andFilterWhere(['like', 'gate_type', $this->gate_type])
            ->andFilterWhere(['like', 'gate_num', $this->gate_num])
            ->andFilterWhere(['like', 'column_grid', $this->column_grid])
            ->andFilterWhere(['like', 'internet', $this->internet])
            ->andFilterWhere(['like', 'heating', $this->heating])
            ->andFilterWhere(['like', 'facing', $this->facing])
            ->andFilterWhere(['like', 'ventilation', $this->ventilation])
            ->andFilterWhere(['like', 'guard', $this->guard])
            ->andFilterWhere(['like', 'firefighting_name', $this->firefighting_name])
            ->andFilterWhere(['like', 'cadastral_number', $this->cadastral_number])
            ->andFilterWhere(['like', 'cadastral_number_land', $this->cadastral_number_land])
            ->andFilterWhere(['like', 'field_allow_usage', $this->field_allow_usage])
            ->andFilterWhere(['like', 'available_from', $this->available_from])
            ->andFilterWhere(['like', 'own_type', $this->own_type])
            ->andFilterWhere(['like', 'own_type_land', $this->own_type_land])
            ->andFilterWhere(['like', 'land_category', $this->land_category])
            ->andFilterWhere(['like', 'entry_territory', $this->entry_territory])
            ->andFilterWhere(['like', 'parking_car_value', $this->parking_car_value])
            ->andFilterWhere(['like', 'parking_lorry_value', $this->parking_lorry_value])
            ->andFilterWhere(['like', 'parking_truck_value', $this->parking_truck_value])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['<=', 'from_mkad', $this->approximateDistanceFromMKAD])
            ->andFilterWhere(['<=', 'from_mkad', $this->rangeMaxDistanceFromMKAD])
            ->andFilterWhere(['>=', 'power_value', $this->rangeMinElectricity])
            ->andFilterWhere(['<=', 'power_value', $this->rangeMaxElectricity]);


        if ($this->deal_type == OfferMix::DEAL_TYPE_RENT || $this->deal_type == OfferMix::DEAL_TYPE_SUBLEASE) {
            $query->andFilterWhere(['<=', 'GREATEST(c_industry_offers_mix.price_mezzanine_min, c_industry_offers_mix.price_mezzanine_max, c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max )', $this->pricePerFloor]);
        } elseif ($this->deal_type == OfferMix::DEAL_TYPE_SALE) {
            $query->andFilterWhere(['<=', 'c_industry_offers_mix.price_sale_max', $this->pricePerFloor]);
        }
        $query->andFilterWhere(['or like', 'c_industry_offers_mix.gates', $this->gates]);
        $query->andFilterWhere(['or like', 'c_industry_offers_mix.purposes', $this->purposes]);
        $query->andFilterWhere(['or like', 'c_industry_offers_mix.object_type', $this->object_type]);
        $query->andFilterWhere(['or like', 'c_industry_offers_mix.floor_types', $this->floor_types]);
        $query->andFilterWhere(['or like', 'c_industry_offers_mix.floor_types', $this->floor_types]);


        if ($this->sewage_central !== null) {
            $query->andFilterWhere(['in', new Expression("
                (CASE WHEN c_industry_offers_mix.type_id = 1 THEN c_industry_blocks.sewage
                WHEN c_industry_offers_mix.type_id = 2 THEN c_industry_offers_mix.sewage_central
                ELSE c_industry_offers_mix.sewage_central
                END)
            "), $this->sewage_central]);
        }

        $query->andFilterWhere([
            'or',
            ['c_industry_offers_mix.district_moscow' => $this->district_moscow],
            ['c_industry_offers_mix.direction' => $this->direction]
        ]);



        $query->andFilterWhere([
            'or',
            ['=', 'c_industry_offers_mix.floor_min', $this->firstFloorOnly],
            ['=', 'c_industry_offers_mix.floor_max', $this->firstFloorOnly]
        ]);
        $query->andFilterWhere([
            'and',
            ['<=', 'LEAST(c_industry_offers_mix.area_max, c_industry_offers_mix.area_min)', $this->rangeMaxArea],
            ['>=', 'GREATEST(c_industry_offers_mix.area_max, c_industry_offers_mix.area_min)', $this->rangeMinArea],
        ]);

        $query->andFilterWhere([
            'and',
            [
                '<=',
                new Expression('LEAST(c_industry_offers_mix.ceiling_height_min, c_industry_offers_mix.ceiling_height_max)'),
                $this->rangeMaxCeilingHeight
            ],
            [
                '>=',
                new Expression('GREATEST(c_industry_offers_mix.ceiling_height_min, c_industry_offers_mix.ceiling_height_max)'),
                $this->rangeMinCeilingHeight
            ],
        ]);

        if ($this->objectsOnly) {
            $query->groupBy($this->getField('object_id'));
        }

        $rent_price_least = "IF(
            LEAST(c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max) 
            IS NULL,
             0,
              LEAST(c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max)
              )";
        $rent_price_greatest = "IF(GREATEST(c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max) IS NULL, 0, GREATEST(c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max))";
        $sale_price_least = "IF(LEAST(c_industry_offers_mix.price_sale_max, c_industry_offers_mix.price_sale_min) IS NULL, 0, LEAST(c_industry_offers_mix.price_sale_max, c_industry_offers_mix.price_sale_min))";
        $sale_price_greatest = "IF(GREATEST(c_industry_offers_mix.price_sale_max, c_industry_offers_mix.price_sale_min) IS NULL, 0, GREATEST(c_industry_offers_mix.price_sale_max, c_industry_offers_mix.price_sale_min))";
        $rs_price_least = "IF(LEAST(c_industry_offers_mix.price_safe_pallet_max, c_industry_offers_mix.price_safe_pallet_min) IS NULL, 0, LEAST(c_industry_offers_mix.price_safe_pallet_max, c_industry_offers_mix.price_safe_pallet_min))";
        $rs_price_greatest = "IF(GREATEST(c_industry_offers_mix.price_safe_pallet_max, c_industry_offers_mix.price_safe_pallet_min) IS NULL, 0, GREATEST(c_industry_offers_mix.price_safe_pallet_max, c_industry_offers_mix.price_safe_pallet_min))";

        $query->andFilterWhere([
            'and',
            [
                '<=',
                new Expression("CASE WHEN c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_RENT
                    . " OR c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_SUBLEASE
                    . "  THEN $rent_price_least WHEN c_industry_offers_mix.deal_type = "
                    . OfferMix::DEAL_TYPE_SALE
                    . " THEN $sale_price_least ELSE $rs_price_least END"),
                $this->rangeMaxPricePerFloor
            ],
            [
                '>=',
                new Expression("CASE WHEN c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_RENT
                    . " OR c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_SUBLEASE
                    . "  THEN $rent_price_greatest WHEN c_industry_offers_mix.deal_type = "
                    . OfferMix::DEAL_TYPE_SALE
                    . " THEN $sale_price_greatest ELSE $rs_price_greatest END"),
                $this->rangeMinPricePerFloor
            ],
        ]);


        if ($this->water !== null) {
            if ($this->water == 1) {
                $query->andFilterWhere(['not in', 'c_industry_offers_mix.water', ['0', 'нет', '']]);
            }
            if ($this->water == 0) {
                $query->andFilterWhere(['c_industry_offers_mix.water' => ['0', 'нет', '']]);
            }
        }

        if ($this->status == null) {
            $query->andFilterWhere(
                ['=', 'c_industry_offers_mix.status', new Expression("
                    IF(c_industry_offers_mix.type_id IN (1,2), 1, 2)
                ")]
            );
        }
        if ($this->status == 3) {
            $query->andFilterWhere(['c_industry_offers_mix.status' => [1, 2]]);
        }
        if ($this->status == 1) {
            $query->andFilterWhere(['c_industry_offers_mix.status' => $this->status]);
        }
        if ($this->status == 2) {
            $query->andFilterWhere(
                ['=', 'c_industry_offers_mix.status', new Expression("
                    IF(c_industry_offers_mix.type_id IN (1,2), '', 2)
                ")]
            );
        }

        if ($this->deal_type !== null) {
            $query->andFilterWhere([
                'or',
                ['in', 'c_industry_offers_mix.deal_type', $this->deal_type],
                ['is', 'c_industry_offers_mix.deal_type', new Expression('null')]
            ]);
        }

        $this->setPolygonFilter($query);
    }

    /**
     * @param ActiveQuery $query
     * @return void
     */
    public function setPolygonFilter(ActiveQuery $query): void
    {
        if (!$this->polygon) {
            return;
        }

        $coords = implode(", ", $this->polygon);
        $polygonCondition = <<< EOF
                ST_CONTAINS(
                ST_GEOMFROMTEXT(
                    'POLYGON(
                ($coords)
                )'
                        ),
                        POINT(c_industry_offers_mix.latitude, c_industry_offers_mix.longitude)
                    )
            EOF;
        $query->andWhere(new Expression($polygonCondition));
    }

    public function getSelect(): array
    {
        $fields = (new OfferMix())->getAttributes();

        unset($fields['year_built']);
        unset($fields['agent_visited']);
        unset($fields['land_width']);
        unset($fields['land_length']);
        unset($fields['landscape_type']);
        unset($fields['land_use_restrictions']);
        unset($fields['cian_region']);
        unset($fields['videos']);
        unset($fields['blocks_amount']);
        unset($fields['highway_moscow']);
        unset($fields['metro']);
        unset($fields['metro_name']);
        unset($fields['from_metro_value']);
        unset($fields['from_metro']);
        unset($fields['railway_station']);
        unset($fields['from_station_value']);
        unset($fields['from_station']);
        // unset($fields['blocks']);
        unset($fields['photos']);
        unset($fields['thumbs']);
        unset($fields['commission_client']);
        unset($fields['commission_owner']);
        unset($fields['deposit']);
        unset($fields['pledge']);
        unset($fields['area_floor_full']);
        unset($fields['area_mezzanine_full']);
        unset($fields['area_office_full']);
        // unset($fields['area_min']);
        // unset($fields['area_max']);
        // unset($fields['area_floor_min']);
        // unset($fields['area_floor_max']);
        // unset($fields['area_mezzanine_min']);
        // unset($fields['area_mezzanine_max']);
        unset($fields['area_mezzanine_add']);
        // unset($fields['area_office_min']);
        // unset($fields['area_office_max']);
        unset($fields['area_office_add']);
        // unset($fields['area_tech_min']);
        // unset($fields['area_tech_max']);
        unset($fields['area_field_min']);
        unset($fields['area_field_max']);
        // unset($fields['pallet_place_min']);
        // unset($fields['pallet_place_max']);
        unset($fields['cells_place_min']);
        unset($fields['cells_place_max']);
        unset($fields['inc_electricity']);
        unset($fields['inc_heating']);
        unset($fields['inc_water']);
        unset($fields['price_opex_inc']);
        unset($fields['price_opex']);
        unset($fields['price_opex_min']);
        unset($fields['price_opex_max']);
        unset($fields['price_public_services_inc']);
        unset($fields['price_public_services']);
        unset($fields['public_services']);
        unset($fields['price_public_services_min']);
        unset($fields['price_public_services_max']);
        // unset($fields['price_floor_min']);
        // unset($fields['price_floor_max']);
        unset($fields['price_floor_min_month']);
        unset($fields['price_floor_max_month']);
        unset($fields['price_min_month_all']);
        unset($fields['price_max_month_all']);
        unset($fields['price_floor_100_min']);
        unset($fields['price_floor_100_max']);
        // unset($fields['price_mezzanine_min']);
        // unset($fields['price_mezzanine_max']);
        // unset($fields['price_office_min']);
        // unset($fields['price_office_max']);
        // unset($fields['price_safe_pallet_min']);
        // unset($fields['price_safe_pallet_max']);
        unset($fields['price_safe_volume_min']);
        unset($fields['price_safe_volume_max']);
        unset($fields['price_safe_floor_min']);
        unset($fields['price_safe_floor_max']);
        unset($fields['price_safe_calc_min']);
        unset($fields['price_safe_calc_max']);
        unset($fields['price_safe_calc_month_min']);
        unset($fields['price_safe_calc_month_max']);
        unset($fields['price_sale_min_all']);
        unset($fields['price_sale_max_all']);
        // unset($fields['load_mezzanine_min']);
        // unset($fields['load_floor_max']);
        // unset($fields['load_mezzanine_max']);
        unset($fields['safe_type']);
        unset($fields['safe_type_furl']);
        unset($fields['prepay']);
        // unset($fields['floor_min']);
        // unset($fields['floor_max']);
        unset($fields['column_grid']);
        unset($fields['elevators_min']);
        unset($fields['elevators_max']);
        unset($fields['elevators_num']);
        // unset($fields['cranes_min']);
        // unset($fields['cranes_max']);
        unset($fields['cranes_num']);
        unset($fields['cranes_railway_min']);
        unset($fields['cranes_railway_max']);
        unset($fields['cranes_railway_num']);
        // unset($fields['cranes_gantry_min']);
        // unset($fields['cranes_gantry_max']);
        unset($fields['cranes_gantry_num']);
        unset($fields['cranes_overhead_min']);
        unset($fields['cranes_overhead_max']);
        unset($fields['cranes_overhead_num']);
        unset($fields['cranes_cathead_min']);
        unset($fields['cranes_cathead_max']);
        unset($fields['cranes_cathead_num']);
        unset($fields['telphers_min']);
        unset($fields['telphers_max']);
        unset($fields['telphers_num']);
        unset($fields['railway_value']);
        unset($fields['steam_value']);
        unset($fields['gas_value']);
        unset($fields['phone']);
        unset($fields['internet']);
        unset($fields['heating']);
        unset($fields['facing']);
        unset($fields['ventilation']);
        unset($fields['water_value']);
        unset($fields['sewage_central_value']);
        unset($fields['sewage_rain']);
        unset($fields['guard']);
        unset($fields['firefighting']);
        unset($fields['firefighting_name']);
        unset($fields['video_control']);
        unset($fields['access_control']);
        unset($fields['security_alert']);
        unset($fields['fire_alert']);
        unset($fields['smoke_exhaust']);
        unset($fields['canteen']);
        unset($fields['hostel']);
        unset($fields['warehouse_equipment']);
        unset($fields['charging_room']);
        unset($fields['cross_docking']);
        unset($fields['cranes_runways']);
        unset($fields['cadastral_number']);
        unset($fields['cadastral_number_land']);
        unset($fields['field_allow_usage']);
        unset($fields['available_from']);
        unset($fields['own_type']);
        unset($fields['own_type_land']);
        unset($fields['land_category']);
        unset($fields['entry_territory']);
        unset($fields['parking_car']);
        unset($fields['parking_car_value']);
        unset($fields['parking_lorry']);
        unset($fields['parking_lorry_value']);
        unset($fields['parking_truck']);
        unset($fields['parking_truck_value']);
        unset($fields['built_to_suit']);
        unset($fields['built_to_suit_time']);
        unset($fields['built_to_suit_plan']);
        unset($fields['rent_business']);
        unset($fields['rent_business_fill']);
        unset($fields['rent_business_price']);
        unset($fields['rent_business_long_contracts']);
        unset($fields['rent_business_last_repair']);
        unset($fields['rent_business_payback']);
        unset($fields['rent_business_income']);
        unset($fields['rent_business_profit']);
        unset($fields['sale_company']);
        unset($fields['holidays']);
        unset($fields['ad_cian_hl']);
        unset($fields['ad_yandex_raise']);
        unset($fields['ad_yandex_promotion']);
        unset($fields['ad_yandex_premium']);
        unset($fields['ad_arendator']);
        unset($fields['ad_special']);
        unset($fields['is_exclusive']);
        unset($fields['deal_id']);

        $fields = array_keys($fields);

        $select = array_map(function ($elem) {
            return $this->getField($elem);
        }, $fields);

        return $select;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = OfferMix::find()
            ->select($this->getSelect())
            ->joinForSearch(true)
            ->with([
                'object.objectFloors',
                'consultant.userProfile'
            ])
            ->groupBy($this->getField('id'));

        $this->load($params, '');
        $this->normalizeProps();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 50,
                'pageSizeLimit' => [0, 50],
            ],
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'default' => SORT_DESC
                ],
                'attributes' => [
                    $this->getField('last_update'),
                    'from_mkad',
                    'price' => [
                        'asc' => [
                            new Expression("CASE WHEN c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_RENT . " OR c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_RENT . "  THEN GREATEST(c_industry_offers_mix.price_mezzanine_min, c_industry_offers_mix.price_mezzanine_max, c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max ) WHEN c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_SALE . " THEN c_industry_offers_mix.price_sale_max ELSE c_industry_offers_mix.price_safe_pallet_max END ASC")
                        ],
                        'desc' => [
                            new Expression("CASE WHEN c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_RENT . " OR c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_RENT . "  THEN GREATEST(c_industry_offers_mix.price_mezzanine_min, c_industry_offers_mix.price_mezzanine_max, c_industry_offers_mix.price_floor_min, c_industry_offers_mix.price_floor_max ) WHEN c_industry_offers_mix.deal_type = " . OfferMix::DEAL_TYPE_SALE . " THEN c_industry_offers_mix.price_sale_max ELSE c_industry_offers_mix.price_safe_pallet_max END DESC")
                        ]
                    ],
                    'area' => [
                        'asc' => [
                            'area_max' => SORT_ASC
                        ],
                        'desc' => [
                            'area_max' => SORT_DESC
                        ]
                    ],
                    'status' => [
                        'asc' => ['c_industry_offers_mix.status' => SORT_ASC],
                        'desc' => ['c_industry_offers_mix.status' => SORT_DESC]
                    ],
                    'original_ids' => [
                        'asc' => [
                            new Expression("FIELD(c_industry_offers_mix.original_id, {$this->sort_original_id}) ASC"),
                            $this->getField('last_update') => SORT_ASC,
                            'c_industry_offers_mix.status' => SORT_ASC
                        ],
                        'desc' => [
                            new Expression("FIELD(c_industry_offers_mix.original_id, {$this->sort_original_id}) DESC"),
                            $this->getField('last_update') => SORT_DESC,
                            'c_industry_offers_mix.status' => SORT_DESC
                        ],
                    ],
                    'default' => [
                        'asc' => [
                            $this->getField('last_update') => SORT_ASC,
                            'c_industry_offers_mix.status' => SORT_ASC,
                        ],
                        'desc' => [
                            $this->getField('last_update') => SORT_DESC,
                            'c_industry_offers_mix.status' => SORT_DESC,
                        ],
                    ]
                ]
            ]
        ]);


        if (!$this->validate()) {
            throw new ValidationErrorHttpException($this->getErrorSummary(false));
            return $dataProvider;
        }

        $this->setFilters($query);
        // Этот запрос решает проблему дубликатов, но выполняется долго (около 7 секунд)
        // $query->andWhere(new Expression("IF(c_industry_offers_mix.type_id = 2, JSON_LENGTH(c_industry_offers_mix.blocks) > 1 AND (SELECT COUNT(off.id) FROM c_industry_offers_mix as off WHERE off.type_id = 1 AND off.status = 1 AND FIND_IN_SET(off.id, REPLACE(REPLACE(TRIM(']' FROM TRIM('[' FROM c_industry_offers_mix.blocks->>\"$[*]\")), '\"', ''), ' ', '')) > 0) > 1,JSON_LENGTH(c_industry_offers_mix.blocks) IS NOT NULL OR JSON_LENGTH(c_industry_offers_mix.blocks) IS NULL)"));

        return $dataProvider;
    }
}
