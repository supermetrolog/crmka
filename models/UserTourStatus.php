<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserQuery;
use app\models\ActiveQuery\UserTourStatusQuery;

/**
 * @property int       $id
 * @property int       $user_id
 * @property string    $tour_id
 * @property bool      $viewed
 * @property string    $reset_at
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @property-read User $user
 */
class UserTourStatus extends AR
{
	public static function tableName(): string
	{
		return 'user_tour_status';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'tour_id'], 'required'],
			[['user_id'], 'integer'],
			[['tour_id'], 'string', 'max' => 64],
			['viewed', 'boolean'],
			['reset_at', 'safe']
		];
	}

	public static function find(): UserTourStatusQuery
	{
		return new UserTourStatusQuery(static::class);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}
}
