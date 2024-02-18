<?php

namespace app\models;

use app\helpers\ColumnGridsHelper;
use app\helpers\DbHelper;
use app\helpers\JsonFieldNormalizer;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\Json;

/**
 * @property int         $id
 * @property int         $id_visual
 * @property int         $object_id              Номер здания
 * @property int         $offer_id               id предложения
 * @property string      $title                  Название
 * @property string      $photo_block
 * @property string      $building_layouts_block Планировки блока
 * @property string      $photos
 * @property string      $purposes_block
 * @property int|null    $is_land
 * @property int|null    $land
 * @property int|null    $land_width
 * @property int|null    $land_length
 * @property int|null    $area_floor
 * @property int|null    $area_floor_min
 * @property int|null    $area_floor_max
 * @property int|null    $area                   Площадь(вспомогательная)
 * @property int|null    $area_min               Площадь от
 * @property int|null    $area_max               2 - Площадь до
 * @property int|null    $racks                  Есть ли стеллажи
 * @property int|null    $rack_levels
 * @property int|null    $pallet_place           Палетт мест(вспомогательная)
 * @property int|null    $pallet_place_min       Палетт мест минимально
 * @property int|null    $pallet_place_max       Палетт мест максимально
 * @property int|null    $cells_place
 * @property int|null    $cells_place_min
 * @property int|null    $cells_place_max
 * @property int|null    $area_mezzanine         2 - Площадь мезонина
 * @property int|null    $area_mezzanine_min
 * @property int|null    $area_mezzanine_max
 * @property int|null    $area_office            2 - Площадь офисов
 * @property int|null    $area_office_min
 * @property int|null    $area_office_max
 * @property int|null    $area_tech
 * @property int|null    $area_tech_min
 * @property int|null    $area_tech_max
 * @property string|null $floor                  Этаж
 * @property string      $floor_types            Тип пола
 * @property int         $floor_id
 * @property int         $ceiling_height         высота потолков(вспомогательное)
 * @property float|null  $ceiling_height_min     Высота потолков от
 * @property float|null  $ceiling_height_max     2 - Высота потолков до
 * @property float|null  $floor_level            уровень пола
 * @property int|null    $temperature            температурный режим
 * @property int|null    $temperature_min        температура мин
 * @property int|null    $temperature_max        температура макс
 * @property float|null  $power                  эл-во на блок
 * @property string|null $description_auto       Описание авто
 * @property string|null $description            Описание
 * @property int         $description_manual_use Использовать ручное описание
 * @property int         $description_complex
 * @property int         $deleted                Удален
 * @property string      $photo_small            Фотографии блоков
 * @property int|null    $deal_type              Тип сделки
 * @property int         $heated                 Отапливаемый
 * @property string      $column_grids           Сетка колонн
 * @property float|null  $load_floor             Нагрузка на пол
 * @property float|null  $load_floor_min
 * @property float|null  $load_floor_max
 * @property int|null    $load_mezzanine         Нагрузка на мезонин
 * @property float|null  $load_mezzanine_min
 * @property float|null  $load_mezzanine_max
 * @property int|null    $gates_number           Количество ворот
 * @property int         $gate_type              Тип ворот
 * @property string      $gates                  Ворота
 * @property int         $warehouse_equipment
 * @property string|null $finishing              Готов
 * @property string|null $telphers               Тельферы
 * @property string|null $cranes_cathead         Кран балки
 * @property string|null $cranes
 * @property string|null $cranes_overhead        Мостовые краны
 * @property int|null    $has_cranes
 * @property string|null $elevators              Грузовые лифты
 * @property int|null    $has_elevators
 * @property int         $publ_time
 * @property int         $last_update
 * @property int         $order_row
 * @property int         $activity
 * @property int|null    $status_id
 * @property string|null $photo
 * @property int|null    $charging_room
 * @property int|null    $cranes_runways
 * @property int|null    $cross_docking
 * @property string|null $empty_line
 * @property string|null $empty_title_underline_mech
 * @property string|null $empty_title_underline_manual
 * @property string|null $empty_title_underline_complement
 * @property string|null $floor_types_land
 * @property int|null    $title_empty_main
 * @property string|null $photos_360_block
 * @property string|null $building_presentations_block
 * @property int|null    $landscape_type
 * @property int|null    $area_mezzanine_add
 * @property int|null    $area_office_add
 * @property int|null    $area_tech_add
 * @property string|null $rack_types
 * @property string|null $safe_type
 * @property int|null    $cells
 * @property int|null    $enterance_block
 * @property int|null    $area_field
 * @property int|null    $area_field_min
 * @property int|null    $area_field_max
 * @property int|null    $title_empty_stats
 * @property int|null    $title_empty_equipment
 * @property int|null    $title_empty_communications
 * @property int|null    $title_empty_security
 * @property int|null    $title_empty_cranes
 * @property int|null    $title_empty_elevators
 * @property string|null $lighting
 * @property int|null    $water
 * @property int|null    $sewage
 * @property int|null    $ventilation
 * @property int|null    $climate_control
 * @property int|null    $gas
 * @property int|null    $steam
 * @property int|null    $internet
 * @property string|null $internet_type
 * @property string|null $phone
 * @property string|null $water_type
 * @property int|null    $water_value
 * @property int|null    $firefighting_type
 * @property int|null    $smoke_exhaust
 * @property int|null    $video_control
 * @property int|null    $access_control
 * @property int|null    $security_alert
 * @property int|null    $fire_alert
 * @property int|null    $phone_line
 * @property int|null    $fence
 * @property int|null    $barrier
 */
