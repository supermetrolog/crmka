<?php
declare(strict_types=1);

namespace app\usecases\Whatsapp;

use app\dto\User\UserWhatsappLinkDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\User\UserWhatsappLink;

final class UserWhatsappLinkService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(UserWhatsappLinkDto $dto): UserWhatsappLink
	{
		$link = new UserWhatsappLink();

		$link->user_id             = $dto->userId;
		$link->whatsapp_profile_id = $dto->whatsappProfileId;
		$link->phone               = $dto->phone;
		$link->first_name          = $dto->firstName;
		$link->full_name           = $dto->fullName;
		$link->push_name           = $dto->pushName;

		$link->saveOrThrow();

		return $link;
	}

	/**
	 * @throws SaveModelException
	 */
	public function revoke(UserWhatsappLink $link): void
	{
		if ($link->isRevoked()) {
			return;
		}

		$link->revoked_at = DateTimeHelper::nowf();

		$link->saveOrThrow();
	}
}
