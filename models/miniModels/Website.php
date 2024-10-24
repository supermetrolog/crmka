<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Contact;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "website".
 *
 * @property int     $id
 * @property int     $contact_id
 * @property string  $website
 *
 * @property Contact $contact
 */
class Website extends AR
{
	public const MAIN_COLUMN = 'website';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'website';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['contact_id', 'website'], 'required'],
			[['contact_id'], 'integer'],
			[['website'], 'string', 'max' => 255],
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
			'website'    => 'Website',
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
