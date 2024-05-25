<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Media;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Media]].
 *
 * @see Media
 */
class MediaQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Media[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Media|ActiveRecord|null
	 */
	public function one($db = null): ?Media
	{
		return parent::one($db);
	}

	/**
	 * @return Media|ActiveRecord|null
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Media
	{
		return parent::oneOrThrow($db);
	}

	public function byModelId(int $id): self
	{
		return $this->andWhere([$this->field('model_id') => $id]);
	}

	public function byModelType(string $type): self
	{
		return $this->andWhere([$this->field('model_type') => $type]);
	}

	public function byMorph(int $id, string $type): self
	{
		return $this->byModelId($id)->byModelType($type);
	}
}
