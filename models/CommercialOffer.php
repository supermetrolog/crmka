<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\oldDb\Offers;
use yii\db\ActiveQuery;

class CommercialOffer extends Offers
{
	/**
	 * @return ActiveQuery
	 */
	public function getDealTypeRecord(): ActiveQuery
	{
		return $this->hasOne(DealType::class, ['id' => 'deal_type']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getCompanyRecord(): ActiveQuery
	{
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getBlocks(): ActiveQuery
	{
		return $this->hasMany(Block::class, ['offer_id' => 'id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getSummaryBlock(): ActiveQuery
	{
		return Block::find()->andWhere(['offer_id' => $this->id])
		            ->select([
			            'offer_id'                  => 'offer_id',
			            'price_sale_min'            => 'MIN(IFNULL(price_sale_min, 0))',
			            'price_sale_max'            => 'MAX(IFNULL(price_sale_max, 0))',
//			            'price'                     => 'MIN(IFNULL(price, 0))',
//			            'price_sale'                => 'MIN(IFNULL(price_sale, 0))',
			            'price_floor_min'           => 'MIN(IFNULL(price_floor_min, 0))',
//			            'price_floor'               => 'MIN(IFNULL(price_floor, 0))',
			            'price_floor_max'           => 'MAX(IFNULL(price_floor_max, 0))',
//			            'price_field'               => 'MIN(IFNULL(price_field, 0))',
			            'price_field_min'           => 'MIN(IFNULL(price_field_min, 0))',
			            'price_field_max'           => 'MAX(IFNULL(price_field_max, 0))',
//			            'price_mezzanine'           => 'MIN(IFNULL(price_mezzanine, 0))',
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
//			            'area'                      => 'MIN(IFNULL(price_mezzanine_three_max, 0))',
			            'area_floor_min'            => 'MIN(IFNULL(price_mezzanine_four_max, 0))',
//			            'area_floor'                => 'MIN(IFNULL(area, 0))',
			            'area_floor_max'            => 'MAX(IFNULL(area_floor_min, 0))',
//			            'area_field'                => 'MIN(IFNULL(area_floor, 0))',
			            'area_field_min'            => 'MIN(IFNULL(area_floor_max, 0))',
			            'area_field_max'            => 'MAX(IFNULL(area_field, 0))',
//			            'area_mezzanine'            => 'MIN(IFNULL(area_field_min, 0))',
			            'area_mezzanine_max'        => 'MAX(IFNULL(area_field_max, 0))',
			            'area_mezzanine_min'        => 'MIN(IFNULL(area_mezzanine, 0))',
//			            'area_warehouse'            => 'MIN(IFNULL(area_mezzanine_max, 0))',
			            'area_warehouse_min'        => 'MIN(IFNULL(area_mezzanine_min, 0))',
			            'area_warehouse_max'        => 'MAX(IFNULL(area_warehouse, 0))',
//			            'area_mezzanine_add'        => 'MIN(IFNULL(area_warehouse_min, 0))',
//			            'area_office'               => 'MIN(IFNULL(area_warehouse_max, 0))',
			            'area_office_min'           => 'MIN(IFNULL(area_mezzanine_add, 0))',
			            'area_office_max'           => 'MAX(IFNULL(area_office, 0))',
//			            'area_office_add'           => 'MIN(IFNULL(area_office_min, 0))',
//			            'area_tech'                 => 'MIN(IFNULL(area_office_max, 0))',
			            'area_tech_min'             => 'MIN(IFNULL(area_office_add, 0))',
			            'area_tech_max'             => 'MAX(IFNULL(area_tech, 0))',
//			            'area_tech_add'             => 'MIN(IFNULL(area_tech_min, 0))',
		            ])
		            ->groupBy(['offer_id']);
	}

	public function getIncType(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->inc_opex);
	}

	public function getIncServices(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->inc_services);
	}

	public function fields()
	{
		$f = parent::fields();

		$f['inc_opex'] = function () {
			return $this->getIncType();
		};

		$f['inc_services'] = function () {
			return $this->getIncServices();
		};

		return $f;
	}

	public function extraFields()
	{
		$f = parent::extraFields();

		$f['summaryBlock'] = function () {
			return $this->getSummaryBlock()->asArray()->one();
		};

		return $f;
	}
}
