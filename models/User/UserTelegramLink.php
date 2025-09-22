<?php

namespace app\models\User;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserQuery;
use app\models\ActiveQuery\UserTelegramLinkQuery;

/**
 * @property int       $id
 * @property int       $user_id
 * @property ?string   $telegram_user_id
 * @property ?string   $chat_id
 * @property ?string   $username
 * @property ?string   $first_name
 * @property ?string   $last_name
 * @property bool      $is_enabled
 * @property ?string   $revoked_at
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @property-read User $user
 */
class UserTelegramLink extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'user_telegram_link';
	}

	public function rules(): array
	{
		return [
			[['user_id'], 'required'],
			[['user_id', 'telegram_user_id', 'chat_id'], 'integer'],
			[['is_enabled'], 'boolean'],
			[['revoked_at', 'created_at', 'updated_at'], 'safe'],
			[['username'], 'string', 'max' => 64],
			[['first_name', 'last_name'], 'string', 'max' => 256],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): UserTelegramLinkQuery
	{
		return new UserTelegramLinkQuery(static::class);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function isRevoked(): bool
	{
		return !is_null($this->revoked_at);
	}
}
