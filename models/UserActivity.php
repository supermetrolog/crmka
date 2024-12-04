<?php

namespace app\models;

use app\helpers\DateTimeHelper;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\UserActivityQuery;
use app\models\ActiveQuery\UserQuery;
use DateTimeInterface;
use Exception;

/**
 * This is the model class for table "user_access_token".
 *
 * @property int       $id
 * @property int       $user_id
 * @property string    $ip
 * @property string    $user_agent
 * @property string    $started_at
 * @property string    $last_activity_at
 * @property ?string   $last_page
 *
 * @property-read User $user
 */
class UserActivity extends AR
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'user_activity';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['user_id', 'ip', 'user_agent'], 'required'],
			[['user_id'], 'integer'],
			[['user_agent'], 'string', 'max' => 1024],
			[['ip'], 'string', 'max' => 15],
			[['last_page'], 'string', 'max' => 128],
			[['started_at', 'last_activity_at'], 'safe'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'               => 'ID',
			'user_id'          => 'User ID',
			'ip'               => 'IP',
			'user_agent'       => 'User Agent',
			'last_page'        => 'Last Page',
			'started_at'       => 'Started At',
			'last_activity_at' => 'Last Activity At',
		];
	}

	/**
	 * @throws Exception
	 */
	public function getStartedAt(): DateTimeInterface
	{
		return DateTimeHelper::make($this->started_at);
	}

	/**
	 * @throws Exception
	 */
	public function getLastActivityAt(): DateTimeInterface
	{
		return DateTimeHelper::make($this->last_activity_at);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery $query */
		$query = $this->hasOne(User::class, ['id' => 'user_id']);

		return $query;
	}

	public static function find(): UserActivityQuery
	{
		return new UserActivityQuery(get_called_class());
	}
}
