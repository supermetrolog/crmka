<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AQ\SoftDeleteTrait;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Survey;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Surveys]].
 *
 * @see Survey
 */
class SurveyQuery extends AQ
{
	use SoftDeleteTrait;

	/**
	 * @return Survey[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return Survey|ActiveRecord|null
	 */
	public function one($db = null): ?Survey
	{
		return parent::one($db);
	}

	/**
	 * @return Survey|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Survey
	{
		return parent::oneOrThrow($db);
	}

	public function byStatus(string $status): self
	{
		return $this->andWhere([$this->field('status') => $status]);
	}

	/**
	 * @param int[] $status
	 */
	public function byStatuses(array $status): self
	{
		return $this->andWhere([$this->field('status') => $status]);
	}

	public function byType(string $type): self
	{
		return $this->andWhere([$this->field('type') => $type]);
	}

	public function draft(): self
	{
		return $this->byStatus(Survey::STATUS_DRAFT);
	}

	public function completed(): self
	{
		return $this->byStatus(Survey::STATUS_COMPLETED);
	}

	public function pending(): self
	{
		return $this->byStatuses([Survey::STATUS_DRAFT, Survey::STATUS_DELAYED]);
	}

	public function byChatMemberId(int $chatMemberId): self
	{
		return $this->andWhere([$this->field('chat_member_id') => $chatMemberId]);
	}

	public function byUserId(int $userId): self
	{
		return $this->andWhere([$this->field('user_id') => $userId]);
	}
}
