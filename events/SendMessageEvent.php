<?php

namespace app\events;

use app\exceptions\ValidationErrorHttpException;
use yii\base\DynamicModel;
use yii\base\Event;
use Yii;

class SendMessageEvent extends Event
{

    public ?array $contacts = [];
    public ?array $wayOfSending = [];
    public ?array $from = [];
    public ?string $subject = null;
    public ?string $htmlBody = null;

    public ?string $view = null;
    public ?array $viewArgv = [];

    public ?int $user_id = null;
    public ?string $description = null;
    public ?int $type = null;

    public $files = [];
    public bool $notSend = false;

    public ?string $username = null;
    public ?string $password = null;

    public function init()
    {
        parent::init();
        if (!$this->from) {
            $this->from = [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
        }
        if (!$this->username || !$this->password) {
            $this->username = Yii::$app->params['senderUsername'];
            $this->password = Yii::$app->params['senderPassword'];
            $this->from = [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']];
        }
        $model = new DynamicModel(['password' => $this->password, 'username' => $this->username, 'wayOfSending' => $this->wayOfSending, 'user_id' => $this->user_id, 'from' => $this->from, 'contacts' => $this->contacts, 'subject' => $this->subject, 'type' => $this->type, 'description' => $this->description, 'htmlBody' => $this->htmlBody]);
        $model->addRule(['subject', 'contacts', 'description', 'type', 'wayOfSending', 'username', 'password'], 'required')
            ->validate();

        if ($model->hasErrors()) {
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        }
    }
}
