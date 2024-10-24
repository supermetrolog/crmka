<?php

declare(strict_types=1);

namespace app\models\forms\Contact;

use app\dto\ContactComment\CreateContactCommentDto;
use app\dto\ContactComment\UpdateContactCommentDto;
use app\kernel\common\models\Form\Form;
use app\models\Contact;

class ContactCommentForm extends Form
{
	public const SCENARIO_CREATE = 'scenario_create';
	public const SCENARIO_UPDATE = 'scenario_update';

	public $contact_id;
	public $comment;
	public $author_id;

	public function rules(): array
	{
		return [
			[['author_id', 'contact_id'], 'integer'],
			[['author_id', 'comment', 'contact_id'], 'required'],
			[['comment'], 'string', 'max' => 255],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']]
		];
	}

	public function scenarios(): array
	{
		$common = [
			'comment'
		];

		return [
			self::SCENARIO_CREATE => [...$common, 'contact_id', 'author_id'],
			self::SCENARIO_UPDATE => $common
		];
	}


	public function getDto()
	{
		if ($this->getScenario() === self::SCENARIO_CREATE) {
			return new CreateContactCommentDto([
				'contact_id' => $this->contact_id,
				'author_id'  => $this->author_id,
				'comment'    => $this->comment
			]);
		}

		return new UpdateContactCommentDto([
			'comment' => $this->comment
		]);
	}
}