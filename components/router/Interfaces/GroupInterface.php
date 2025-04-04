<?php

namespace app\components\router\Interfaces;

use app\components\router\RouteRule;

interface GroupInterface
{
	public function __construct(string $controller);

	public function addRule(RouteRule $rule): void;

	public function disablePluralize(): void;

	public function alias(string $alias): void;

	public function build(): array;
}