<?php

namespace app\components\router;

use app\helpers\ArrayHelper;
use Exception;
use Yii;
use yii\base\InvalidConfigException;

class Route
{
    /** @var Group[] */
    private static array $groups = [];
    private static ?string $currentController = null;
    private static ?string $prefix = null;

    /**
     * @throws InvalidConfigException
     */
    private function getCurrentGroup(): Group
    {
        if (is_null(self::$currentController)) {
            throw new InvalidConfigException('Current controller is not set');
        }

        return self::$groups[self::$currentController];
    }

    public static function controller(string $controller): self
    {
        self::$currentController = $controller;

        self::$groups[$controller] = new Group($controller);

        return new self();
    }

    public function group(callable $callback): void
    {
        try {
            $callback();

            self::$currentController = null;
        } catch (InvalidConfigException $th) {
            Yii::error('Route::group() error: ' . $th->getMessage());
        }
    }

    public function crud(): self
    {
        $this->getCurrentGroup()->crud();

        return $this;
    }

    public function alias(string $alias): self
    {
        $this->getCurrentGroup()->alias($alias);

        return $this;
    }

    public function disablePluralize(): self
    {
        $this->getCurrentGroup()->disablePluralize();

        return $this;
    }

    public static function buildTree(): array
    {
        return ArrayHelper::map(self::$groups, static function (Group $group) {
            return $group->build();
        });
    }

    /**
     * @throws Exception
     */
    public static function addRoute(array $methods, string $pattern, ?string $action = null): RouteRule
    {
        if (is_null(self::$currentController)) {
            throw new InvalidConfigException('Route::addRoute() must be called inside Route::group()');
        }

        $rule = new RouteRule($methods, self::normalizePattern($pattern), $action);

        self::$groups[self::$currentController]->addRule($rule);

        return $rule;
    }

    private static function normalizePattern(string $pattern): string
    {
        if (is_null(self::$prefix)) {
            return $pattern;
        }

        return self::$prefix . $pattern;
    }

    /**
     * @throws Exception
     */
    public static function get(string $pattern, ?string $action = null): RouteRule
    {
        return self::addRoute([Method::GET, Method::OPTIONS], $pattern, $action);
    }

    /**
     * @throws Exception
     */
    public static function post(string $pattern, ?string $action = null): RouteRule
    {
        return self::addRoute([Method::POST, Method::OPTIONS], $pattern, $action);
    }

    /**
     * @throws Exception
     */
    public static function put(string $pattern, ?string $action = null): RouteRule
    {
        return self::addRoute([Method::PUT, Method::OPTIONS], $pattern, $action);
    }

    /**
     * @throws Exception
     */
    public static function patch(string $pattern, ?string $action = null): RouteRule
    {
        return self::addRoute([Method::PATCH, Method::OPTIONS], $pattern, $action);
    }

    /**
     * @throws Exception
     */
    public static function delete(string $pattern, ?string $action = null): RouteRule
    {
        return self::addRoute([Method::DELETE, Method::OPTIONS], $pattern, $action);
    }

    public static function prefix(string $prefix, callable $callback): void
    {
        self::$prefix = $prefix;

        $callback();

        self::$prefix = null;
    }
}