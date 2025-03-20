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

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['rangeMinArea', 'rangeMaxArea', 'id', 'location_id', 'last_update', 'is_land', 'buildings_on_territory', 'first_line', 'complex_id', 'contact_id', 'company_id', 'author_id', 'type', 'status_id', 'status_rent', 'status_sale', 'status_safe', 'status_subrent', 'onsite_noprice', 'floor_type', 'firefighting_type', 'object_class', 'region', 'district', 'direction', 'village', 'highway', 'highway_secondary', 'from_mkad', 'metro', 'area_field_full', 'area_building', 'area_floor_full', 'area_office_full', 'area_tech_full', 'land', 'land_length', 'land_width', 'barrier', 'fence_around_perimeter', 'finishing', 'l_category', 'l_function', 'l_property', 'floors', 'deposit', 'pledge', 'prepay_subrent', 'facing_type', 'cranes_runways', 'railway', 'railway_value', 'nooffice', 'phone_line', 'year_build', 'year_repair', 'guard', 'entry_territory', 'entry_territory_type', 'gas', 'ttk_mkad', 'parking_car', 'parking_car_value', 'parking_lorry', 'parking_lorry_value', 'parking_truck', 'parking_truck_value', 'steam', 'deposit_former', 'is_prepay', 'agent_visited', 'agent_visited_sale', 'agent_visited_safe', 'agent_visited_subrent', 'gas_value', 'steam_value', 'water', 'water_value', 'sewage', 'sewage_central', 'sewage_central_value', 'sewage_rain', 'heating', 'heating_central', 'ventilation', 'internet', 'sale_price', 'sale_price_metr', 'rent_price', 'subrent_price', 'rent_price_safe', 'office_price', 'price_mezzanine', 'tax_form', 'result', 'result_sale', 'result_safe', 'result_subrent', 'result_who', 'agent_id', 'agent_sale', 'agent_safe', 'agent_subrent', 'onsite', 'contract', 'onsite_top', 'electricity_included', 'deleted', 'openstage', 'bargain_rent', 'bargain_sale', 'bargain_office', 'bargain_safe', 'from_metro', 'from_metro_value', 'railway_station', 'from_station', 'from_station_value', 'from_busstop', 'from_busstop_value', 'entrance_type', 'plain_type', 'safe_price_rack', 'safe_price_rack_oversized', 'safe_price_cell', 'safe_price_floor_oversized', 'publ_time', 'activity', 'order_row', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'smoke_exhaust', 'canteen', 'hostel', 'street_area', 'own_type', 'fence', 'land_category', 'status', 'status_reason', 'own_type_land', 'area_outside', 'description_complex', 'description_manual_use', 'gas_near', 'mkad_ttk_between', 'empty_line', 'title_empty_main', 'title_empty_communications', 'title_empty_security', 'title_empty_railway', 'title_empty_infrastructure', 'landscape_type', 'land_use_restrictions', 'documents_old', 'test_only'], 'integer'],
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
		$query = Objects::find()->distinct()->joinWith(['offerMix'])->with(['deals.consultant.userProfile', 'deals.company', 'deals.competitor', 'blocks', 'objectFloors', 'complex.location.highwayRel'])->with(['offerMix' => function ($query) {
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

		// grid filtering conditions
		$query->andFilterWhere([
			'c_industry.id'                      => $this->id,
			'location_id'                        => $this->location_id,
			'last_update'                        => $this->last_update,
			'is_land'                            => $this->is_land,
			'buildings_on_territory'             => $this->buildings_on_territory,
			'first_line'                         => $this->first_line,
			'complex_id'                         => $this->complex_id,
			'contact_id'                         => $this->contact_id,
			'c_industry.company_id'              => $this->company_id,
			'author_id'                          => $this->author_id,
			'type'                               => $this->type,
			'status_id'                          => $this->status_id,
			'status_rent'                        => $this->status_rent,
			'status_sale'                        => $this->status_sale,
			'status_safe'                        => $this->status_safe,
			'status_subrent'                     => $this->status_subrent,
			'onsite_noprice'                     => $this->onsite_noprice,
			'floor_type'                         => $this->floor_type,
			'firefighting_type'                  => $this->firefighting_type,
			'object_class'                       => $this->object_class,
			'region'                             => $this->region,
			'district'                           => $this->district,
			'direction'                          => $this->direction,
			'village'                            => $this->village,
			'highway'                            => $this->highway,
			'highway_secondary'                  => $this->highway_secondary,
			'from_mkad'                          => $this->from_mkad,
			'metro'                              => $this->metro,
			'area_field_full'                    => $this->area_field_full,
			'area_building'                      => $this->area_building,
			'area_floor_full'                    => $this->area_floor_full,
			'area_office_full'                   => $this->area_office_full,
			'area_tech_full'                     => $this->area_tech_full,
			'land'                               => $this->land,
			'land_length'                        => $this->land_length,
			'land_width'                         => $this->land_width,
			'barrier'                            => $this->barrier,
			'fence_around_perimeter'             => $this->fence_around_perimeter,
			'finishing'                          => $this->finishing,
			'l_category'                         => $this->l_category,
			'l_function'                         => $this->l_function,
			'l_property'                         => $this->l_property,
			'floors'                             => $this->floors,
			'deposit'                            => $this->deposit,
			'pledge'                             => $this->pledge,
			'prepay_subrent'                     => $this->prepay_subrent,
			'facing_type'                        => $this->facing_type,
			'cranes_runways'                     => $this->cranes_runways,
			'railway'                            => $this->railway,
			'railway_value'                      => $this->railway_value,
			'nooffice'                           => $this->nooffice,
			'phone_line'                         => $this->phone_line,
			'year_build'                         => $this->year_build,
			'year_repair'                        => $this->year_repair,
			'guard'                              => $this->guard,
			'entry_territory'                    => $this->entry_territory,
			'entry_territory_type'               => $this->entry_territory_type,
			'gas'                                => $this->gas,
			'ttk_mkad'                           => $this->ttk_mkad,
			'parking_car'                        => $this->parking_car,
			'parking_car_value'                  => $this->parking_car_value,
			'parking_lorry'                      => $this->parking_lorry,
			'parking_lorry_value'                => $this->parking_lorry_value,
			'parking_truck'                      => $this->parking_truck,
			'parking_truck_value'                => $this->parking_truck_value,
			'steam'                              => $this->steam,
			'deposit_former'                     => $this->deposit_former,
			'is_prepay'                          => $this->is_prepay,
			'agent_visited'                      => $this->agent_visited,
			'agent_visited_sale'                 => $this->agent_visited_sale,
			'agent_visited_safe'                 => $this->agent_visited_safe,
			'agent_visited_subrent'              => $this->agent_visited_subrent,
			'power'                              => $this->power,
			'power_all'                          => $this->power_all,
			'power_available'                    => $this->power_available,
			'gas_value'                          => $this->gas_value,
			'steam_value'                        => $this->steam_value,
			'water'                              => $this->water,
			'water_value'                        => $this->water_value,
			'sewage'                             => $this->sewage,
			'sewage_central'                     => $this->sewage_central,
			'sewage_central_value'               => $this->sewage_central_value,
			'sewage_rain'                        => $this->sewage_rain,
			'heating'                            => $this->heating,
			'heating_central'                    => $this->heating_central,
			'ventilation'                        => $this->ventilation,
			'internet'                           => $this->internet,
			'sale_price'                         => $this->sale_price,
			'sale_price_metr'                    => $this->sale_price_metr,
			'rent_price'                         => $this->rent_price,
			'subrent_price'                      => $this->subrent_price,
			'rent_price_safe'                    => $this->rent_price_safe,
			'office_price'                       => $this->office_price,
			'price_mezzanine'                    => $this->price_mezzanine,
			'tax_form'                           => $this->tax_form,
			'result'                             => $this->result,
			'result_sale'                        => $this->result_sale,
			'result_safe'                        => $this->result_safe,
			'result_subrent'                     => $this->result_subrent,
			'result_who'                         => $this->result_who,
			'longitude'                          => $this->longitude,
			'latitude'                           => $this->latitude,
			'agent_id'                           => $this->agent_id,
			'agent_sale'                         => $this->agent_sale,
			'agent_safe'                         => $this->agent_safe,
			'agent_subrent'                      => $this->agent_subrent,
			'onsite'                             => $this->onsite,
			'contract'                           => $this->contract,
			'onsite_top'                         => $this->onsite_top,
			'electricity_included'               => $this->electricity_included,
			'deleted'                            => $this->deleted,
			'openstage'                          => $this->openstage,
			'owner_pays_howmuch'                 => $this->owner_pays_howmuch,
			'owner_pays_howmuch_sale'            => $this->owner_pays_howmuch_sale,
			'owner_pays_howmuch_safe'            => $this->owner_pays_howmuch_safe,
			'owner_pays_howmuch_subrent'         => $this->owner_pays_howmuch_subrent,
			'owner_pays_howmuch_4client'         => $this->owner_pays_howmuch_4client,
			'owner_pays_howmuch_4client_sale'    => $this->owner_pays_howmuch_4client_sale,
			'owner_pays_howmuch_4client_safe'    => $this->owner_pays_howmuch_4client_safe,
			'owner_pays_howmuch_4client_subrent' => $this->owner_pays_howmuch_4client_subrent,
			'contract_date'                      => $this->contract_date,
			'bargain_rent'                       => $this->bargain_rent,
			'bargain_sale'                       => $this->bargain_sale,
			'bargain_office'                     => $this->bargain_office,
			'bargain_safe'                       => $this->bargain_safe,
			'from_metro'                         => $this->from_metro,
			'from_metro_value'                   => $this->from_metro_value,
			'railway_station'                    => $this->railway_station,
			'from_station'                       => $this->from_station,
			'from_station_value'                 => $this->from_station_value,
			'from_busstop'                       => $this->from_busstop,
			'from_busstop_value'                 => $this->from_busstop_value,
			'entrance_type'                      => $this->entrance_type,
			'plain_type'                         => $this->plain_type,
			'safe_price_rack'                    => $this->safe_price_rack,
			'safe_price_rack_oversized'          => $this->safe_price_rack_oversized,
			'safe_price_cell'                    => $this->safe_price_cell,
			'safe_price_floor_oversized'         => $this->safe_price_floor_oversized,
			'publ_time'                          => $this->publ_time,
			'activity'                           => $this->activity,
			'order_row'                          => $this->order_row,
			'video_control'                      => $this->video_control,
			'access_control'                     => $this->access_control,
			'security_alert'                     => $this->security_alert,
			'fire_alert'                         => $this->fire_alert,
			'smoke_exhaust'                      => $this->smoke_exhaust,
			'canteen'                            => $this->canteen,
			'hostel'                             => $this->hostel,
			'street_area'                        => $this->street_area,
			'own_type'                           => $this->own_type,
			'fence'                              => $this->fence,
			'land_category'                      => $this->land_category,
			'status'                             => $this->status,
			'status_reason'                      => $this->status_reason,
			'own_type_land'                      => $this->own_type_land,
			'area_outside'                       => $this->area_outside,
			'description_complex'                => $this->description_complex,
			'description_manual_use'             => $this->description_manual_use,
			'gas_near'                           => $this->gas_near,
			'mkad_ttk_between'                   => $this->mkad_ttk_between,
			'empty_line'                         => $this->empty_line,
			'title_empty_main'                   => $this->title_empty_main,
			'title_empty_communications'         => $this->title_empty_communications,
			'title_empty_security'               => $this->title_empty_security,
			'title_empty_railway'                => $this->title_empty_railway,
			'title_empty_infrastructure'         => $this->title_empty_infrastructure,
			'landscape_type'                     => $this->landscape_type,
			'land_use_restrictions'              => $this->land_use_restrictions,
			'documents_old'                      => $this->documents_old,
			'test_only'                          => $this->test_only,
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

		if (!empty($this->search)) {
			$query->andFilterWhere([
				'or',
				['like', Objects::field('id'), $this->search],
				['like', Objects::field('company_id'), $this->search],
				['like', Objects::field('complex_id'), $this->search],
				['like', Objects::field('address'), $this->search]
			]);
		}

		return $dataProvider;
	}
}
