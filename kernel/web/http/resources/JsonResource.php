<?php

declare(strict_types=1);

namespace app\kernel\web\http\resources;

abstract class JsonResource
{
	abstract public function toArray(): array;
}