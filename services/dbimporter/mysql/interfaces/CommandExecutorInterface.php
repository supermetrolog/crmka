<?php


namespace app\services\dbimporter\mysql\interfaces;

interface CommandExecutorInterface
{
    public function exec(string $command): bool;
    public function getLastError(): string;
}
