<?php

namespace app\components\router\Interfaces;

interface RouteRuleInterface
{
	public function __construct(array $methods, string $pattern, ?string $action = null);

	public function action(string $action): self;

	public function prefix(string $prefix): self;

	public function build(): array;
}