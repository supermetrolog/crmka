<?php

namespace app\models\ActiveQuery;

use app\components\ExpressionBuilder\CompareExpressionBuilder;
use app\helpers\SQLHelper;
use app\kernel\common\models\AQ\AQ;
use app\models\Call;
use app\models\ChatMember;
use app\models\Objects;
use app\models\Relation;
use app\models\Request;
use app\models\views\ChatMemberSearchView;
use yii\base\ErrorException;
use yii\db\ActiveRecord;

/**
 * @see ChatMember
 */
class ChatMemberQuery extends AQ
{

	/**
	 * @return ChatMember[]|ChatMemberSearchView[]|ActiveRecord[]
	 */
	public function all($db = null): array
	{
		return parent::all($db);
	}

	/**
	 * @return ChatMember|ChatMemberSearchView|ActiveRecord|null
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

	public function byModelTypes(array $types): self
	{
		return $this->andWhere([$this->field('model_type') => $types]);
	}

	public function byMorph(int $id, string $type): self
	{
		return $this->byModelType($type)->byModelId($id);
	}

	public function leftJoinLastCallRelation(): self
	{
		$maxIdsSubQuery = Relation::find()
		                          ->select(['MAX(id)'])
		                          ->byFirstType(ChatMember::getMorphClass())
		                          ->bySecondType(Call::getMorphClass())
		                          ->groupBy(['first_id', 'first_type']);

		$subQuery = Relation::find()
		                    ->byFirstType(ChatMember::getMorphClass())
		                    ->bySecondType(Call::getMorphClass())
		                    ->andWhere(['id' => $maxIdsSubQuery]);

		$this->leftJoin(['last_call_rel' => $subQuery], $this->field('id') . '=' . 'last_call_rel.first_id');

		return $this;
	}

	/**
	 * @throws ErrorException
	 */
	public function needCalling(): self
	{
		$interval = 'DATE_SUB(NOW(), INTERVAL 1 YEAR)';

		return $this->andWhere([
				'or',
				CompareExpressionBuilder::create()
				                        ->left('last_call_rel.created_at')
				                        ->lower()
				                        ->right($interval)
				                        ->build(),
				[
					'and',
					['last_call_rel.created_at' => null],
					CompareExpressionBuilder::create()
					                        ->left(SQLHelper::fromUnixTime(Objects::field('last_update')))
					                        ->lower()
					                        ->right($interval)
					                        ->build()
				],
				[
					'and',
					['last_call_rel.created_at' => null],
					CompareExpressionBuilder::create()
					                        ->left(Request::field('updated_at'))
					                        ->lower()
					                        ->right($interval)
					                        ->build()
				]
			]
		);
	}
}
