<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\services\backup\mysql\Backup;
use app\services\backuper\databases\Backuper;
use app\services\dbimporter\mysql\Importer;
use app\services\filemanager\FileManager;
use app\services\ftpclient\FtpClient;
use app\services\ssh\CommandExecutorAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Yii;
use yii\console\Controller;

class BackupController extends Controller
{
    public function actionIndex()
    {
        $this->actionClients();
        $this->actionObjects();
    }

    public function actionImport()
    {
        $this->actionImportClients();
        $this->actionImportObjects();
    }

    public function actionClients()
    {
        try {
            $params = Yii::$app->params;
            $dump_tmp_dir = $params['db_backup']['db']['dump_tmp_dir'];
            $dbConfig = include $params['db_backup']['db']['db_config_path'];

            $backup = new Backup($dump_tmp_dir, [
                'dbname' => $this->getDsnAttribute('dbname', $dbConfig['dsn']),
                'host' => $this->getDsnAttribute('host', $dbConfig['dsn']),
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
            ]);

            $repo = FtpClient::getInstance(FtpConnectionOptions::fromArray($params['db_backup']['ftp_client_options']));

            $ssh = new CommandExecutorAdapter();
            $cmdExecutor = $ssh->to($params['ssh']['reserve_server']['host'])
                ->as($params['ssh']['reserve_server']['username'])
                ->withPassword($params['ssh']['reserve_server']['password'])
                ->connect();

            $importerDbConfig = include $params['db_importer']['db']['db_config_path'];

            $importer = new Importer($cmdExecutor, $params['db_importer']['workdir'], $importerDbConfig['username'], $importerDbConfig['password'], $this->getDsnAttribute("dbname", $importerDbConfig['dsn']));

            $backuper = new Backuper($backup, $repo, $importer);
            $backuper->run(false);
            FileManager::UnlinkFiles($dump_tmp_dir, [$backup->getFilename()]);
        } catch (\Throwable $th) {
            FileManager::UnlinkFiles($dump_tmp_dir, [$backup->getFilename()]);
            throw $th;
        }
    }

    public function actionObjects()
    {
        try {
            $params = Yii::$app->params;
            $dump_tmp_dir = $params['db_backup']['db_old']['dump_tmp_dir'];
            $dbConfig = include $params['db_backup']['db_old']['db_config_path'];
            $importerDbConfig = include $params['db_importer']['db_old']['db_config_path'];

            $backup = new Backup($dump_tmp_dir, [
                'dbname' => $this->getDsnAttribute('dbname', $dbConfig['dsn']),
                'host' => $this->getDsnAttribute('host', $dbConfig['dsn']),
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
            ]);

            $repo = FtpClient::getInstance(FtpConnectionOptions::fromArray($params['db_backup']['ftp_client_options']));

            $ssh = new CommandExecutorAdapter();
            $cmdExecutor = $ssh->to($params['ssh']['reserve_server']['host'])
                ->as($params['ssh']['reserve_server']['username'])
                ->withPassword($params['ssh']['reserve_server']['password'])
                ->connect();

            $importer = new Importer(
                $cmdExecutor,
                $params['db_importer']['workdir'],
                $importerDbConfig['username'],
                $importerDbConfig['password'],
                $this->getDsnAttribute("dbname", $importerDbConfig['dsn'])
            );

            $backuper = new Backuper($backup, $repo, $importer);
            $backuper->run(false);
            FileManager::UnlinkFiles($dump_tmp_dir, [$backup->getFilename()]);
        } catch (\Throwable $th) {
            FileManager::UnlinkFiles($dump_tmp_dir, [$backup->getFilename()]);
            throw $th;
        }
    }

    public function actionImportClients()
    {
        $params = Yii::$app->params;
        $dump_tmp_dir = $params['db_backup']['db']['dump_tmp_dir'];
        $importerDbConfig = include $params['db_importer']['db']['db_config_path'];
        $dbConfig = include $params['db_backup']['db']['db_config_path'];
        $importerDbConfig = include $params['db_importer']['db']['db_config_path'];

        $ssh = new CommandExecutorAdapter();
        $cmdExecutor = $ssh->to($params['ssh']['reserve_server']['host'])
            ->as($params['ssh']['reserve_server']['username'])
            ->withPassword($params['ssh']['reserve_server']['password'])
            ->connect();


        $importer = new Importer(
            $cmdExecutor,
            $params['db_importer']['workdir'],
            $importerDbConfig['username'],
            $importerDbConfig['password'],
            $this->getDsnAttribute("dbname", $importerDbConfig['dsn'])
        );

        $lastDumpName = Backup::getLastDumpFilename($dump_tmp_dir, $this->getDsnAttribute("dbname", $dbConfig['dsn']));

        $importer->import($lastDumpName);
    }
    public function actionImportObjects()
    {
        $params = Yii::$app->params;
        $dump_tmp_dir = $params['db_backup']['db_old']['dump_tmp_dir'];
        $importerDbConfig = include $params['db_importer']['db_old']['db_config_path'];
        $dbConfig = include $params['db_backup']['db_old']['db_config_path'];
        $importerDbConfig = include $params['db_importer']['db_old']['db_config_path'];

        $ssh = new CommandExecutorAdapter();
        $cmdExecutor = $ssh->to($params['ssh']['reserve_server']['host'])
            ->as($params['ssh']['reserve_server']['username'])
            ->withPassword($params['ssh']['reserve_server']['password'])
            ->connect();


        $importer = new Importer(
            $cmdExecutor,
            $params['db_importer']['workdir'],
            $importerDbConfig['username'],
            $importerDbConfig['password'],
            $this->getDsnAttribute("dbname", $importerDbConfig['dsn'])
        );

        $lastDumpName = Backup::getLastDumpFilename($dump_tmp_dir, $this->getDsnAttribute("dbname", $dbConfig['dsn']));

        $importer->import($lastDumpName);
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
