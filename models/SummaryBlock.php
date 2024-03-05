<?php

declare(strict_types=1);

namespace app\models;

use app\helpers\ArrayHelper;
use app\helpers\JsonFieldNormalizer;
use yii\db\ActiveQuery;

class SummaryBlock extends Block
{
	public bool $racks_exists               = false;
	public bool $charging_room_exists       = false;
	public bool $warehouse_equipment_exists = false;
	public bool $heating_exists             = false;
	public bool $water_exists               = false;
	public bool $sewage_exists              = false;
	public bool $climate_control_exists     = false;
	public bool $gas_exists                 = false;
	public bool $steam_exists               = false;
	public bool $phone_line_exists          = false;
	public bool $internet_exists            = false;
	public bool $smoke_exhaust_exists       = false;
	public bool $video_control_exists       = false;
	public bool $access_control_exists      = false;
	public bool $security_alert_exists      = false;
	public bool $cranes_runways_exists      = false;

	protected function jsonToArrayStringElements($values): array
	{
		if ($values === null) {
			return [];
		}

		$result = [];

		foreach (explode(":", $values) as $value) {
			$result = ArrayHelper::merge($result, JsonFieldNormalizer::jsonToArrayStringElements($value));
		}

		return array_unique($result);
	}

	protected function jsonToArrayIntElements($values): array
	{
		if ($values === null) {
			return [];
		}

		$result = [];

		foreach (explode(":", $values) as $value) {
			$result = ArrayHelper::merge($result, JsonFieldNormalizer::jsonToArrayIntElements($value));
		}

		return array_unique($result);
	}

	public static function find(?int $offer_id = null): ActiveQuery
	{
		$query = parent::find();

		if (!$offer_id) {
			return $query;
		}

		$query->andWhere(['offer_id' => $offer_id])
		      ->andWhere(['status' => 1])
		      ->groupBy('offer_id');

		$query->select([
			'offer_id'                  => 'offer_id',
			'price_sale_min'            => 'MIN(IFNULL(price_sale_min, 0))',
			'price_sale_max'            => 'MAX(IFNULL(price_sale_max, 0))',
			'price_floor_min'           => 'MIN(IFNULL(price_floor_min, 0))',
			'price_floor_max'           => 'MAX(IFNULL(price_floor_max, 0))',
			'price_field_min'           => 'MIN(IFNULL(price_field_min, 0))',
			'price_field_max'           => 'MAX(IFNULL(price_field_max, 0))',
			'price_mezzanine_max'       => 'MAX(IFNULL(price_mezzanine_max, 0))',
			'price_mezzanine_min'       => 'MIN(IFNULL(price_mezzanine_min, 0))',
			'price_floor_two_min'       => 'MIN(IFNULL(price_floor_two_min, 0))',
			'price_floor_two_max'       => 'MAX(IFNULL(price_floor_two_max, 0))',
			'price_floor_three_min'     => 'MIN(IFNULL(price_floor_three_min, 0))',
			'price_floor_four_min'      => 'MIN(IFNULL(price_floor_four_min, 0))',
			'price_floor_five_min'      => 'MIN(IFNULL(price_floor_five_min, 0))',
			'price_floor_five_max'      => 'MAX(IFNULL(price_floor_five_min, 0))',
			'price_floor_six_min'       => 'MIN(IFNULL(price_floor_six_min, 0))',
			'price_mezzanine_two_min'   => 'MIN(IFNULL(price_mezzanine_two_min, 0))',
			'price_mezzanine_three_min' => 'MIN(IFNULL(price_mezzanine_three_min, 0))',
			'price_mezzanine_four_min'  => 'MIN(IFNULL(price_mezzanine_four_min, 0))',
			'price_floor_three_max'     => 'MAX(IFNULL(price_floor_three_max, 0))',
			'price_floor_four_max'      => 'MAX(IFNULL(price_floor_four_max, 0))',
			'price_floor_six_max'       => 'MAX(IFNULL(price_floor_six_max, 0))',
			'price_mezzanine_two_max'   => 'MAX(IFNULL(price_mezzanine_two_max, 0))',
			'price_mezzanine_three_max' => 'MAX(IFNULL(price_mezzanine_three_max, 0))',
			'price_mezzanine_four_max'  => 'MAX(IFNULL(price_mezzanine_four_max, 0))',
			'area_floor_min'            => 'MIN(IFNULL(area_floor_min, 0))',
			'area_floor_max'            => 'MAX(IFNULL(area_floor_max, 0))',
			'area_field_min'            => 'MIN(IFNULL(area_field_min, 0))',
			'area_field_max'            => 'MAX(IFNULL(area_field_max, 0))',
			'area_mezzanine_max'        => 'MAX(IFNULL(area_mezzanine_max, 0))',
			'area_mezzanine_min'        => 'MIN(IFNULL(area_mezzanine_min, 0))',
			'area_warehouse_min'        => 'MIN(IFNULL(area_warehouse_min, 0))',
			'area_warehouse_max'        => 'MAX(IFNULL(area_warehouse_max, 0))',
			'area_office_min'           => 'MIN(IFNULL(area_office_min, 0))',
			'area_office_max'           => 'MAX(IFNULL(area_office_max, 0))',
			'area_tech_min'             => 'MIN(IFNULL(area_tech_min, 0))',
			'area_tech_max'             => 'MAX(IFNULL(area_tech_max, 0))',
			'power'                     => 'MAX(IFNULL(power, 0))',
			'load_floor_min'            => 'MIN(IFNULL(load_floor_min, 0))',
			'load_floor_max'            => 'MAX(IFNULL(load_floor_max, 0))',
			'load_mezzanine_min'        => 'MIN(IFNULL(load_mezzanine_min, 0))',
			'load_mezzanine_max'        => 'MAX(IFNULL(load_mezzanine_max, 0))',
			'ceiling_height_min'        => 'MIN(IFNULL(ceiling_height_min, 0))',
			'ceiling_height_max'        => 'MAX(IFNULL(ceiling_height_max, 0))',
			'temperature_min'           => 'MIN(IFNULL(temperature_min, 0))',
			'temperature_max'           => 'MAX(IFNULL(temperature_max, 0))',
			'pallet_place_min'          => 'MIN(IFNULL(pallet_place_min, 0))',
			'pallet_place_max'          => 'MAX(IFNULL(pallet_place_max, 0))',


			'racks_exists'               => 'SUM(IF(racks = 1, 1, 0)) > 0',
			'charging_room_exists'       => 'SUM(IF(charging_room = 1, 1, 0)) > 0',
			'warehouse_equipment_exists' => 'SUM(IF(warehouse_equipment = 1, 1, 0)) > 0',
			'heating_exists'             => 'SUM(IF(heated = 1, 1, 0)) > 0',
			'water_exists'               => 'SUM(IF(water = 1, 1, 0)) > 0',
			'sewage_exists'              => 'SUM(IF(sewage = 1, 1, 0)) > 0',
			'climate_control_exists'     => 'SUM(IF(climate_control = 1, 1, 0)) > 0',
			'gas_exists'                 => 'SUM(IF(gas = 1, 1, 0)) > 0',
			'steam_exists'               => 'SUM(IF(steam = 1, 1, 0)) > 0',
			'phone_line_exists'          => 'SUM(IF(phone_line = 1, 1, 0)) > 0',
			'internet_exists'            => 'SUM(IF(internet = 1, 1, 0)) > 0',
			'smoke_exhaust_exists'       => 'SUM(IF(smoke_exhaust = 1, 1, 0)) > 0',
			'video_control_exists'       => 'SUM(IF(video_control = 1, 1, 0)) > 0',
			'access_control_exists'      => 'SUM(IF(access_control = 1, 1, 0)) > 0',
			'security_alert_exists'      => 'SUM(IF(security_alert = 1, 1, 0)) > 0',
			'cranes_runways_exists'      => 'SUM(IF(cranes_runways = 1, 1, 0)) > 0',


			'column_grids'      => "GROUP_CONCAT(column_grids SEPARATOR ':')",
			'ventilation'       => "GROUP_CONCAT(ventilation SEPARATOR ':')",
			'elevators'         => "GROUP_CONCAT(elevators SEPARATOR ':')",
			'cranes'            => "GROUP_CONCAT(cranes SEPARATOR ':')",
			'photo_block'       => "GROUP_CONCAT(photo_block SEPARATOR ':')",
			'purposes_block'    => "GROUP_CONCAT(purposes_block SEPARATOR ':')",
			'floor'             => "GROUP_CONCAT(floor SEPARATOR ':')",
			'floor_types'       => "GROUP_CONCAT(floor_types SEPARATOR ':')",
			'parts'             => "GROUP_CONCAT(parts SEPARATOR ':')",
			'cranes_cathead'    => "GROUP_CONCAT(cranes_cathead SEPARATOR ':')",
			'cranes_overhead'   => "GROUP_CONCAT(cranes_overhead SEPARATOR ':')",
			'gates'             => "GROUP_CONCAT(gates SEPARATOR ':')",
			'floor_types_land'  => "GROUP_CONCAT(floor_types_land SEPARATOR ':')",
			'safe_type'         => "GROUP_CONCAT(safe_type SEPARATOR ':')",
			'lighting'          => "GROUP_CONCAT(lighting SEPARATOR ':')",
			'rack_types'        => "GROUP_CONCAT(rack_types SEPARATOR ':')",
			'telphers'          => "GROUP_CONCAT(telphers SEPARATOR ':')",
			'firefighting_type' => "GROUP_CONCAT(firefighting_type SEPARATOR ':')",
		]);

		return $query;
	}

