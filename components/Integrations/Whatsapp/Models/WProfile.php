<?php

declare(strict_types=1);

namespace app\components\Integrations\Whatsapp\Models;

use app\components\Integrations\IntegrationModel;

class WProfile extends IntegrationModel
{
	protected array $casts = [
		'user'    => 'setUser',
		'contact' => WProfileContact::class
	];

	public bool   $is_one_whatsapp;
	public string $id;

	/** @var array<string, WProfileUser> */
	public ?array           $user;
	public ?WProfileContact $contact;

	public function setUser(array $payload): void
	{
		foreach ($payload as $key => $value) {
			$this->user[$key] = new WProfileUser($value);
		}
	}
}
