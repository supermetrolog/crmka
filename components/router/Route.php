<?php

namespace app\components\router;

use app\components\router\Interfaces\RouteInterface;
use app\helpers\ArrayHelper;
use Closure;
use yii\base\InvalidCallException;

class Route implements RouteInterface
{
	private const prefixSeparator = '/';
	private ?Group  $group   = null;
	private ?string $prefix  = null;
	private bool    $hasCrud = false;

	public static function controller(string $controller): self
	{
		$route = new self();

		$route->group = new Group($controller);

		return $route;
	}

	public function group(Closure $callback): self
	{
		$callback($this);

		return $this;
	}

	public function prefix(string $prefix, Closure $callback): self
	{
		$route        = new self();
		$route->group = $this->group;

		if (!is_null($this->prefix)) {
			$route->prefix = $this->prefix . self::prefixSeparator . $prefix;
		} else {
			$route->prefix = $prefix;
		}

		$callback($route);

		return $this;
	}

	public function alias(string $alias): self
	{
		$this->group->alias($alias);

		return $this;
	}

	public function crud(array $only = []): self
	{
		if ($this->hasCrud) {
			throw new InvalidCallException('Current Route already has CRUD rules');
		}

		$defaults = [
			'create' => ['methods' => [Method::POST, Method::OPTIONS], 'pattern' => '/', 'action' => 'create'],
			'index'  => ['methods' => [Method::GET, Method::OPTIONS], 'pattern' => '/', 'action' => 'index'],
			'update' => ['methods' => [Method::PUT, Method::OPTIONS], 'pattern' => '<id>', 'action' => 'update'],
			'delete' => ['methods' => [Method::DELETE, Method::OPTIONS], 'pattern' => '<id>', 'action' => 'delete'],
			'view'   => ['methods' => [Method::GET, Method::OPTIONS], 'pattern' => '<id>', 'action' => 'view'],
		];

		if (ArrayHelper::notEmpty($only)) {
			foreach ($only as $key) {
				$this->addRule($defaults[$key]['methods'], $defaults[$key]['pattern'], $defaults[$key]['action']);
			}
		} else {
			foreach ($defaults as $key => $value) {
				$this->addRule($value['methods'], $value['pattern'], $value['action']);
			}
		}

		$this->hasCrud = true;

		return $this;
	}

	public function disablePluralize(): self
	{
		$this->group->disablePluralize();

		return $this;
	}

	public function addRule(array $methods, string $pattern, ?string $action = null): RouteRule
	{
		$rule = new RouteRule($methods, $pattern, $action);

		if (!is_null($this->prefix)) {
			$rule->prefix($this->prefix);
		}

		$this->group->addRule($rule);

		return $rule;
	}

	public function get(string $pattern = '/', ?string $action = null): RouteRule
	{
		return $this->addRule([Method::GET, Method::OPTIONS], $pattern, $action);
	}

	public function post(string $pattern = '/', ?string $action = null): RouteRule
	{
		return $this->addRule([Method::POST, Method::OPTIONS], $pattern, $action);
	}

	public function put(string $pattern = '/', ?string $action = null): RouteRule
	{
		return $this->addRule([Method::PUT, Method::OPTIONS], $pattern, $action);
	}

	public function patch(string $pattern = '/', ?string $action = null): RouteRule
	{
		return $this->addRule([Method::PATCH, Method::OPTIONS], $pattern, $action);
	}

	public function delete(string $pattern = '/', ?string $action = null): RouteRule
	{
		return $this->addRule([Method::DELETE, Method::OPTIONS], $pattern, $action);
	}

	public function build(): array
	{
		return $this->group->build();
	}
}