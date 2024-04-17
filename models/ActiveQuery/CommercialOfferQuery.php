<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ;
use app\models\CommercialOffer;
use yii\db\ActiveRecord;

class CommercialOfferQuery extends AQ
{
	/**
	 * @param $db
	 *
	 * @return array|CommercialOffer[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @param $db
	 *
	 * @return CommercialOffer|null|ActiveRecord
	 */
	public function one($db = null): CommercialOffer
	{
		return parent::one($db);
	}

	public function notDeleted(): self
	{
		return $this->andWhere(['!=', $this->field('deleted'), 1]);
	}

	/**
	 * @param int|array $dealType
	 *
	 * @return self
	 */
	public function byDealType($dealType): self
	{
		return $this->andWhere([$this->field('deal_type') => $dealType]);
	}

	public function rent(): self
	{
		return $this->byDealType(CommercialOffer::DEAL_TYPE_RENT);
	}

	public function sale(): self
	{
		return $this->byDealType(CommercialOffer::DEAL_TYPE_SALE);
	}

	public function sublease(): self
	{
		return $this->byDealType(CommercialOffer::DEAL_TYPE_SUBLEASE);
	}

	public function responseStorage(): self
	{
		return $this->byDealType(CommercialOffer::DEAL_TYPE_RESPONSE_STORAGE);
	}

	public function rentOrSale(): self
	{
		return $this->byDealType([CommercialOffer::DEAL_TYPE_SALE, CommercialOffer::DEAL_TYPE_RENT]);
	}
}