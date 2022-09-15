<?php

namespace app\services\queue\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class TestJob extends BaseObject implements JobInterface
{
    public $text;
    public function execute($q)
    {
        file_put_contents(Yii::getAlias("@app") . "/suka.txt", $this->text . "\n", FILE_APPEND);
    }
}
