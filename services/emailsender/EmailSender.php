<?php

namespace app\services\emailsender;

use app\helpers\validators\EmailsArrayValidator;
use app\helpers\validators\IsArrayValidator;
use Exception;
use RuntimeException;
use yii\base\Model;
use yii\mail\MailerInterface;
use yii\swiftmailer\Mailer;
use yii\validators\EmailValidator;

class EmailSender extends Model
{
	public $emails;
	public $from;
	public $subject;
	public $htmlBody;
	public $textBody;
	public $view;
	public $viewArgv;
	public $files;

	public $username;
	public $password;


	public function rules()
	{
		return [
			[['emails', 'from', 'username', 'password'], 'required'],
			[['emails', 'from', 'viewArgv', 'files'], IsArrayValidator::class],
			[['emails'], EmailsArrayValidator::class],
			[['from'], 'validateFrom'],
			[['subject', 'htmlBody', 'textBody', 'view', 'username', 'password'], 'string'],
		];
	}

	public function validateFrom($attr): void
	{
		if (!is_array($this->from)) {
			return;
		}

		$validator = new EmailValidator();

		foreach ($this->from as $email => $name) {
			if (!$validator->validate($email)) {
				$this->addError($attr, '"{attribute}" contain invalid email');

				return;
			}
		}
	}

	private function getMailer(): MailerInterface
	{
		return new Mailer(
			[
				'htmlLayout'       => 'layouts/html',
				'useFileTransport' => false,
				'transport'        => [
					'class'      => 'Swift_SmtpTransport',
					'host'       => 'smtp.yandex.com',
					'port'       => 465,
					'encryption' => 'ssl',
					'username'   => $this->username,
					'password'   => $this->password,
				]
			]
		);
	}

	/**
	 * @throws Exception
	 */
	public function send(): bool
	{
		if ($this->hasErrors()) {
			throw new RuntimeException("Invalid data");
		}

		$mailer = $this->getMailer();

		$message = $mailer->compose($this->view, $this->viewArgv)
		                  ->setFrom($this->from)
		                  ->setTo($this->emails)
		                  ->setSubject($this->subject);

		if ($this->files) {
			foreach ($this->files as $file) {
				$message->attach($file);
			}
		}

		if ($this->textBody) {
			$message->setTextBody($this->textBody);
		}

		if ($this->htmlBody) {
			$message->setHtmlBody($this->htmlBody);
		}

		return $message->send();
	}
}
