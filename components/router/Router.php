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

	public function cacheTo(string $path): void
	{
		$routes = $this->build();

		$export = var_export($routes, true);

		$content = <<<PHP
<?php

// Автосгенерированный кеш маршрутов

return {$export};

PHP;

		file_put_contents($path, $content);
	}

	public function toOpenApi(): void
	{
		// TODO: Сделать генерацию OpenAPI
	}
}