<?php

declare(strict_types=1);

namespace app\kernel\common\models\AR\ManyToManyTrait;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AR\AR;
use Exception;

/**
 * @mixin AR
 */
trait ManyToManyLinkTrait
{
	/**
	 * @param string         $column    - Условное название поля со связью
	 * @param (int|string)[] $relations - Новые связи в виде ID
	 *
	 * Привяжет новые $relations, если таковых не существует уже в текущих связях
	 *
	 * @return void
	 * @throws \yii\db\Exception
	 */
	public function linkManyToManyRelations(string $column, array $relations)
	{
		if (ArrayHelper::empty($relations)) {
			return;
		}

		$relationQuery   = $this->getRelation($column);
		$primaryKeyValue = $this->getPrimaryKey();
		$via             = $relationQuery->via;

		if (ArrayHelper::isArray($via)) {
			$via          = $relationQuery->via[1];
			$viaTableName = $via->modelClass::tableName();
			$viaModel     = $via->primaryModel;
		} else {
			$viaModel = $via->primaryModel;
			[$viaTableName] = ArrayHelper::values($via->from);
		}

		[$junctionColumnName] = ArrayHelper::keys($via->link);
		[$relatedColumnName] = ArrayHelper::values($relationQuery->link);

		$connection  = $this->getDb();
		$transaction = $connection->beginTransaction();

		try {
			$instance      = new $via->modelClass;
			$hasSoftDelete = $instance->hasAttribute(self::SOFT_DELETE_ATTRIBUTE);

			$currentRelationsQuery = $viaModel::find()
			                                  ->from($viaTableName)
			                                  ->select($relatedColumnName)
			                                  ->where([$junctionColumnName => $primaryKeyValue]);
			if ($hasSoftDelete) {
				$currentRelationsQuery->andWhere([self::SOFT_DELETE_ATTRIBUTE => null]);
			}

			$currentRelations = $currentRelationsQuery->column();
			$addedRelations   = ArrayHelper::diff($relations, $currentRelations);

			if (ArrayHelper::notEmpty($addedRelations)) {
				$junctionRows = ArrayHelper::map($addedRelations, function ($relation) use ($primaryKeyValue) {
					return [$primaryKeyValue, $relation];
				});

				$connection->createCommand()
				           ->batchInsert($viaTableName, [$junctionColumnName, $relatedColumnName], $junctionRows)
				           ->execute();

				$this->refresh();
			}

			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}