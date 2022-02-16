<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "companygroup".
 *
 * @property int $id
 * @property string $nameEng
 * @property string $nameRu
 * @property int|null $formOfOrganization
 * @property string|null $description
 *
 * @property Company[] $companies
 */
class Companygroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companygroup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nameRu'], 'required'],
            [['description'], 'string'],
            [['formOfOrganization'], 'integer'],
            [['nameEng', 'nameRu'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nameEng' => 'Name Eng',
            'nameRu' => 'Name Ru',
            'description' => 'Description',
            'formOfOrganization' => 'FormOfOrganization'
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['full_name'] = function ($fields) {
            $formOfOrganization = $fields['formOfOrganization'];
            $nameEng = $fields['nameEng'];
            $nameRu = $fields['nameRu'];
            $name = "";
            if ($formOfOrganization !== null) {
                $name .= Company::FORM_OF_ORGANIZATION_LIST[$formOfOrganization];
            }
            $name .= " $nameRu";
            if ($nameEng) {
                $name .= " - $nameEng";
            }
            return trim($name);
        };
        return $fields;
    }
    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['companyGroup_id' => 'id']);
    }
}
