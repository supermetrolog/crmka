<?php

namespace app\services\ssh;

use app\services\dbimporter\mysql\interfaces\CommandExecutorInterface;
use LibrasoftFr\SSHConnection\SSHCommand;
use LibrasoftFr\SSHConnection\SSHConnection;

class CommandExecutorAdapter extends SSHConnection implements CommandExecutorInterface
{
    private string $lastError = "";

    public function exec(string $command): bool
    {
        $command = $this->run($command);
        $this->processedResponse($command);
        return !$this->hasError($command);
    }
    public function connect(): self
    {
        return parent::connect();
    }
    private function hasError(SSHCommand $command): bool
    {
        return $command->getError() !== "";
    }
    private function processedResponse(SSHCommand $command): void
    {
        if ($command->getError() !== "") {
            $this->lastError = $command->getError();
        }
    }
    public function getLastError(): string
    {
        return $this->lastError;
    }
}
