<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\services\selection\Selection;
use yii\console\Controller;

class SelectionController extends Controller
{
    public function actionIndex()
    {
        $model = new Selection();
        $model->run();
    }
}
