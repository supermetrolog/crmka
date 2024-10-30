<?php

declare(strict_types=1);

namespace app\helpers;

class PersonNameHelper
{
	public static function generateFullName(string $firstName, string $middleName, string $lastName): string
	{
		return StringHelper::join(StringHelper::SYMBOL_SPACE,
			$middleName,
			$firstName,
			$lastName
		);
	}

	public static function generateShortName(string $firstName, string $middleName, string $lastName): string
	{
		$firstNameCharacter = StringHelper::ucFirst(StringHelper::first($firstName));
		$lastNameCharacter  = StringHelper::ucFirst(StringHelper::first($lastName));

		$characters = StringHelper::join(". ", $firstNameCharacter, $lastNameCharacter);

		return StringHelper::join(StringHelper::SYMBOL_SPACE, $middleName, $characters) . ".";
	}
}