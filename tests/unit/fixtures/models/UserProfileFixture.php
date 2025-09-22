<?php

namespace app\tests\unit\fixtures\models;

use app\models\User\UserProfile;
use yii\test\ActiveFixture;

class UserProfileFixture extends ActiveFixture
{
	public $modelClass = UserProfile::class;
	public $dataFile   = __DIR__ . '/../data/models/user_profile.php';
	public $depends    = [UserFixture::class];
}
