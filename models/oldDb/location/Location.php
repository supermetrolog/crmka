<?php

namespace app\models\oldDb\location;

use Yii;

/**
 * This is the model class for table "l_locations".
 *
 * @property int $id
 * @property int|null $region
 * @property int|null $outside_mkad
 * @property int|null $show_inside_mkad
 * @property int|null $show_in_mo
 * @property int|null $near_mo
 * @property int|null $cian_region
 * @property int|null $town
 * @property int|null $town_type
 * @property int|null $town_central
 * @property int|null $town_central_type
 * @property string|null $towns_relevant
 * @property int|null $direction
 * @property string|null $direction_relevant
 * @property int|null $district_moscow
 * @property string|null $district_moscow_relevant
 * @property int|null $district
 * @property int|null $district_type
 * @property int|null $district_former
 * @property int|null $district_former_type
 * @property int|null $highway
 * @property string|null $highways_relevant
 * @property int|null $highway_moscow
 * @property string|null $highways_moscow_relevant
 * @property int|null $metro
 * @property int|null $deleted
 * @property int|null $empty_line
 * @property int|null $publ_time
 * @property int|null $description
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'l_locations';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_old');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region', 'outside_mkad', 'show_inside_mkad', 'show_in_mo', 'near_mo', 'cian_region', 'town', 'town_type', 'town_central', 'town_central_type', 'direction', 'district_moscow', 'district', 'district_type', 'district_former', 'district_former_type', 'highway', 'highway_moscow', 'metro', 'deleted', 'empty_line', 'publ_time', 'description'], 'integer'],
            [['towns_relevant', 'direction_relevant', 'district_moscow_relevant', 'highways_relevant', 'highways_moscow_relevant'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region' => 'Region',
            'outside_mkad' => 'Outside Mkad',
            'show_inside_mkad' => 'Show Inside Mkad',
            'show_in_mo' => 'Show In Mo',
            'near_mo' => 'Near Mo',
            'cian_region' => 'Cian Region',
            'town' => 'Town',
            'town_type' => 'Town Type',
            'town_central' => 'Town Central',
            'town_central_type' => 'Town Central Type',
            'towns_relevant' => 'Towns Relevant',
            'direction' => 'Direction',
            'direction_relevant' => 'Direction Relevant',
            'district_moscow' => 'District Moscow',
            'district_moscow_relevant' => 'District Moscow Relevant',
            'district' => 'District',
            'district_type' => 'District Type',
            'district_former' => 'District Former',
            'district_former_type' => 'District Former Type',
            'highway' => 'Highway',
            'highways_relevant' => 'Highways Relevant',
            'highway_moscow' => 'Highway Moscow',
            'highways_moscow_relevant' => 'Highways Moscow Relevant',
            'metro' => 'Metro',
            'deleted' => 'Deleted',
            'empty_line' => 'Empty Line',
            'publ_time' => 'Publ Time',
            'description' => 'Description',
        ];
    }
}
