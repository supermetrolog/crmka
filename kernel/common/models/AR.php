<?php

declare(strict_types=1);

namespace app\kernel\common\models;

use app\exceptions\domain\model\SaveModelException;
use app\helpers\DbHelper;
use DateTime;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class AR extends ActiveRecord
{
	public const SOFT_DELETE_ATTRIBUTE = 'deleted_at';
	public const SOFT_UPDATE_ATTRIBUTE = 'updated_at';

	protected bool $useSoftDelete = false;
	protected bool $useSoftUpdate = false;


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
	public static function getColumn(string $field): string
	{
		return DbHelper::getDBField(static::dbName(), static::tableName(), $field);
	}

	/**
	 * @throws ErrorException
	 */
	public static function getTable(): string
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
	 * @param bool        $runValidation
	 * @param string|null $attributeNames
	 *
	 * @return bool
	 * @throws ErrorException
	 */
	public function save($runValidation = true, $attributeNames = null): bool
	{
		if ($this->useSoftUpdate && !$this->hasAttribute(self::SOFT_UPDATE_ATTRIBUTE)) {
			throw new ErrorException('Soft update attribute (' . self::SOFT_UPDATE_ATTRIBUTE . ') not exist');
		}

		if ($this->useSoftUpdate) {
			$this->setAttribute(self::SOFT_UPDATE_ATTRIBUTE, (new DateTime())->format('Y-m-d H:i:s'));
		}

		return parent::save($runValidation, $attributeNames);
	}

	/**
	 * @return void
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(): void
	{
		if ($this->useSoftDelete) {
			$this->softDelete();
		} else {
			if (!parent::delete()) {
				throw new ErrorException('Delete model error');
			}
		}
	}

	/**
	 * @return void
	 * @throws ErrorException
	 * @throws SaveModelException
	 */
	protected function softDelete(): void
	{
		if (!$this->hasAttribute(self::SOFT_DELETE_ATTRIBUTE)) {
			throw new ErrorException('Soft delete attribute (' . self::SOFT_DELETE_ATTRIBUTE . ') not exist');
		}

		$this->setAttribute(self::SOFT_DELETE_ATTRIBUTE, (new DateTime())->format('Y-m-d H:i:s'));
		$this->saveOrThrow();
	}

	/**
	 * @return array
	 */
	protected function exceptFields(): array
	{
		return [];
	}

	/**
	 * @return array<string,Closure>
	 */
	protected function addFields(): array
	{
		return [];
	}

	/**
	 * @return array
	 */
	public function fields(): array
	{
		$fields = parent::fields();

		foreach ($this->exceptFields() as $exceptField) {
			unset($fields[$exceptField]);
		}

		foreach ($this->addFields() as $key => $addField) {
			$fields[$key] = $addField;
		}

		return $fields;
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

		return $this->hasOne($class, [$column => $id])->innerJoin(static::getTable(), [
			static::getColumn($id)   => new Expression($class::getColumn($column)),
			static::getColumn($type) => $class::tableName()
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
		            ->andOnCondition([$class::getColumn($type) => static::tableName()]);
	}
}