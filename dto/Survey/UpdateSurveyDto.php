<?php

declare(strict_types=1);

namespace app\dto\Survey;

use app\models\Call;
use app\models\Contact;
use yii\base\BaseObject;

class UpdateSurveyDto extends BaseObject
{
	public ?Contact $contact;
	public ?string  $comment;

	/** @var Call[] */
	public array $calls = [];
}