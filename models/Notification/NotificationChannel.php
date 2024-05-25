<?php

namespace app\models\Notification;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\NotificationChannelQuery;

/**
 * This is the model class for table "notification_channel".
 *
 * @property int    $id
 * @property string $name
 * @property string $slug
 * @property int    $is_enabled
 * @property string $created_at
 * @property string $updated_at
 */
class NotificationChannel extends AR
{
	public const EMAIL    = 'email';
	public const TELEGRAM = 'telegram';
	public const WEB      = 'web';

	public static function tableName(): string
	{
		return 'notification_channel';
	}

	public function rules(): array
	{
		return [
			[['name', 'slug'], 'required'],
			[['is_enabled'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['name', 'slug'], 'string', 'max' => 255],
			[['slug'], 'unique'],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'name'       => 'Name',
			'slug'       => 'Slug',
			'is_enabled' => 'Is Enabled',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}


	public static function find(): NotificationChannelQuery
	{
		return new NotificationChannelQuery(get_called_class());
	}
}
