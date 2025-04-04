<?php

namespace app\components\router\Interfaces;

interface RouterInterface
{
	public function controller(string $controller): RouteInterface;

	public function build(): array;
}