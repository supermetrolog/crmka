<?php

namespace app\services\backuper\databases;

use app\services\backuper\databases\interfaces\DbDumperInterface;
use app\services\backuper\databases\interfaces\DbImporterInterface;
use app\services\backuper\databases\interfaces\RepositoryInterface;
use Exception;

class Backuper
{
    private DbDumperInterface $dbDumper;
    private RepositoryInterface $repo;
    private DbImporterInterface $dbImporter;
    private int $backupOffset;

    public function __construct(DbDumperInterface $dbDumper, RepositoryInterface $repo, DbImporterInterface $dbImporter, $backupOffset = 10)
    {
        $this->dbImporter = $dbImporter;
        $this->dbDumper = $dbDumper;
        $this->repo = $repo;
        $this->backupOffset = $backupOffset;
    }

    public function run(bool $withImport = false): void
    {
        $this->dbDumper->dump();
        $this->repo->createFile($this->dbDumper->getFilename(), $this->dbDumper->getContent());
        $this->removeOldBackups();
        if ($withImport) {
            $result = $this->dbImporter->import($this->dbDumper->getFilename());
            if (!$result) {
                throw new Exception("Import db error");
            }
        }
    }

    private function removeOldBackups(): void
    {
        $stream = $this->repo->getStream();
        $files = [];
        foreach ($stream as $filename) {
            $dbName = substr($filename, 20, -4);
            if ($dbName !== $this->dbDumper->getDbName()) {
                continue;
            }
            $datetime = substr($filename, 0, 19);
            $chunks = explode("_", $datetime);
            $unixtime = strtotime("$chunks[0]-$chunks[1]-$chunks[2] $chunks[3]:$chunks[4]:$chunks[5]");
            $files[] = [$filename, $unixtime];
        }

        if (count($files) < $this->backupOffset) {
            return;
        }

        usort($files, function ($a, $b) {
            if ($a[1] == $b[1]) {
                return 0;
            }
            return ($a[1] < $b[1]) ? 1 : -1;
        });

        for ($i = 0; $i < count($files); $i++) {
            if ($i >= $this->backupOffset) {
                $this->repo->removeFile($files[$i][0]);
            }
        }
    }
}
