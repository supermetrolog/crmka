<?php

namespace app\services\backup;

use Exception;
use Yii;

class Backup
{
    private $tmpPath;
    private $dbCfg;

    private $filename;
    private $fullpath;

    public function __construct($tmpPath, $dbCfg)
    {
        $this->tmpPath = $tmpPath;
        $this->dbCfg = $dbCfg;
    }

    public function dump()
    {
        $dumpCommand = $this->getMysqlDumpCommand();
        $this->runCommand($dumpCommand);
        return $this;
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
        $dbname = $this->getDsnAttribute("dbname", $this->dbCfg['dsn']);
        $date = date("Y_m_d_H_i_s");
        $this->filename = $date . "_" . $dbname . ".sql";
    }
    public function generateFullPath()
    {
        $this->generateBackupFileName();
        $this->fullpath = $this->tmpPath . "/" . $this->filename;
    }
    private function getMysqlDumpCommand()
    {
        $dbname = $this->getDsnAttribute("dbname", $this->dbCfg['dsn']);
        $host = $this->getDsnAttribute("host", $this->dbCfg['dsn']);
        $this->generateFullPath();
        if ($this->dbCfg['password'] == '') {
            return sprintf(
                "mysqldump -u%s -h%s %s > %s",
                $this->dbCfg['username'],
                $host,
                $dbname,
                $this->fullpath
            );
        }
        return sprintf(
            "mysqldump -u%s -h%s -p%s %s > %s",
            $this->dbCfg['username'],
            $host,
            $this->dbCfg['password'],
            $dbname,
            $this->fullpath
        );
    }
    private function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}
