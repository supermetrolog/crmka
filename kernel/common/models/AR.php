<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use app\exceptions\domain\model\SaveModelException;
use app\helpers\DbHelper;
use app\models\ChatMember;
use app\models\User;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class AR extends ActiveRecord
{
	/**
	 * @throws ErrorException
	 */
	public static function dbName(): string
	{
		return DbHelper::getDBName(static::getDb()->dsn);
	}

	/**
	 * @throws ErrorException
	 */
	public static function getField(string $field): string
	{
		return DbHelper::getDBField(static::dbName(), static::tableName(), $field);
	}

	/**
	 * @throws ErrorException
	 */
	public static function getTablePath(): string
	{
		return DbHelper::getTablePath(static::dbName(), static::tableName());
	}

	/**
	 * @throws Exception
	 */
	public function getAnyError(): ?string
	{
		return ArrayHelper::getValue($this->getFirstErrors(), 0);
	}

	/**
	 * @throws SaveModelException
	 */
	public function saveOrThrow(bool $runValidation = true): void
	{
		try {
			if (!$this->save($runValidation)) {
				throw new SaveModelException($this);
			}
		} catch (Throwable $th) {
			throw new SaveModelException($this, $th);
		}
	}

	/**
	 * @param string|AR $class
	 * @param string    $column
	 * @param string    $name
	 *
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function morphBelongTo(string $class, string $column = 'id', string $name = 'model'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasOne($class, [$column => $id])->innerJoin(static::getTablePath(), [
			static::getField($id)   => new Expression($class::getField($column)),
			static::getField($type) => $class::tableName()
		]);
	}

	/**
	 * @param string|AR $class
	 * @param string    $column
	 * @param string    $name
	 *
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function morphHasOne(string $class, string $column = 'id', string $name = 'model'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasOne($class, [$id => $column])
		            ->andOnCondition([$class::getField($type) => static::tableName()]);
	}
}