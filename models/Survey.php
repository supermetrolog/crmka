<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\SurveyQuery;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "survey".
 *
 * @property int     $id
 * @property int     $user_id
 * @property int     $contact_id
 * @property string  $created_at
 * @property string  $updated_at
 *
 * @property Contact $contact
 * @property User    $user
 */
class Survey extends AR
{
	protected bool $useSoftUpdate = true;
	
	public static function tableName(): string
	{
		return 'survey';
	}

	public function rules(): array
	{
		return [
			[['user_id', 'contact_id'], 'required'],
			[['user_id', 'contact_id'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['contact_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contact::className(), 'targetAttribute' => ['contact_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function attributeLabels(): array
	{
		return [
			'id'         => 'ID',
			'user_id'    => 'User ID',
			'contact_id' => 'Contact ID',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getContact(): ActiveQuery
	{
		return $this->hasOne(Contact::className(), ['id' => 'contact_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getUser(): ActiveQuery
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}


	public static function find(): SurveyQuery
	{
		return new SurveyQuery(get_called_class());
	}
}
