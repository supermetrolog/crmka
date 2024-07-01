<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Call;
use app\models\Equipment;
use app\models\Relation;
use yii\db\ActiveRecord;

/**
 * This is the ActiveQuery class for [[\app\models\Equipment]].
 *
 * @see Equipment
 */
class EquipmentQuery extends AQ
{

    /**
     * @return Equipment[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return Equipment|ActiveRecord|null
	 */
    public function one($db = null): ?Equipment
    {
        return parent::one($db);
    }

	/**
	 * @return Equipment|ActiveRecord
	 * @throws ModelNotFoundException
	 */
	public function oneOrThrow($db = null): Equipment
	{
		return parent::oneOrThrow($db);
	}


	public function byModelId(int $id): self
	{
		return $this->andWhere([$this->field('model_id') => $id]);
	}

	public function byModelType(string $type): self
	{
		return $this->andWhere([$this->field('model_type') => $type]);
	}

	public function byMorph(int $id, string $type): self
	{
		return $this->byModelType($type)->byModelId($id);
	}

	public function leftJoinLastCallRelation(): self
	{
		$maxIdsSubQuery = Relation::find()
		                          ->select(['MAX(id)'])
		                          ->byFirstType(Equipment::getMorphClass())
		                          ->bySecondType(Call::getMorphClass())
		                          ->groupBy(['first_id', 'first_type']);

		$subQuery = Relation::find()
		                    ->byFirstType(Equipment::getMorphClass())
		                    ->bySecondType(Call::getMorphClass())
		                    ->andWhere(['id' => $maxIdsSubQuery]);

		$this->leftJoin(['last_call_rel' => $subQuery], $this->field('id') . '=' . 'last_call_rel.first_id');

		return $this;
	}
}