class FloorPart extends ActiveRecord
{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string
	{
		return 'c_industry_parts';
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
			[['id_visual', 'object_id', 'offer_id', 'is_land', 'land', 'land_width', 'land_length', 'area_floor', 'area_floor_min', 'area_floor_max', 'area', 'area_min', 'area_max', 'racks', 'rack_levels', 'pallet_place', 'pallet_place_min', 'pallet_place_max', 'cells_place', 'cells_place_min', 'cells_place_max', 'area_mezzanine', 'area_mezzanine_min', 'area_mezzanine_max', 'area_office', 'area_office_min', 'area_office_max', 'area_tech', 'area_tech_min', 'area_tech_max', 'floor_id', 'ceiling_height', 'temperature', 'temperature_min', 'temperature_max', 'description_manual_use', 'description_complex', 'deleted', 'deal_type', 'heated', 'load_mezzanine', 'gates_number', 'gate_type', 'warehouse_equipment', 'has_cranes', 'has_elevators', 'publ_time', 'last_update', 'order_row', 'activity', 'status_id', 'charging_room', 'cranes_runways', 'cross_docking', 'title_empty_main', 'landscape_type', 'area_mezzanine_add', 'area_office_add', 'area_tech_add', 'cells', 'enterance_block', 'area_field', 'area_field_min', 'area_field_max', 'title_empty_stats', 'title_empty_equipment', 'title_empty_communications', 'title_empty_security', 'title_empty_cranes', 'title_empty_elevators', 'water', 'sewage', 'ventilation', 'climate_control', 'gas', 'steam', 'internet', 'water_value', 'firefighting_type', 'smoke_exhaust', 'video_control', 'access_control', 'security_alert', 'fire_alert', 'phone_line', 'fence', 'barrier'], 'integer'],
			[['offer_id', 'title', 'photo_block', 'building_layouts_block', 'photos', 'purposes_block', 'floor_id', 'ceiling_height', 'description_complex', 'photo_small', 'gates', 'publ_time', 'last_update', 'order_row', 'activity'], 'required'],
			[['photo_block', 'photos', 'description_auto', 'description', 'photo_small', 'photo', 'empty_line', 'empty_title_underline_mech', 'empty_title_underline_manual', 'empty_title_underline_complement', 'photos_360_block', 'building_presentations_block', 'rack_types', 'lighting', 'phone'], 'string'],
			[['ceiling_height_min', 'ceiling_height_max', 'floor_level', 'power', 'load_floor', 'load_floor_min', 'load_floor_max', 'load_mezzanine_min', 'load_mezzanine_max'], 'number'],
			[['title', 'purposes_block', 'telphers'], 'string', 'max' => 200],
			[['building_layouts_block', 'gates', 'elevators'], 'string', 'max' => 500],
			[['floor', 'finishing'], 'string', 'max' => 10],
			[['floor_types', 'column_grids', 'floor_types_land'], 'string', 'max' => 100],
			[['cranes_cathead', 'cranes', 'cranes_overhead'], 'string', 'max' => 300],
			[['safe_type', 'water_type'], 'string', 'max' => 30],
			[['internet_type'], 'string', 'max' => 20],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array
	{
		return [
			'id'                               => 'ID',
			'id_visual'                        => 'Id Visual',
			'object_id'                        => 'Object ID',
			'offer_id'                         => 'Offer ID',
			'title'                            => 'Title',
			'photo_block'                      => 'Photo Block',
			'building_layouts_block'           => 'Building Layouts Block',
			'photos'                           => 'Photos',
			'purposes_block'                   => 'Purposes Block',
			'is_land'                          => 'Is Land',
			'land'                             => 'Land',
			'land_width'                       => 'Land Width',
			'land_length'                      => 'Land Length',
			'area_floor'                       => 'Area Floor',
			'area_floor_min'                   => 'Area Floor Min',
			'area_floor_max'                   => 'Area Floor Max',
			'area'                             => 'Area',
			'area_min'                         => 'Area Min',
			'area_max'                         => 'Area Max',
			'racks'                            => 'Racks',
			'rack_levels'                      => 'Rack Levels',
			'pallet_place'                     => 'Pallet Place',
			'pallet_place_min'                 => 'Pallet Place Min',
			'pallet_place_max'                 => 'Pallet Place Max',
			'cells_place'                      => 'Cells Place',
			'cells_place_min'                  => 'Cells Place Min',
			'cells_place_max'                  => 'Cells Place Max',
			'area_mezzanine'                   => 'Area Mezzanine',
			'area_mezzanine_min'               => 'Area Mezzanine Min',
			'area_mezzanine_max'               => 'Area Mezzanine Max',
			'area_office'                      => 'Area Office',
			'area_office_min'                  => 'Area Office Min',
			'area_office_max'                  => 'Area Office Max',
			'area_tech'                        => 'Area Tech',
			'area_tech_min'                    => 'Area Tech Min',
			'area_tech_max'                    => 'Area Tech Max',
			'floor'                            => 'Floor',
			'floor_types'                      => 'Floor Types',
			'floor_id'                         => 'Floor ID',
			'ceiling_height'                   => 'Ceiling Height',
			'ceiling_height_min'               => 'Ceiling Height Min',
			'ceiling_height_max'               => 'Ceiling Height Max',
			'floor_level'                      => 'Floor Level',
			'temperature'                      => 'Temperature',
			'temperature_min'                  => 'Temperature Min',
			'temperature_max'                  => 'Temperature Max',
			'power'                            => 'Power',
			'description_auto'                 => 'Description Auto',
			'description'                      => 'Description',
			'description_manual_use'           => 'Description Manual Use',
			'description_complex'              => 'Description Complex',
			'deleted'                          => 'Deleted',
			'photo_small'                      => 'Photo Small',
			'deal_type'                        => 'Deal Type',
			'heated'                           => 'Heated',
			'column_grids'                     => 'Column Grids',
			'load_floor'                       => 'Load Floor',
			'load_floor_min'                   => 'Load Floor Min',
			'load_floor_max'                   => 'Load Floor Max',
			'load_mezzanine'                   => 'Load Mezzanine',
			'load_mezzanine_min'               => 'Load Mezzanine Min',
			'load_mezzanine_max'               => 'Load Mezzanine Max',
			'gates_number'                     => 'Gates Number',
			'gate_type'                        => 'Gate Type',
			'gates'                            => 'Gates',
			'warehouse_equipment'              => 'Warehouse Equipment',
			'finishing'                        => 'Finishing',
			'telphers'                         => 'Telphers',
			'cranes_cathead'                   => 'Cranes Cathead',
			'cranes'                           => 'Cranes',
			'cranes_overhead'                  => 'Cranes Overhead',
			'has_cranes'                       => 'Has Cranes',
			'elevators'                        => 'Elevators',
			'has_elevators'                    => 'Has Elevators',
			'publ_time'                        => 'Publ Time',
			'last_update'                      => 'Last Update',
			'order_row'                        => 'Order Row',
			'activity'                         => 'Activity',
			'status_id'                        => 'Status ID',
			'photo'                            => 'Photo',
			'charging_room'                    => 'Charging Room',
			'cranes_runways'                   => 'Cranes Runways',
			'cross_docking'                    => 'Cross Docking',
			'empty_line'                       => 'Empty Line',
			'empty_title_underline_mech'       => 'Empty Title Underline Mech',
			'empty_title_underline_manual'     => 'Empty Title Underline Manual',
			'empty_title_underline_complement' => 'Empty Title Underline Complement',
			'floor_types_land'                 => 'Floor Types Land',
			'title_empty_main'                 => 'Title Empty Main',
			'photos_360_block'                 => 'Photos 360 Block',
			'building_presentations_block'     => 'Building Presentations Block',
			'landscape_type'                   => 'Landscape Type',
			'area_mezzanine_add'               => 'Area Mezzanine Add',
			'area_office_add'                  => 'Area Office Add',
			'area_tech_add'                    => 'Area Tech Add',
			'rack_types'                       => 'Rack Types',
			'safe_type'                        => 'Safe Type',
			'cells'                            => 'Cells',
			'enterance_block'                  => 'Enterance Block',
			'area_field'                       => 'Area Field',
			'area_field_min'                   => 'Area Field Min',
			'area_field_max'                   => 'Area Field Max',
			'title_empty_stats'                => 'Title Empty Stats',
			'title_empty_equipment'            => 'Title Empty Equipment',
			'title_empty_communications'       => 'Title Empty Communications',
			'title_empty_security'             => 'Title Empty Security',
			'title_empty_cranes'               => 'Title Empty Cranes',
			'title_empty_elevators'            => 'Title Empty Elevators',
			'lighting'                         => 'Lighting',
			'water'                            => 'Water',
			'sewage'                           => 'Sewage',
			'ventilation'                      => 'Ventilation',
			'climate_control'                  => 'Climate Control',
			'gas'                              => 'Gas',
			'steam'                            => 'Steam',
			'internet'                         => 'Internet',
			'internet_type'                    => 'Internet Type',
			'phone'                            => 'Phone',
			'water_type'                       => 'Water Type',
			'water_value'                      => 'Water Value',
			'firefighting_type'                => 'Firefighting Type',
			'smoke_exhaust'                    => 'Smoke Exhaust',
			'video_control'                    => 'Video Control',
			'access_control'                   => 'Access Control',
			'security_alert'                   => 'Security Alert',
			'fire_alert'                       => 'Fire Alert',
			'phone_line'                       => 'Phone Line',
			'fence'                            => 'Fence',
			'barrier'                          => 'Barrier',
		];
	}

	/**
	 * @return array
	 */
	public function getPhotos(): array
	{
		return Json::decode($this->photo_block) ?? [];
	}

	public function getFloorTypes(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->floor_types);
	}

