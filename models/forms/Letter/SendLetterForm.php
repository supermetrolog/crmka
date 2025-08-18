<?php

namespace app\models\forms\Letter;

use app\dto\Letter\SendLetterDto;
use app\kernel\common\models\Form\Form;

class SendLetterForm extends Form
{
	public int    $company_id;
	public string $subject;
	public string $body;
	public array  $emails = [];
	public array  $phones = [];
	public array  $ways   = [];
	public int    $shipping_method;

	public function rules(): array
	{
		return [
			[['company_id', 'subject', 'body', 'emails', 'ways', 'shipping_method'], 'required'],
			[['company_id', 'shipping_method'], 'integer'],
			[['subject'], 'string', 'max' => 255],
			[['body'], 'string'],
			[['phones'], 'each', 'rule' => ['string']],
			[['ways'], 'each', 'rule' => ['integer']],
		];
	}

	public function getDto(int $user_id, string $sender_email): SendLetterDto
	{
		return new SendLetterDto([
			'user_id'         => $user_id,
			'company_id'      => $this->company_id,
			'sender_email'    => $sender_email,
			'subject'         => $this->subject,
			'body'            => $this->body,
			'emails'          => $this->emails,
			'phones'          => $this->phones,
			'ways'            => $this->ways,
			'shipping_method' => $this->shipping_method,
		]);
	}
}
