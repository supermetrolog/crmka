<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use app\models\oldDb\Offers;
use yii\db\ActiveQuery;

class CommercialOffer extends Offers
{
	public static function getMorphClass(): string
	{
		return 'commercial_offer';
	}

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

	public function getConsultant(): ActiveQuery
	{
		return $this->hasOne(User::class, ['user_id_old' => 'agent_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getSummaryBlock(): ActiveQuery
	{
		return SummaryBlock::find($this->id);
	}

	public function getIncType(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->inc_opex);
	}

	public function getIncServices(): array
	{
		return JsonFieldNormalizer::jsonToArrayIntElements($this->inc_services);
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
			return $this->getSummaryBlock()->one();
		};

		return $f;
	}
}
