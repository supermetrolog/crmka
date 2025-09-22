<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserQuery;
use app\models\ActiveQuery\UserTourViewQuery;
use app\models\User\User;

/**
 * @property int       $id
 * @property int       $user_id
 * @property string    $tour_id
 * @property int       $steps_viewed
 * @property int       $steps_total
 * @property string    $created_at
 *
 * @property-read User $user
 */
class UserTourView extends AR
{
	public static function tableName(): string
	{
		return 'user_tour_view';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'tour_id', 'steps_viewed', 'steps_total'], 'required'],
			[['user_id', 'steps_viewed', 'steps_total'], 'integer'],
			[['tour_id'], 'string', 'max' => 64],
			[['created_at', 'updated_at'], 'safe']
		];
	}

	public static function find(): UserTourViewQuery
	{
		return new UserTourViewQuery(static::class);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function isCompleted(): bool
	{
		return $this->steps_viewed === $this->steps_total;
	}
}
