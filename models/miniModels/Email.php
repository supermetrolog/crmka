<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Contact;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "email".
 *
 * @property int      $id
 * @property int      $contact_id
 * @property string   $email
 * @property int|null $isMain
 *
 * @property Contact  $contact
 */
class Email extends AR
{
	public const MAIN_COLUMN = 'email';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'email';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['contact_id', 'email'], 'required'],
			[['contact_id', 'isMain'], 'integer'],
			[['email'], 'string', 'max' => 255],
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
			'email'      => 'Email',
			'isMain'     => 'IsMain',
		];
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
