<?php

namespace app\components\avito;

class AvitoObject
{
    public string $name;

    /**
     * @var string|array
     */
    public $value;

    /**
     * @param string $name
     * @param string|array $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}