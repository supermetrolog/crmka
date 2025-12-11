<?php

namespace app\models;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\AttributeQuery;
use app\models\ActiveQuery\AttributeRelationEntityQuery;
use app\models\location\Location;
use yii\db\ActiveQuery;

/**
 * @property int            $id
 * @property int            $attribute_id
 * @property int            $entity_id
 * @property string         $entity_type
 * @property string         $created_at
 * @property ?string        $updated_at
 * @property ?string        $deleted_at
 *
 * @property-read Attribute $attributeRel
 * @property-read Location  $location
 */
class AttributeRelationEntity extends AR
{
	protected bool $useSoftCreate = true;
	protected bool $useSoftUpdate = true;
	protected bool $useSoftDelete = true;

	public static function tableName(): string
	{
		return 'attribute_relation_entity';
	}

	public static function getAvailableEntityTypes(): array
	{
		// TODO: attribute | new Location class
		return [
			'location',
			// Location::getMorphClass(),
		];
	}

	public static function getEntityMorphMap(): array
	{
		return [
			'location' => Location::class,
			// Location::getMorphClass() => Location::class,
		];
	}

	public function rules(): array
	{
		return [
			[['attribute_id', 'entity_id', 'entity_type'], 'required'],
			[['attribute_id', 'entity_id'], 'integer'],
			['attribute_id', 'exist', 'targetClass' => Attribute::class, 'targetAttribute' => ['attribute_id' => 'id']],
			[['entity_type'], 'string'],
			[['created_at', 'updated_at', 'deleted_at'], 'safe'],
		];
	}

	public function getAttributeRel(): AttributeQuery
	{
		/** @var AttributeQuery */
		return $this->hasOne(Attribute::class, ['id' => 'attribute_id']);
	}

	public function morphBelongTo($class, string $column = 'id', string $morphColumn = 'entity', string $ownerColumn = 'morph'): ActiveQuery
	{
		return parent::morphBelongTo($class, $column, $morphColumn);
	}

	public function getLocation(): AQ
	{
		/** @var AQ */
		return $this->morphBelongTo(Location::class);
	}

	public static function find(): AttributeRelationEntityQuery
	{
		return new AttributeRelationEntityQuery(self::class);
	}
}