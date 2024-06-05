<?php

namespace app\models\ActiveQuery;

use app\kernel\common\models\AQ\AQ;
use app\models\ChatMember;
use yii\db\ActiveRecord;

/**
 * @see ChatMember
 */
class ChatMemberQuery extends AQ
{

    /**
     * @return ChatMember[]|ActiveRecord[]
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

	/**
	 * @return ChatMember|ActiveRecord|null
	 */
    public function one($db = null): ?ChatMember
    {
        return parent::one($db);
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
}
