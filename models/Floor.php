<?php

namespace app\models;

use app\helpers\JsonFieldNormalizer;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\Json;

/**
 * @property int         $id
 * @property string|null $title
 * @property int|null    $object_id
 * @property string|null $floor_num
 * @property int|null    $floor_num_id
 * @property string|null $description
 * @property int|null    $area_floor_full
 * @property int|null    $area_mezzanine_full
 * @property int|null    $area_office_full
 * @property int|null    $area_tech_full
 * @property int|null    $area_field_full
 * @property string|null $floor_type
 * @property string|null $floor_types
 * @property string|null $floor_types_land
 * @property int|null    $load_floor
 * @property float|null  $load_floor_min
 * @property float|null  $load_floor_max
 * @property string|null $gates
 * @property int|null    $column_grid
 * @property string|null $column_grids
 * @property int|null    $column_grid_width
 * @property int|null    $column_grid_length
 * @property int|null    $ceiling_height
 * @property float|null  $ceiling_height_min
 * @property float|null  $ceiling_height_max
 * @property int|null    $temperature
 * @property int|null    $temperature_min
 * @property int|null    $temperature_max
 * @property string|null $lighting
 * @property int|null    $lighting_value
 * @property int|null    $power
 * @property int|null    $power_value
 * @property int|null    $heated
 * @property int|null    $heating
 * @property int|null    $heating_value
 * @property int|null    $water
 * @property int|null    $sewage
 * @property int|null    $sewage_value
 * @property int|null    $ventilation
 * @property int|null    $climate_control
 * @property int|null    $firefighting_type
 * @property int|null    $smoke_exhaust
 * @property int|null    $video_control
 * @property int|null    $access_control
 * @property int|null    $security_alert
 * @property int|null    $fire_alert
 * @property int|null    $gas
 * @property int|null    $steam
 * @property int|null    $internet
 * @property int|null    $internet_value
 * @property int|null    $phone_line
 * @property string|null $lifts
 * @property string|null $elevators
 * @property string|null $hoists
 * @property string|null $cranes_overhead
 * @property string|null $cranes_cathead
 * @property string|null $telphers
 * @property int|null    $deleted
 * @property int|null    $order_row
 * @property int         $publ_time
 * @property int         $last_update
 * @property string|null $photo_block
 * @property int|null    $title_empty_main
 * @property int|null    $title_empty_infrastructure
 * @property int|null    $title_empty_security
 * @property int|null    $title_empty_communications
 * @property int|null    $title_empty_stats
 * @property int|null    $title_empty_cranes
 * @property int|null    $fence
 * @property int|null    $barrier
 * @property int|null    $land
 * @property int|null    $land_length
 * @property int|null    $land_width
 * @property int|null    $landscape_type
 */
