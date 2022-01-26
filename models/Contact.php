<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\miniModels\WayOfInforming;
use app\models\miniModels\Email;
use app\models\miniModels\Phone;
use app\models\miniModels\Website;
use app\models\miniModels\ContactComment;
use app\exceptions\ValidationErrorHttpException;
use ReflectionClass;

/**
 * This is the model class for table "contact".
 *
 * @property int $id
 * @property int $company_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property int|null $status
 * @property int|null $type
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $consultant_id [связь] с пользователями
 * @property int|null $position Должность
 * @property int|null $faceToFaceMeeting [флаг] Очная встреча
 * @property int|null $warning [флаг] Внимание
 * @property int|null $good [флаг] Хор. взаимоотношения
 *
 * @property Company $company
 * @property User $consultant
 * @property ContactComment[] $contactComments
 * @property Email[] $emails
 * @property Phone[] $phones
 * @property WayOfInforming[] $wayOfInformings
 * @property Website[] $websites
 */
class Contact extends \yii\db\ActiveRecord
{
    public const GENERAL_CONTACT_TYPE = 1;
    public const LIST_CONTACT_TYPE = 0;
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
            [['company_id', 'status', 'type', 'consultant_id', 'position', 'faceToFaceMeeting', 'warning', 'good'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 255],
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
            'id' => 'ID',
            'company_id' => 'Company ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'status' => 'Status',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'consultant_id' => 'Consultant ID',
            'position' => 'Position',
            'faceToFaceMeeting' => 'Face To Face Meeting',
            'warning' => 'Warning',
            'good' => 'Good',
        ];
    }
    public static function getCompanyContactList($company_id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => self::find()->joinWith(['emails', 'phones', 'websites', 'wayOfInformings', 'consultant', 'contactComments' => function ($query) {
                $query->with(['author']);
            }])->where(['contact.company_id' => $company_id]),
            'pagination' => [
                'pageSize' => 10000,
            ],
        ]);
        return $dataProvider;
    }
    //функция для создания сразу нескольких строк в связанных моделях
    public  function createManyMiniModels($className, $data)
    {
        $columnName = $className::MAIN_COLUMN;

        if (!$data || !$className || !$columnName) {
            return false;
        }
        [];
        $class = new ReflectionClass($className);

        foreach ($data as $item) {
            $model = $class->newInstance();
            $array['contact_id'] = $this->id;
            $array[$columnName] = $item;
            if ($model->load($array, '') && $model->save()) {
                continue;
            } else {
                throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
        }
        return true;
    }
    // public  function createManyMiniModelsNew($className, $data)
    // {
    //     if (!$data || !$className) {
    //         return false;
    //     }
    //     [];
    //     $class = new ReflectionClass($className);

    //     foreach ($data as $item) {
    //         $model = $class->newInstance();
    //         $item['contact_id'] = $this->id;
    //         if ($model->load($item, '') && $model->save()) {
    //             continue;
    //         } else {
    //             throw new ValidationErrorHttpException($model->getErrorSummary(false));
    //         }
    //     }
    //     return true;
    // }
    public  function createManyMiniModelsNew(array $modelsData)
    {
        foreach ($modelsData as $className => $data) {
            $class = new ReflectionClass($className);
            foreach ($data as $item) {
                $model = $class->newInstance();
                $item['contact_id'] = $this->id;
                if (!$model->load($item, '') || !$model->save())
                    throw new ValidationErrorHttpException($model->getErrorSummary(false));
            }
        }
        return true;
    }
    public static function createContact($post_data)
    {
        $db = Yii::$app->db;
        $model = new Contact();
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post_data, '') && $model->save()) {
                $model->createManyMiniModels(Email::class,  $post_data['emails']);
                $model->createManyMiniModels(Phone::class,  $post_data['phones']);
                $model->createManyMiniModels(WayOfInforming::class,  $post_data['wayOfInformings']);
                // $transaction->rollBack();

                $transaction->commit();
                return ['message' => "Контакт создан", 'data' => $model->id];
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public static function createContactNew($post_data)
    {
        $model = new static();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->load($post_data, '') || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));

            $model->createManyMiniModelsNew([
                Email::class =>  $post_data['emails'],
                Phone::class => $post_data['phones'],
                Website::class => $post_data['websites'],
            ]);
            $transaction->commit();
            return ['message' => "Контакт создан", 'data' => $model->id];
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public static function updateContactNew($model, $post_data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->load($post_data, '') || !$model->save())
                throw new ValidationErrorHttpException($model->getErrorSummary(false));

            $model->updateManyMiniModelsNew([
                Email::class =>  $post_data['emails'],
                Phone::class => $post_data['phones'],
                Website::class => $post_data['websites'],
            ]);
            $transaction->commit();
            return ['message' => "Контакт изменен", 'data' => $model->id];
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }
    public function updateManyMiniModelsNew($modelsData)
    {
        foreach ($modelsData as $className => $item) {
            $className::deleteAll(['contact_id' => $this->id]);
        }
        $this->createManyMiniModelsNew($modelsData);
    }
    public function updateManyMiniModels($className, $data)
    {
        $className::deleteAll(['contact_id' => $this->id]);
        $this->createManyMiniModels($className, $data);
    }
    public static function updateContact($model, $post_data)
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if ($model->load($post_data, '') && $model->save()) {
                $model->updateManyMiniModels(Email::class,  $post_data['emails']);
                $model->updateManyMiniModels(Phone::class,  $post_data['phones']);
                $model->updateManyMiniModels(WayOfInforming::class,  $post_data['wayOfInformings']);
                // $transaction->rollBack();

                $transaction->commit();
                return ['message' => "Запрос изменен", 'data' => $model->id];
            }
            throw new ValidationErrorHttpException($model->getErrorSummary(false));
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
}
