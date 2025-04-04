<?php

namespace app\components\router;

use app\components\router\Interfaces\GroupInterface;
use app\helpers\ArrayHelper;
use yii\rest\UrlRule;

class Group implements GroupInterface
{
	/** @var Route[] */
	private array $rules = [];

	private string  $controller;
	private bool    $pluralize = true;
	private ?string $alias     = null;

	public function __construct(string $controller)
	{
		$this->controller = $controller;
	}

	public function addRule(RouteRule $rule): void
	{
		$this->rules[] = $rule;
	}

	public function disablePluralize(): void
	{
		$this->pluralize = false;
	}

	public function build(): array
	{
		$definition = [
			'class'      => UrlRule::class,
			'controller' => $this->generateController(),
			'pluralize'  => $this->pluralize
		];

		if (ArrayHelper::notEmpty($this->rules)) {
			$definition['extraPatterns'] = $this->generatePatterns();
		}

		return $definition;
	}

	public function alias(string $alias): void
	{
		$this->alias = $alias;
	}

	private function generatePatterns(): array
	{
		return ArrayHelper::reduce($this->rules, static function (array $rules, RouteRule $rule) {
			$builtRule = $rule->build();

			$builtRulePattern = key($builtRule);

			$rules[$builtRulePattern] = $builtRule[$builtRulePattern];

			return $rules;
		}, []);
	}

	private function generateController()
	{
		if (is_null($this->alias)) {
			return $this->controller;
		}

		return [$this->alias => $this->controller];
	}

}