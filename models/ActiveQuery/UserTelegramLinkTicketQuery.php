<?php

declare(strict_types=1);

namespace app\models\ActiveQuery;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\User\UserTelegramLinkTicket;

class UserTelegramLinkTicketQuery extends AQ
{
	public function one($db = null): ?UserTelegramLinkTicket
	{
		/** @var UserTelegramLinkTicket */
		return parent::one($db);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): UserTelegramLinkTicket
	{
		/** @var UserTelegramLinkTicket */
		return parent::oneOrThrow($db);
	}

	/**
	 * @return UserTelegramLinkTicket[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	public function byUserId(int $userId): self
	{
		return $this->andWhere(['user_id' => $userId]);
	}

	public function byCode(string $code): self
	{
		return $this->andWhere(['code' => $code]);
	}

	public function active(): self
	{
		return $this->andWhereNull('consumed_at')->andWhereNotNull('expires_at')->andWhere(['>', 'expires_at', DateTimeHelper::nowf()]);
	}
}