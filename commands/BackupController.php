<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\services\backup\Backup;
use app\services\filemanager\FileManager;
use app\services\googledrive\GoogleDrive;
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
        $crmkaDBConfig = include Yii::getAlias("@app") . "/config/db.php";
        $tmpPath = Yii::getAlias("@app") . "/services/backup/tmp";
        $backup = new Backup($tmpPath, $crmkaDBConfig);

        $backup->dump();

        $backupFileName = $backup->getBackupFileName();
        $backupFullPath = $backup->getFullPath();


        $credentialsFileName = Yii::getAlias("@app") . "/mysqlbackup_service_account_cred.json";
        $googleDriveService = new GoogleDrive($credentialsFileName, "ClientsBackup");
        $backupDriveFolderId = $googleDriveService->createFolder("pennylane_backup");
        $googleDriveService->createFile($backupFullPath, $backupFileName, $backupDriveFolderId, "backup clients db", "text/plain");


        FileManager::UnlinkFiles($tmpPath);
    }

    public function actionObjects()
    {
        $crmkaDBConfig = include Yii::getAlias("@app") . "/config/db_old.php";
        $tmpPath = Yii::getAlias("@app") . "/services/backup/tmp";
        $backup = new Backup($tmpPath, $crmkaDBConfig);

        $backup->dump();

        $backupFileName = $backup->getBackupFileName();
        $backupFullPath = $backup->getFullPath();


        $credentialsFileName = Yii::getAlias("@app") . "/mysqlbackup_service_account_cred.json";
        $googleDriveService = new GoogleDrive($credentialsFileName, "ObjectsBackup");
        $backupDriveFolderId = $googleDriveService->createFolder("pennylane_backup");
        $googleDriveService->createFile($backupFullPath, $backupFileName, $backupDriveFolderId, "backup objects db", "text/plain");


        FileManager::UnlinkFiles($tmpPath);
    }
}
