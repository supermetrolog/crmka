<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\FolderEntityQuery;
use app\models\ActiveQuery\FolderQuery;
use app\models\ActiveQuery\UserQuery;

/**
 * @property int         $id
 * @property int         $folder_id
 * @property float       $sort_order
 * @property string      $created_at
 * @property string      $entity_type
 * @property int         $entity_id
 *
 * @property-read Folder $folder
 * @property-read User   $user
 */
class FolderEntity extends AR
{
	protected bool $useSoftCreate = true;

	public static function tableName(): string
	{
		return 'folder_entity';
	}

	public static function getAvailableTypes(): array
	{
		return [
			Company::getMorphClass(),
			Task::getMorphClass(),
			Request::getMorphClass(),
			OfferMix::getMorphClass()
		];
	}

	public static function getMorphMap(): array
	{
		return [
			Company::getMorphClass()  => Company::class,
			Task::getMorphClass()     => Task::class,
			Request::getMorphClass()  => Request::class,
			OfferMix::getMorphClass() => OfferMix::class
		];
	}

	public function rules(): array
	{
		return [
			[['folder_id'], 'required'],
			[['folder_id', 'entity_id'], 'integer'],
			[['sort_order'], 'double'],
			[['created_at', 'updated_at'], 'safe'],
			[['entity_type'], 'string', 'max' => 255],
			[['folder_id'], 'exist', 'targetClass' => Folder::class, 'targetAttribute' => ['folder_id' => 'id']],
		];
	}

	public static function find(): FolderEntityQuery
	{
		return new FolderEntityQuery(static::class);
	}

	public function getFolder(): FolderQuery
	{
		/** @var FolderQuery */
		return $this->hasOne(Folder::class, ['id' => 'folder_id']);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id'])->via('folder');
	}
}
