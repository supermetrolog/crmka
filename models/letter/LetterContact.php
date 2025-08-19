<?php

namespace app\models\letter;

use app\kernel\common\models\AQ\AQ;
use app\models\ActiveQuery\ContactQuery;
use app\models\Contact;
use app\models\LetterContactAnswer;
use yii\db\ActiveQuery;
use yii\validators\RequiredValidator;

/**
 * @property int                        $id
 * @property int                        $letter_id  [СВЯЗЬ] с отправленными письмами
 * @property ?string                    $email
 * @property ?string                    $phone
 * @property int                        $contact_id [СВЯЗЬ] с таблицей контактов
 *
 * @property-read Letter                $letter
 * @property-read LetterContactAnswer[] $answers
 */
class LetterContact extends \yii\db\ActiveRecord
{
	public static function tableName(): string
	{
		return 'letter_contact';
	}

	public function rules(): array
	{
		return [
			[['letter_id'], 'required'],
			[['letter_id', "contact_id"], 'integer'],
			[['email', 'phone'], 'string'],
			['email', 'validateContacts'],
			[['letter_id'], 'exist', 'targetClass' => Letter::class, 'targetAttribute' => ['letter_id' => 'id']],
		];
	}

	public function validateContacts(): void
	{
		$required = new RequiredValidator();

		if ($required->validate($this->email) && $required->validate($this->phone)) {
			$this->addError('contacts', 'Phone or email cannot be empty');
		}
	}

	public function getContact(): ContactQuery
	{
		/** @var ContactQuery */
		return $this->hasOne(Contact::class, ['id' => 'contact_id']);
	}

	public function getLetter(): ActiveQuery
	{
		return $this->hasOne(Letter::class, ['id' => 'letter_id']);
	}

	public function getAnswers(): AQ
	{
		/** @var AQ */
		return $this->hasMany(LetterContactAnswer::class, ['letter_contact_id' => 'id']);
	}
}
