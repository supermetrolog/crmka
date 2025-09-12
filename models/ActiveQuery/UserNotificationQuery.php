<?php

namespace app\models\ActiveQuery;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Notification\UserNotification;
use yii\base\ErrorException;

class UserNotificationQuery extends AQ
{
	/**
	 * @return UserNotification[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function one($db = null): ?UserNotification
	{
		/** @var ?UserNotification */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserNotification
	{
		/** @var ?UserNotification */
		return parent::oneOrThrow($db);
	}

	/**
	 * @throws ErrorException
	 */
	public function byUserId(int $userId): self
	{
		return $this->andWhere([UserNotification::field('user_id') => $userId]);
	}

	/**
	 * @throws ErrorException
	 */
	public function notExpired(): self
	{
		return $this->andWhere(['or', [UserNotification::field('expires_at') => null], ['>', UserNotification::field('expires_at'), DateTimeHelper::nowf()]]);
	}

	/**
	 * @throws ErrorException
	 */
	public function notActed(): self
	{
		return $this->andWhere([UserNotification::field('acted_at') => null]);
	}
}
