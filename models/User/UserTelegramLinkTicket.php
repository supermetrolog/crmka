<?php

namespace app\models\User;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserQuery;
use app\models\ActiveQuery\UserTelegramLinkTicketQuery;
use Exception;

/**
 * @property int       $id
 * @property int       $user_id
 * @property string    $code
 * @property string    $expires_at
 * @property ?string   $consumed_at
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @property-read User $user
 */
class UserTelegramLinkTicket extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'user_telegram_link_ticket';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'expires_at'], 'required'],
			[['user_id'], 'integer'],
			[['expires_at', 'consumed_at', 'created_at', 'updated_at'], 'safe'],
			[['code'], 'string', 'max' => 32],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): UserTelegramLinkTicketQuery
	{
		return new UserTelegramLinkTicketQuery(static::class);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	/**
	 * @throws Exception
	 */
	public function isExpired(): bool
	{
		return DateTimeHelper::compare(DateTimeHelper::make($this->expires_at), DateTimeHelper::now()) < 0;
	}

	public function isConsumed(): bool
	{
		return !is_null($this->consumed_at);
	}
}
