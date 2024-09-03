<?php

declare(strict_types=1);

namespace app\kernel\common\models\AR\ManyToManyTrait;

use app\helpers\ArrayHelper;
use app\kernel\common\models\AR\AR;
use Exception;
use yii\db\Expression;

/**
 * @mixin AR
 */
trait ManyToManyUnlinkTrait
{
	/**
	 * @param string         $column    - Условное название поля со связью
	 * @param (int|string)[] $relations - Связи в виде ID
	 *
	 * Отвяжет существующие $relations, если они привязаны к модели
	 *
	 * @return void
	 * @throws \yii\db\Exception
	 */
	public function unlinkManyToManyRelations(string $column, array $relations)
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
			$hasSoftDelete = $viaModel->hasAttribute(self::SOFT_DELETE_ATTRIBUTE);

			$currentRelationsQuery = $viaModel::find()
			                                  ->from($viaTableName)
			                                  ->select($relatedColumnName)
			                                  ->where([$junctionColumnName => $primaryKeyValue]);
			if ($hasSoftDelete) {
				$currentRelationsQuery->andWhere([self::SOFT_DELETE_ATTRIBUTE => null]);
			}

			$currentRelations = $currentRelationsQuery->column();

			if (ArrayHelper::notEmpty($currentRelations)) {
				$deletedRelations = ArrayHelper::intersect($currentRelations, $relations);

				if (ArrayHelper::notEmpty($deletedRelations)) {
					if ($hasSoftDelete) {
						$connection->createCommand()
						           ->update($viaTableName, [self::SOFT_DELETE_ATTRIBUTE => new Expression('NOW()')], [
							           $junctionColumnName         => $primaryKeyValue,
							           $relatedColumnName          => $deletedRelations,
							           self::SOFT_DELETE_ATTRIBUTE => null,
						           ])
						           ->execute();
					} else {
						$connection->createCommand()
						           ->delete($viaTableName, [
							           $junctionColumnName => $primaryKeyValue,
							           $relatedColumnName  => $deletedRelations
						           ])
						           ->execute();
					}

					$this->refresh();
				}
			}

			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}