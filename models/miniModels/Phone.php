<?php

namespace app\models\miniModels;

use app\helpers\StringHelper;
use app\kernel\common\models\AR\AR;
use app\models\Contact;
use floor12\phone\PhoneFormatter;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "phone".
 *
 * @property int      $id
 * @property int      $contact_id
 * @property string   $phone
 * @property string   $exten
 * @property int|null $isMain
 *
 * @property Contact  $contact
 */
class Phone extends AR
{
	public const MAIN_COLUMN = 'phone';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'phone';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['contact_id', 'phone'], 'required'],
			[['contact_id', 'isMain'], 'integer'],
			[['phone', 'exten'], 'string', 'max' => 255],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::class, 'targetAttribute' => ['contact_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'contact_id' => 'Contact ID',
			'phone'      => 'Phone',
			'exten'      => 'Exten',
			'isMain'     => 'IsMain',
		];
	}

	public static function isValidPhoneNumber(string $number): bool
	{
		if (StringHelper::length($number) !== 11) {
			return false;
		}

		if (!StringHelper::isOnlyDigits($number)) {
			return false;
		}

		if (StringHelper::first($number) !== "7") {
			return false;
		}

		return true;
	}

	/** TODO: Вынести потом все в сервис, избавиться от этих createManyMiniModels */
	public function beforeSave($insert): bool
	{
		parent::beforeSave($insert);

		$this->phone = StringHelper::extractDigits($this->phone);

		return true;
	}

	public function toFormattedPhone(): string
	{
		if (self::isValidPhoneNumber($this->phone)) {
			return PhoneFormatter::format($this->phone);
		}

		return $this->phone;
	}

	/**
	 * Gets query for [[Contact]].
	 *
	 * @return ActiveQuery
	 */
	public function getContact(): ActiveQuery
	{
		return $this->hasOne(Contact::class, ['id' => 'contact_id']);
	}
}
