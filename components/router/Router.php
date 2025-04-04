<?php

namespace app\components\router;

use app\components\router\Interfaces\RouterInterface;
use app\helpers\ArrayHelper;

class Router implements RouterInterface
{
	/** @var Route[] */
	private array $routes = [];

	public function controller(string $controller): Route
	{
		$route = Route::controller($controller);

		$this->routes[] = $route;

		return $route;
	}

	public function build(): array
	{
		return ArrayHelper::map($this->routes, static function (Route $route) {
			return $route->build();
		});
	}

	public function toOpenApi(): void
	{
		// TODO: Сделать генерацию OpenAPI
	}
}