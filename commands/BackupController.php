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
        // $this->actionObjects();
    }

    public function actionClients()
    {
        $crmkaDBConfig = include Yii::getAlias("@app") . "/config/db.php";
        $tmpPath = Yii::getAlias("@app") . "/services/backup/tmp";
        $backup = new Backup($tmpPath, $crmkaDBConfig);

        $backup->dump();

        $backupFileName = $backup->getBackupFileName();
        $backupFullPath = $backup->getFullPath();


        $credentialsFileName = Yii::getAlias("@app") . '/google_drive_secrets.json';
        $googleDriveService = new GoogleDrive($credentialsFileName, "backup");
        $backupDriveFolderId = $googleDriveService->createFolder("pennylane_backup");
        $googleDriveService->createFile($backupFullPath, $backupFileName, $backupDriveFolderId, "backup crmka db", "text/plain");
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


        $credentialsFileName = Yii::getAlias("@app") . '/google_drive_secrets.json';
        $googleDriveService = new GoogleDrive($credentialsFileName, "backup");
        $backupDriveFolderId = $googleDriveService->createFolder("pennylane_backup");
        $googleDriveService->createFile($backupFullPath, $backupFileName, $backupDriveFolderId, "backup crmka db", "text/plain");
        FileManager::UnlinkFiles($tmpPath);
    }
}
