<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FolderEntityQuery;
use app\models\ActiveQuery\FolderQuery;
use app\models\ActiveQuery\UserQuery;
use yii\base\ErrorException;

/**
 * @property int                  $id
 * @property int                  $user_id
 * @property string               $name
 * @property ?string              $icon
 * @property ?string              $color
 * @property integer              $sort_order
 * @property string               $category
 * @property string               $created_at
 * @property string               $updated_at
 * @property ?string              $deleted_at
 *
 * @property-read  User           $user
 * @property-read  FolderEntity[] $entities
 */
class Folder extends AR
{
	public const DEFAULT_SORT_ORDER = 0;

	protected bool $useSoftDelete = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'folder';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'name', 'category'], 'required'],
			[['user_id', 'sort_order'], 'integer'],
			[['name', 'icon'], 'string', 'max' => 64],
			['color', 'string', 'max' => 6],
			['category', 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getEntities(): FolderEntityQuery
	{
		/** @var FolderEntityQuery */
		return $this->hasMany(FolderEntity::class, ['folder_id' => 'id']);
	}

	public static function find(): FolderQuery
	{
		return new FolderQuery(static::class);
	}

	/**
	 * @throws ErrorException
	 */
	public function hasEntityByTypeAndEntityId(string $type, $id): bool
	{
		return $this->getEntities()->byEntityType($type)->byEntityId($id)->exists();
	}
}
