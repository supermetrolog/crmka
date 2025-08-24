<?php

namespace app\enum\UserProfile;

use app\enum\AbstractEnum;

class UserProfileGenderEnum extends AbstractEnum
{
	public const FEMALE = 'f';
	public const MALE   = 'm';

	public static function labels(): array
	{
		return [
			self::FEMALE => 'Она',
			self::MALE   => 'Он',
		];
	}
}