<?php

namespace app\dto\Letter;

use yii\base\BaseObject;

class SendLetterDto extends BaseObject
{
    public int $user_id;
    public int $company_id;
    public string $sender_email;
    public string $subject;
    public string $body;
    public array $emails;
    public array $phones;
    public array $ways;
    public int $shipping_method;
}
