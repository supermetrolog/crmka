<?php

declare(strict_types=1);

namespace app\dto\SurveyDraft;

use app\models\ChatMember;
use app\models\User;
use yii\base\BaseObject;

class CreateSurveyDraftDto extends BaseObject
{
	public User       $user;
	public ChatMember $chatMember;
	public            $data = null;
}