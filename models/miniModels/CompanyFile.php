<?php

namespace app\models\miniModels;

use app\models\Company;
use Yii;

/**
 * This is the model class for table "company_file".
 *
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property string $filename
 * @property string $size
 * @property string|null $type
 * @property string|null $created_at
 *
 * @property Company $company
 */
class CompanyFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'name', 'filename', 'size'], 'required'],
            [['company_id'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'filename', 'size', 'type'], 'string', 'max' => 255],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
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
            'name' => 'Name',
            'filename' => 'Filename',
            'size' => 'Size',
            'type' => 'Type',
            'created_at' => 'Created At',
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

    public function fields()
    {
        $fields = parent::fields();
        $fields['src'] = function ($fields) {
            return 'http://' . $_SERVER['HTTP_HOST'] . '/uploads/' . $fields['filename'];
        };
        return $fields;
    }
}
