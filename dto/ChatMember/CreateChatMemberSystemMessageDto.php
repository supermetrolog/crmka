<?php

declare(strict_types=1);

namespace app\dto\ChatMember;

use app\models\ChatMember;
use app\models\ChatMemberMessage;
use yii\base\BaseObject;

class CreateChatMemberSystemMessageDto extends BaseObject
{
	public ChatMember         $to;
	public ?ChatMemberMessage $replyTo    = null;
	public ?string            $message;
	public array              $contactIds = [];
	public array              $tagIds     = [];
	public array              $surveyIds  = [];
	public ?string            $template   = null;
}