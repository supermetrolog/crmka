<?php

namespace app\events;

use app\exceptions\ValidationErrorHttpException;
use yii\base\DynamicModel;
use yii\base\Event;
use Yii;

class SendMessageEvent extends Event
{

    public ?array $contacts = [];
    public ?array $from = [];
    public ?string $subject = null;
    public ?string $htmlBody = null;

    public ?int $user_id = null;
    public ?string $description = null;
    public ?int $type = null;

    public bool $notSend = false;


    public function init()
    {
        parent::init();
        if (!$this->from) {
            $this->from =  [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
        }
        $model = new DynamicModel(['user_id' => $this->user_id, 'from' => $this->from, 'contacts' => $this->contacts, 'subject' => $this->subject, 'type' => $this->type, 'description' => $this->description, 'htmlBody' => $this->htmlBody]);
        $model->addRule(['subject', 'contacts', 'description', 'type', 'htmlBody'], 'required')
            ->validate();

        if ($model->hasErrors()) {
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
}
