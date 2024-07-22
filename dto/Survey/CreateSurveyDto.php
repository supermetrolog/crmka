<?php

declare(strict_types=1);

namespace app\dto\Survey;

use app\models\ChatMember;
use app\models\Contact;
use app\models\User;
use yii\base\BaseObject;

class CreateSurveyDto extends BaseObject
{
	public User       $user;
	public Contact    $contact;
	public ChatMember $chatMember;
}