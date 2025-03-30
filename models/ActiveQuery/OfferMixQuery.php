<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\models\OfferMix;
use app\models\oldDb\ObjectsBlock;
use app\models\oldDb\OfferMix as OfferMixOld;

class OfferMixQuery extends oldDb\OfferMixQuery
{
	/**
	 * @param int $originalId
	 *
	 * @return self
	 */
	public function byOriginalId(int $originalId): self
	{
		return $this->andWhere(['original_id' => $originalId]);
	}

	/**
	 * @return self
	 */
	public function notDelete(): self
	{
		return $this->andWhere(['!=', OfferMix::tableName() . '.deleted', 1]);
	}

	/**
	 * @return self
	 */
	public function active(): self
	{
		return $this->andWhere([OfferMix::tableName() . '.status' => 1]);
	}

	/**
	 * @return self
	 */
	public function blockType(): self
	{
		return $this->andWhere(['type_id' => OfferMixOld::MINI_TYPE_ID]);
	}

	/**
	 * @return self
	 */
	public function generalType(): self
	{
		return $this->andWhere(['type_id' => OfferMixOld::GENERAL_TYPE_ID]);
	}

	/**
	 * @return self
	 */
	public function offersType(): self
	{
		return $this->andWhere(['type_id' => [1, 2]]);
	}

	/**
	 * @return self
	 */
	public function saleDealType(): self
	{
		return $this->andWhere(['deal_type' => OfferMixOld::DEAL_TYPE_SALE]);
	}

	/**
	 * @return self
	 */
	public function rentDealType(): self
	{
		return $this->andWhere(['deal_type' => OfferMixOld::DEAL_TYPE_RENT]);
	}

	/**
	 * @return self
	 */
	public function rentAllDealType(): self
	{
		return $this->andWhere(['deal_type' => [OfferMixOld::DEAL_TYPE_RENT, OfferMixOld::DEAL_TYPE_SUBLEASE]]);
	}

	/**
	 * @return self
	 */
	public function notResponseStorageDealType(): self
	{
		return $this->andWhere(['!=', OfferMix::tableName() . '.deal_type', OfferMixOld::DEAL_TYPE_RESPONSE_STORAGE]);
	}

	/**
	 * @return self
	 */
	public function adAvito(): self
	{
		$this->joinWith(['block']);

		return $this->andWhere([ObjectsBlock::tableName() . '.ad_avito' => 1]);
	}

	/** @param int|int[] $type */
	public function byType($type): self
	{
		return $this->andWhere(['type_id' => $type]);
	}

	/** @param int|int[] $objectId */
	public function byObjectId($objectId): self
	{
		return $this->andWhere(['object_id' => $objectId]);
	}
}