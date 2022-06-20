<?php

namespace app\services\backup;

use Exception;
use Yii;

class Backup
{
    private $tmpPath;
    private $dbCfg;
    public function __construct($tmpPath, $dbCfg)
    {
        $this->tmpPath = $tmpPath;
        $this->dbCfg = $dbCfg;
    }

    public function dump()
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
    private function getMysqlDumpCommand()
    {
        $dbname = $this->getDsnAttribute("dbname", $this->dbCfg['dsn']);
        $host = $this->getDsnAttribute("host", $this->dbCfg['dsn']);
        if ($this->dbCfg['password'] == '') {
            return sprintf(
                "mysqldump -u%s -h%s %s > %s",
                $this->dbCfg['username'],
                $host,
                $dbname,
                $this->tmpPath . "/$dbname.sql"
            );
        }
        return sprintf(
            "mysqldump -u%s -h%s -p%s %s > %s",
            $this->dbCfg['username'],
            $host,
            $this->dbCfg['password'],
            $dbname,
            $this->tmpPath . "/$dbname.sql"
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
