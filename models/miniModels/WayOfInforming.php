<?php

namespace app\models\miniModels;

use app\kernel\common\models\AR\AR;
use app\models\Contact;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "way_of_informing".
 *
 * @property int     $id
 * @property int     $contact_id
 * @property int     $way
 *
 * @property Contact $contact
 */
class WayOfInforming extends AR
{
	public const MAIN_COLUMN = 'way';

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'way_of_informing';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array
	{
		return [
			[['contact_id'], 'required'],
			[['contact_id', 'way'], 'integer'],
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
			'way'        => 'Way',
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
