<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\MediaQuery;

/**
 * This is the model class for table "media".
 *
 * @property int         $id
 * @property string      $name
 * @property string      $original_name
 * @property string      $extension
 * @property string      $path
 * @property string      $category
 * @property string      $model_type
 * @property int         $model_id
 * @property string      $created_at
 * @property string|null $deleted_at
 */
class Media extends AR
{
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'media';
	}

	public function rules(): array
	{
		return [
			[['name', 'original_name', 'extension', 'path', 'category', 'model_type', 'model_id'], 'required'],
			[['model_id'], 'integer'],
			[['created_at', 'deleted_at'], 'safe'],
			[['name', 'original_name', 'extension', 'path', 'category', 'model_type'], 'string', 'max' => 255],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'            => 'ID',
			'name'          => 'Name',
			'original_name' => 'Original Name',
			'extension'     => 'Extension',
			'path'          => 'Path',
			'category'      => 'Category',
			'model_type'    => 'Model Type',
			'model_id'      => 'Model ID',
			'created_at'    => 'Created At',
			'deleted_at'    => 'Deleted At',
		];
	}

	public static function find(): MediaQuery
	{
		return new MediaQuery(get_called_class());
	}
}
