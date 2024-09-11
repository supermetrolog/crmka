<?php

namespace app\components\ExpressionBuilder;

use Closure;
use yii\db\Expression;

/**
 * Построитель выражений для SQL запросов с использованием `yii\db\Expression`
 *
 * @package app\components\ExpressionBuilder
 */
class ExpressionBuilder
{
	protected Closure $transformFn;

	public function __construct()
	{
		$this->transformFn = fn($expression) => $expression;
	}

	/**
	 * Создает новый объект `ExpressionBuilder`
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
	 * Возвращает строковое представление выражения
	 *
	 * @return string
	 */
	public function toString(): string
	{
		return ($this->transformFn)();
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