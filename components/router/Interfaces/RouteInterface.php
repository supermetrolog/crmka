<?php

namespace app\components\router\Interfaces;

use Closure;

interface RouteInterface
{
	public static function controller(string $controller): self;

	public function group(Closure $callback): self;

	public function prefix(string $prefix, Closure $callback): self;

	public function crud(array $only = []): self;

	public function alias(string $alias): self;

	public function disablePluralize(): self;

	public function addRule(array $methods, string $pattern, ?string $action = null);

	public function get(string $pattern, ?string $action = null): RouteRuleInterface;

	public function post(string $pattern, ?string $action = null): RouteRuleInterface;

	public function put(string $pattern, ?string $action = null): RouteRuleInterface;

	public function patch(string $pattern, ?string $action = null): RouteRuleInterface;

	public function delete(string $pattern, ?string $action = null): RouteRuleInterface;

	public function build(): array;
}
