<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Notification\NotificationChannel;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[NotificationChannel]].
 *
 * @see NotificationChannel
 */
class NotificationChannelQuery extends AQ
{

	/**
	 * @return NotificationChannel[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return NotificationChannel|ActiveRecord|null
	 */
	public function one($db = null): ?NotificationChannel
	{
		return parent::one($db);
	}

	/**
	 * @return NotificationChannel|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): NotificationChannel
	{
		return parent::oneOrThrow($db);
	}

	public function bySlug(string $slug): self
	{
		return $this->andWhere([$this->field('slug') => $slug]);
	}

	/**
	 * @param string[] $slug
	 *
	 * @return self
	 */
	public function bySlugs(array $slug): self
	{
		return $this->andWhere([$this->field('slug') => $slug]);
	}
}
