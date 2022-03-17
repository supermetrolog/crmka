<?php

namespace app\events;

use yii\base\Event;

class NotificationEvent extends Event
{
    public int $consultant_id;
    public string $title;
    public string $body;
    public int $type;
}
