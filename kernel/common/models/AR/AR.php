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
use yii\db\Expression;
use yii\db\StaleObjectException;

class AR extends ActiveRecord
{
	public const SOFT_DELETE_ATTRIBUTE = 'deleted_at';
	public const SOFT_CREATE_ATTRIBUTE = 'created_at';
	public const SOFT_UPDATE_ATTRIBUTE = 'updated_at';

	protected bool $useSoftDelete = false;
	protected bool $useSoftUpdate = false;
	protected bool $useSoftCreate = false;

	protected bool $useUnixSoftCreate = false;
	protected bool $useUnixSoftUpdate = false;


	public function formName(): string
	{
		return '';
	}

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
		if (($this->useSoftUpdate || $this->useUnixSoftUpdate) && !$this->hasAttribute(self::SOFT_UPDATE_ATTRIBUTE)) {
			throw new ErrorException('Soft update attribute (' . self::SOFT_UPDATE_ATTRIBUTE . ') not exist');
		}

		if ($this->useUnixSoftUpdate && $this->useSoftUpdate) {
			throw new ErrorException('You can not use useUnixSoftUpdate and useSoftUpdate at the same time');
		}

		if ($this->useSoftUpdate) {
			$this->setAttribute(self::SOFT_UPDATE_ATTRIBUTE, DateTimeHelper::nowf());
		}

		if ($this->useUnixSoftUpdate) {
			$this->setAttribute(self::SOFT_UPDATE_ATTRIBUTE, DateTimeHelper::unix());
		}

		if ($this->isNewRecord) {
			if (($this->useSoftCreate || $this->useUnixSoftCreate) && !$this->hasAttribute(self::SOFT_CREATE_ATTRIBUTE)) {
				throw new ErrorException('Soft create attribute (' . self::SOFT_CREATE_ATTRIBUTE . ') not exist');
			}

			if ($this->useUnixSoftCreate && $this->useSoftCreate) {
				throw new ErrorException('You can not use useUnixSoftCreate and useSoftCreate at the same time');
			}

			if ($this->useSoftCreate) {
				$this->setAttribute(self::SOFT_CREATE_ATTRIBUTE, DateTimeHelper::nowf());
			}

			if ($this->useUnixSoftCreate) {
				$this->setAttribute(self::SOFT_CREATE_ATTRIBUTE, DateTimeHelper::unix());
			}
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
	 * @throws SaveModelException
	 * @throws ErrorException
	 */
	public function restore(): void
	{
		if (!$this->useSoftDelete) {
			throw new ErrorException('Model not use soft delete');
		}

		if (!$this->hasAttribute(self::SOFT_DELETE_ATTRIBUTE)) {
			throw new ErrorException('Soft delete attribute (' . self::SOFT_DELETE_ATTRIBUTE . ') not exist');
		}

		$this->setAttribute(self::SOFT_DELETE_ATTRIBUTE, null);
		$this->saveOrThrow();
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
	 * @param string|AR $class
	 * @param string    $column
	 * @param string    $name
	 * @param string    $localColumn
	 *
	 * @return ActiveQuery
	 * @throws ErrorException
	 */
	public function morphHasOneVia(string $class, string $column = 'id', string $name = 'model', string $localColumn = 'morph'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasOne($class, [
			$column      => $id,
			$localColumn => $type,
		])->from([$class::tableName() => $class::getTable()]);
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
	public function morphHasMany(string $class, string $column = 'id', string $name = 'model', string $localColumn = 'morph'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasMany($class, [
			$id   => $column,
			$type => $localColumn
		])->from([$class::tableName() => $class::getTable()]);
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
	public function morphHasManyVia(string $class, string $column = 'id', string $name = 'model', string $localColumn = 'morph'): ActiveQuery
	{
		$type = $name . '_type';
		$id   = $name . '_id';

		return $this->hasMany($class, [
			$column      => $id,
			$localColumn => $type
		])->from([$class::tableName() => $class::getTable()]);
	}

	/**
	 * @param array      $insertColumns
	 * @param array|bool $updateColumns
	 *
	 * @return void
	 * @throws \yii\db\Exception
	 */
	public static function upsert(array $insertColumns, $updateColumns = true): void
	{
		self::getDb()->createCommand()->upsert(static::tableName(), $insertColumns, $updateColumns)->execute();
	}

	/**
	 * @throws ErrorException
	 */
	public static function field(string $name): string
	{
		return static::getColumn($name);
	}

	/**
	 * Returns the expression object representing the SQL of the field.
	 *
	 * @param string $name
	 *
	 * @return Expression
	 * @throws ErrorException
	 */
	public static function xfield(string $name): Expression
	{
		return new Expression(static::field($name));
	}

	public static function getMorphClass(): string
	{
		return static::tableName();
	}

	public function isDeleted(): bool
	{
		if ($this->useSoftDelete && $this->hasAttribute(self::SOFT_DELETE_ATTRIBUTE)) {
			return !is_null($this->getAttribute(self::SOFT_DELETE_ATTRIBUTE));
		}

		return false;
	}
}