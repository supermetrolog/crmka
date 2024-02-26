<?php

declare(strict_types=1);

namespace app\models;

use app\helpers\ArrayHelper;
use app\helpers\JsonFieldNormalizer;
use yii\db\ActiveQuery;

class SummaryBlock extends Block
{

	protected function jsonToArrayStringElements($values): array
	{
		if ($values === null) {
			return [];
		}

		$result = [];

		foreach (explode(":", $values) as $value) {
			$result = ArrayHelper::merge($result, JsonFieldNormalizer::jsonToArrayStringElements($value));
		}

		return $result;
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

		return $result;
	}

	public static function find(?int $offer_id = null): ActiveQuery
	{
		$query = parent::find();

		if (!$offer_id) {
			return $query;
		}

		$query->andWhere(['offer_id' => $offer_id])
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
			'price_floor_six_max'       => 'MAX(IFNULL(price_floor_five_max, 0))',
			'price_mezzanine_two_max'   => 'MAX(IFNULL(price_floor_five_max, 0))',
			'price_mezzanine_three_max' => 'MAX(IFNULL(price_floor_six_max, 0))',
			'price_mezzanine_four_max'  => 'MAX(IFNULL(price_mezzanine_two_max, 0))',
			'area_floor_min'            => 'MIN(IFNULL(price_mezzanine_four_max, 0))',
			'area_floor_max'            => 'MAX(IFNULL(area_floor_min, 0))',
			'area_field_min'            => 'MIN(IFNULL(area_floor_max, 0))',
			'area_field_max'            => 'MAX(IFNULL(area_field, 0))',
			'area_mezzanine_max'        => 'MAX(IFNULL(area_field_max, 0))',
			'area_mezzanine_min'        => 'MIN(IFNULL(area_mezzanine, 0))',
			'area_warehouse_min'        => 'MIN(IFNULL(area_mezzanine_min, 0))',
			'area_warehouse_max'        => 'MAX(IFNULL(area_warehouse, 0))',
			'area_office_min'           => 'MIN(IFNULL(area_mezzanine_add, 0))',
			'area_office_max'           => 'MAX(IFNULL(area_office, 0))',
			'area_tech_min'             => 'MIN(IFNULL(area_office_add, 0))',
			'area_tech_max'             => 'MAX(IFNULL(area_tech, 0))',
			'power'                     => 'MAX(IFNULL(power, 0))',
			'column_grids'              => "GROUP_CONCAT(column_grids SEPARATOR ':')",
			'ventilation'               => "GROUP_CONCAT(ventilation SEPARATOR ':')",
			//			'elevatorss' "> 'GROUP_CONCAT(elevatorss SEPARATOR ':')",
			//			'craness' "> 'GROUP_CONCAT(craness SEPARATOR ':')",
			'photos'                    => "GROUP_CONCAT(photos SEPARATOR ':')",
			'purposes_block'            => "GROUP_CONCAT(purposes_block SEPARATOR ':')",
			'floor'                     => "GROUP_CONCAT(floor SEPARATOR ':')",
			'floor_types'               => "GROUP_CONCAT(floor_types SEPARATOR ':')",
			'parts'                     => "GROUP_CONCAT(parts SEPARATOR ':')",
			'cranes_cathead'            => "GROUP_CONCAT(cranes_cathead SEPARATOR ':')",
			'cranes_overhead'           => "GROUP_CONCAT(cranes_overhead SEPARATOR ':')",
			'gates'                     => "GROUP_CONCAT(gates SEPARATOR ':')",
			'floor_types_land'          => "GROUP_CONCAT(floor_types_land SEPARATOR ':')",
			'safe_type'                 => "GROUP_CONCAT(safe_type SEPARATOR ':')",
			'lighting'                  => "GROUP_CONCAT(lighting SEPARATOR ':')",
			'rack_types'                => "GROUP_CONCAT(rack_types SEPARATOR ':')",
			'telphers'                  => "GROUP_CONCAT(telphers SEPARATOR ':')",
		]);


		return $query;
	}
}