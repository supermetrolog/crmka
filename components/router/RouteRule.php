<?php

namespace app\components\router;

use app\helpers\StringHelper;

class RouteRule
{
    private ?string $action;
    private string $pattern;
    private array $methods;

    public function __construct(array $methods, string $pattern, ?string $action = null)
    {
        $this->methods = $methods;
        $this->pattern = $pattern;
        $this->action = $action;
    }

    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    private function generatePattern(): string
    {
        $methods = StringHelper::join(StringHelper::SYMBOL_COMMA, ...$this->methods);
        return StringHelper::join(StringHelper::SYMBOL_SPACE, $methods, $this->pattern);
    }

    private function generateAction(): string
    {
        return $this->action ?? $this->pattern;
    }

    public function build(): array
    {
        return [$this->generatePattern() => $this->generateAction()];
    }
}