<?php

namespace app\models;

use app\kernel\common\models\AR\AR;
use app\models\ActiveQuery\ContactQuery;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\miniModels\WayOfInforming;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\Website;
use app\models\miniModels\ContactComment;
use app\exceptions\ValidationErrorHttpException;
use yii\helpers\ArrayHelper;
use app\behaviors\CreateManyMiniModelsBehaviors;

/**
 * This is the model class for table "contact".
 *
 * @property int              $id
 * @property int              $company_id
 * @property string           $first_name
 * @property string|null      $middle_name
 * @property string|null      $last_name
 * @property int|null         $status
 * @property int|null         $type
 * @property string|null      $created_at
 * @property string|null      $updated_at
 * @property int|null         $consultant_id     [связь] с пользователями
 * @property int|null         $position          Должность
 * @property int|             $position_unknown  Должность неизвестна
 * @property int|null         $faceToFaceMeeting [флаг] Очная встреча
 * @property int|null         $warning           [флаг] Внимание
 * @property int|null         $good              [флаг] Хор. взаимоотношения
 * @property int|null         $passive_why
 * @property string|null      $passive_why_comment
 * @property string|null      $warning_why_comment
 * @property int|null         $isMain            основной контакт
 * @property Company          $company
 * @property User             $consultant
 * @property ContactComment[] $contactComments
 * @property Email[]          $emails
 * @property Phone[]          $phones
 * @property WayOfInforming[] $wayOfInformings
 * @property Website[]        $websites
 */
class Contact extends AR
{
	public const GENERAL_CONTACT_TYPE = 1;
	public const DEFAULT_CONTACT_TYPE = 0;
	public const IS_MAIN_CONTACT      = 1;
	public const LIST_CONTACT_TYPE    = 0;

