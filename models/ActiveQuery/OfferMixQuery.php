<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\OfferMix;
use app\models\oldDb\ObjectsBlock;
use yii\db\ActiveRecord;

class OfferMixQuery extends oldDb\OfferMixQuery
{
	/**
	 * @param mixed $db
	 *
	 * @return OfferMix[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @param mixed $db
	 *
	 * @return OfferMix|null|ActiveRecord
	 */
	public function one($db = null): ?OfferMix
	{
		$this->limit(1);

		return parent::one($db);
	}

	/**
	 * @return OfferMix|array|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): OfferMix
	{
		$model = $this->one($db);

		if ($model) {
			return $model;
		}

		throw new ModelNotFoundException();
	}

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
		return $this->andWhere(['type_id' => OfferMix::MINI_TYPE_ID]);
	}

	/**
	 * @return self
	 */
	public function generalType(): self
	{
		return $this->andWhere(['type_id' => OfferMix::GENERAL_TYPE_ID]);
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
		return $this->andWhere(['deal_type' => OfferMix::DEAL_TYPE_SALE]);
	}

	/**
	 * @return self
	 */
	public function rentDealType(): self
	{
		return $this->andWhere(['deal_type' => OfferMix::DEAL_TYPE_RENT]);
	}

	/**
	 * @return self
	 */
	public function rentAllDealType(): self
	{
		return $this->andWhere(['deal_type' => [OfferMix::DEAL_TYPE_RENT, OfferMix::DEAL_TYPE_SUBLEASE]]);
	}

	/**
	 * @return self
	 */
	public function notResponseStorageDealType(): self
	{
		return $this->andWhere(['!=', OfferMix::tableName() . '.deal_type', OfferMix::DEAL_TYPE_RESPONSE_STORAGE]);
	}

	/**
	 * @return self
	 */
	public function adAvito(): self
	{
		$this->joinWith(['block']);

		return $this->andWhere([ObjectsBlock::tableName() . '.ad_avito' => 1]);
	}
}