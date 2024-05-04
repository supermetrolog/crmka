<?php

declare(strict_types=1);

namespace app\kernel\common\models\AR;

use app\helpers\DateTimeHelper;
use app\helpers\DbHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

class AR extends ActiveRecord
{
	public const SOFT_DELETE_ATTRIBUTE = 'deleted_at';
	public const SOFT_CREATE_ATTRIBUTE = 'created_at';
	public const SOFT_UPDATE_ATTRIBUTE = 'updated_at';

	protected bool $useSoftDelete = false;
	protected bool $useSoftUpdate = false;
	protected bool $useSoftCreate = false;


	/**
	 * @throws ErrorException
	 */
	public static function dbName(): string
	{
		return DbHelper::getDbName(static::getDb());
	}

	/**
	 * @throws ErrorException
	 */
	public static function getColumn(string $field): string
	{
		return DbHelper::getFullPathAR(static::class, $field);
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
		$errors = $this->getFirstErrors();

		return array_pop($errors);
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
		} catch (SaveModelException $th) {
			throw $th;
		} catch (Throwable $th) {
			throw new SaveModelException($this, $th);
		}
	}

	/**
	 * @throws ValidateException
	 */
	public function validateOrThrow(?array $attributes = null, bool $clearError = true): void
	{
		if (!$this->validate($attributes, $clearError)) {
			throw new ValidateException($this);
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
			$this->setAttribute(self::SOFT_UPDATE_ATTRIBUTE, DateTimeHelper::nowf());
		}

		if ($this->useSoftCreate && !$this->hasAttribute(self::SOFT_CREATE_ATTRIBUTE)) {
			throw new ErrorException('Soft create attribute (' . self::SOFT_CREATE_ATTRIBUTE . ') not exist');
		}

		if ($this->useSoftCreate) {
			$this->setAttribute(self::SOFT_CREATE_ATTRIBUTE, DateTimeHelper::nowf());
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

		$this->setAttribute(self::SOFT_DELETE_ATTRIBUTE, DateTimeHelper::nowf());

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
	 * @param string    $ownerColumn
	 *
	 * @return ActiveQuery
	 */
	public function morphBelongTo(string $class, string $column = 'id', string $name = 'model', string $ownerColumn = 'morph'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasOne($class, [
			$column      => $id,
			$ownerColumn => $type
		]);
	}

	/**
	 * @param string|AR $class
	 * @param string    $column
	 * @param string    $name
	 * @param string    $localColumn
	 *
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function morphHasOne(string $class, string $column = 'id', string $name = 'model', string $localColumn = 'morph'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasOne($class, [
			$id   => $column,
			$type => $localColumn
		])->from([$class::tableName() => $class::getTable()]);
	}

	/**
	 * @param string     $tableName
	 * @param array      $insertColumns
	 * @param array|bool $updateColumns
	 *
	 * @return void
	 * @throws \yii\db\Exception
	 */
	public static function upsert(string $tableName, array $insertColumns, $updateColumns = true): void
	{
		self::getDb()->createCommand()->upsert($tableName, $insertColumns, $updateColumns)->execute();
	}

	/**
	 * @throws ErrorException
	 */
	public static function field(string $name): string
	{
		return static::getColumn($name);
	}

	/**
	 * @throws ErrorException
	 */
	public static function getMorphClass(): string
	{
		throw new ErrorException(__FUNCTION__ . ' must be implements');
	}
}