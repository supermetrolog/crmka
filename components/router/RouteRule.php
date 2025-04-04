<?php

namespace app\components\router;

use app\components\router\Interfaces\RouteRuleInterface;
use app\helpers\StringHelper;

class RouteRule implements RouteRuleInterface
{
	private ?string $action;
	private string  $pattern;
	private array   $methods;
	private ?string $prefix = null;

	public function __construct(array $methods, string $pattern, ?string $action = null)
	{
		$this->methods = $methods;
		$this->pattern = $pattern;
		$this->action  = $action;
	}

	public function action(string $action): self
	{
		$this->action = $action;

		return $this;
	}

	public function prefix(string $prefix): self
	{
		$this->prefix = $prefix;

		return $this;
	}

	private function getNormalizedPattern(): string
	{
		if (!is_null($this->prefix)) {
			$pattern = $this->prefix . $this->pattern;
		} else {
			$pattern = $this->pattern;
		}


		return trim(preg_replace('#/+#', '/', $pattern), '/');
	}

	private function generatePattern(): string
	{
		$methods = StringHelper::join(StringHelper::SYMBOL_COMMA, ...$this->methods);

		return StringHelper::join(StringHelper::SYMBOL_SPACE, $methods, $this->getNormalizedPattern());
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