<?php

namespace app\tests\unit\fixtures\models;

use app\models\User\User;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
	public $modelClass = User::class;
	public $dataFile   = __DIR__ . '/../data/models/user.php';
}
