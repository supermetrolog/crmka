<?php

namespace app\components\router;

use app\helpers\ArrayHelper;
use yii\rest\UrlRule;

class Group
{
    /** @var Route[] */
    private array $rules = [];

    private array $except = [];

    private string $controller;
    private bool $pluralize = true;
    private ?string $alias = null;

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
        return [
            'class' => UrlRule::class,
            'except' => $this->except,
            'controller' => $this->generateController(),
            'extraPatterns' => $this->generatePatterns(),
            'pluralize' => $this->pluralize
        ];
    }

    public function alias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function crud(): void
    {
        $this->addRule(Route::get('/', 'index'));
        $this->addRule(Route::get('<id>', 'view'));
        $this->addRule(Route::post('/', 'create'));
        $this->addRule(Route::put('<id>', 'update'));
        $this->addRule(Route::delete('<id>', 'delete'));
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