	/**
	 * @return array
	 */
	public function getColumnGrids(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->column_grids);
	}

	/**
	 * @return array
	 */
	public function getCranesCatHead(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_cathead);
	}

	/**
	 * @return array
	 */
	public function getCranesOverHead(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->cranes_overhead);
	}

	/**
	 * @return array
	 */
	public function getGates(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->gates);
	}

	/**
	 * @return array
	 */
	public function getPurposesBlock(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->purposes_block);
	}

	/**
	 * @return array
	 */
	public function getTelphers(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->telphers);
	}

	/**
	 * @return array
	 */
	public function getInternetType(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->internet_type);
	}

	/**
	 * @return array
	 */
	public function getFloorTypesLand(): array
	{
		return JsonFieldNormalizer::jsonToArrayWithIntElements($this->floor_types_land);
	}

	/**
	 * @return array
	 */
	public function fields(): array
	{
		$f = parent::fields();

		$f['photos']                      = function () {
			return $this->getPhotos();
		};
		$f['is_active']                   = function () {
			return $this->isActive();
		};
		$f['floor_types']                 = function () {
			return $this->getFloorTypes();
		};
		$f['column_grids']                = function () {
			return $this->getColumnGrids();
		};
		$f['column_grids_human_readable'] = function () {
			return ColumnGridsHelper::toHumanReadable($this->getColumnGrids());
		};
		$f['cranes_cathead']              = function () {
			return $this->getCranesCatHead();
		};
		$f['cranes_overhead']             = function () {
			return $this->getCranesOverHead();
		};
		$f['gates']                       = function () {
			return $this->getGates();
		};
		$f['purposes_block']              = function () {
			return $this->getPurposesBlock();
		};
		$f['telphers']                    = function () {
			return $this->getTelphers();
		};
		$f['internet_type']               = function () {
			return $this->getInternetType();
		};
		$f['floor_types_land']               = function () {
			return $this->getFloorTypesLand();
		};

		return $f;
	}

	/**
	 * @return array
	 */
	public function extraFields(): array
	{
		$f = parent::extraFields();

		$f['lastDeal'] = 'lastDeal';

		$f['floorTypes'] = function () {
			return FloorType::find()->where(['id' => $this->getFloorTypes()])->all();
		};

		return $f;
	}

	/**
	 * @return Deal|null
	 */
	public function getLastDeal(): ?Deal
	{
		return Deal::find()->joinWith(['block' => function (ActiveQuery $query) {
			return $query->from(['block' => DbHelper::getDsnAttribute('dbname', Block::getDb()->dsn) . '.' . Block::tableName()])
			             ->andWhere(['LIKE', 'block.parts', "\"{$this->id}\""])
			             ->with(['company']);
		}], false)
		           ->with(['company'])
		           ->orderBy(['id' => SORT_DESC])
		           ->one();
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return (int)Block::find()->select('deal_id')->orderBy(['id' => SORT_DESC])->andWhere(['LIKE', 'parts', "\"{$this->id}\""])->scalar() <= 0;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getFloor(): ActiveQuery
	{
		return $this->hasOne(Floor::class, ['id' => 'floor_id']);
	}
}
