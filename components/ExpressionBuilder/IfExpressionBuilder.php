<?php

namespace app\components\ExpressionBuilder;

use Closure;
use yii\db\Expression;

/**
 * Построитель выражения `IF` для SQL запросов с использованием `yii\db\Expression`
 *
 * Пример использования:
 *
 * ```php
 * $expression = IfExpressionBuilder::create()
 *         ->condition('object.count > 10')
 *         ->left(1)
 *         ->right(0)
 *         ->as('is_big')
 *         ->build();
 *  // equals to:
 *  // $expression = new Expression("IF(object.count > 10, 1, 0) AS is_big");
 * ```
 *
 * @package app\components\ExpressionBuilder
 */
class IfExpressionBuilder
{
	private           $condition;
	private           $trueExpression;
	private           $falseExpression;
	private ?string   $alias = null;
	protected Closure $transformFn;

	public function __construct($condition = null, $trueExpression = 1, $falseExpression = 0)
	{
		$this->condition       = $condition;
		$this->trueExpression  = $trueExpression;
		$this->falseExpression = $falseExpression;
		$this->transformFn     = fn($expression) => $expression;
	}

	/**
	 * Создает новый объект `IfExpressionBuilder`
	 *
	 * @param mixed ...$argv
	 *
	 * @return static
	 */
	public static function create(...$argv): self
	{
		return new static(...$argv);
	}

	/**
	 * Устанавливает условие для оператора `IF`
	 *
	 * @param $condition
	 *
	 * @return $this
	 */
	public function condition($condition): self
	{
		$this->condition = $condition;

		return $this;
	}

	/**
	 * Устанавливает выражение для ветки `THEN`
	 *
	 * @param $trueExpression
	 *
	 * @return $this
	 */
	public function left($trueExpression): self
	{
		$this->trueExpression = $trueExpression;

		return $this;
	}

	/**
	 * Устанавливает выражение для ветки `ELSE`
	 *
	 * @param $falseExpression
	 *
	 * @return $this
	 */
	public function right($falseExpression): self
	{
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
		$this->alias = $alias;

		return $this;
	}

	/**
	 * Устанавливает функцию для обработки выражения перед созданием объекта `Expression`
	 *
	 * @param callable $transformFn
	 *
	 * @return $this
	 */
	public function beforeBuild(callable $transformFn): self
	{
		$this->transformFn = $transformFn;

		return $this;
	}

	/**
	 * Возвращает созданное выражение без примненения `beforeBuild`
	 *
	 * @return Expression
	 */
	public function getCleanExpression(): Expression
	{
		return new Expression("IF($this->condition, $this->trueExpression, $this->falseExpression)");
	}

	/**
	 * Возвращает строковое представление выражения
	 *
	 * @return string
	 */
	public function toString(): string
	{
		$expression = "IF($this->condition, $this->trueExpression, $this->falseExpression)";
		$string     = ($this->transformFn)($expression);

		if (!is_null($this->alias)) {
			$string .= " AS $this->alias";
		}

		return $string;
	}

	/**
	 * Возвращает созданное выражение
	 *
	 * @return Expression
	 */
	public function build(): Expression
	{
		return new Expression($this->toString());
	}
}