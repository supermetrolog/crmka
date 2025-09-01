<?php

namespace app\models\oldDb;

use app\models\Objects;
use yii\base\ErrorException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ObjectsSearch represents the model behind the search form of `app\models\oldDb\Objects`.
 */
class ObjectsSearch extends Objects
{
	public $rangeMinArea;
	public $rangeMaxArea;
	public $search;
	public $offer_company_id;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['rangeMinArea', 'rangeMaxArea', 'id', 'location_id', 'last_update', 'is_land', 'buildings_on_territory', 'first_line', 'complex_id', 'contact_id', 'company_id', 'offer_company_id', 'author_id', 'type', 'status_id', 'status_rent', 'status_sale', 'status_safe', 'status_subrent', 'onsite_noprice', 'floor_type', 'firefighting_type', 'object_class', 'region', 'district', 'direction', 'village', 'highway', 'highway_secondary', 'from_mkad', 'metro', 'area_field_full', 'area_building', 'area_floor_full', 'area_office_full', 'area_tech_full', 'land', 'land_length', 'land_width', 'barrier', 'fence_around_perimeter', 'finishing', 'l_category', 'l_function', 'l_property', 'floors', 'deposit', 'pledge', 'prepay_subrent', 'facing_type', 'cranes_runways', 'railway', 'railway_value', 'nooffice', 'phone_line', 'year_build', 'year_repair', 'guard', 'entry_territory', 'entry_territory_type', 'gas', 'ttk_mkad', 'parking_car', 'parking_car_value', 'parking_lorry', 'parking_lorry_value', 'parking_truck', 'parking_truck_value', 'steam', 'deposit_former', 'is_prepay', 'agent_visited', 'agent_visited_sale', 'agent_visited_safe', 'agent_visited_subrent', 'gas_value', 'steam_value', 'water', 'water_value', 'sewage', 'sewage_central', 'sewage_central_value', 'sewage_rain', 'heating', 'heating_central', 'ventilation', 'internet', 'sale_price', 'sale_price_metr', 'rent_price', 'subrent_price', 'rent_price_safe', 'office_price', 'price_mezzanine', 'tax_form', 'result', 'result_sale', 'result_safe', 'result_subrent', 'result_who', 'agent_id', 'agent_sale', 'agent_safe', 'agent_subrent', 'onsite', 'contract', 'onsite_top', 'electricity_included', 'deleted', 'openstage', 'bargain_rent', 'bargain_sale', 'bargain_office', 'bargain_safe', 'from_metro', 'from_metro_value', 'railway_station', 'from_station', 'from_station_value', 'from_busstop', 'from_busstop_value', 'entrance_type', 'plain_type', 'safe_price_rack', 'safe_price_rack_oversized', 'safe_price_cell', 'safe_price_floor_oversized', 'publ_time', 'activity', 'order_row', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'street_area', 'own_type', 'fence', 'land_category', 'status', 'status_reason', 'own_type_land', 'area_outside', 'description_complex', 'description_manual_use', 'gas_near', 'mkad_ttk_between', 'empty_line', 'title_empty_main', 'title_empty_communications', 'title_empty_security', 'title_empty_railway', 'title_empty_infrastructure', 'landscape_type', 'land_use_restrictions', 'documents_old', 'test_only'], 'integer'],
			[['title', 'buildings_on_territory_id', 'buildings_on_territory_description', 'owners', 'object_type', 'floors_building', 'object_type2', 'purpose_warehouse', 'purposes', 'address', 'cadastral_number', 'yandex_address', 'dsection', 'elevators', 'cranes_gantry', 'cranes_railway', 'telecommunications', 'parking_car_type', 'parking_lorry_type', 'parking_truck_type', 'comments', 'description', 'description_auto', 'infrastructure', 'parking', 'internet_type', 'safety_systems', 'deal_type_help', 'rent_inc', 'rent_inc_safe', 'rent_inc_office', 'inc_services', 'incs_currency', 'slcomments', '_calc_rent_payinc', '_calc_safe_payinc', '_calc_sale_payinc', '_calc_subrent_payinc', 'contract_date', 'area_mezzanine_full', 'photo', 'videos', 'building_layouts', 'building_presentations', 'building_contracts', 'building_property_documents', 'photos_360', 'import_sale_cian', 'import_sale_free', 'import_sale_yandex', 'import_rent_cian', 'import_rent_free', 'import_rent_yandex', 'import_sale_cian_premium', 'import_rent_cian_premium', 'import_sale_cian_top3', 'import_rent_cian_top3', 'import_sale_cian_hl', 'import_rent_cian_hl', 'field_allow_usage', 'status_description', 'cadastral_number_land', 'search'], 'safe'],
			[['power', 'power_all', 'power_available', 'longitude', 'latitude', 'owner_pays_howmuch', 'owner_pays_howmuch_sale', 'owner_pays_howmuch_safe', 'owner_pays_howmuch_subrent', 'owner_pays_howmuch_4client', 'owner_pays_howmuch_4client_sale', 'owner_pays_howmuch_4client_safe', 'owner_pays_howmuch_4client_subrent'], 'number'],
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

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 */
	public function search($params)
	{
		$query = Objects::find()
		                ->distinct()
		                ->joinWith(['offerMix'])
		                ->with(['deals.consultant.userProfile', 'deals.company', 'deals.competitor', 'blocks', 'objectFloors', 'complex.location.highwayRel'])
		                ->with(['offerMix' => function ($query) {
			                $query->with(['generalOffersMix']);

			                return $query->where(['c_industry_offers_mix.deleted' => 0, 'c_industry_offers_mix.type_id' => 2]);
		                }]);

		$this->load($params);

		$dataProvider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				'defaultPageSize' => 10,
				'pageSizeLimit'   => [0, 30],
			],
			'sort'       => [
				'enableMultiSort' => true,
				'defaultOrder'    => [
					'default' => SORT_DESC
				],
				'attributes'      => [
					'default' => [
						'asc'     => [
							'c_industry_offers_mix.type_id'     => SORT_ASC,
							'c_industry_offers_mix.last_update' => SORT_ASC,
						],
						'desc'    => [
							'c_industry_offers_mix.type_id'     => SORT_DESC,
							'c_industry_offers_mix.last_update' => SORT_DESC,
						],
						'default' => SORT_DESC,
					],
				],
			]
		]);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		if (!empty($this->search)) {
			$query->andFilterWhere([
				'or',
				['like', Objects::field('id'), $this->search],
				['like', Objects::field('company_id'), $this->search],
				['like', Objects::field('complex_id'), $this->search],
				['like', Objects::field('address'), $this->search]
			]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			Objects::field('id')                                 => $this->id,
			Objects::field('location_id')                        => $this->location_id,
			Objects::field('last_update')                        => $this->last_update,
			Objects::field('is_land')                            => $this->is_land,
			Objects::field('buildings_on_territory')             => $this->buildings_on_territory,
			Objects::field('first_line')                         => $this->first_line,
			Objects::field('complex_id')                         => $this->complex_id,
			Objects::field('contact_id')                         => $this->contact_id,
			Objects::field('company_id')                         => $this->company_id,
			Objects::field('author_id')                          => $this->author_id,
			Objects::field('type')                               => $this->type,
			Objects::field('status_id')                          => $this->status_id,
			Objects::field('status_rent')                        => $this->status_rent,
			Objects::field('status_sale')                        => $this->status_sale,
			Objects::field('status_safe')                        => $this->status_safe,
			Objects::field('status_subrent')                     => $this->status_subrent,
			Objects::field('onsite_noprice')                     => $this->onsite_noprice,
			Objects::field('floor_type')                         => $this->floor_type,
			Objects::field('firefighting_type')                  => $this->firefighting_type,
			Objects::field('object_class')                       => $this->object_class,
			Objects::field('region')                             => $this->region,
			Objects::field('district')                           => $this->district,
			Objects::field('direction')                          => $this->direction,
			Objects::field('village')                            => $this->village,
			Objects::field('highway')                            => $this->highway,
			Objects::field('highway_secondary')                  => $this->highway_secondary,
			Objects::field('from_mkad')                          => $this->from_mkad,
			Objects::field('metro')                              => $this->metro,
			Objects::field('area_field_full')                    => $this->area_field_full,
			Objects::field('area_building')                      => $this->area_building,
			Objects::field('area_floor_full')                    => $this->area_floor_full,
			Objects::field('area_office_full')                   => $this->area_office_full,
			Objects::field('area_tech_full')                     => $this->area_tech_full,
			Objects::field('land')                               => $this->land,
			Objects::field('land_length')                        => $this->land_length,
			Objects::field('land_width')                         => $this->land_width,
			Objects::field('barrier')                            => $this->barrier,
			Objects::field('fence_around_perimeter')             => $this->fence_around_perimeter,
			Objects::field('finishing')                          => $this->finishing,
			Objects::field('l_category')                         => $this->l_category,
			Objects::field('l_function')                         => $this->l_function,
			Objects::field('l_property')                         => $this->l_property,
			Objects::field('floors')                             => $this->floors,
			Objects::field('deposit')                            => $this->deposit,
			Objects::field('pledge')                             => $this->pledge,
			Objects::field('prepay_subrent')                     => $this->prepay_subrent,
			Objects::field('facing_type')                        => $this->facing_type,
			Objects::field('cranes_runways')                     => $this->cranes_runways,
			Objects::field('railway')                            => $this->railway,
			Objects::field('railway_value')                      => $this->railway_value,
			Objects::field('nooffice')                           => $this->nooffice,
			Objects::field('phone_line')                         => $this->phone_line,
			Objects::field('year_build')                         => $this->year_build,
			Objects::field('year_repair')                        => $this->year_repair,
			Objects::field('guard')                              => $this->guard,
			Objects::field('entry_territory')                    => $this->entry_territory,
			Objects::field('entry_territory_type')               => $this->entry_territory_type,
			Objects::field('gas')                                => $this->gas,
			Objects::field('ttk_mkad')                           => $this->ttk_mkad,
			Objects::field('parking_car')                        => $this->parking_car,
			Objects::field('parking_car_value')                  => $this->parking_car_value,
			Objects::field('parking_lorry')                      => $this->parking_lorry,
			Objects::field('parking_lorry_value')                => $this->parking_lorry_value,
			Objects::field('parking_truck')                      => $this->parking_truck,
			Objects::field('parking_truck_value')                => $this->parking_truck_value,
			Objects::field('steam')                              => $this->steam,
			Objects::field('deposit_former')                     => $this->deposit_former,
			Objects::field('is_prepay')                          => $this->is_prepay,
			Objects::field('agent_visited')                      => $this->agent_visited,
			Objects::field('agent_visited_sale')                 => $this->agent_visited_sale,
			Objects::field('agent_visited_safe')                 => $this->agent_visited_safe,
			Objects::field('agent_visited_subrent')              => $this->agent_visited_subrent,
			Objects::field('power')                              => $this->power,
			Objects::field('power_all')                          => $this->power_all,
			Objects::field('power_available')                    => $this->power_available,
			Objects::field('gas_value')                          => $this->gas_value,
			Objects::field('steam_value')                        => $this->steam_value,
			Objects::field('water')                              => $this->water,
			Objects::field('water_value')                        => $this->water_value,
			Objects::field('sewage')                             => $this->sewage,
			Objects::field('sewage_central')                     => $this->sewage_central,
			Objects::field('sewage_central_value')               => $this->sewage_central_value,
			Objects::field('sewage_rain')                        => $this->sewage_rain,
			Objects::field('heating')                            => $this->heating,
			Objects::field('heating_central')                    => $this->heating_central,
			Objects::field('ventilation')                        => $this->ventilation,
			Objects::field('internet')                           => $this->internet,
			Objects::field('sale_price')                         => $this->sale_price,
			Objects::field('sale_price_metr')                    => $this->sale_price_metr,
			Objects::field('rent_price')                         => $this->rent_price,
			Objects::field('subrent_price')                      => $this->subrent_price,
			Objects::field('rent_price_safe')                    => $this->rent_price_safe,
			Objects::field('office_price')                       => $this->office_price,
			Objects::field('price_mezzanine')                    => $this->price_mezzanine,
			Objects::field('tax_form')                           => $this->tax_form,
			Objects::field('result')                             => $this->result,
			Objects::field('result_sale')                        => $this->result_sale,
			Objects::field('result_safe')                        => $this->result_safe,
			Objects::field('result_subrent')                     => $this->result_subrent,
			Objects::field('result_who')                         => $this->result_who,
			Objects::field('longitude')                          => $this->longitude,
			Objects::field('latitude')                           => $this->latitude,
			Objects::field('agent_id')                           => $this->agent_id,
			Objects::field('agent_sale')                         => $this->agent_sale,
			Objects::field('agent_safe')                         => $this->agent_safe,
			Objects::field('agent_subrent')                      => $this->agent_subrent,
			Objects::field('onsite')                             => $this->onsite,
			Objects::field('contract')                           => $this->contract,
			Objects::field('onsite_top')                         => $this->onsite_top,
			Objects::field('electricity_included')               => $this->electricity_included,
			Objects::field('deleted')                            => $this->deleted,
			Objects::field('openstage')                          => $this->openstage,
			Objects::field('owner_pays_howmuch')                 => $this->owner_pays_howmuch,
			Objects::field('owner_pays_howmuch_sale')            => $this->owner_pays_howmuch_sale,
			Objects::field('owner_pays_howmuch_safe')            => $this->owner_pays_howmuch_safe,
			Objects::field('owner_pays_howmuch_subrent')         => $this->owner_pays_howmuch_subrent,
			Objects::field('owner_pays_howmuch_4client')         => $this->owner_pays_howmuch_4client,
			Objects::field('owner_pays_howmuch_4client_sale')    => $this->owner_pays_howmuch_4client_sale,
			Objects::field('owner_pays_howmuch_4client_safe')    => $this->owner_pays_howmuch_4client_safe,
			Objects::field('owner_pays_howmuch_4client_subrent') => $this->owner_pays_howmuch_4client_subrent,
			Objects::field('contract_date')                      => $this->contract_date,
			Objects::field('bargain_rent')                       => $this->bargain_rent,
			Objects::field('bargain_sale')                       => $this->bargain_sale,
			Objects::field('bargain_office')                     => $this->bargain_office,
			Objects::field('bargain_safe')                       => $this->bargain_safe,
			Objects::field('from_metro')                         => $this->from_metro,
			Objects::field('from_metro_value')                   => $this->from_metro_value,
			Objects::field('railway_station')                    => $this->railway_station,
			Objects::field('from_station')                       => $this->from_station,
			Objects::field('from_station_value')                 => $this->from_station_value,
			Objects::field('from_busstop')                       => $this->from_busstop,
			Objects::field('from_busstop_value')                 => $this->from_busstop_value,
			Objects::field('entrance_type')                      => $this->entrance_type,
			Objects::field('plain_type')                         => $this->plain_type,
			Objects::field('safe_price_rack')                    => $this->safe_price_rack,
			Objects::field('safe_price_rack_oversized')          => $this->safe_price_rack_oversized,
			Objects::field('safe_price_cell')                    => $this->safe_price_cell,
			Objects::field('safe_price_floor_oversized')         => $this->safe_price_floor_oversized,
			Objects::field('publ_time')                          => $this->publ_time,
			Objects::field('activity')                           => $this->activity,
			Objects::field('order_row')                          => $this->order_row,
			Objects::field('video_control')                      => $this->video_control,
			Objects::field('access_control')                     => $this->access_control,
			Objects::field('security_alert')                     => $this->security_alert,
			Objects::field('fire_alert')                         => $this->fire_alert,
			Objects::field('smoke_exhaust')                      => $this->smoke_exhaust,
			Objects::field('canteen')                            => $this->canteen,
			Objects::field('hostel')                             => $this->hostel,
			Objects::field('street_area')                        => $this->street_area,
			Objects::field('own_type')                           => $this->own_type,
			Objects::field('fence')                              => $this->fence,
			Objects::field('land_category')                      => $this->land_category,
			Objects::field('status')                             => $this->status,
			Objects::field('status_reason')                      => $this->status_reason,
			Objects::field('own_type_land')                      => $this->own_type_land,
			Objects::field('area_outside')                       => $this->area_outside,
			Objects::field('description_complex')                => $this->description_complex,
			Objects::field('description_manual_use')             => $this->description_manual_use,
			Objects::field('gas_near')                           => $this->gas_near,
			Objects::field('mkad_ttk_between')                   => $this->mkad_ttk_between,
			Objects::field('empty_line')                         => $this->empty_line,
			Objects::field('title_empty_main')                   => $this->title_empty_main,
			Objects::field('title_empty_communications')         => $this->title_empty_communications,
			Objects::field('title_empty_security')               => $this->title_empty_security,
			Objects::field('title_empty_railway')                => $this->title_empty_railway,
			Objects::field('title_empty_infrastructure')         => $this->title_empty_infrastructure,
			Objects::field('landscape_type')                     => $this->landscape_type,
			Objects::field('land_use_restrictions')              => $this->land_use_restrictions,
			Objects::field('documents_old')                      => $this->documents_old,
			Objects::field('test_only')                          => $this->test_only,
			'c_industry_offers_mix.company_id'                   => $this->offer_company_id
		]);

		$query->andFilterWhere(['like', 'title', $this->title])
		      ->andFilterWhere(['like', 'buildings_on_territory_id', $this->buildings_on_territory_id])
		      ->andFilterWhere(['like', 'buildings_on_territory_description', $this->buildings_on_territory_description])
		      ->andFilterWhere(['like', 'owners', $this->owners])
		      ->andFilterWhere(['like', 'object_type', $this->object_type])
		      ->andFilterWhere(['like', 'floors_building', $this->floors_building])
		      ->andFilterWhere(['like', 'object_type2', $this->object_type2])
		      ->andFilterWhere(['like', 'purpose_warehouse', $this->purpose_warehouse])
		      ->andFilterWhere(['like', 'purposes', $this->purposes])
		      ->andFilterWhere(['like', 'address', $this->address])
		      ->andFilterWhere(['like', 'cadastral_number', $this->cadastral_number])
		      ->andFilterWhere(['like', 'yandex_address', $this->yandex_address])
		      ->andFilterWhere(['like', 'dsection', $this->dsection])
		      ->andFilterWhere(['like', 'elevators', $this->elevators])
		      ->andFilterWhere(['like', 'cranes_gantry', $this->cranes_gantry])
		      ->andFilterWhere(['like', 'cranes_railway', $this->cranes_railway])
		      ->andFilterWhere(['like', 'telecommunications', $this->telecommunications])
		      ->andFilterWhere(['like', 'parking_car_type', $this->parking_car_type])
		      ->andFilterWhere(['like', 'parking_lorry_type', $this->parking_lorry_type])
		      ->andFilterWhere(['like', 'parking_truck_type', $this->parking_truck_type])
		      ->andFilterWhere(['like', 'comments', $this->comments])
		      ->andFilterWhere(['like', 'description', $this->description])
		      ->andFilterWhere(['like', 'description_auto', $this->description_auto])
		      ->andFilterWhere(['like', 'infrastructure', $this->infrastructure])
		      ->andFilterWhere(['like', 'parking', $this->parking])
		      ->andFilterWhere(['like', 'internet_type', $this->internet_type])
		      ->andFilterWhere(['like', 'safety_systems', $this->safety_systems])
		      ->andFilterWhere(['like', 'deal_type_help', $this->deal_type_help])
		      ->andFilterWhere(['like', 'rent_inc', $this->rent_inc])
		      ->andFilterWhere(['like', 'rent_inc_safe', $this->rent_inc_safe])
		      ->andFilterWhere(['like', 'rent_inc_office', $this->rent_inc_office])
		      ->andFilterWhere(['like', 'inc_services', $this->inc_services])
		      ->andFilterWhere(['like', 'incs_currency', $this->incs_currency])
		      ->andFilterWhere(['like', 'slcomments', $this->slcomments])
		      ->andFilterWhere(['like', '_calc_rent_payinc', $this->_calc_rent_payinc])
		      ->andFilterWhere(['like', '_calc_safe_payinc', $this->_calc_safe_payinc])
		      ->andFilterWhere(['like', '_calc_sale_payinc', $this->_calc_sale_payinc])
		      ->andFilterWhere(['like', '_calc_subrent_payinc', $this->_calc_subrent_payinc])
		      ->andFilterWhere(['like', 'area_mezzanine_full', $this->area_mezzanine_full])
		      ->andFilterWhere(['like', 'photo', $this->photo])
		      ->andFilterWhere(['like', 'videos', $this->videos])
		      ->andFilterWhere(['like', 'building_layouts', $this->building_layouts])
		      ->andFilterWhere(['like', 'building_presentations', $this->building_presentations])
		      ->andFilterWhere(['like', 'building_contracts', $this->building_contracts])
		      ->andFilterWhere(['like', 'building_property_documents', $this->building_property_documents])
		      ->andFilterWhere(['like', 'photos_360', $this->photos_360])
		      ->andFilterWhere(['like', 'import_sale_cian', $this->import_sale_cian])
		      ->andFilterWhere(['like', 'import_sale_free', $this->import_sale_free])
		      ->andFilterWhere(['like', 'import_sale_yandex', $this->import_sale_yandex])
		      ->andFilterWhere(['like', 'import_rent_cian', $this->import_rent_cian])
		      ->andFilterWhere(['like', 'import_rent_free', $this->import_rent_free])
		      ->andFilterWhere(['like', 'import_rent_yandex', $this->import_rent_yandex])
		      ->andFilterWhere(['like', 'import_sale_cian_premium', $this->import_sale_cian_premium])
		      ->andFilterWhere(['like', 'import_rent_cian_premium', $this->import_rent_cian_premium])
		      ->andFilterWhere(['like', 'import_sale_cian_top3', $this->import_sale_cian_top3])
		      ->andFilterWhere(['like', 'import_rent_cian_top3', $this->import_rent_cian_top3])
		      ->andFilterWhere(['like', 'import_sale_cian_hl', $this->import_sale_cian_hl])
		      ->andFilterWhere(['like', 'import_rent_cian_hl', $this->import_rent_cian_hl])
		      ->andFilterWhere(['like', 'field_allow_usage', $this->field_allow_usage])
		      ->andFilterWhere(['like', 'status_description', $this->status_description])
		      ->andFilterWhere(['like', 'cadastral_number_land', $this->cadastral_number_land])
		      ->andFilterWhere(['<=', 'c_industry_offers_mix.area_max', $this->rangeMaxArea])
		      ->andFilterWhere(['>=', 'c_industry_offers_mix.area_max', $this->rangeMinArea]);

		return $dataProvider;
	}
}