	public function fields(): array
	{
		$f = parent::fields();

		$f['racks_exists'] = function () {
			return $this->racks_exists;
		};

		$f['charging_room_exists']       = function () {
			return $this->charging_room_exists;
		};
		$f['warehouse_equipment_exists'] = function () {
			return $this->warehouse_equipment_exists;
		};
		$f['heating_exists']             = function () {
			return $this->heating_exists;
		};
		$f['water_exists']               = function () {
			return $this->water_exists;
		};
		$f['sewage_exists']              = function () {
			return $this->sewage_exists;
		};
		$f['climate_control_exists']     = function () {
			return $this->climate_control_exists;
		};
		$f['gas_exists']                 = function () {
			return $this->gas_exists;
		};
		$f['steam_exists']               = function () {
			return $this->steam_exists;
		};
		$f['phone_line_exists']          = function () {
			return $this->phone_line_exists;
		};
		$f['internet_exists']            = function () {
			return $this->internet_exists;
		};
		$f['smoke_exhaust_exists']       = function () {
			return $this->smoke_exhaust_exists;
		};
		$f['video_control_exists']       = function () {
			return $this->video_control_exists;
		};
		$f['access_control_exists']      = function () {
			return $this->access_control_exists;
		};
		$f['security_alert_exists']      = function () {
			return $this->security_alert_exists;
		};
		$f['cranes_runways_exists']      = function () {
			return $this->cranes_runways_exists;
		};
		$f['elevators_exists']           = function () {
			return !!$this->getElevators();
		};

		return $f;
	}

	public function extraFields(): array
	{
		$f = parent::extraFields();

		$f['partsRecords'] = function () {
			return FloorPart::find()
			                ->andWhere(['id' => $this->getParts()])
			                ->all();
		};

		return $f;
	}
}