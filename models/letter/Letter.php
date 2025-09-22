<?php

namespace app\models\letter;

use app\kernel\common\models\AQ\AQ;
use app\models\ActiveQuery\CompanyQuery;
use app\models\ActiveQuery\UserQuery;
use app\models\Company\Company;
use app\models\LetterContactAnswer;
use app\models\User\User;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "letter".
 *
 * @property int                        $id
 * @property int                        $user_id         [СВЯЗЬ] с таблицей юзеров
 * @property string                     $sender_email    почта отправителя
 * @property ?string                    $subject         Тема письма
 * @property ?string                    $body            Текст письма
 * @property string                     $created_at
 * @property int                        $status          1 - отправлено, 0 - ошибка
 * @property int                        $type            Отправлено из таймлайна или другим способом
 * @property int                        $shipping_method Отправлено из таймлайна или другим способом
 * @property int                        $company_id      [СВЯЗЬ] с таблицей компаний
 *
 * @property-read User                  $user
 * @property-read LetterContact[]       $letterContacts
 * @property-read LetterOffer[]         $letterOffers
 * @property-read LetterWay[]           $letterWays
 * @property-read LetterContactAnswer[] $answers
 */
class Letter extends \yii\db\ActiveRecord
{
	const TYPE_FROM_TIMELINE = 1;
	const TYPE_DEFAULT       = 0;

	const STATUS_ERROR   = 0;
	const STATUS_SUCCESS = 1;

	const SHIPPING_FROM_SYSTEM_METHOD = 1;
	const SHIPPING_OTHER_METHOD       = 0;

	public static function tableName(): string
	{
		return 'letter';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'type', 'shipping_method', 'sender_email', 'company_id'], 'required'],
			[['company_id', 'user_id', 'status', 'type', 'shipping_method'], 'integer'],
			[['body'], 'string'],
			[['created_at'], 'safe'],
			[['subject'], 'string', 'max' => 255],
			[['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function getCompany(): CompanyQuery
	{
		/** @var CompanyQuery */
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}

	public function getUser(): UserQuery
	{
		/** @var UserQuery */
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public function getLetterContacts(): ActiveQuery
	{
		return $this->hasMany(LetterContact::class, ['letter_id' => 'id']);
	}

	public function getLetterEmails(): ActiveQuery
	{
		return $this->hasMany(LetterContact::class, ['letter_id' => 'id'])->where(['is not', 'email', new Expression("null")]);
	}

	public function getLetterPhones(): ActiveQuery
	{
		return $this->hasMany(LetterContact::class, ['letter_id' => 'id'])->where(['is not', 'phone', new Expression("null")]);
	}

	public function getLetterOffers(): ActiveQuery
	{
		return $this->hasMany(LetterOffer::class, ['letter_id' => 'id']);
	}

	public function getLetterWays(): ActiveQuery
	{
		return $this->hasMany(LetterWay::class, ['letter_id' => 'id']);
	}

	public function getAnswers(): AQ
	{
		/** @var AQ */
		return $this->hasMany(LetterContactAnswer::class, ['letter_contact_id' => 'id'])->via('letterContacts');
	}
}
