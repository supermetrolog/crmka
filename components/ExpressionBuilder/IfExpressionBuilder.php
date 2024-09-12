<?php

namespace app\components\ExpressionBuilder;

use app\helpers\StringHelper;
use InvalidArgumentException;
use yii\base\InvalidValueException;
use yii\db\Expression;

/**
 * Построитель выражения `IF` для SQL запросов с использованием `yii\db\Expression`
 *
 * Пример использования:
 *
 * ```php
 * $expression = IfExpressionBuilder::create()
 *         ->condition('object.count > 10')
 *         ->left('true')
 *         ->right('false')
 *         ->as('is_big')
 *         ->build();
 *  // equals to:
 *  // $expression = new Expression("IF(object.count > 10, true, false) AS is_big");
 * ```
 *
 * @package app\components\ExpressionBuilder
 */
class IfExpressionBuilder extends ExpressionBuilder
{
	private ?string $condition;
	private ?string $trueExpression;
	private ?string $falseExpression;
	private ?string $alias = null;

	public function __construct(?string $condition = null, ?string $trueExpression = null, ?string $falseExpression = null)
	{
		$this->condition       = $condition;
		$this->trueExpression  = $trueExpression;
		$this->falseExpression = $falseExpression;

		parent::__construct();
	}

	/**
	 * Устанавливает условие для оператора `IF`
	 *
	 * @param string $condition
	 *
	 * @return $this
	 */
	public function condition(string $condition): self
	{
		if (StringHelper::empty($condition)) {
			throw new InvalidValueException('Condition expression cannot be empty string');
		}

		$this->condition = $condition;

		return $this;
	}

	/**
	 * Устанавливает выражение для ветки `THEN`
	 *
	 * @param ?string $trueExpression
	 *
	 * @return $this
	 */
	public function left(string $trueExpression): self
	{
		if (StringHelper::empty($trueExpression)) {
			throw new InvalidValueException('True (left) expression cannot be empty string');
		}

		$this->trueExpression = $trueExpression;

		return $this;
	}

	/**
	 * Устанавливает выражение для ветки `ELSE`
	 *
	 * @param ?string $falseExpression
	 *
	 * @return $this
	 */
	public function right(string $falseExpression): self
	{
		if (StringHelper::empty($falseExpression)) {
			throw new InvalidValueException('False (right) expression cannot be empty string');
		}

		$this->falseExpression = $falseExpression;

		return $this;
	}

	/**
	 * Устанавливает псевдоним для выражения
	 *
	 * @param string $alias
	 *
	 * @return $this
	 */
	public function as(string $alias): self
	{
		if (StringHelper::empty($alias)) {
			throw new InvalidValueException('Alias for final expression cannot be empty string');
		}

		$this->alias = $alias;

		return $this;
	}

	/**
	 * Валидирует выражение и выбрасывает исключение, если не все поля установлены
	 *
	 * @return void
	 */
	private function validateOrThrow(): void
	{
		if (is_null($this->condition)) {
			throw new InvalidArgumentException('Condition must be set');
		}

		if (is_null($this->trueExpression)) {
			throw new InvalidArgumentException('True (left) expression must be set');
		}

		if (is_null($this->falseExpression)) {
			throw new InvalidArgumentException('False (right) expression must be set');
		}
	}

	/**
	 * Возвращает созданное выражение без примненения `beforeBuild`
	 *
	 * @return Expression
	 */
	public function getCleanExpression(): Expression
	{
		$this->validateOrThrow();

		return new Expression("IF($this->condition, $this->trueExpression, $this->falseExpression)");
	}

	public function __toString(): string
	{
		$this->validateOrThrow();

		$expression = "IF($this->condition, $this->trueExpression, $this->falseExpression)";
		$string     = ($this->transformFn)($expression);

		if (!is_null($this->alias)) {
			$string .= " AS $this->alias";
		}

		return $string;
	}
}