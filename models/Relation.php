<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\RelationQuery;
use app\models\ActiveQuery\TaskQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "relation".
 *
 * @property int       $id
 * @property string    $first_type
 * @property int       $first_id
 * @property string    $second_type
 * @property int       $second_id
 * @property string    $created_at
 * @property string    $updated_at
 *
 * @property Task|null $taskFirst
 * @property Task|null $taskSecond
 */
class Relation extends AR
{

	public static function tableName(): string
	{
		return 'relation';
	}

	public function rules(): array
	{
		return [
			[['first_type', 'first_id', 'second_type', 'second_id'], 'required'],
			[['first_id', 'second_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['first_type', 'second_type'], 'string', 'max' => 255],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'          => 'ID',
			'first_type'  => 'First Type',
			'first_id'    => 'First ID',
			'second_type' => 'Second Type',
			'second_id'   => 'Second ID',
			'created_at'  => 'Created At',
			'updated_at'  => 'Updated At',
		];
	}

	public static function find(): RelationQuery
	{
		return new RelationQuery(get_called_class());
	}

	/**
	 * @return TaskQuery|ActiveQuery
	 */
	public function getTaskSecond(): TaskQuery
	{
		return $this->morphBelongTo(Task::class, 'id', 'second');
	}

	/**
	 * @return TaskQuery|ActiveQuery
	 */
	public function getTaskFirst(): TaskQuery
	{
		return $this->morphBelongTo(Task::class, 'id', 'first');
	}
}
