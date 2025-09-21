<?php

declare(strict_types=1);

namespace app\dto\Survey;

use app\models\Call;
use app\models\ChatMember;
use app\models\Contact;
use app\models\Survey;
use app\models\User\User;
use yii\base\BaseObject;

class CreateSurveyDto extends BaseObject
{
	public User       $user;
	public ?Contact   $contact;
	public ChatMember $chatMember;
	public ?string    $comment;
	public ?int       $related_survey_id = null;

	public string $status = Survey::STATUS_DRAFT;
	public string $type   = Survey::TYPE_BASIC;

	/** @var Call[] */
	public array $calls = [];
}