class Floor extends ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'c_industry_floors';
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
			[['object_id', 'floor_num_id', 'area_floor_full', 'area_mezzanine_full', 'area_office_full', 'area_tech_full', 'area_field_full', 'load_floor', 'column_grid', 'column_grid_width', 'column_grid_length', 'ceiling_height', 'temperature', 'temperature_min', 'temperature_max', 'lighting_value', 'power', 'power_value', 'heated', 'heating', 'heating_value', 'water', 'sewage', 'sewage_value', 'ventilation', 'climate_control', 'firefighting_type', 'smoke_exhaust', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'gas', 'steam', 'internet', 'internet_value', 'phone_line', 'deleted', 'order_row', 'publ_time', 'last_update', 'title_empty_main', 'title_empty_infrastructure', 'title_empty_security', 'title_empty_communications', 'title_empty_stats', 'title_empty_cranes', 'fence', 'barrier', 'land', 'land_length', 'land_width', 'landscape_type'], 'integer'],
			[['load_floor_min', 'load_floor_max', 'ceiling_height_min', 'ceiling_height_max'], 'number'],
			[['publ_time', 'last_update'], 'required'],
			[['photo_block'], 'string'],
			[['title', 'description', 'column_grids'], 'string', 'max' => 100],
			[['floor_num'], 'string', 'max' => 20],
			[['floor_type', 'floor_types', 'floor_types_land', 'lifts', 'elevators', 'hoists', 'cranes_overhead', 'cranes_cathead', 'telphers'], 'string', 'max' => 300],
			[['gates'], 'string', 'max' => 1000],
			[['lighting'], 'string', 'max' => 30],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'                         => 'ID',
			'title'                      => 'Title',
			'object_id'                  => 'Object ID',
			'floor_num'                  => 'Floor Num',
			'floor_num_id'               => 'Floor Num ID',
			'description'                => 'Description',
			'area_floor_full'            => 'Area Floor Full',
			'area_mezzanine_full'        => 'Area Mezzanine Full',
			'area_office_full'           => 'Area Office Full',
			'area_tech_full'             => 'Area Tech Full',
			'area_field_full'            => 'Area Field Full',
			'floor_type'                 => 'Floor Type',
			'floor_types'                => 'Floor Types',
			'floor_types_land'           => 'Floor Types Land',
			'load_floor'                 => 'Load Floor',
			'load_floor_min'             => 'Load Floor Min',
			'load_floor_max'             => 'Load Floor Max',
			'gates'                      => 'Gates',
			'column_grid'                => 'Column Grid',
			'column_grids'               => 'Column Grids',
			'column_grid_width'          => 'Column Grid Width',
			'column_grid_length'         => 'Column Grid Length',
			'ceiling_height'             => 'Ceiling Height',
			'ceiling_height_min'         => 'Ceiling Height Min',
			'ceiling_height_max'         => 'Ceiling Height Max',
			'temperature'                => 'Temperature',
			'temperature_min'            => 'Temperature Min',
			'temperature_max'            => 'Temperature Max',
			'lighting'                   => 'Lighting',
			'lighting_value'             => 'Lighting Value',
			'power'                      => 'Power',
			'power_value'                => 'Power Value',
			'heated'                     => 'Heated',
			'heating'                    => 'Heating',
			'heating_value'              => 'Heating Value',
			'water'                      => 'Water',
			'sewage'                     => 'Sewage',
			'sewage_value'               => 'Sewage Value',
			'ventilation'                => 'Ventilation',
			'climate_control'            => 'Climate Control',
			'firefighting_type'          => 'Firefighting Type',
			'smoke_exhaust'              => 'Smoke Exhaust',
			'video_control'              => 'Video Control',
			'access_control'             => 'Access Control',
			'security_alert'             => 'Security Alert',
			'fire_alert'                 => 'Fire Alert',
			'gas'                        => 'Gas',
			'steam'                      => 'Steam',
			'internet'                   => 'Internet',
			'internet_value'             => 'Internet Value',
			'phone_line'                 => 'Phone Line',
			'lifts'                      => 'Lifts',
			'elevators'                  => 'Elevators',
			'hoists'                     => 'Hoists',
			'cranes_overhead'            => 'Cranes Overhead',
			'cranes_cathead'             => 'Cranes Cathead',
			'telphers'                   => 'Telphers',
			'deleted'                    => 'Deleted',
			'order_row'                  => 'Order Row',
			'publ_time'                  => 'Publ Time',
			'last_update'                => 'Last Update',
			'photo_block'                => 'Photo Block',
			'title_empty_main'           => 'Title Empty Main',
			'title_empty_infrastructure' => 'Title Empty Infrastructure',
			'title_empty_security'       => 'Title Empty Security',
			'title_empty_communications' => 'Title Empty Communications',
			'title_empty_stats'          => 'Title Empty Stats',
			'title_empty_cranes'         => 'Title Empty Cranes',
			'fence'                      => 'Fence',
			'barrier'                    => 'Barrier',
			'land'                       => 'Land',
			'land_length'                => 'Land Length',
			'land_width'                 => 'Land Width',
			'landscape_type'             => 'Landscape Type',
		];
	}

	/**
	 * @return array
	 */
	public function getFloorTypes(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->floor_types);
	}

	/**
	 * @return array
	 */
	public function getPhotos(): array
	{
		return Json::decode($this->photo_block) ?? [];
	}

	/**
	 * @return array
	 */
	public function fields(): array
	{
		$f = parent::fields();

		unset($f['photo_block']);

		$f['floor_types'] = function () {
			return $this->getFloorTypes();
		};
		$f['photos']      = function () {
			return $this->getPhotos();
		};

		return $f;
	}

	/**
	 * @return array
	 */
	public function extraFields(): array
	{
		$f = parent::extraFields();

		$f['floorTypes'] = function () {
			return FloorType::find()->where(['id' => $this->getFloorTypes()])->all();
		};

		return $f;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getNumber(): ActiveQuery
	{
		return $this->hasOne(FloorNumber::class, ['id' => 'floor_num_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getParts(): ActiveQuery
	{
		return $this->hasMany(FloorPart::class, ['floor_id' => 'id'])
		            ->andOnCondition(['!=', FloorPart::tableName() . '.deleted', 1]);
	}
}
