<?php

namespace app\daemons;

use yii\base\InvalidValueException;
use yii\base\Model;

class Message extends Model
{
    public const ACTION_NEW_NOTIFICATION = 'new_notifications';
    public const ACTION_CHECK_NOTIFICATIONS_COUNT = 'check_notifications_count';
    private $body;
    private string $action;
    private bool $error = false;

    public function setBody($data)
    {
        $this->body = $data;
    }
    public function setAction($data)
    {
        $this->action = $data;
    }
    public function setError()
    {
        $this->error = true;
    }

    public function getData()
    {
        $data =  [
            'message' => $this->body,
            'action' => $this->action,
            'error' => $this->error
        ];

        foreach ($data as $key => $item) {
            if (is_null($item)) {
                throw new InvalidValueException("$key was not be NULL");
            }
        }
        return json_encode($data);
    }
}
