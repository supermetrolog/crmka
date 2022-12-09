<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\services\backup\Backup;
use app\services\backuper\Backuper;
use app\services\filemanager\FileManager;
use app\services\ftpclient\FtpClient;
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
    public function actionClients()
    {
        try {
            $params = Yii::$app->params;
            $dump_tmp_dir = $params['db_backup']['dump_tmp_dir'];
            $backup = new Backup($dump_tmp_dir, include $params['db_backup']['db']['db_config_path']);

            $repo = FtpClient::getInstance(FtpConnectionOptions::fromArray($params['db_backup']['ftp_client_options']));
            $backuper = new Backuper($backup, $repo);
            $backuper->run();
        } catch (\Throwable $th) {
            FileManager::UnlinkFiles($dump_tmp_dir);
            throw $th;
        }
        FileManager::UnlinkFiles($dump_tmp_dir);
    }

    public function actionObjects()
    {
        try {
            $params = Yii::$app->params;
            $dump_tmp_dir = $params['db_backup']['dump_tmp_dir'];
            $backup = new Backup($dump_tmp_dir, include $params['db_backup']['db_old']['db_config_path']);

            $repo = FtpClient::getInstance(FtpConnectionOptions::fromArray($params['db_backup']['ftp_client_options']));
            $backuper = new Backuper($backup, $repo);
            $backuper->run();
        } catch (\Throwable $th) {
            FileManager::UnlinkFiles($dump_tmp_dir);
            throw $th;
        }
        FileManager::UnlinkFiles($dump_tmp_dir);
    }
}
