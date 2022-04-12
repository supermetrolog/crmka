<?php

namespace app\models\oldDb;

use app\exceptions\ValidationErrorHttpException;
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
    public $rangeMinArea;
    public $rangeMaxArea;
    public $rangeMinCeilingHeight;
    public $rangeMaxCeilingHeight;
    public $pricePerFloor;
    public $approximateDistanceFromMKAD;
    public $minElectricity;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minElectricity', 'approximateDistanceFromMKAD', 'rangeMaxCeilingHeight', 'rangeMinCeilingHeight', 'pricePerFloor', 'rangeMinArea', 'rangeMaxArea', 'id', 'original_id', 'deal_type', 'status', 'object_id', 'complex_id', 'parent_id', 'company_id', 'contact_id', 'year_built', 'agent_id', 'agent_visited', 'is_land', 'land_width', 'land_length', 'land_use_restrictions', 'from_mkad', 'cian_region', 'outside_mkad', 'near_mo', 'from_metro_value', 'from_metro', 'from_station_value', 'from_station', 'blocks_amount', 'last_update', 'commission_client', 'commission_owner', 'deposit', 'pledge', 'area_building', 'area_floor_full', 'area_mezzanine_full', 'area_office_full', 'area_min', 'area_max', 'area_floor_min', 'area_floor_max', 'area_mezzanine_min', 'area_mezzanine_max', 'area_mezzanine_add', 'area_office_min', 'area_office_max', 'area_office_add', 'area_tech_min', 'area_tech_max', 'area_field_min', 'area_field_max', 'pallet_place_min', 'pallet_place_max', 'cells_place_min', 'cells_place_max', 'inc_electricity', 'inc_heating', 'inc_water', 'price_opex_inc', 'price_opex', 'price_opex_min', 'price_opex_max', 'price_public_services_inc', 'price_public_services', 'public_services', 'price_public_services_min', 'price_public_services_max', 'price_floor_min', 'price_floor_max', 'price_floor_min_month', 'price_floor_max_month', 'price_min_month_all', 'price_max_month_all', 'price_floor_100_min', 'price_floor_100_max', 'price_mezzanine_min', 'price_mezzanine_max', 'price_office_min', 'price_office_max', 'price_sale_min', 'price_sale_max', 'price_safe_pallet_min', 'price_safe_pallet_max', 'price_safe_volume_min', 'price_safe_volume_max', 'price_safe_floor_min', 'price_safe_floor_max', 'price_safe_calc_min', 'price_safe_calc_max', 'price_safe_calc_month_min', 'price_safe_calc_month_max', 'price_sale_min_all', 'price_sale_max_all', 'temperature_min', 'temperature_max', 'prepay', 'floor_min', 'floor_max', 'self_leveling', 'heated', 'elevators_min', 'elevators_max', 'elevators_num', 'has_cranes', 'cranes_num', 'cranes_railway_num', 'cranes_gantry_num', 'cranes_overhead_num', 'cranes_cathead_num', 'telphers_min', 'telphers_max', 'telphers_num', 'railway', 'railway_value', 'power', 'power_value', 'steam', 'steam_value', 'gas', 'gas_value', 'phone', 'water_value', 'sewage_central', 'sewage_central_value', 'sewage_rain', 'firefighting', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'racks', 'warehouse_equipment', 'charging_room', 'cross_docking', 'cranes_runways', 'parking_car', 'parking_lorry', 'parking_truck', 'built_to_suit', 'built_to_suit_time', 'built_to_suit_plan', 'rent_business', 'rent_business_fill', 'rent_business_price', 'rent_business_long_contracts', 'rent_business_last_repair', 'rent_business_payback', 'rent_business_income', 'rent_business_profit', 'sale_company', 'holidays', 'ad_realtor', 'ad_cian', 'ad_cian_top3', 'ad_cian_premium', 'ad_cian_hl', 'ad_yandex', 'ad_yandex_raise', 'ad_yandex_promotion', 'ad_yandex_premium', 'ad_arendator', 'ad_free', 'ad_special', 'deleted', 'test_only', 'is_exclusive', 'deal_id', 'hide_from_market'], 'integer'],
            [['type_id', 'visual_id', 'deal_type_name', 'title', 'object_type', 'purposes', 'purposes_furl', 'object_type_name', 'agent_name', 'landscape_type', 'address', 'class', 'class_name', 'region', 'region_name', 'town', 'town_name', 'district', 'district_name', 'district_moscow', 'district_moscow_name', 'direction', 'direction_name', 'highway', 'highway_name', 'highway_moscow', 'highway_moscow_name', 'metro', 'metro_name', 'railway_station', 'blocks', 'photos', 'videos', 'thumbs', 'tax_form', 'safe_type', 'safe_type_furl', 'floor_type', 'floor_types', 'gates', 'gate_type', 'gate_num', 'column_grid', 'internet', 'heating', 'facing', 'ventilation', 'water', 'guard', 'firefighting_name', 'cadastral_number', 'cadastral_number_land', 'field_allow_usage', 'available_from', 'own_type', 'own_type_land', 'land_category', 'entry_territory', 'parking_car_value', 'parking_lorry_value', 'parking_truck_value', 'description'], 'safe'],
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
    public function normalizeProps()
    {
        $this->deal_type = OfferMix::normalizeDealType($this->deal_type);
        $this->type_id = $this->stringToArray($this->type_id);
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
        $query = OfferMix::find()->with(['object'])->andWhere(['deleted' => 0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'original_id' => $this->original_id,
            'type_id' => $this->type_id,
            'deal_type' => $this->deal_type,
            'status' => $this->status,
            'object_id' => $this->object_id,
            'complex_id' => $this->complex_id,
            'parent_id' => $this->parent_id,
            'company_id' => $this->company_id,
            'contact_id' => $this->contact_id,
            'year_built' => $this->year_built,
            'agent_id' => $this->agent_id,
            'agent_visited' => $this->agent_visited,
            'is_land' => $this->is_land,
            'land_width' => $this->land_width,
            'land_length' => $this->land_length,
            'land_use_restrictions' => $this->land_use_restrictions,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'from_mkad' => $this->from_mkad,
            'cian_region' => $this->cian_region,
            'outside_mkad' => $this->outside_mkad,
            'near_mo' => $this->near_mo,
            'from_metro_value' => $this->from_metro_value,
            'from_metro' => $this->from_metro,
            'from_station_value' => $this->from_station_value,
            'from_station' => $this->from_station,
            'blocks_amount' => $this->blocks_amount,
            'last_update' => $this->last_update,
            'commission_client' => $this->commission_client,
            'commission_owner' => $this->commission_owner,
            'deposit' => $this->deposit,
            'pledge' => $this->pledge,
            'area_building' => $this->area_building,
            'area_floor_full' => $this->area_floor_full,
            'area_mezzanine_full' => $this->area_mezzanine_full,
            'area_office_full' => $this->area_office_full,
            'area_min' => $this->area_min,
            'area_max' => $this->area_max,
            'area_floor_min' => $this->area_floor_min,
            'area_floor_max' => $this->area_floor_max,
            'area_mezzanine_min' => $this->area_mezzanine_min,
            'area_mezzanine_max' => $this->area_mezzanine_max,
            'area_mezzanine_add' => $this->area_mezzanine_add,
            'area_office_min' => $this->area_office_min,
            'area_office_max' => $this->area_office_max,
            'area_office_add' => $this->area_office_add,
            'area_tech_min' => $this->area_tech_min,
            'area_tech_max' => $this->area_tech_max,
            'area_field_min' => $this->area_field_min,
            'area_field_max' => $this->area_field_max,
            'pallet_place_min' => $this->pallet_place_min,
            'pallet_place_max' => $this->pallet_place_max,
            'cells_place_min' => $this->cells_place_min,
            'cells_place_max' => $this->cells_place_max,
            'inc_electricity' => $this->inc_electricity,
            'inc_heating' => $this->inc_heating,
            'inc_water' => $this->inc_water,
            'price_opex_inc' => $this->price_opex_inc,
            'price_opex' => $this->price_opex,
            'price_opex_min' => $this->price_opex_min,
            'price_opex_max' => $this->price_opex_max,
            'price_public_services_inc' => $this->price_public_services_inc,
            'price_public_services' => $this->price_public_services,
            'public_services' => $this->public_services,
            'price_public_services_min' => $this->price_public_services_min,
            'price_public_services_max' => $this->price_public_services_max,
            'price_floor_min' => $this->price_floor_min,
            'price_floor_max' => $this->price_floor_max,
            'price_floor_min_month' => $this->price_floor_min_month,
            'price_floor_max_month' => $this->price_floor_max_month,
            'price_min_month_all' => $this->price_min_month_all,
            'price_max_month_all' => $this->price_max_month_all,
            'price_floor_100_min' => $this->price_floor_100_min,
            'price_floor_100_max' => $this->price_floor_100_max,
            'price_mezzanine_min' => $this->price_mezzanine_min,
            'price_mezzanine_max' => $this->price_mezzanine_max,
            'price_office_min' => $this->price_office_min,
            'price_office_max' => $this->price_office_max,
            'price_sale_min' => $this->price_sale_min,
            'price_sale_max' => $this->price_sale_max,
            'price_safe_pallet_min' => $this->price_safe_pallet_min,
            'price_safe_pallet_max' => $this->price_safe_pallet_max,
            'price_safe_volume_min' => $this->price_safe_volume_min,
            'price_safe_volume_max' => $this->price_safe_volume_max,
            'price_safe_floor_min' => $this->price_safe_floor_min,
            'price_safe_floor_max' => $this->price_safe_floor_max,
            'price_safe_calc_min' => $this->price_safe_calc_min,
            'price_safe_calc_max' => $this->price_safe_calc_max,
            'price_safe_calc_month_min' => $this->price_safe_calc_month_min,
            'price_safe_calc_month_max' => $this->price_safe_calc_month_max,
            'price_sale_min_all' => $this->price_sale_min_all,
            'price_sale_max_all' => $this->price_sale_max_all,
            'ceiling_height_min' => $this->ceiling_height_min,
            'ceiling_height_max' => $this->ceiling_height_max,
            'temperature_min' => $this->temperature_min,
            'temperature_max' => $this->temperature_max,
            'load_floor_min' => $this->load_floor_min,
            'load_floor_max' => $this->load_floor_max,
            'load_mezzanine_min' => $this->load_mezzanine_min,
            'load_mezzanine_max' => $this->load_mezzanine_max,
            'prepay' => $this->prepay,
            'floor_min' => $this->floor_min,
            'floor_max' => $this->floor_max,
            'self_leveling' => $this->self_leveling,
            'heated' => $this->heated,
            'elevators_min' => $this->elevators_min,
            'elevators_max' => $this->elevators_max,
            'elevators_num' => $this->elevators_num,
            'has_cranes' => $this->has_cranes,
            'cranes_min' => $this->cranes_min,
            'cranes_max' => $this->cranes_max,
            'cranes_num' => $this->cranes_num,
            'cranes_railway_min' => $this->cranes_railway_min,
            'cranes_railway_max' => $this->cranes_railway_max,
            'cranes_railway_num' => $this->cranes_railway_num,
            'cranes_gantry_min' => $this->cranes_gantry_min,
            'cranes_gantry_max' => $this->cranes_gantry_max,
            'cranes_gantry_num' => $this->cranes_gantry_num,
            'cranes_overhead_min' => $this->cranes_overhead_min,
            'cranes_overhead_max' => $this->cranes_overhead_max,
            'cranes_overhead_num' => $this->cranes_overhead_num,
            'cranes_cathead_min' => $this->cranes_cathead_min,
            'cranes_cathead_max' => $this->cranes_cathead_max,
            'cranes_cathead_num' => $this->cranes_cathead_num,
            'telphers_min' => $this->telphers_min,
            'telphers_max' => $this->telphers_max,
            'telphers_num' => $this->telphers_num,
            'railway' => $this->railway,
            'railway_value' => $this->railway_value,
            'power' => $this->power,
            'power_value' => $this->power_value,
            'steam' => $this->steam,
            'steam_value' => $this->steam_value,
            'gas' => $this->gas,
            'gas_value' => $this->gas_value,
            'phone' => $this->phone,
            'water_value' => $this->water_value,
            'sewage_central' => $this->sewage_central,
            'sewage_central_value' => $this->sewage_central_value,
            'sewage_rain' => $this->sewage_rain,
            'firefighting' => $this->firefighting,
            'video_control' => $this->video_control,
            'access_control' => $this->access_control,
            'security_alert' => $this->security_alert,
            'fire_alert' => $this->fire_alert,
            'smoke_exhaust' => $this->smoke_exhaust,
            'canteen' => $this->canteen,
            'hostel' => $this->hostel,
            'racks' => $this->racks,
            'warehouse_equipment' => $this->warehouse_equipment,
            'charging_room' => $this->charging_room,
            'cross_docking' => $this->cross_docking,
            'cranes_runways' => $this->cranes_runways,
            'parking_car' => $this->parking_car,
            'parking_lorry' => $this->parking_lorry,
            'parking_truck' => $this->parking_truck,
            'built_to_suit' => $this->built_to_suit,
            'built_to_suit_time' => $this->built_to_suit_time,
            'built_to_suit_plan' => $this->built_to_suit_plan,
            'rent_business' => $this->rent_business,
            'rent_business_fill' => $this->rent_business_fill,
            'rent_business_price' => $this->rent_business_price,
            'rent_business_long_contracts' => $this->rent_business_long_contracts,
            'rent_business_last_repair' => $this->rent_business_last_repair,
            'rent_business_payback' => $this->rent_business_payback,
            'rent_business_income' => $this->rent_business_income,
            'rent_business_profit' => $this->rent_business_profit,
            'sale_company' => $this->sale_company,
            'holidays' => $this->holidays,
            'ad_realtor' => $this->ad_realtor,
            'ad_cian' => $this->ad_cian,
            'ad_cian_top3' => $this->ad_cian_top3,
            'ad_cian_premium' => $this->ad_cian_premium,
            'ad_cian_hl' => $this->ad_cian_hl,
            'ad_yandex' => $this->ad_yandex,
            'ad_yandex_raise' => $this->ad_yandex_raise,
            'ad_yandex_promotion' => $this->ad_yandex_promotion,
            'ad_yandex_premium' => $this->ad_yandex_premium,
            'ad_arendator' => $this->ad_arendator,
            'ad_free' => $this->ad_free,
            'ad_special' => $this->ad_special,
            'deleted' => $this->deleted,
            'test_only' => $this->test_only,
            'is_exclusive' => $this->is_exclusive,
            'deal_id' => $this->deal_id,
            'hide_from_market' => $this->hide_from_market,
        ]);

        $query->andFilterWhere(['like', 'visual_id', $this->visual_id])
            ->andFilterWhere(['like', 'deal_type_name', $this->deal_type_name])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'object_type', $this->object_type])
            ->andFilterWhere(['like', 'purposes', $this->purposes])
            ->andFilterWhere(['like', 'purposes_furl', $this->purposes_furl])
            ->andFilterWhere(['like', 'object_type_name', $this->object_type_name])
            ->andFilterWhere(['like', 'agent_name', $this->agent_name])
            ->andFilterWhere(['like', 'landscape_type', $this->landscape_type])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'class_name', $this->class_name])
            ->andFilterWhere(['like', 'region', $this->region])
            ->andFilterWhere(['like', 'region_name', $this->region_name])
            ->andFilterWhere(['like', 'town', $this->town])
            ->andFilterWhere(['like', 'town_name', $this->town_name])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'district_name', $this->district_name])
            ->andFilterWhere(['like', 'district_moscow', $this->district_moscow])
            ->andFilterWhere(['like', 'district_moscow_name', $this->district_moscow_name])
            ->andFilterWhere(['like', 'direction', $this->direction])
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
            ->andFilterWhere(['like', 'floor_types', $this->floor_types])
            ->andFilterWhere(['like', 'gates', $this->gates])
            ->andFilterWhere(['like', 'gate_type', $this->gate_type])
            ->andFilterWhere(['like', 'gate_num', $this->gate_num])
            ->andFilterWhere(['like', 'column_grid', $this->column_grid])
            ->andFilterWhere(['like', 'internet', $this->internet])
            ->andFilterWhere(['like', 'heating', $this->heating])
            ->andFilterWhere(['like', 'facing', $this->facing])
            ->andFilterWhere(['like', 'ventilation', $this->ventilation])
            ->andFilterWhere(['like', 'water', $this->water])
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
            ->andFilterWhere(['<=', new Expression('CASE WHEN ceiling_height_min > ceiling_height_max THEN ceiling_height_min ELSE ceiling_height_max END'), $this->rangeMaxCeilingHeight]) // area_warehouse_max < maxArea
            ->andFilterWhere(['>=', 'ceiling_height_min', $this->rangeMinCeilingHeight])
            ->andFilterWhere(['<=', 'from_mkad', $this->approximateDistanceFromMKAD])
            ->andFilterWhere(['>=', 'power_value', $this->minElectricity]);
        if ($this->deal_type == self::DEAL_TYPE_RENT || $this->deal_type == self::DEAL_TYPE_SUBLEASE) {
            $query->andFilterWhere(['<=', 'GREATEST(price_mezzanine_min, price_mezzanine_max, price_floor_min, price_floor_max )', $this->pricePerFloor]);
        } elseif ($this->deal_type == self::DEAL_TYPE_SALE) {
            $query->andFilterWhere(['<=', 'price_sale_max', $this->pricePerFloor]);
        }

        return $dataProvider;
    }
}