	public function behaviors()
	{
		return [
			CreateManyMiniModelsBehaviors::class
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName()
	{
		return 'contact';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['company_id'], 'required'],
			[['company_id', 'status', 'type', 'consultant_id', 'position', 'faceToFaceMeeting', 'warning', 'good', 'passive_why', 'position_unknown', 'isMain'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['first_name', 'middle_name', 'last_name', 'passive_why_comment', 'warning_why_comment'], 'string', 'max' => 255],
			[['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
			[['consultant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['consultant_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
			'id'                  => 'ID',
			'company_id'          => 'Company ID',
			'first_name'          => 'First Name',
			'middle_name'         => 'Middle Name',
			'last_name'           => 'Last Name',
			'status'              => 'Status',
			'type'                => 'Type',
			'created_at'          => 'Created At',
			'updated_at'          => 'Updated At',
			'consultant_id'       => 'Consultant ID',
			'position'            => 'Position',
			'faceToFaceMeeting'   => 'Face To Face Meeting',
			'warning'             => 'Warning',
			'good'                => 'Good',
			'passive_why'         => 'PassiveWhy',
			'passive_why_comment' => 'PassiveWhyComment',
			'warning_why_comment' => 'WarningWhyComment',
			'position_unknown'    => 'PositionUnknown',
			'isMain'              => 'IsMain'
		];
	}

	public function fields(): array
	{
		$fields                        = parent::fields();
		$fields['full_name']           = function ($fields) {
			$full_name = $fields['first_name'];
			if ($fields['middle_name']) {
				$full_name = $fields['middle_name'] . " " . $full_name;
			}
			if ($fields['last_name']) {
				$full_name .= " " . $fields['last_name'];
			}

			return $full_name;
		};
		$fields['short_name']          = function ($fields) {
			$first_name  = ucfirst(mb_substr($fields['first_name'], 0, 1)) . ".";
			$last_name   = "";
			$middle_name = "";
			if ($fields['middle_name']) {
				$middle_name = ucfirst(mb_substr($fields['middle_name'], 0, 1)) . ".";
			}
			if ($fields['last_name']) {
				$last_name = ucfirst(mb_substr($fields['last_name'], 0, 1)) . ".";
			}
			$short_name = "$middle_name $first_name $last_name";

			return trim($short_name);
		};
		$fields['first_and_last_name'] = function ($fields) {
			$full_name = $fields['first_name'];
			if ($fields['last_name']) {
				$full_name .= " {$fields['last_name']}";
			}

			return $full_name;
		};

		$fields['updated_at'] = function ($fields) {
			return $fields->updated_at == "0000-00-00 00:00:00" ? null : $fields->updated_at;
		};

		$fields['created_at'] = function ($fields) {
			return $fields->created_at == "0000-00-00 00:00:00" ? null : $fields->created_at;
		};

		return $fields;
	}

	public function extraFields()
	{
		$extraFields                  = parent::extraFields();
		$extraFields['phones']        = function ($ef) {
			$phones = array_filter($ef['phones'], function ($phone) {
				$phone = $phone->toArray();

				return Phone::isValidPhoneNumber($phone['native_phone']);
			});

			return array_values($phones);
		};
		$extraFields['invalidPhones'] = function ($ef) {
			$phones = array_filter($ef['phones'], function ($phone) {
				$phone = $phone->toArray();

				return !Phone::isValidPhoneNumber($phone['native_phone']);
			});

			return array_values($phones);
		};

		return $extraFields;
	}

	private function changeIsMain()
	{
		$query = static::find()->where(['isMain' => 1, 'company_id' => $this->company_id]);
		if ($this->id) {
			$query->andWhere(['!=', 'id', $this->id]);
		}
		$model = $query->limit(1)->one();
		if ($model) {
			$model->isMain = null;
			$model->save(false);
		}
	}

	public function beforeSave($insert)
	{
		if ($this->isMain == 1) {
			$this->changeIsMain();
		}
		parent::beforeSave($insert);

		return true;
	}

	public static function getCompanyContactList($company_id)
	{
		$dataProvider = new ActiveDataProvider([
			'query'      => self::find()->with(['emails', 'phones', 'websites', 'wayOfInformings', 'consultant.userProfile', 'contactComments.author.userProfile'])->where(['contact.company_id' => $company_id]),
			'pagination' => [
				'pageSize' => 0,
			],
		]);

		return $dataProvider;
	}

	public static function createContact($post_data)
	{
		$model       = new static();
		$transaction = Yii::$app->db->beginTransaction();
		try {
			if (!$model->load($post_data, '') || !$model->save()) {
				throw new ValidationErrorHttpException($model->getErrorSummary(false));
			}

			$model->createManyMiniModels([
				Email::class          => ArrayHelper::getValue($post_data, 'emails'),
				Phone::class          => ArrayHelper::merge(ArrayHelper::getValue($post_data, 'phones'), ArrayHelper::getValue($post_data, 'invalidPhones', [])),
				Website::class        => ArrayHelper::getValue($post_data, 'websites'),
				WayOfInforming::class => ArrayHelper::getValue($post_data, 'wayOfInformings'),
			]);
			$transaction->commit();

			return ['message' => "Контакт создан", 'data' => $model->id];
		} catch (\Throwable $th) {
			$transaction->rollBack();
			throw $th;
		}
	}

	public static function updateContact($model, $post_data)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$post_data['updated_at'] = date('Y-m-d H:i:s');
			if (!$model->load($post_data, '') || !$model->save()) {
				throw new ValidationErrorHttpException($model->getErrorSummary(false));
			}

			$model->updateManyMiniModels([
				Email::class          => ArrayHelper::getValue($post_data, 'emails'),
				Phone::class          => ArrayHelper::merge(ArrayHelper::getValue($post_data, 'phones'), ArrayHelper::getValue($post_data, 'invalidPhones', [])),
				Website::class        => ArrayHelper::getValue($post_data, 'websites'),
				WayOfInforming::class => ArrayHelper::getValue($post_data, 'wayOfInformings'),
			]);
			$transaction->commit();

			return ['message' => "Контакт изменен", 'data' => $model->id];
		} catch (\Throwable $th) {
			$transaction->rollBack();
			throw $th;
		}
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
	 * Gets query for [[Consultant]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getConsultant()
	{
		return $this->hasOne(User::className(), ['id' => 'consultant_id']);
	}

	/**
	 * Gets query for [[ContactComments]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getContactComments()
	{
		return $this->hasMany(ContactComment::className(), ['contact_id' => 'id']);
	}

	/**
	 * Gets query for [[Emails]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getEmails()
	{
		return $this->hasMany(Email::className(), ['contact_id' => 'id']);
	}

	/**
	 * Gets query for [[Phones]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getPhones()
	{
		return $this->hasMany(Phone::className(), ['contact_id' => 'id']);
	}

	/**
	 * Gets query for [[WayOfInformings]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getWayOfInformings()
	{
		return $this->hasMany(WayOfInforming::className(), ['contact_id' => 'id']);
	}

	/**
	 * Gets query for [[Websites]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getWebsites()
	{
		return $this->hasMany(Website::className(), ['contact_id' => 'id']);
	}

	public static function find(): ContactQuery
	{
		return new ContactQuery(get_called_class());
	}
}
