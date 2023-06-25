<?php

namespace app\models\location;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_towns".
 *
 * @property int $id
 * @property string $title
 * @property string $title_eng
 * @property string|null $town_type
 * @property string|null $town_district
 * @property int|null $exclude
 * @property int|null $deleted
 * @property int|null $publ_time
 * @property int|null $last_update
 * @property int|null $activity
 * @property int|null $description
 */
class Town extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_towns';
    }

    /**
     * @return Connection
     * @throws InvalidConfigException
     */
    public static function getDb(): Connection
    {
        return Yii::$app->get('db_old');
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'title_eng'], 'required'],
            [['exclude', 'deleted', 'publ_time', 'last_update', 'activity', 'description'], 'integer'],
            [['title', 'title_eng', 'town_type', 'town_district'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'title_eng' => 'Title Eng',
            'town_type' => 'Town Type',
            'town_district' => 'Town District',
            'exclude' => 'Exclude',
            'deleted' => 'Deleted',
            'publ_time' => 'Publ Time',
            'last_update' => 'Last Update',
            'activity' => 'Activity',
            'description' => 'Description',
        ];
    }

    /**
     * @return int|null
     */
    public function getTownType(): ?int
    {
        return is_null($this->town_type) ? null : (int) $this->town_type;
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        $fields = parent::fields();

        return [
            'id' => $fields['id'],
            'title' => $fields['title'],
            'title_eng' => $fields['title_eng'],
            'town_type' => function () { return $this->getTownType(); },
            'exclude' => $fields['exclude'],
            'deleted' => $fields['deleted'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTownTypeRecord(): ActiveQuery
    {
        return $this->hasOne(TownType::class, ['id' => 'town_type']);
    }
}
