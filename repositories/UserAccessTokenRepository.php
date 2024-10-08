<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\UserAccessToken;

class UserAccessTokenRepository
{
	/**
	 * @param int $id
	 *
	 * @return UserAccessToken[]
	 */
	public function findAllValidByUserId(int $id): array
	{
		return UserAccessToken::find()->byUserId($id)->valid()->all();
	}
}