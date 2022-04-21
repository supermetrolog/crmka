<?php

namespace app\models\oldDb;

use app\exceptions\ValidationErrorHttpException;
use app\models\Contact;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\oldDb\OfferMix;
use yii\db\Expression;

/**
 * OfferMixSearch represents the model behind the search form of `app\models\oldDb\OfferMix`.
 */
class OfferMixSearch extends OfferMix
{
    private const APPROXIMATE_PERCENT_FOR_DISTANCE_FROM_MKAD =  30;
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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rangeMinPricePerFloor', 'rangeMaxPricePerFloor', 'rangeMaxElectricity', 'rangeMinElectricity', 'rangeMaxDistanceFromMKAD', 'approximateDistanceFromMKAD', 'rangeMaxCeilingHeight', 'rangeMinCeilingHeight', 'pricePerFloor', 'rangeMinArea', 'rangeMaxArea', 'id', 'original_id', 'status', 'object_id', 'complex_id', 'parent_id', 'company_id', 'contact_id', 'year_built', 'agent_id', 'agent_visited', 'is_land', 'land_width', 'land_length', 'land_use_restrictions', 'from_mkad', 'cian_region', 'outside_mkad', 'near_mo', 'from_metro_value', 'from_metro', 'from_station_value', 'from_station', 'blocks_amount', 'last_update', 'commission_client', 'commission_owner', 'deposit', 'pledge', 'area_building', 'area_floor_full', 'area_mezzanine_full', 'area_office_full', 'area_min', 'area_max', 'area_floor_min', 'area_floor_max', 'area_mezzanine_min', 'area_mezzanine_max', 'area_mezzanine_add', 'area_office_min', 'area_office_max', 'area_office_add', 'area_tech_min', 'area_tech_max', 'area_field_min', 'area_field_max', 'pallet_place_min', 'pallet_place_max', 'cells_place_min', 'cells_place_max', 'inc_electricity', 'inc_heating', 'inc_water', 'price_opex_inc', 'price_opex', 'price_opex_min', 'price_opex_max', 'price_public_services_inc', 'price_public_services', 'public_services', 'price_public_services_min', 'price_public_services_max', 'price_floor_min', 'price_floor_max', 'price_floor_min_month', 'price_floor_max_month', 'price_min_month_all', 'price_max_month_all', 'price_floor_100_min', 'price_floor_100_max', 'price_mezzanine_min', 'price_mezzanine_max', 'price_office_min', 'price_office_max', 'price_sale_min', 'price_sale_max', 'price_safe_pallet_min', 'price_safe_pallet_max', 'price_safe_volume_min', 'price_safe_volume_max', 'price_safe_floor_min', 'price_safe_floor_max', 'price_safe_calc_min', 'price_safe_calc_max', 'price_safe_calc_month_min', 'price_safe_calc_month_max', 'price_sale_min_all', 'price_sale_max_all', 'temperature_min', 'temperature_max', 'prepay', 'floor_min', 'floor_max', 'self_leveling', 'heated', 'elevators_min', 'elevators_max', 'elevators_num', 'cranes_num', 'cranes_railway_num', 'cranes_gantry_num', 'cranes_overhead_num', 'cranes_cathead_num', 'telphers_min', 'telphers_max', 'telphers_num', 'railway_value', 'power', 'power_value', 'steam_value', 'gas_value', 'phone', 'water_value', 'sewage_central_value', 'sewage_rain', 'firefighting', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'warehouse_equipment', 'charging_room', 'cross_docking', 'cranes_runways', 'parking_car', 'parking_lorry', 'parking_truck', 'built_to_suit', 'built_to_suit_time', 'built_to_suit_plan', 'rent_business', 'rent_business_fill', 'rent_business_price', 'rent_business_long_contracts', 'rent_business_last_repair', 'rent_business_payback', 'rent_business_income', 'rent_business_profit', 'sale_company', 'holidays', 'ad_realtor', 'ad_cian', 'ad_cian_top3', 'ad_cian_premium', 'ad_cian_hl', 'ad_yandex', 'ad_yandex_raise', 'ad_yandex_promotion', 'ad_yandex_premium', 'ad_arendator', 'ad_free', 'ad_special', 'deleted', 'test_only', 'is_exclusive', 'deal_id', 'hide_from_market'], 'integer'],
            [['has_cranes', 'railway', 'racks', 'sewage_central', 'steam', 'gas', 'deal_type', 'all', 'type_id', 'visual_id', 'deal_type_name', 'title', 'object_type', 'purposes', 'purposes_furl', 'object_type_name', 'agent_name', 'landscape_type', 'address', 'class', 'class_name', 'region', 'region_name', 'town', 'town_name', 'district', 'district_name', 'district_moscow', 'district_moscow_name', 'direction', 'direction_name', 'highway', 'highway_name', 'highway_moscow', 'highway_moscow_name', 'metro', 'metro_name', 'railway_station', 'blocks', 'photos', 'videos', 'thumbs', 'tax_form', 'safe_type', 'safe_type_furl', 'floor_type', 'floor_types', 'gates', 'gate_type', 'gate_num', 'column_grid', 'internet', 'heating', 'facing', 'ventilation', 'water', 'guard', 'firefighting_name', 'cadastral_number', 'cadastral_number_land', 'field_allow_usage', 'available_from', 'own_type', 'own_type_land', 'land_category', 'entry_territory', 'parking_car_value', 'parking_lorry_value', 'parking_truck_value', 'description'], 'safe'],
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
    public function stringToArray($value)
    {
        if (is_string($value)) {
            return explode(",", $value);
        }
        return $value;
    }
    public function normalizeRegion()
    {
        $regions = $this->stringToArray($this->region);
        $array = [];
        if (is_array($regions)) {
            foreach ($regions as $region) {
                $array[] = OfferMix::normalizeRegions($region);
            }

            $this->region = $array;
        }
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
                $array[] = OfferMix::normalizeGateTypes($gate);
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
                $array[] = OfferMix::normalizeObjectTypes($purpose);
            }

