<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ContactQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\User\User;

/**
 * @property int            $id
 * @property string         $name
 * @property ?string        $slug
 * @property ?string        $short_name
 * @property ?string        $description
 * @property ?string        $icon
 * @property ?string        $color
 * @property ?int           $created_by_id
 * @property int            $sort_order
 * @property bool           $is_active
 * @property string         $created_at
 * @property string         $updated_at
 * @property ?string        $deleted_at
 *
 * @property-read ?User     $createdBy
 * @property-read Contact[] $contacts
 */
class ContactPosition extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public const DEFAULT_SORT_ORDER = 100;

	public static function tableName(): string
	{
		return 'contact_position';
	}

	public function rules(): array
	{
		return [
			[['name', 'sort_order', 'is_active'], 'required'],
			[['created_by_id', 'sort_order'], 'integer'],
			[['name', 'slug', 'icon'], 'string', 'max' => 64],
			[['short_name'], 'string', 'max' => 32],
			[['description'], 'string', 'max' => 128],
			[['color'], 'string', 'max' => 6],
			[['is_active'], 'boolean'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
			[['created_by_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
		];
	}

	public static function find(): AQ
	{
		return new AQ(static::class);
	}

	public function getContacts(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasMany(Contact::class, ['position_id' => 'id']);
	}

	public function getCreatedBy(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'created_by_id']);
	}
}
