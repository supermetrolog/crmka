<?php

declare(strict_types=1);

namespace app\components;

class ConsoleLogger
{
    public static function info($message): void
    {
        if (is_string($message)) {
            echo $message . "\n";
            return;
        }

        if (is_numeric($message)) {
            echo $message . "\n";
            return;
        }

        var_dump($message);
    }
}
