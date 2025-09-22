<?php

namespace app\models\User;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserQuery;
use app\models\ActiveQuery\UserWhatsappLinkQuery;

/**
 * @property int       $id
 * @property int       $user_id
 * @property string    $whatsapp_profile_id
 * @property string    $phone
 * @property ?string   $first_name
 * @property ?string   $full_name
 * @property ?string   $push_name
 * @property ?string   $revoked_at
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @property-read User $user
 */
class UserWhatsappLink extends AR
{
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'user_whatsapp_link';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'whatsapp_profile_id', 'phone'], 'required'],
			[['user_id'], 'integer'],
			[['revoked_at', 'created_at', 'updated_at'], 'safe'],
			[['phone'], 'string', 'max' => 16],
			[['first_name'], 'string', 'max' => 64],
			[['full_name', 'push_name'], 'string', 'max' => 128],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): UserWhatsappLinkQuery
	{
		return new UserWhatsappLinkQuery(static::class);
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
