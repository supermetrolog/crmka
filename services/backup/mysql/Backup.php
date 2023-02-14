<?php

namespace app\services\backup\mysql;

use app\services\backuper\databases\interfaces\DbDumperInterface;
use Exception;
use Yii;

class Backup implements DbDumperInterface
{
    private $tmpPath;
    private $dbCfg;

    private $filename;
    private $fullpath;

    public function __construct($tmpPath, $dbCfg)
    {
        $this->setTmpPath($tmpPath);
        $this->dbCfg = $dbCfg;
    }
    private function setTmpPath($tmpPath)
    {
        $this->tmpPath = $tmpPath;
        if (is_dir($this->tmpPath)) return;
        mkdir($this->tmpPath, 0700, true);
    }
    public function dump(): void
    {
        $dumpCommand = $this->getMysqlDumpCommand();
        $this->runCommand($dumpCommand);
    }
    private function runCommand($command)
    {
        $output = null;
        $result_code = null;

        exec($command, $output, $result_code);

        if ($result_code !== 0) {
            Yii::error("Command: $command, exited with error status $result_code");
            throw new Exception("Command: $command, exited with error status $result_code");
        }
    }
    public function getFilename(): string
    {
        return $this->filename;
    }
    public function getContent(): string
    {
        return file_get_contents($this->fullpath);
    }
    public function getDbName(): string
    {
        return $this->dbCfg['dbname'];
    }
    public function getBackupFileName()
    {
        return $this->filename;
    }
    public function getFullPath()
    {
        return $this->fullpath;
    }
    private function generateBackupFileName()
    {
        $date = date("Y_m_d_H_i_s");
        $this->filename = $date . "_" . $this->dbCfg['dbname'] . ".sql";
    }
    public function generateFullPath()
    {
        $this->generateBackupFileName();
        $this->fullpath = $this->tmpPath . "/" . $this->filename;
    }
    private function getMysqlDumpCommand()
    {
        $this->generateFullPath();
        if ($this->dbCfg['password'] == '') {
            return sprintf(
                "mysqldump -u%s -h%s %s > %s",
                $this->dbCfg['username'],
                $this->dbCfg['host'],
                $this->dbCfg['dbname'],
                $this->fullpath
            );
        }
        return sprintf(
            "mysqldump -u%s -h%s -p%s %s > %s",
            $this->dbCfg['username'],
            $this->dbCfg['host'],
            $this->dbCfg['password'],
            $this->dbCfg['dbname'],
            $this->fullpath
        );
    }

    public static function getLastDumpFilename(string $tmpDir, string $dbName): ?string
    {
        $files = glob($tmpDir . '/*');
        if ($files === false) {
            return null;
        }
        $needle = null;
        foreach ($files as $fullname) {
            $filename = str_replace($tmpDir . '/', '', $fullname);
            $pos = strpos($filename, $dbName);
            if ($pos !== false) {
                $needle = $filename;
                break;
            }
        }

        return $needle;
    }
}
