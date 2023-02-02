<?php

namespace app\services\dbimporter\mysql;

use app\services\backuper\databases\interfaces\DbImporterInterface;
use app\services\dbimporter\mysql\interfaces\CommandExecutorInterface;
use Exception;

class Importer  implements DbImporterInterface
{
    private CommandExecutorInterface $commandExecutor;
    private string $username;
    private string $password;
    private string $dbname;
    private string $workdir;
    public function __construct(CommandExecutorInterface $commandExecutor, string $workdir, string $username, string $password, string $dbname)
    {
        $this->commandExecutor = $commandExecutor;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->workdir = $workdir;
    }
    public function import(string $filename): bool
    {
        $command = $this->getImportCommand($filename);
        $isSuccess = $this->commandExecutor->exec($command);
        if (!$isSuccess) {
            throw new Exception($this->commandExecutor->getLastError());
        }
        return $isSuccess;
    }

    private function getImportCommand(string $filename): string
    {
        $filename = $this->workdir . "/$filename";
        return sprintf(
            "mysql -u%s -p%s %s < %s",
            $this->username,
            $this->password,
            $this->dbname,
            $filename
        );
    }
}