            $this->purposes = $array;
        }
    }
    public function normalizeGas()
    {
        if ($this->gas == 2) {
            $this->gas = [0, 2];
        }
    }
    public function normalizeSteam()
    {
        if ($this->steam == 2) {
            $this->steam = [0, 2];
        }
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
    public function normalizeProps()
    {
        $this->deal_type = OfferMix::normalizeDealType($this->deal_type);
        $this->agent_id = OfferMix::normalizeAgentId($this->agent_id);
        // $this->class = OfferMix::normalizeObjectClasses($this->class);
        $this->normalizeClass();
        $this->normalizeRegion();
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
        $this->floor_types = $this->stringToArray($this->floor_types);
        $this->type_id = $this->stringToArray($this->type_id);
        $this->object_type = $this->stringToArray($this->object_type);
        if ($this->approximateDistanceFromMKAD) {
            $this->approximateDistanceFromMKAD = floor(($this->approximateDistanceFromMKAD * self::APPROXIMATE_PERCENT_FOR_DISTANCE_FROM_MKAD / 100) + $this->approximateDistanceFromMKAD);
        }
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // $query = OfferMix::find()->distinct()->select(['crm.c_industry_offers_mix.id', 'crmka.company.id as anal'])->from(['crm.c_industry_offers_mix', 'crmka.company'])->with(['object', 'miniOffersMix', 'generalOffersMix.offer', 'offer', 'company.mainContact.emails'])->andWhere(['deleted' => 0]);
        // $query = OfferMix::find()->joinWith(['company.mainContact.phones'])->with(['object', 'miniOffersMix', 'generalOffersMix.offer', 'offer', 'company.mainContact.emails'])->andWhere(['deleted' => 0]);
        $query = OfferMix::find()->distinct()->joinWith(['company' => function ($query) {
            return $query->from('crmka.company')->joinWith(['mainContact' => function ($query) {
                return $query->from('crmka.contact')->joinWith(['phones' => function ($query) {
                    return $query->from('crmka.phone');
                }]);
            }]);
        }])->with(['object', 'miniOffersMix', 'generalOffersMix.offer', 'offer', 'company.mainContact.emails']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'enableMultiSort' => true,
                'defaultOrder' => [
                    'default' => SORT_DESC
                ],
                'attributes' => [
                    'last_update',
                    'from_mkad',
                    'price' => [
                        'asc' => [
                            new Expression("CASE WHEN deal_type = " . OfferMix::DEAL_TYPE_RENT . " OR deal_type = " . OfferMix::DEAL_TYPE_RENT . "  THEN GREATEST(price_mezzanine_min, price_mezzanine_max, price_floor_min, price_floor_max ) WHEN deal_type = " . OfferMix::DEAL_TYPE_SALE . " THEN price_sale_max ELSE price_safe_pallet_max END ASC")
                        ],
                        'desc' => [
                            new Expression("CASE WHEN deal_type = " . OfferMix::DEAL_TYPE_RENT . " OR deal_type = " . OfferMix::DEAL_TYPE_RENT . "  THEN GREATEST(price_mezzanine_min, price_mezzanine_max, price_floor_min, price_floor_max ) WHEN deal_type = " . OfferMix::DEAL_TYPE_SALE . " THEN price_sale_max ELSE price_safe_pallet_max END DESC")
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
                    'default' => [
                        'asc' => [
                            'last_update' => SORT_ASC,
                            'crm.c_industry_offers_mix.status' => SORT_ASC,
                        ],
                        'desc' => [
                            'last_update' => SORT_DESC,
                            'crm.c_industry_offers_mix.status' => SORT_DESC,
                        ],
                    ]
                ]
            ]
        ]);

        $this->load($params, '');
        $this->normalizeProps();

        if (!$this->validate()) {
            throw new ValidationErrorHttpException($this->getErrorSummary(false));

            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->orFilterWhere(['c_industry_offers_mix.object_id' => $this->all])
            ->orFilterWhere(['like', 'company.nameEng', $this->all])
            ->orFilterWhere(['like', 'company.nameRu', $this->all])
            ->orFilterWhere(['like', 'contact.first_name', $this->all])
            ->orFilterWhere(['like', 'contact.middle_name', $this->all])
            ->orFilterWhere(['like', 'contact.last_name', $this->all])
            ->orFilterWhere(['like', 'phone.phone', $this->all]);
        // grid filtering conditions
        $query->andFilterWhere([
            'crm.c_industry_offers_mix.id' => $this->id,
            'crm.c_industry_offers_mix.original_id' => $this->original_id,
            'crm.c_industry_offers_mix.type_id' => $this->type_id,
            'crm.c_industry_offers_mix.deal_type' => $this->deal_type,
            'crm.c_industry_offers_mix.status' => $this->status,
            'crm.c_industry_offers_mix.object_id' => $this->object_id,
            'crm.c_industry_offers_mix.complex_id' => $this->complex_id,
            'crm.c_industry_offers_mix.parent_id' => $this->parent_id,
            'crm.c_industry_offers_mix.company_id' => $this->company_id,
            'crm.c_industry_offers_mix.contact_id' => $this->contact_id,
            'crm.c_industry_offers_mix.year_built' => $this->year_built,
            'crm.c_industry_offers_mix.agent_id' => $this->agent_id,
            'crm.c_industry_offers_mix.agent_visited' => $this->agent_visited,
            'crm.c_industry_offers_mix.is_land' => $this->is_land,
            'crm.c_industry_offers_mix.land_width' => $this->land_width,
            'crm.c_industry_offers_mix.land_length' => $this->land_length,
            'crm.c_industry_offers_mix.land_use_restrictions' => $this->land_use_restrictions,
            'crm.c_industry_offers_mix.latitude' => $this->latitude,
            'crm.c_industry_offers_mix.longitude' => $this->longitude,
            'crm.c_industry_offers_mix.from_mkad' => $this->from_mkad,
            'crm.c_industry_offers_mix.cian_region' => $this->cian_region,
            'crm.c_industry_offers_mix.outside_mkad' => $this->outside_mkad,
            'crm.c_industry_offers_mix.near_mo' => $this->near_mo,
            'crm.c_industry_offers_mix.from_metro_value' => $this->from_metro_value,
            'crm.c_industry_offers_mix.from_metro' => $this->from_metro,
            'crm.c_industry_offers_mix.from_station_value' => $this->from_station_value,
            'crm.c_industry_offers_mix.from_station' => $this->from_station,
            'crm.c_industry_offers_mix.blocks_amount' => $this->blocks_amount,
            'crm.c_industry_offers_mix.last_update' => $this->last_update,
            'crm.c_industry_offers_mix.commission_client' => $this->commission_client,
            'crm.c_industry_offers_mix.commission_owner' => $this->commission_owner,
            'crm.c_industry_offers_mix.deposit' => $this->deposit,
            'crm.c_industry_offers_mix.pledge' => $this->pledge,
            'crm.c_industry_offers_mix.area_building' => $this->area_building,
            'crm.c_industry_offers_mix.area_floor_full' => $this->area_floor_full,
            'crm.c_industry_offers_mix.area_mezzanine_full' => $this->area_mezzanine_full,
            'crm.c_industry_offers_mix.area_office_full' => $this->area_office_full,
            'crm.c_industry_offers_mix.area_min' => $this->area_min,
            'crm.c_industry_offers_mix.area_max' => $this->area_max,
            'crm.c_industry_offers_mix.area_floor_min' => $this->area_floor_min,
            'crm.c_industry_offers_mix.area_floor_max' => $this->area_floor_max,
            'crm.c_industry_offers_mix.area_mezzanine_min' => $this->area_mezzanine_min,
            'crm.c_industry_offers_mix.area_mezzanine_max' => $this->area_mezzanine_max,
            'crm.c_industry_offers_mix.area_mezzanine_add' => $this->area_mezzanine_add,
            'crm.c_industry_offers_mix.area_office_min' => $this->area_office_min,
            'crm.c_industry_offers_mix.area_office_max' => $this->area_office_max,
            'crm.c_industry_offers_mix.area_office_add' => $this->area_office_add,
            'crm.c_industry_offers_mix.area_tech_min' => $this->area_tech_min,
            'crm.c_industry_offers_mix.area_tech_max' => $this->area_tech_max,
            'crm.c_industry_offers_mix.area_field_min' => $this->area_field_min,
            'crm.c_industry_offers_mix.area_field_max' => $this->area_field_max,
            'crm.c_industry_offers_mix.pallet_place_min' => $this->pallet_place_min,
            'crm.c_industry_offers_mix.pallet_place_max' => $this->pallet_place_max,
            'crm.c_industry_offers_mix.cells_place_min' => $this->cells_place_min,
            'crm.c_industry_offers_mix.cells_place_max' => $this->cells_place_max,
            'crm.c_industry_offers_mix.inc_electricity' => $this->inc_electricity,
            'crm.c_industry_offers_mix.inc_heating' => $this->inc_heating,
            'crm.c_industry_offers_mix.inc_water' => $this->inc_water,
            'crm.c_industry_offers_mix.price_opex_inc' => $this->price_opex_inc,
            'crm.c_industry_offers_mix.price_opex' => $this->price_opex,
            'crm.c_industry_offers_mix.price_opex_min' => $this->price_opex_min,
            'crm.c_industry_offers_mix.price_opex_max' => $this->price_opex_max,
            'crm.c_industry_offers_mix.price_public_services_inc' => $this->price_public_services_inc,
            'crm.c_industry_offers_mix.price_public_services' => $this->price_public_services,
            'crm.c_industry_offers_mix.public_services' => $this->public_services,
            'crm.c_industry_offers_mix.price_public_services_min' => $this->price_public_services_min,
            'crm.c_industry_offers_mix.price_public_services_max' => $this->price_public_services_max,
            'crm.c_industry_offers_mix.price_floor_min' => $this->price_floor_min,
            'crm.c_industry_offers_mix.price_floor_max' => $this->price_floor_max,
            'crm.c_industry_offers_mix.price_floor_min_month' => $this->price_floor_min_month,
            'crm.c_industry_offers_mix.price_floor_max_month' => $this->price_floor_max_month,
            'crm.c_industry_offers_mix.price_min_month_all' => $this->price_min_month_all,
            'crm.c_industry_offers_mix.price_max_month_all' => $this->price_max_month_all,
            'crm.c_industry_offers_mix.price_floor_100_min' => $this->price_floor_100_min,
            'crm.c_industry_offers_mix.price_floor_100_max' => $this->price_floor_100_max,
            'crm.c_industry_offers_mix.price_mezzanine_min' => $this->price_mezzanine_min,
            'crm.c_industry_offers_mix.price_mezzanine_max' => $this->price_mezzanine_max,
            'crm.c_industry_offers_mix.price_office_min' => $this->price_office_min,
            'crm.c_industry_offers_mix.price_office_max' => $this->price_office_max,
            'crm.c_industry_offers_mix.price_sale_min' => $this->price_sale_min,
            'crm.c_industry_offers_mix.price_sale_max' => $this->price_sale_max,
            'crm.c_industry_offers_mix.price_safe_pallet_min' => $this->price_safe_pallet_min,
            'crm.c_industry_offers_mix.price_safe_pallet_max' => $this->price_safe_pallet_max,
            'crm.c_industry_offers_mix.price_safe_volume_min' => $this->price_safe_volume_min,
            'crm.c_industry_offers_mix.price_safe_volume_max' => $this->price_safe_volume_max,
            'crm.c_industry_offers_mix.price_safe_floor_min' => $this->price_safe_floor_min,
            'crm.c_industry_offers_mix.price_safe_floor_max' => $this->price_safe_floor_max,
            'crm.c_industry_offers_mix.price_safe_calc_min' => $this->price_safe_calc_min,
            'crm.c_industry_offers_mix.price_safe_calc_max' => $this->price_safe_calc_max,
            'crm.c_industry_offers_mix.price_safe_calc_month_min' => $this->price_safe_calc_month_min,
            'crm.c_industry_offers_mix.price_safe_calc_month_max' => $this->price_safe_calc_month_max,
            'crm.c_industry_offers_mix.price_sale_min_all' => $this->price_sale_min_all,
            'crm.c_industry_offers_mix.price_sale_max_all' => $this->price_sale_max_all,
            'crm.c_industry_offers_mix.ceiling_height_min' => $this->ceiling_height_min,
            'crm.c_industry_offers_mix.ceiling_height_max' => $this->ceiling_height_max,
            'crm.c_industry_offers_mix.temperature_min' => $this->temperature_min,
            'crm.c_industry_offers_mix.temperature_max' => $this->temperature_max,
            'crm.c_industry_offers_mix.load_floor_min' => $this->load_floor_min,
            'crm.c_industry_offers_mix.load_floor_max' => $this->load_floor_max,
            'crm.c_industry_offers_mix.load_mezzanine_min' => $this->load_mezzanine_min,
            'crm.c_industry_offers_mix.load_mezzanine_max' => $this->load_mezzanine_max,
            'crm.c_industry_offers_mix.prepay' => $this->prepay,
            'crm.c_industry_offers_mix.floor_min' => $this->floor_min,
            'crm.c_industry_offers_mix.floor_max' => $this->floor_max,
            'crm.c_industry_offers_mix.self_leveling' => $this->self_leveling,
            'crm.c_industry_offers_mix.heated' => $this->heated,
            'crm.c_industry_offers_mix.elevators_min' => $this->elevators_min,
            'crm.c_industry_offers_mix.elevators_max' => $this->elevators_max,
            'crm.c_industry_offers_mix.elevators_num' => $this->elevators_num,
            'crm.c_industry_offers_mix.has_cranes' => $this->has_cranes,
            'crm.c_industry_offers_mix.cranes_min' => $this->cranes_min,
            'crm.c_industry_offers_mix.cranes_max' => $this->cranes_max,
            'crm.c_industry_offers_mix.cranes_num' => $this->cranes_num,
            'crm.c_industry_offers_mix.cranes_railway_min' => $this->cranes_railway_min,
            'crm.c_industry_offers_mix.cranes_railway_max' => $this->cranes_railway_max,
            'crm.c_industry_offers_mix.cranes_railway_num' => $this->cranes_railway_num,
            'crm.c_industry_offers_mix.cranes_gantry_min' => $this->cranes_gantry_min,
            'crm.c_industry_offers_mix.cranes_gantry_max' => $this->cranes_gantry_max,
            'crm.c_industry_offers_mix.cranes_gantry_num' => $this->cranes_gantry_num,
            'crm.c_industry_offers_mix.cranes_overhead_min' => $this->cranes_overhead_min,
            'crm.c_industry_offers_mix.cranes_overhead_max' => $this->cranes_overhead_max,
            'crm.c_industry_offers_mix.cranes_overhead_num' => $this->cranes_overhead_num,
            'crm.c_industry_offers_mix.cranes_cathead_min' => $this->cranes_cathead_min,
            'crm.c_industry_offers_mix.cranes_cathead_max' => $this->cranes_cathead_max,
            'crm.c_industry_offers_mix.cranes_cathead_num' => $this->cranes_cathead_num,
            'crm.c_industry_offers_mix.telphers_min' => $this->telphers_min,
            'crm.c_industry_offers_mix.telphers_max' => $this->telphers_max,
            'crm.c_industry_offers_mix.telphers_num' => $this->telphers_num,
            'crm.c_industry_offers_mix.railway' => $this->railway,
            'crm.c_industry_offers_mix.railway_value' => $this->railway_value,
            'crm.c_industry_offers_mix.power' => $this->power,
            'crm.c_industry_offers_mix.power_value' => $this->power_value,
            'crm.c_industry_offers_mix.steam' => $this->steam,
            'crm.c_industry_offers_mix.steam_value' => $this->steam_value,
            'crm.c_industry_offers_mix.gas' => $this->gas,
            'crm.c_industry_offers_mix.gas_value' => $this->gas_value,
            'crm.c_industry_offers_mix.phone' => $this->phone,
            'crm.c_industry_offers_mix.water_value' => $this->water_value,
            'crm.c_industry_offers_mix.sewage_central' => $this->sewage_central,
            'crm.c_industry_offers_mix.sewage_central_value' => $this->sewage_central_value,
            'crm.c_industry_offers_mix.sewage_rain' => $this->sewage_rain,
            'crm.c_industry_offers_mix.firefighting' => $this->firefighting,
            'crm.c_industry_offers_mix.video_control' => $this->video_control,
            'crm.c_industry_offers_mix.access_control' => $this->access_control,
            'crm.c_industry_offers_mix.security_alert' => $this->security_alert,
            'crm.c_industry_offers_mix.fire_alert' => $this->fire_alert,
            'crm.c_industry_offers_mix.smoke_exhaust' => $this->smoke_exhaust,
            'crm.c_industry_offers_mix.canteen' => $this->canteen,
            'crm.c_industry_offers_mix.hostel' => $this->hostel,
            'crm.c_industry_offers_mix.racks' => $this->racks,
            'crm.c_industry_offers_mix.warehouse_equipment' => $this->warehouse_equipment,
            'crm.c_industry_offers_mix.charging_room' => $this->charging_room,
            'crm.c_industry_offers_mix.cross_docking' => $this->cross_docking,
            'crm.c_industry_offers_mix.cranes_runways' => $this->cranes_runways,
            'crm.c_industry_offers_mix.parking_car' => $this->parking_car,
            'crm.c_industry_offers_mix.parking_lorry' => $this->parking_lorry,
            'crm.c_industry_offers_mix.parking_truck' => $this->parking_truck,
            'crm.c_industry_offers_mix.built_to_suit' => $this->built_to_suit,
            'crm.c_industry_offers_mix.built_to_suit_time' => $this->built_to_suit_time,
            'crm.c_industry_offers_mix.built_to_suit_plan' => $this->built_to_suit_plan,
            'crm.c_industry_offers_mix.rent_business' => $this->rent_business,
            'crm.c_industry_offers_mix.rent_business_fill' => $this->rent_business_fill,
            'crm.c_industry_offers_mix.rent_business_price' => $this->rent_business_price,
            'crm.c_industry_offers_mix.rent_business_long_contracts' => $this->rent_business_long_contracts,
            'crm.c_industry_offers_mix.rent_business_last_repair' => $this->rent_business_last_repair,
            'crm.c_industry_offers_mix.rent_business_payback' => $this->rent_business_payback,
            'crm.c_industry_offers_mix.rent_business_income' => $this->rent_business_income,
            'crm.c_industry_offers_mix.rent_business_profit' => $this->rent_business_profit,
            'crm.c_industry_offers_mix.sale_company' => $this->sale_company,
            'crm.c_industry_offers_mix.holidays' => $this->holidays,
            'crm.c_industry_offers_mix.ad_realtor' => $this->ad_realtor,
            'crm.c_industry_offers_mix.ad_cian' => $this->ad_cian,
            'crm.c_industry_offers_mix.ad_cian_top3' => $this->ad_cian_top3,
            'crm.c_industry_offers_mix.ad_cian_premium' => $this->ad_cian_premium,
            'crm.c_industry_offers_mix.ad_cian_hl' => $this->ad_cian_hl,
            'crm.c_industry_offers_mix.ad_yandex' => $this->ad_yandex,
            'crm.c_industry_offers_mix.ad_yandex_raise' => $this->ad_yandex_raise,
            'crm.c_industry_offers_mix.ad_yandex_promotion' => $this->ad_yandex_promotion,
            'crm.c_industry_offers_mix.ad_yandex_premium' => $this->ad_yandex_premium,
            'crm.c_industry_offers_mix.ad_arendator' => $this->ad_arendator,
            'crm.c_industry_offers_mix.ad_free' => $this->ad_free,
            'crm.c_industry_offers_mix.ad_special' => $this->ad_special,
            'crm.c_industry_offers_mix.deleted' => 0,
            'crm.c_industry_offers_mix.test_only' => $this->test_only,
            'crm.c_industry_offers_mix.is_exclusive' => $this->is_exclusive,
            'crm.c_industry_offers_mix.deal_id' => $this->deal_id,
            'crm.c_industry_offers_mix.hide_from_market' => $this->hide_from_market,
            'crm.c_industry_offers_mix.region' => $this->region,
            'crm.c_industry_offers_mix.direction' => $this->direction,
            'crm.c_industry_offers_mix.district' => $this->district,
            'crm.c_industry_offers_mix.district_moscow' => $this->district_moscow,
            'crm.c_industry_offers_mix.class' => $this->class
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
            ->andFilterWhere(['<=', '`area_mezzanine_max`+`area_floor_max`', $this->rangeMaxArea]) // area_warehouse_max < maxArea
            ->andFilterWhere(['>=', '`area_floor_min`', $this->rangeMinArea]) // area_warehouse_min < minArea
            ->andFilterWhere(['<=', new Expression('CASE WHEN ceiling_height_min > ceiling_height_max THEN ceiling_height_min ELSE ceiling_height_max END'), $this->rangeMaxCeilingHeight])
            ->andFilterWhere(['>=', 'ceiling_height_min', $this->rangeMinCeilingHeight])
            ->andFilterWhere(['<=', 'from_mkad', $this->approximateDistanceFromMKAD])
            ->andFilterWhere(['<=', 'from_mkad', $this->rangeMaxDistanceFromMKAD])
            ->andFilterWhere(['>=', 'power_value', $this->rangeMinElectricity])
            ->andFilterWhere(['<=', 'power_value', $this->rangeMaxElectricity])
            ->andFilterWhere([
                '<=', new Expression("CASE WHEN deal_type = " . OfferMix::DEAL_TYPE_RENT . " OR deal_type = " . OfferMix::DEAL_TYPE_RENT . "  THEN GREATEST(price_mezzanine_min, price_mezzanine_max, price_floor_min, price_floor_max ) WHEN deal_type = " . OfferMix::DEAL_TYPE_SALE . " THEN price_sale_max ELSE price_safe_pallet_max END"), $this->rangeMaxPricePerFloor
            ])
            ->andFilterWhere([
                '>=', new Expression("CASE WHEN deal_type = " . OfferMix::DEAL_TYPE_RENT . " OR deal_type = " . OfferMix::DEAL_TYPE_RENT . "  THEN LEAST(price_mezzanine_min, price_mezzanine_max, price_floor_min, price_floor_max ) WHEN deal_type = " . OfferMix::DEAL_TYPE_SALE . " THEN price_sale_min ELSE price_safe_pallet_min END"), $this->rangeMinPricePerFloor
            ]);



        if ($this->deal_type == self::DEAL_TYPE_RENT || $this->deal_type == self::DEAL_TYPE_SUBLEASE) {
            $query->andFilterWhere(['<=', 'GREATEST(price_mezzanine_min, price_mezzanine_max, price_floor_min, price_floor_max )', $this->pricePerFloor]);
        } elseif ($this->deal_type == self::DEAL_TYPE_SALE) {
            $query->andFilterWhere(['<=', 'price_sale_max', $this->pricePerFloor]);
        }

        if ($this->gates && is_array($this->gates)) {
            // $query->andFilterWhere(['=', new Expression("JSON_EXTRACT(`c_industry_offers_mix`.`gates`, '$[0]')"), "{$this->gates[0]}"]);
            foreach ($this->gates as $gate) {
                $query->andFilterWhere(['like', 'c_industry_offers_mix.gates', new Expression("'%\"{$gate}\"%'")]);
            }
        }
        if ($this->purposes && is_array($this->purposes)) {
            foreach ($this->purposes as $purpose) {
                $query->andFilterWhere(['like', 'c_industry_offers_mix.purposes', new Expression("'%\"{$purpose}\"%'")]);
            }
        }
        if ($this->object_type && is_array($this->object_type)) {
            foreach ($this->object_type as $type) {
                $query->andFilterWhere(['like', 'c_industry_offers_mix.object_type', new Expression("'%\"{$type}\"%'")]);
            }
        }
        if ($this->floor_types && is_array($this->floor_types)) {
            foreach ($this->floor_types as $floor_type) {
                $query->andFilterWhere(['like', 'c_industry_offers_mix.floor_types', new Expression("'%\"{$floor_type}\"%'")]);
            }
        }
        if ($this->water !== null) {
            if ($this->water == 1) {
                $query->andFilterWhere(['!=', 'c_industry_offers_mix.water', '0']);
            }
            if ($this->water == 0) {
                $query->andFilterWhere(['=', 'c_industry_offers_mix.water', '0']);
            }
        }

        return $dataProvider;
    }
}
