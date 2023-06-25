<?php

namespace app\models\location;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is the model class for table "l_directions".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $title_en
 * @property string|null $title_eng
 * @property string|null $link
 * @property string|null $icon
 * @property string|null $sklad_rent_title
 * @property string|null $sklad_rent_text
 * @property string|null $sklad_sale_title
 * @property string|null $sklad_sale_text
 * @property string|null $industry_rent_title
 * @property string|null $industry_rent_text
 * @property string|null $industry_sale_title
 * @property string|null $industry_sale_text
 * @property string|null $promland_title
 * @property string|null $promland_text
 * @property string|null $coords
 * @property string|null $title_short
 * @property string|null $title_short1
 * @property string $description
 * @property int $order_row
 * @property int $publ_time
 * @property int $activity
 * @property int|null $exclude
 * @property int|null $deleted
 */
class Direction extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'l_directions';
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
            [['sklad_rent_title', 'sklad_rent_text', 'sklad_sale_title', 'sklad_sale_text', 'industry_rent_title', 'industry_rent_text', 'industry_sale_title', 'industry_sale_text', 'promland_title', 'promland_text', 'coords', 'description'], 'string'],
            [['description', 'order_row', 'publ_time', 'activity'], 'required'],
            [['order_row', 'publ_time', 'activity', 'exclude', 'deleted'], 'integer'],
            [['title', 'title_en', 'title_eng', 'link'], 'string', 'max' => 20],
            [['icon', 'title_short', 'title_short1'], 'string', 'max' => 200],
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
            'title_en' => 'Title En',
            'title_eng' => 'Title Eng',
            'link' => 'Link',
            'icon' => 'Icon',
            'sklad_rent_title' => 'Sklad Rent Title',
            'sklad_rent_text' => 'Sklad Rent Text',
            'sklad_sale_title' => 'Sklad Sale Title',
            'sklad_sale_text' => 'Sklad Sale Text',
            'industry_rent_title' => 'Industry Rent Title',
            'industry_rent_text' => 'Industry Rent Text',
            'industry_sale_title' => 'Industry Sale Title',
            'industry_sale_text' => 'Industry Sale Text',
            'promland_title' => 'Promland Title',
            'promland_text' => 'Promland Text',
            'coords' => 'Coords',
            'title_short' => 'Title Short',
            'title_short1' => 'Title Short1',
            'description' => 'Description',
            'order_row' => 'Order Row',
            'publ_time' => 'Publ Time',
            'activity' => 'Activity',
            'exclude' => 'Exclude',
            'deleted' => 'Deleted',
        ];
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
            'title_short' => $fields['title_short'],
            'title_eng' => $fields['title_eng'],
            'exclude' => $fields['exclude'],
            'deleted' => $fields['deleted'],
        ];
    }
}
