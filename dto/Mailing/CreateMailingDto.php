<?php

declare(strict_types=1);

namespace app\dto\Mailing;

use yii\base\BaseObject;

class CreateMailingDto extends BaseObject
{
	public int    $channel_id;
	public string $subject;
	public string $message;
	public string $created_by_type;
	public int    $created_by_id;
}