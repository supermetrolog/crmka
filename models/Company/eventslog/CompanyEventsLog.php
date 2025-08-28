<?php

namespace app\models\company\eventslog;

use app\models\Company\Company;
use app\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "company_events_log".
 *
 * @property int         $id
 * @property string|null $message
 * @property int|null    $type       Тип события
 * @property int         $company_id [СВЯЗЬ] с компанией
 * @property int         $user_id    [СВЯЗЬ] с пользователями
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null    $question_id
 * @property int|null    $question_parent
 *
 * @property Company     $company
 * @property User        $user
 */
class CompanyEventsLog extends ActiveRecord
{
	public const TYPE_DEFAULT            = 1;
	public const TYPE_ANSWER_TO_QUESTION = 2;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'company_events_log';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['message'], 'string', 'min' => 3],
			['type', 'in', 'range' => [self::TYPE_DEFAULT, self::TYPE_ANSWER_TO_QUESTION]],
			[['question_id', 'question_parent'], 'validateQuestion'],
			[['type', 'company_id', 'user_id'], 'integer'],
			[['company_id', 'user_id', 'message', 'type'], 'required'],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
		];
	}

	public function validateQuestion($attr): void
	{
		if ($this->question_id === null && $this->question_parent === null) {
			return;
		}

		if ($this->question_id === null || $this->question_parent === null) {
			$this->addError($attr, '"{attribute}" cannot be null');

			return;
		}

		if ($this->type !== self::TYPE_ANSWER_TO_QUESTION) {
			$this->addError($attr, '"{attribute}" must be answer to question type');

			return;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'         => 'ID',
			'message'    => 'Message',
			'type'       => 'Type',
			'company_id' => 'Company ID',
			'user_id'    => 'User ID',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * Gets query for [[Company]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany()
	{
		return $this->hasOne(Company::className(), ['id' => 'company_id']);
	}

	/**
	 * Gets query for [[User]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}
