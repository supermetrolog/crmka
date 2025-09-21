<?php

namespace app\models;

use app\models\User\User;
use phpDocumentor\Reflection\Types\Self_;


/**
 * This is the model class for table "notification".
 *
 * @property int         $id
 * @property int         $consultant_id [связь] с юзером
 * @property string|null $title         заголовок оповещения
 * @property string      $body          [html]текс оповещения
 * @property int         $type          тип оповещения
 * @property int|null    $status
 * @property string|null $created_at
 *
 * @property User        $consultant
 */
class Notification extends \yii\db\ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public const FETCHED_STATUS    = 0;
	public const NO_FETCHED_STATUS = -1;
	public const VIEWED_STATUS     = 1;
	public const NO_VIEWED_STATUS  = 0;
	public const PROCESSED_STATUS  = 2;
	public const NO_COUNT_STATUS   = 3;

	public const TYPE_SYSTEM_INFO        = 0;
	public const TYPE_SYSTEM_WARNING     = 1;
	public const TYPE_SYSTEM_DANGER      = 2;
	public const TYPE_COMPANY_INFO       = 3;
	public const TYPE_COMPANY_WARNING    = 4;
	public const TYPE_COMPANY_DANGER     = 5;
	public const TYPE_REQUEST_INFO       = 6;
	public const TYPE_REQUEST_WARNING    = 7;
	public const TYPE_REQUEST_DANGER     = 8;
	public const TYPE_CALENDAR_INFO      = 9;
	public const TYPE_CALENDAR_WARNING   = 10;
	public const TYPE_CALENDAR_DANGER    = 11;
	public const TYPE_TIMELINE_INFO      = 12;
	public const TYPE_TIMELINE_WARNING   = 13;
	public const TYPE_TIMELINE_DANGER    = 14;
	public const TYPE_COLLECTION_INFO    = 15;
	public const TYPE_COLLECTION_WARNING = 16;
	public const TYPE_COLLECTION_DANGER  = 17;

	public static function tableName()
	{
		return 'notification';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['consultant_id', 'body', 'type'], 'required'],
			[['consultant_id', 'type', 'status'], 'integer'],
			[['created_at'], 'safe'],
			[['title'], 'string', 'max' => 255],
			[['body'], 'string'],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'            => 'ID',
			'consultant_id' => 'Consultant ID',
			'title'         => 'Title',
			'body'          => 'Body',
			'type'          => 'Type',
			'status'        => 'Status',
			'created_at'    => 'Created At',
		];
	}

	public static function viewedNotCount($id)
	{
		$models = self::find()->where(['consultant_id' => $id])->andWhere(['status' => self::NO_COUNT_STATUS])->all();
		foreach ($models as $model) {
			$model->status = self::VIEWED_STATUS;
			$model->save();
		}
	}

	public static function viewedAll($id)
	{
		$models = self::find()->where(['consultant_id' => $id])->andWhere(['status' => [self::NO_COUNT_STATUS, self::NO_VIEWED_STATUS]])->all();
		foreach ($models as $model) {
			$model->status = self::VIEWED_STATUS;
			$model->save();
		}
	}

	public static function getNotificationsCount($id)
	{
		return self::find()->where(['consultant_id' => $id, 'status' => [self::NO_FETCHED_STATUS, self::NO_VIEWED_STATUS]])->count();
	}

	public static function array_copy($array)
	{
		$newArray = [];
		foreach ($array as $item) {
			$newArray[] = clone $item;
		}

		return $newArray;
	}

	public static function changeNoFetchedStatusToFetched($models)
	{
		foreach ($models as $model) {
			if ($model->status == self::NO_FETCHED_STATUS) {
				$model->status = self::FETCHED_STATUS;
				$model->save();
			}
		}
	}

	public static function changeNoViewedStatusToNoCount($models)
	{
		foreach ($models as $model) {
			if (($model->status == self::NO_VIEWED_STATUS || $model->status == self::NO_FETCHED_STATUS) && $model->status != self::PROCESSED_STATUS) {
				$model->status = self::NO_COUNT_STATUS;
				$model->save();
			}
		}
	}

	/**
	 * Gets query for [[Consultant]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getConsultant()
	{
		return $this->hasOne(User::className(), ['id' => 'consultant_id']);
	}
}
