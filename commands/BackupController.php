<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\services\backup\Backup;
use app\services\filemanager\FileManager;
use Yii;
use yii\console\Controller;

class BackupController extends Controller
{
    public function actionIndex()
    {
        $crmkaDBConfig = include Yii::getAlias("@app") . "/config/db.php";
        $tmpPath = Yii::getAlias("@app") . "/services/backup/tmp";
        $backup = new Backup($tmpPath, $crmkaDBConfig);
        $backup->dump();

        FileManager::UnlinkFiles($tmpPath);
    }
}
